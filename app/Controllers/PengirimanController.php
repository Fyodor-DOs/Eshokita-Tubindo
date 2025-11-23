<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class PengirimanController extends BaseController
{
    protected $pengirimanModel;
    protected $customerModel;
    protected $ruteModel;

    public function __construct()
    {
        $this->pengirimanModel = new \App\Models\PengirimanModel();
        $this->customerModel = new \App\Models\CustomerModel();
        $this->ruteModel = new \App\Models\RuteModel();
    }

    public function index()
    {
        $data['pengiriman'] = $this->pengirimanModel
            ->select('pengiriman.*, customer.nama as nama_customer, rute.nama_wilayah as nama_wilayah')
            ->join('customer', 'pengiriman.id_customer = customer.id_customer', 'left')
            ->join('rute', 'pengiriman.kode_rute = rute.kode_rute', 'left')
            ->findAll();

        return view('pages/pengiriman/index', $data);
    }

    // Upload foto Surat Jalan -> ubah status ke 'mengirim'
    public function uploadSuratJalan($id)
    {
        $file = $this->request->getFile('foto');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid']);
        }
        $dir = FCPATH . 'uploads/suratjalan';
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
        $ext = $file->getExtension() ?: 'jpg';
        $newName = 'sj_' . $id . '_' . time() . '.' . $ext;
        try {
            $file->move($dir, $newName, true);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memindahkan file: '.$e->getMessage()]);
        }

        // Simpan langsung via Query Builder untuk menghindari kendala allowedFields/Model
        $db = \Config\Database::connect();
        $ok = $db->table('pengiriman')->where('id_pengiriman', (int)$id)->update([
            'foto_surat_jalan' => $newName,
            'status' => 'mengirim',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if (!$ok) {
            return $this->response->setJSON(['success' => false, 'message' => 'DB update gagal']);
        }
        // catat tracking
        try { $t = new \App\Models\ShipmentTrackingModel(); $t->insert(['id_pengiriman'=>$id,'status'=>'on-route','note'=>'Foto SJ diunggah']); } catch (\Throwable $th) {}
        return $this->response->setJSON(['success' => true, 'message' => 'Foto SJ diunggah', 'file' => $newName]);
    }

    // Upload foto Penerimaan -> ubah status ke 'diterima'
    public function uploadPenerimaan($id)
    {
        $db = \Config\Database::connect();
        // Pastikan sudah ada Surat Jalan terlebih dahulu
        $row = $db->table('pengiriman')->where('id_pengiriman', (int)$id)->get()->getRowArray();
        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pengiriman tidak ditemukan']);
        }
        if (empty($row['foto_surat_jalan'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Silakan upload Surat Jalan terlebih dahulu']);
        }

        $file = $this->request->getFile('foto');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid']);
        }
        $dir = FCPATH . 'uploads/penerimaan';
        if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
        $ext = $file->getExtension() ?: 'jpg';
        $newName = 'terima_' . $id . '_' . time() . '.' . $ext;
        try {
            $file->move($dir, $newName, true);
        } catch (\Throwable $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memindahkan file: '.$e->getMessage()]);
        }

        $ok = $db->table('pengiriman')->where('id_pengiriman', (int)$id)->update([
            'foto_penerimaan' => $newName,
            'status' => 'diterima',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if (!$ok) {
            return $this->response->setJSON(['success' => false, 'message' => 'DB update gagal']);
        }
        try { $t = new \App\Models\ShipmentTrackingModel(); $t->insert(['id_pengiriman'=>$id,'status'=>'delivered','note'=>'Penerimaan diunggah']); } catch (\Throwable $th) {}
        return $this->response->setJSON(['success' => true, 'message' => 'Bukti diterima diunggah', 'file' => $newName]);
    }

    public function create()
    {
        $db = \Config\Database::connect();
        if ($this->request->getMethod() == 'POST') {
            $invoiceIds = array_values(array_filter(array_map('intval', (array) $this->request->getPost('invoice_ids'))));
            if (empty($invoiceIds)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Pilih minimal satu invoice yang akan dikirim']);
            }

            $supir = $this->request->getPost('supir') ?? '';
            $kenek = $this->request->getPost('kenek') ?? '';
            $plat  = $this->request->getPost('plat_kendaraan') ?? '';
            $tanggalInput = $this->request->getPost('tanggal') ?: date('Y-m-d');
            $tanggal = date('Y-m-d H:i:s', strtotime($tanggalInput));

            // Ambil semua invoice + transaction + customer
            $rows = $db->query(
                "SELECT i.id_invoice, i.id_transaction, c.id_customer, c.kode_rute
                 FROM invoice i
                 JOIN transaction t ON i.id_transaction = t.id_transaction
                 JOIN customer c ON t.id_customer = c.id_customer
                 WHERE i.id_invoice IN (" . implode(',', array_fill(0, count($invoiceIds), '?')) . ")",
                $invoiceIds
            )->getResultArray();
            if (empty($rows) || count($rows) !== count($invoiceIds)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Sebagian invoice tidak ditemukan']);
            }

            // Validasi: semua invoice harus untuk RUTE yang sama (bukan customer yang sama)
            $kodeRute = $this->request->getPost('kode_rute');
            if (!$kodeRute) {
                return $this->response->setJSON(['success' => false, 'message' => 'Pilih rute pengiriman terlebih dahulu']);
            }
            
            // Cek apakah semua invoice memiliki rute yang sama dengan yang dipilih
            $ruteTidakSesuai = [];
            foreach ($rows as $r) {
                if ($r['kode_rute'] !== $kodeRute) {
                    $ruteTidakSesuai[] = $r['id_invoice'];
                }
            }
            if (!empty($ruteTidakSesuai)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Semua invoice harus memiliki rute yang sama dengan rute pengiriman yang dipilih']);
            }

            // TIDAK PERLU idCustomer tunggal karena 1 BON bisa untuk banyak customer di rute yang sama
            // Kita hanya simpan id_customer pertama untuk backward compatibility dengan schema existing
            $idCustomer = (int)$rows[0]['id_customer'];

            // Ambil metode pembayaran terakhir dari salah satu invoice
            $lastPay = $db->table('payment')
                ->whereIn('id_invoice', $invoiceIds)
                ->orderBy('paid_at', 'DESC')
                ->get()->getRowArray();
            $payMethod = strtolower($lastPay['method'] ?? 'cash');
            if (!in_array($payMethod, ['cash','kredit','transfer'])) $payMethod = 'cash';

            // Gabungkan items dari semua transaksi
            $productModel = new \App\Models\ProductModel();
            $pemesanan = [];
            foreach ($rows as $r) {
                $trx = $db->table('transaction')->where('id_transaction', (int)$r['id_transaction'])->get()->getRowArray();
                if (!$trx) continue;
                $items = json_decode($trx['items'] ?? '[]', true) ?: [];
                foreach ($items as $it) {
                    $prod = $productModel->find((int)($it['id_product'] ?? 0));
                    if (!$prod) continue;
                    $qty = (int)($it['qty'] ?? 0);
                    $harga = (float)($it['price'] ?? 0);
                    $total = (float)($it['subtotal'] ?? ($qty*$harga));
                    $pemesanan[] = [
                        'id_product' => (int)$prod['id_product'],
                        'qty' => $qty,
                        'harga' => $harga,
                        'total' => $total,
                    ];
                }
            }

            // Buat 1 BON untuk semua invoice terpilih
            $noBon = $this->pengirimanModel->generateNoBon();
            $payload = [
                'tanggal' => $tanggal,
                'no_bon' => $noBon,
                'supir' => $supir,
                'kenek' => $kenek,
                'plat_kendaraan' => $plat,
                'kode_rute' => $kodeRute,
                'id_customer' => $idCustomer,
                'nama_penerima' => '',
                'pembayaran' => $payMethod,
                'pemesanan' => !empty($pemesanan) ? json_encode($pemesanan) : '[]',
                'ttd_penerima' => '',
            ];

            $db->transStart();
            $okInsert = $this->pengirimanModel->insert($payload);
            if ($okInsert) {
                $idPengirimanBaru = (int)$this->pengirimanModel->getInsertID();
                // tautkan semua invoice ke BON ini jika kolom tersedia
                try {
                    $fields = $db->getFieldNames('invoice');
                    if (in_array('id_pengiriman', $fields)) {
                        $db->table('invoice')->whereIn('id_invoice', $invoiceIds)->update([
                            'id_pengiriman' => $idPengirimanBaru,
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } catch (\Throwable $th) { /* ignore and continue */ }
            }
            $db->transComplete();

            if ($db->transStatus() === false || !$okInsert) {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal membuat pengiriman']);
            }

            return $this->response->setJSON(['success' => true, 'message' => '1 pengiriman (BON) dibuat dari invoice terpilih', 'url' => '/pengiriman']);
        }

                // GET: tampilkan HANYA invoice siap dikirim (PAID SAJA) dan BELUM memiliki pengiriman yang terhubung
    $fields = $db->getFieldNames('invoice');
    $hasInvoicePengiriman = in_array('id_pengiriman', $fields);

                $where = "WHERE i.status = 'paid'";
        if ($hasInvoicePengiriman) {
            // Hanya invoice yang belum pernah dibuatkan pengiriman
            $where .= " AND (i.id_pengiriman IS NULL OR i.id_pengiriman = 0)";
        }
                $sql = "
                        SELECT i.id_invoice, i.invoice_no, i.issue_date, i.amount, i.status,
                                     c.id_customer, c.nama AS customer_name, c.kode_rute
                        FROM invoice i
                        JOIN transaction t ON i.id_transaction = t.id_transaction
                        JOIN customer c ON t.id_customer = c.id_customer
                        $where
                        ORDER BY c.kode_rute, c.nama, i.issue_date DESC
                ";
    $data['invoiceCandidates'] = $db->query($sql)->getResultArray();

        $data['rutes'] = $this->ruteModel->findAll();
        return view("pages/pengiriman/create", $data);
    }

    /**
     * Generate Pengiriman dari Invoice yang sudah dibuat (idealnya sudah dibayar)
     * - Ambil invoice -> transaction -> customer & items
     * - Mapping items ke struktur pemesanan (besar/kecil/serut) jika SKU produk cocok
     * - Buat pengiriman dan arahkan ke detail (agar bisa lanjut Tracking & Penerimaan)
     */
    public function createFromInvoice($idInvoice)
    {
        $db = \Config\Database::connect();
        // Ambil invoice, join transaction & customer
        $invoice = $db->table('invoice')->where('id_invoice', (int)$idInvoice)->get()->getRowArray();
        if (!$invoice) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invoice tidak ditemukan']);
        }
        // Ambil transaction untuk items & customer
        $trx = null;
        if (!empty($invoice['id_transaction'])) {
            $trx = $db->table('transaction')->where('id_transaction', $invoice['id_transaction'])->get()->getRowArray();
        }
        if (!$trx) {
            return $this->response->setJSON(['success' => false, 'message' => 'Transaksi untuk invoice ini tidak ditemukan']);
        }
        $customer = $db->table('customer')->where('id_customer', $trx['id_customer'])->get()->getRowArray();
        if (!$customer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Customer tidak ditemukan']);
        }

        // Decode items transaksi
        $items = json_decode($trx['items'] ?? '[]', true) ?: [];
        // Map produk -> struktur pemesanan (besar/kecil/serut) jika ada SKU standar
        $productModel = new \App\Models\ProductModel();
        $pemesanan = [];
        foreach ($items as $it) {
            $prod = $productModel->find((int)($it['id_product'] ?? 0));
            if (!$prod) continue;
            $key = null;
            $sku = strtoupper($prod['sku'] ?? '');
            if (str_contains($sku, 'BESAR')) $key = 'besar';
            elseif (str_contains($sku, 'KECIL')) $key = 'kecil';
            elseif (str_contains($sku, 'SERUT')) $key = 'serut';
            // default: skip jika tidak dikenali agar tidak bentrok dengan UI existing
            if (!$key) continue;
            $qty = (int)($it['qty'] ?? 0);
            $harga = (float)($it['price'] ?? 0);
            $total = (float)($it['subtotal'] ?? ($qty * $harga));
            $pemesanan[$key] = [ 'qty' => $qty, 'harga' => $harga, 'total' => $total ];
        }

        // Cari metode pembayaran terakhir (jika ada) untuk set enum pembayaran
        $lastPay = $db->table('payment')
            ->where('id_invoice', (int)$idInvoice)
            ->orderBy('paid_at', 'DESC')
            ->get()->getRowArray();
        $payMethod = strtolower($lastPay['method'] ?? 'cash');
        if (!in_array($payMethod, ['cash', 'kredit', 'transfer'])) {
            $payMethod = 'cash';
        }

        // Generate No BON & buat pengiriman
        $noBon = $this->pengirimanModel->generateNoBon();
        $payload = [
            'tanggal' => date('Y-m-d H:i:s'),
            'no_bon' => $noBon,
            'supir' => null,
            'kenek' => null,
            'plat_kendaraan' => null,
            'kode_rute' => $customer['kode_rute'] ?? '',
            'id_customer' => (int)$customer['id_customer'],
            'nama_penerima' => '',
            'pembayaran' => $payMethod,
            'pemesanan' => !empty($pemesanan) ? json_encode($pemesanan) : '[]',
            'ttd_penerima' => '',
        ];
        if ($this->pengirimanModel->insert($payload)) {
            $idPengiriman = (int)$this->pengirimanModel->getInsertID();
            // Jika kolom id_pengiriman ada di table invoice, simpan relasinya
            try {
                $fields = $db->getFieldNames('invoice');
                if (in_array('id_pengiriman', $fields)) {
                    $db->table('invoice')->where('id_invoice', (int)$idInvoice)->update(['id_pengiriman' => $idPengiriman]);
                }
            } catch (\Throwable $th) { /* ignore */ }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengiriman dibuat dari invoice. Lanjutkan ke detail untuk Tracking/Penerimaan.',
                'url' => base_url('pengiriman/detail/'.$idPengiriman)
            ]);
        }
        return $this->response->setJSON(['success' => false, 'message' => $this->pengirimanModel->errors()]);
    }

    public function detail($id)
    {
        $db = \Config\Database::connect();
        $data['pengiriman'] = $this->pengirimanModel
            ->select('pengiriman.*, customer.nama as nama_customer, customer.telepon as telp_customer, customer.alamat as alamat_customer, rute.nama_wilayah as nama_wilayah')
            ->join('customer', 'pengiriman.id_customer = customer.id_customer', 'left')
            ->join('rute', 'pengiriman.kode_rute = rute.kode_rute', 'left')
            ->find($id);

        // Normalisasi pemesanan -> array of items [name, id_product, qty, harga, total]
        $itemsRaw = json_decode($data['pengiriman']['pemesanan'] ?? '[]', true) ?: [];
        $items = [];
        if (isset($itemsRaw['besar']) || isset($itemsRaw['kecil']) || isset($itemsRaw['serut'])) {
            foreach (['besar','kecil','serut'] as $k) {
                if (!empty($itemsRaw[$k])) {
                    $i = $itemsRaw[$k];
                    $items[] = [
                        'name' => 'Es ' . ucfirst($k),
                        'id_product' => $i['id_product'] ?? null,
                        'qty' => (int)($i['qty'] ?? 0),
                        'harga' => (float)($i['harga'] ?? 0),
                        'total' => (float)($i['total'] ?? 0),
                    ];
                }
            }
        } else {
            foreach ($itemsRaw as $i) {
                $items[] = [
                    'name' => $i['name'] ?? null,
                    'id_product' => $i['id_product'] ?? null,
                    'qty' => (int)($i['qty'] ?? 0),
                    'harga' => (float)($i['harga'] ?? ($i['price'] ?? 0)),
                    'total' => (float)($i['total'] ?? ($i['subtotal'] ?? 0)),
                ];
            }
        }
        // Lengkapi nama produk dari ProductModel apabila tersedia
        $ids = array_values(array_unique(array_filter(array_map(fn($r)=>$r['id_product'] ?? null, $items))));
        if (!empty($ids)) {
            try {
                $pm = new \App\Models\ProductModel();
                $prods = $pm->whereIn('id_product', $ids)->findAll();
                $map = [];
                foreach ($prods as $p) { $map[$p['id_product']] = $p['name'] ?: ($p['sku'] ?? ('#'.$p['id_product'])); }
                foreach ($items as &$it) {
                    if (empty($it['name']) && !empty($it['id_product']) && isset($map[$it['id_product']])) {
                        $it['name'] = $map[$it['id_product']];
                    }
                }
                unset($it);
            } catch (\Throwable $th) {}
        }
        $data['items'] = $items;

        // Ambil invoice terkait BON ini (jika ada)
        try {
            $data['invoices'] = $db->table('invoice i')
                ->select('i.*, t.transaction_no, t.transaction_date')
                ->join('transaction t', 'i.id_transaction = t.id_transaction', 'left')
                ->where('i.id_pengiriman', (int)$id)
                ->orderBy('i.created_at', 'DESC')
                ->get()->getResultArray();
        } catch (\Throwable $th) {
            $data['invoices'] = [];
        }

        return view("pages/pengiriman/detail", $data);
    }

    // JSON: daftar invoice pada BON (pengiriman) tertentu
    public function invoices($idPengiriman)
    {
        $db = \Config\Database::connect();
        $rows = $db->table('invoice i')
            ->select('i.id_invoice, i.invoice_no, i.amount, i.status, i.foto_surat_jalan, i.foto_penerimaan, i.created_at, c.nama as customer_name')
            ->join('transaction t', 't.id_transaction = i.id_transaction', 'left')
            ->join('customer c', 'c.id_customer = t.id_customer', 'left')
            ->where('i.id_pengiriman', (int)$idPengiriman)
            ->orderBy('i.created_at', 'DESC')
            ->get()->getResultArray();
        return $this->response->setJSON([ 'success' => true, 'data' => $rows ]);
    }

    // Upload Surat Jalan per-invoice
    public function uploadSuratJalanInvoice($idInvoice)
    {
        $invoiceModel = new \App\Models\InvoiceModel();
        $inv = $invoiceModel->find((int)$idInvoice);
        if (!$inv) return $this->response->setJSON(['success'=>false,'message'=>'Invoice tidak ditemukan']);

        $file = $this->request->getFile('foto');
        if (!$file || !$file->isValid()) return $this->response->setJSON(['success'=>false,'message'=>'File tidak valid']);

    $newName = $file->getRandomName();
    $targetDir = FCPATH.'uploads/suratjalan';
        if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);
        $file->move($targetDir, $newName);

        $invoiceModel->update($inv['id_invoice'], [ 'foto_surat_jalan' => $newName ]);

        // Update status pengiriman jika semua invoice sudah punya SJ
        if (!empty($inv['id_pengiriman'])) {
            $db = \Config\Database::connect();
            $rows = $db->table('invoice')->select('foto_surat_jalan,foto_penerimaan')->where('id_pengiriman', (int)$inv['id_pengiriman'])->get()->getResultArray();
            $allSJ = !empty($rows) && array_reduce($rows, fn($c,$r)=>$c && !empty($r['foto_surat_jalan']), true);
            $allTerima = !empty($rows) && array_reduce($rows, fn($c,$r)=>$c && !empty($r['foto_penerimaan']), true);
            $newStatus = $allTerima ? 'diterima' : ($allSJ ? 'mengirim' : null);
            if ($newStatus) {
                $db->table('pengiriman')->where('id_pengiriman', (int)$inv['id_pengiriman'])->update(['status'=>$newStatus]);
            }
        }

        return $this->response->setJSON(['success'=>true, 'file'=>$newName]);
    }

    // Upload Bukti Diterima per-invoice (wajib punya SJ terlebih dahulu)
    public function uploadPenerimaanInvoice($idInvoice)
    {
        $invoiceModel = new \App\Models\InvoiceModel();
        $inv = $invoiceModel->find((int)$idInvoice);
        if (!$inv) return $this->response->setJSON(['success'=>false,'message'=>'Invoice tidak ditemukan']);
        if (empty($inv['foto_surat_jalan'])) return $this->response->setJSON(['success'=>false,'message'=>'Upload Surat Jalan dahulu']);

        $file = $this->request->getFile('foto');
        if (!$file || !$file->isValid()) return $this->response->setJSON(['success'=>false,'message'=>'File tidak valid']);

    $newName = $file->getRandomName();
    $targetDir = FCPATH.'uploads/penerimaan';
        if (!is_dir($targetDir)) @mkdir($targetDir, 0775, true);
        $file->move($targetDir, $newName);

        $invoiceModel->update($inv['id_invoice'], [ 'foto_penerimaan' => $newName ]);

        // Update status pengiriman jika semua invoice sudah punya bukti diterima
        if (!empty($inv['id_pengiriman'])) {
            $db = \Config\Database::connect();
            $rows = $db->table('invoice')->select('foto_surat_jalan,foto_penerimaan')->where('id_pengiriman', (int)$inv['id_pengiriman'])->get()->getResultArray();
            $allSJ = !empty($rows) && array_reduce($rows, fn($c,$r)=>$c && !empty($r['foto_surat_jalan']), true);
            $allTerima = !empty($rows) && array_reduce($rows, fn($c,$r)=>$c && !empty($r['foto_penerimaan']), true);
            $newStatus = $allTerima ? 'diterima' : ($allSJ ? 'mengirim' : null);
            if ($newStatus) {
                $db->table('pengiriman')->where('id_pengiriman', (int)$inv['id_pengiriman'])->update(['status'=>$newStatus]);
            }
        }

        return $this->response->setJSON(['success'=>true, 'file'=>$newName]);
    }

    public function edit($id)
    {
        if ($this->request->getMethod() == 'POST') {
            $existingData = $this->pengirimanModel->find($id);
            if (!$existingData) {
                return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
            }

            // Mengambil data pemesanan lama
            $existingPemesanan = json_decode($existingData['pemesanan'], true);

            // Mengambil input baru
            $qty_besar = $this->request->getPost('qty_es_besar');
            $qty_kecil = $this->request->getPost('qty_es_kecil');
            $qty_serut = $this->request->getPost('qty_es_serut');

            $harga_besar = $this->request->getPost('harga_es_besar');
            $harga_kecil = $this->request->getPost('harga_es_kecil');
            $harga_serut = $this->request->getPost('harga_es_serut');

            // Perbarui data sesuai input
            if (isset($qty_besar) && isset($harga_besar)) {
                $existingPemesanan['besar'] = [
                    'qty' => $qty_besar,
                    'harga' => $harga_besar,
                    'total' => $qty_besar * $harga_besar
                ];
            }
            if (isset($qty_kecil) && isset($harga_kecil)) {
                $existingPemesanan['kecil'] = [
                    'qty' => $qty_kecil,
                    'harga' => $harga_kecil,
                    'total' => $qty_kecil * $harga_kecil
                ];
            }
            if (isset($qty_serut) && isset($harga_serut)) {
                $existingPemesanan['serut'] = [
                    'qty' => $qty_serut,
                    'harga' => $harga_serut,
                    'total' => $qty_serut * $harga_serut
                ];
            }

            // Encode kembali ke JSON
            $updatedPemesanan = json_encode($existingPemesanan);

            // Lakukan update hanya pada field pemesanan
            if ($this->pengirimanModel->update($id, ['pemesanan' => $updatedPemesanan])) {
                return $this->response->setJSON(['success' => true, 'message' => 'Pemesanan berhasil diupdate', 'url' => '/pengiriman']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => $this->pengirimanModel->errors()]);
            }
        }

        $data['pengiriman'] = $this->pengirimanModel
            ->select('pengiriman.*, customer.nama as nama_customer, rute.nama_wilayah as nama_wilayah')
            ->join('customer', 'pengiriman.id_customer = customer.id_customer')
            ->join('rute', 'pengiriman.kode_rute = rute.kode_rute')
            ->find($id);
        return view("pages/pengiriman/edit", $data);
    }


    public function delete($id)
    {
        if ($this->pengirimanModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil dihapus', 'url' => '/pengiriman']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => $this->pengirimanModel->errors()]);
        }
    }
}
