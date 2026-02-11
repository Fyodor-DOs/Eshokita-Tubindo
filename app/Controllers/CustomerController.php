<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\IdGenerator;

class CustomerController extends BaseController
{
    protected $customerModel;
    protected $ruteModel;
    protected $productModel;

    public function __construct()
    {
        $this->customerModel = new \App\Models\CustomerModel();
        $this->ruteModel = new \App\Models\RuteModel();
        $this->productModel = new \App\Models\ProductModel();
    }

    public function index()
    {
        $data['customer'] = $this->customerModel
            ->select('customer.*, rute.nama_wilayah as nama_wilayah')
            ->join('rute', 'customer.kode_rute = rute.kode_rute', 'left')
            ->orderBy('customer.id_customer', 'DESC')
            ->findAll();

        return view("pages/customer/index", $data);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $produk = [];

            // Ambil item pesanan dari form (opsional) untuk menentukan produk yang benar-benar dibeli

            $orderItems = $this->request->getPost('order_items');
            $items = [];
            if (is_string($orderItems) && $orderItems !== '') {
                $decoded = json_decode($orderItems, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $items = $decoded;
                }
            }

            // Buat kamus produk untuk lookup nama & harga default bila diperlukan
            $productsAll = $this->productModel->where('active', 1)->findAll();
            $byId = [];
            foreach ($productsAll as $p) {
                $byId[$p['id_product']] = $p;
            }

            // Bangun mapping produk hanya dari item yang dipesan (jika ada)
            if (!empty($items)) {
                foreach ($items as &$it) {
                    $pid = (int) ($it['id_product'] ?? 0);
                    $p = $byId[$pid] ?? null;
                    $it['name'] = $p['name'] ?? ('Produk #' . $pid);
                }
                unset($it);
                // Untuk field produk (legacy), simpan mapping id->name+price
                foreach ($items as $it) {
                    $pid = (int) ($it['id_product'] ?? 0);
                    if ($pid <= 0)
                        continue;
                    $produk[$pid] = [
                        'name' => $byId[$pid]['name'] ?? ('Produk #' . $pid),
                        'price' => isset($it['price']) ? (float) $it['price'] : (float) ($byId[$pid]['price'] ?? 0)
                    ];
                }
            }

            // Serialize order_items with name included
            $orderItemsJson = !empty($items) ? json_encode($items) : '';

            // Build input payload
            $input = [
                'nama' => $this->request->getPost('nama'),
                'email' => $this->request->getPost('email'),
                'telepon' => $this->request->getPost('telepon'),
                'kode_rute' => $this->request->getPost('kode_rute'),
                'provinsi' => $this->request->getPost('provinsi'),
                'kabupaten' => $this->request->getPost('kabupaten'),
                'kecamatan' => $this->request->getPost('kecamatan'),
                'kelurahan' => $this->request->getPost('kelurahan'),
                'kodepos' => $this->request->getPost('kodepos'),
                'alamat' => $this->request->getPost('alamat'),
                'produk' => json_encode($produk),
                'order_items' => $orderItemsJson,
            ];

            // Check if customer with same telepon or email already exists
            $tel = trim((string) ($input['telepon'] ?? ''));
            $eml = strtolower(trim((string) ($input['email'] ?? '')));
            $existing = null;
            if ($tel !== '') {
                $existing = $this->customerModel->where('telepon', $tel)->first();
            }
            if (!$existing && $eml !== '' && $eml !== 'null') {
                $existing = $this->customerModel->where('email', $eml)->first();
            }

            if ($existing) {
                // Customer already exists, return error to prevent duplicate
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'message' => 'Customer dengan telepon atau email tersebut sudah terdaftar. Silakan gunakan menu Edit untuk memperbarui data.']);
            }

            // Insert new customer
            if (!$this->customerModel->insert($input)) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'message' => 'Gagal menyimpan customer: ' . implode(', ', $this->customerModel->errors())]);
            }

            $idCustomer = $this->customerModel->getGeneratedId();

            // At this point we have $idCustomer (either updated existing or newly created)

            if (!empty($items)) {
                // SIMPLE LOCK: Cegah double submit dengan cache lock
                $cache = \Config\Services::cache();
                $lockKey = 'create_order_' . $idCustomer . '_' . md5(json_encode($items));

                if ($cache && $cache->get($lockKey)) {
                    // Sedang diproses atau baru saja diproses
                    return $this->response->setJSON(['success' => true, 'message' => 'Pesanan sedang diproses...', 'url' => base_url('customer')]);
                }

                // Set lock untuk 10 detik
                $cache && $cache->save($lockKey, 1, 10);

                // Build transaction data
                $db = \Config\Database::connect();
                $total = 0;
                $itemsData = [];

                // Validasi stok terlebih dahulu
                foreach ($items as $it) {
                    $pid = (int) ($it['id_product'] ?? 0);
                    $qty = (int) ($it['qty'] ?? 0);
                    if ($pid > 0 && $qty > 0) {
                        $p = $byId[$pid] ?? null;
                        $stokTersedia = (int) ($p['qty'] ?? 0);
                        if ($qty > $stokTersedia) {
                            return $this->response
                                ->setStatusCode(400)
                                ->setJSON(['success' => false, 'message' => 'Stok tidak cukup untuk "' . ($p['name'] ?? 'Produk') . '". Stok tersedia: ' . $stokTersedia . ', qty diminta: ' . $qty]);
                        }
                    }
                }

                foreach ($items as $it) {
                    $total += (float) ($it['subtotal'] ?? 0);
                    $itemsData[] = [
                        'id_product' => (int) $it['id_product'],
                        'qty' => (int) $it['qty'],
                        'price' => (float) $it['price'],
                        'subtotal' => (float) $it['subtotal'],
                    ];
                }

                $trxPrefix = 'TRX-' . date('y') . date('m');
                $lastTrx = $db->table('transaction')->select('transaction_no')->like('transaction_no', $trxPrefix, 'after')->orderBy('transaction_no', 'DESC')->limit(1)->get()->getRowArray();
                $trxSeq = $lastTrx ? (int) substr($lastTrx['transaction_no'], -3) + 1 : 1;
                $trxNo = $trxPrefix . '-' . str_pad((string) $trxSeq, 3, '0', STR_PAD_LEFT);

                $insertData = [
                    'transaction_no' => $trxNo,
                    'id_customer' => $idCustomer,
                    'transaction_date' => date('Y-m-d H:i:s'),
                    'items' => json_encode($itemsData),
                    'total_amount' => $total,
                    'status' => 'pending',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $idTrx = IdGenerator::generateForTable('transaction', 'id_transaction');
                $insertData['id_transaction'] = $idTrx;
                $db->table('transaction')->insert($insertData);

                // Kurangi stok SEKALI SAJA
                $tx = new \App\Models\StockTransactionModel();
                foreach ($itemsData as $it) {
                    $pid = (int) ($it['id_product'] ?? 0);
                    $qty = (int) ($it['qty'] ?? 0);
                    if ($pid > 0 && $qty > 0) {
                        $tx->recordTransaction($pid, 'out', $qty, 'transaction', $idTrx, 'Order dari create customer');
                    }
                }

                return $this->response->setJSON(['success' => true, 'message' => 'Customer & pesanan dibuat. Lanjutkan buat invoice.', 'url' => base_url('invoice/create-from-transaction/' . $idTrx)]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Customer dibuat. Silakan buat pesanan.', 'url' => '/customer']);
        }

        $data['rutes'] = $this->ruteModel->findAll();
        $data['products'] = $this->productModel->where('active', 1)->orderBy('name', 'ASC')->findAll();
        return view("pages/customer/create", $data);
    }

    public function detail($id)
    {
        $data['customer'] = $this->customerModel
            ->select('customer.*, rute.nama_wilayah')
            ->join('rute', 'customer.kode_rute = rute.kode_rute', 'left')
            ->find($id);

        // Load invoices and payment sums for view-only table
        $db = \Config\Database::connect();
        $invoices = $db->query(
            "SELECT i.* FROM invoice i\n             JOIN transaction t ON i.id_transaction = t.id_transaction\n             WHERE t.id_customer = ?\n             ORDER BY i.issue_date DESC",
            [$id]
        )->getResultArray();

        // Map total_paid by invoice
        $paymentsByInvoice = [];
        if (!empty($invoices)) {
            $invoiceIds = array_column($invoices, 'id_invoice');
            $in = implode(',', array_fill(0, count($invoiceIds), '?'));
            $rows = $db->query("SELECT id_invoice, SUM(amount) as total_paid FROM payment WHERE id_invoice IN ($in) GROUP BY id_invoice", $invoiceIds)->getResultArray();
            foreach ($rows as $r) {
                $paymentsByInvoice[$r['id_invoice']] = (float) $r['total_paid'];
            }
        }
        // Attach computed fields expected by view
        foreach ($invoices as &$inv) {
            $inv['total_paid'] = $paymentsByInvoice[$inv['id_invoice']] ?? 0.0;
            // derive status
            $amt = (float) ($inv['amount'] ?? 0);
            if ($inv['status'] === 'void') {
                // keep void
            } else if ($inv['total_paid'] >= $amt && $amt > 0) {
                $inv['status'] = 'paid';
            } else if ($inv['total_paid'] > 0 && $inv['total_paid'] < $amt) {
                $inv['status'] = 'partial';
            } else if (empty($inv['status'])) {
                $inv['status'] = 'unpaid';
            }
        }
        unset($inv);

        return view("pages/customer/detail", [
            'customer' => $data['customer'],
            'invoices' => $invoices,
        ]);
    }

    public function edit($id)
    {
        $data['customer'] = $this->customerModel
            ->join('rute', 'customer.kode_rute = rute.kode_rute', 'left')
            ->find($id);

        $data['rutes'] = $this->ruteModel->findAll();
        $data['products'] = $this->productModel->where('active', 1)->orderBy('name', 'ASC')->findAll();

        // Load invoices for read-only section like detail
        $db = \Config\Database::connect();
        $invoices = $db->query(
            "SELECT i.* FROM invoice i\n             JOIN transaction t ON i.id_transaction = t.id_transaction\n             WHERE t.id_customer = ?\n             ORDER BY i.issue_date DESC",
            [$id]
        )->getResultArray();

        $paymentsByInvoice = [];
        if (!empty($invoices)) {
            $invoiceIds = array_column($invoices, 'id_invoice');
            $in = implode(',', array_fill(0, count($invoiceIds), '?'));
            $rows = $db->query("SELECT id_invoice, SUM(amount) as total_paid FROM payment WHERE id_invoice IN ($in) GROUP BY id_invoice", $invoiceIds)->getResultArray();
            foreach ($rows as $r) {
                $paymentsByInvoice[$r['id_invoice']] = (float) $r['total_paid'];
            }
        }
        foreach ($invoices as &$inv) {
            $inv['total_paid'] = $paymentsByInvoice[$inv['id_invoice']] ?? 0.0;
            $amt = (float) ($inv['amount'] ?? 0);
            if ($inv['status'] === 'void') {
                // keep void
            } else if ($inv['total_paid'] >= $amt && $amt > 0) {
                $inv['status'] = 'paid';
            } else if ($inv['total_paid'] > 0 && $inv['total_paid'] < $amt) {
                $inv['status'] = 'partial';
            } else if (empty($inv['status'])) {
                $inv['status'] = 'unpaid';
            }
        }
        unset($inv);

        $data['invoices'] = $invoices;

        if ($this->request->getMethod() === 'POST') {
            $produk = [];

            // Simpan hanya produk yang diberi harga (menggambarkan produk yang dibeli/ditetapkan)
            $products = $this->productModel->where('active', 1)->orderBy('name', 'ASC')->findAll();
            foreach ($products as $p) {
                $price = $this->request->getPost('harga_' . $p['id_product']);
                if ($price !== null && $price !== '') {
                    $produk[$p['id_product']] = [
                        'name' => $p['name'],
                        'price' => (float) $price
                    ];
                }
            }

            $input = [
                'nama' => $this->request->getPost('nama'),
                'kode_rute' => $this->request->getPost('kode_rute'),
                'provinsi' => $this->request->getPost('provinsi'),
                'kabupaten' => $this->request->getPost('kabupaten'),
                'kecamatan' => $this->request->getPost('kecamatan'),
                'kelurahan' => $this->request->getPost('kelurahan'),
                'kodepos' => $this->request->getPost('kodepos'),
                'alamat' => $this->request->getPost('alamat'),
                'produk' => json_encode($produk),
                'order_items' => $this->request->getPost('order_items'),
            ];

            // Input Telepon - validate uniqueness if changed
            $inputtelepon = $this->request->getPost('telepon');
            if ($data['customer']['telepon'] !== $inputtelepon) {
                // Telepon changed, check if already exists
                $existing = $this->customerModel->where('telepon', $inputtelepon)
                    ->where('id_customer !=', $id)
                    ->first();

                if ($existing) {
                    return $this->response->setJSON(['success' => false, 'message' => ['telepon' => 'Telepon sudah terdaftar']]);
                }

                $input['telepon'] = $inputtelepon;
            } else {
                $input['telepon'] = $data['customer']['telepon'];
            }

            // Input Email - validate uniqueness if changed
            $inputemail = $this->request->getPost('email');
            if (!empty($inputemail) && $data['customer']['email'] !== $inputemail) {
                // Email changed, check if already exists
                $existing = $this->customerModel->where('email', $inputemail)
                    ->where('id_customer !=', $id)
                    ->first();

                if ($existing) {
                    return $this->response->setJSON(['success' => false, 'message' => ['email' => 'Email sudah terdaftar']]);
                }

                $input['email'] = $inputemail;
            } else {
                $input['email'] = $data['customer']['email'];
            }

            if ($this->customerModel->update($id, $input)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Data berhasil diedit', 'url' => '/customer']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => $this->customerModel->errors()]);
            }
        }

        return view("pages/customer/edit", $data);
    }

    public function delete($id)
    {
        return $this->customerModel->delete($id);
    }

    public function getCustomerById($id)
    {
        $customer = $this->customerModel
            ->select('id_customer, nama, produk')
            ->where('id_customer', $id)->first();

        if ($customer) {
            // Parse produk JSON and get product details
            $produkData = json_decode($customer['produk'], true) ?: [];
            $products = [];

            foreach ($produkData as $productId => $data) {
                $products[] = [
                    'id' => $productId,
                    'name' => $data['name'],
                    'price' => $data['price']
                ];
            }

            $customer['products'] = $products;
        }

        return $this->response->setJSON($customer);
    }

    public function getCustomerByRute($rute)
    {
        $customer = $this->customerModel
            ->select('id_customer, nama')
            ->where('kode_rute', $rute)->findAll();
        return $this->response->setJSON($customer);
    }

    public function transactions($id)
    {
        $db = \Config\Database::connect();
        $customer = $this->customerModel->find($id);
        if (!$customer)
            return redirect()->to('/customer');

        // invoices by customer's transactions
        $invoices = $db->query("
            SELECT i.* FROM invoice i
            JOIN transaction t ON i.id_transaction = t.id_transaction
            WHERE t.id_customer = ?
            ORDER BY i.issue_date DESC
        ", [$id])->getResultArray();

        // payments for those invoices
        $payments = [];
        if (!empty($invoices)) {
            $invoiceIds = array_column($invoices, 'id_invoice');
            $in = implode(',', array_fill(0, count($invoiceIds), '?'));
            $payments = $db->query("SELECT * FROM payment WHERE id_invoice IN ($in) ORDER BY paid_at DESC", $invoiceIds)->getResultArray();
        }


        // shipments: ambil foto penerimaan dari pengiriman (foto_penerimaan),
        // bukan dari tabel penerimaan (pr_photo), agar sesuai permintaan user
        $shipments = $db->query("
            SELECT pg.*,
                   pg.status AS pg_status,
                   pg.foto_penerimaan AS pg_photo
            FROM pengiriman pg
            WHERE pg.id_customer = ?
            ORDER BY pg.tanggal DESC
        ", [$id])->getResultArray();

        $summary = [
            'invoiced' => array_sum(array_map(fn($i) => (float) $i['amount'], $invoices)),
            'paid' => array_sum(array_map(fn($p) => (float) $p['amount'], $payments)),
        ];

        return view('pages/customer/transactions', [
            'customer' => $customer,
            'invoices' => $invoices,
            'payments' => $payments,
            'shipments' => $shipments,
            'summary' => $summary,
        ]);
    }

    public function order($id)
    {
        $customer = $this->customerModel->find($id);
        if (!$customer)
            return redirect()->to('/customer');

        if ($this->request->getMethod() === 'POST') {
            $items = $this->request->getPost('items'); // JSON string
            if (is_string($items)) {
                $decoded = json_decode($items, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $items = $decoded;
                }
            }
            if (empty($items) || !is_array($items)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Item pesanan kosong']);
            }

            // SIMPLE LOCK: Cegah double submit dengan cache lock
            $cache = \Config\Services::cache();
            $lockKey = 'order_' . $id . '_' . md5(json_encode($items));

            if ($cache && $cache->get($lockKey)) {
                // Sedang diproses atau baru saja diproses
                return $this->response->setJSON(['success' => true, 'message' => 'Pesanan sedang diproses...', 'url' => base_url('customer')]);
            }

            // Set lock untuk 10 detik
            $cache && $cache->save($lockKey, 1, 10);

            $db = \Config\Database::connect();
            $productModel = $this->productModel;
            $total = 0;
            $itemsData = [];

            foreach ($items as $it) {
                $pid = (int) ($it['id_product'] ?? 0);
                $qty = (int) ($it['qty'] ?? 0);
                $price = (float) ($it['price'] ?? 0);
                if ($pid <= 0 || $qty <= 0)
                    continue;
                $p = $productModel->find($pid);
                if (!$p)
                    continue;

                // Validasi stok
                $stokTersedia = (int) ($p['qty'] ?? 0);
                if ($qty > $stokTersedia) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Stok tidak cukup untuk "' . $p['name'] . '". Stok tersedia: ' . $stokTersedia . ', qty diminta: ' . $qty
                    ]);
                }

                $subtotal = $qty * $price;
                $total += $subtotal;
                $itemsData[] = [
                    'id_product' => $p['id_product'],
                    'sku' => $p['sku'],
                    'name' => $p['name'],
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
            }

            if (empty($itemsData)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Item pesanan tidak valid']);
            }

            $trxPrefix = 'TRX-' . date('y') . date('m');
            $lastTrx = $db->table('transaction')->select('transaction_no')->like('transaction_no', $trxPrefix, 'after')->orderBy('transaction_no', 'DESC')->limit(1)->get()->getRowArray();
            $trxSeq = $lastTrx ? (int) substr($lastTrx['transaction_no'], -3) + 1 : 1;
            $trxNo = $trxPrefix . '-' . str_pad((string) $trxSeq, 3, '0', STR_PAD_LEFT);

            $insertData = [
                'transaction_no' => $trxNo,
                'id_customer' => $id,
                'transaction_date' => date('Y-m-d H:i:s'),
                'items' => json_encode($itemsData),
                'total_amount' => $total,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $idTrx = IdGenerator::generateForTable('transaction', 'id_transaction');
            $insertData['id_transaction'] = $idTrx;
            $db->table('transaction')->insert($insertData);

            // Kurangi stok SEKALI SAJA
            $tx = new \App\Models\StockTransactionModel();
            foreach ($itemsData as $it) {
                $pid = (int) ($it['id_product'] ?? 0);
                $qty = (int) ($it['qty'] ?? 0);
                if ($pid > 0 && $qty > 0) {
                    $tx->recordTransaction($pid, 'out', $qty, 'transaction', $idTrx, 'Order dari halaman order');
                }
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Pesanan dibuat. Lanjutkan buat invoice.', 'url' => base_url('invoice/create-from-transaction/' . $idTrx)]);
        }

        // Siapkan produk + harga khusus customer
        $products = $this->productModel->where('active', 1)->orderBy('name', 'ASC')->findAll();
        $custProducts = json_decode($customer['produk'] ?? '[]', true) ?: [];
        foreach ($products as &$p) {
            $p['customer_price'] = $custProducts[$p['id_product']]['price'] ?? $p['price'];
        }

        return view('pages/customer/order', [
            'customer' => $customer,
            'products' => $products,
        ]);
    }

    /**
     * Order ulang: tampilan seperti create namun identitas readonly.
     */
    public function orderAgain($id)
    {
        $customer = $this->customerModel
            ->join('rute', 'customer.kode_rute = rute.kode_rute', 'left')
            ->find($id);
        if (!$customer)
            return redirect()->to('/customer');

        if ($this->request->getMethod() === 'POST') {
            $items = $this->request->getPost('items'); // JSON string
            if (is_string($items)) {
                $decoded = json_decode($items, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $items = $decoded;
                }
            }
            if (empty($items) || !is_array($items)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Item pesanan kosong']);
            }

            // SIMPLE LOCK: Cegah double submit dengan cache lock
            $cache = \Config\Services::cache();
            $lockKey = 'order_again_' . $id . '_' . md5(json_encode($items));

            if ($cache && $cache->get($lockKey)) {
                // Sedang diproses atau baru saja diproses
                $url = base_url('customer');
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Pesanan sedang diproses...', 'url' => $url]);
                }
                return redirect()->to($url);
            }

            // Set lock untuk 10 detik
            $cache && $cache->save($lockKey, 1, 10);

            $db = \Config\Database::connect();
            $productModel = $this->productModel;
            $total = 0;
            $itemsData = [];

            foreach ($items as $it) {
                $pid = (int) ($it['id_product'] ?? 0);
                $qty = (int) ($it['qty'] ?? 0);
                $price = (float) ($it['price'] ?? 0);
                if ($pid <= 0 || $qty <= 0)
                    continue;
                $p = $productModel->find($pid);
                if (!$p)
                    continue;

                // Validasi stok
                $stokTersedia = (int) ($p['qty'] ?? 0);
                if ($qty > $stokTersedia) {
                    $errorMsg = 'Stok tidak cukup untuk "' . $p['name'] . '". Stok tersedia: ' . $stokTersedia . ', qty diminta: ' . $qty;
                    if ($this->request->isAJAX()) {
                        return $this->response->setJSON(['success' => false, 'message' => $errorMsg]);
                    }
                    return redirect()->back()->with('error', $errorMsg);
                }

                $subtotal = $qty * $price;
                $total += $subtotal;
                $itemsData[] = [
                    'id_product' => $p['id_product'],
                    'sku' => $p['sku'],
                    'name' => $p['name'],
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal
                ];
            }

            if (empty($itemsData)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Item pesanan tidak valid']);
            }

            $trxPrefix = 'TRX-' . date('y') . date('m');
            $lastTrx = $db->table('transaction')->select('transaction_no')->like('transaction_no', $trxPrefix, 'after')->orderBy('transaction_no', 'DESC')->limit(1)->get()->getRowArray();
            $trxSeq = $lastTrx ? (int) substr($lastTrx['transaction_no'], -3) + 1 : 1;
            $trxNo = $trxPrefix . '-' . str_pad((string) $trxSeq, 3, '0', STR_PAD_LEFT);

            $insertData = [
                'transaction_no' => $trxNo,
                'id_customer' => $id,
                'transaction_date' => date('Y-m-d H:i:s'),
                'items' => json_encode($itemsData),
                'total_amount' => $total,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            $idTrx = IdGenerator::generateForTable('transaction', 'id_transaction');
            $insertData['id_transaction'] = $idTrx;
            $db->table('transaction')->insert($insertData);

            // Kurangi stok SEKALI SAJA
            $tx = new \App\Models\StockTransactionModel();
            foreach ($itemsData as $it) {
                $pid = (int) ($it['id_product'] ?? 0);
                $qty = (int) ($it['qty'] ?? 0);
                if ($pid > 0 && $qty > 0) {
                    $tx->recordTransaction($pid, 'out', $qty, 'transaction', $idTrx, 'Order ulang');
                }
            }

            // Jika request AJAX, kembalikan JSON; jika bukan, redirect standar
            $url = base_url('invoice/create-from-transaction/' . $idTrx);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pesanan dibuat. Lanjutkan buat invoice.',
                    'url' => $url,
                ]);
            }
            return redirect()->to($url);
        }

        // Produk + harga khusus
        $products = $this->productModel->where('active', 1)->orderBy('name', 'ASC')->findAll();
        $custProducts = json_decode($customer['produk'] ?? '[]', true) ?: [];
        foreach ($products as &$p) {
            $p['customer_price'] = $custProducts[$p['id_product']]['price'] ?? $p['price'];
        }
        unset($p);

        return view('pages/customer/order_again', [
            'customer' => $customer,
            'products' => $products,
        ]);
    }

    /**
     * Partial modal detail untuk halaman index (read-only).
     */
    public function detailModal($id)
    {
        $customer = $this->customerModel
            ->select('customer.*, rute.nama_wilayah')
            ->join('rute', 'customer.kode_rute = rute.kode_rute', 'left')
            ->find($id);
        if (!$customer)
            return $this->response->setStatusCode(404)->setBody('Not found');

        // Ambil foto terbaru dari pengiriman untuk customer ini (jika ada)
        $pengirimanModel = new \App\Models\PengirimanModel();
        $latestPg = $pengirimanModel
            ->where('id_customer', $id)
            ->orderBy('tanggal', 'DESC')
            ->first();

        if ($latestPg) {
            $customer['foto_surat_jalan'] = $latestPg['foto_surat_jalan'] ?? null;
            $customer['foto_penerimaan'] = $latestPg['foto_penerimaan'] ?? null;
        } else {
            $customer['foto_surat_jalan'] = null;
            $customer['foto_penerimaan'] = null;
        }

        return view('pages/customer/detail_modal', ['customer' => $customer]);
    }
}
