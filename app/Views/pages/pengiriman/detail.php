<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'pengiriman', 'segment2' => 'detail', 'segment3' => $pengiriman['id_pengiriman']]) ?>
<div class="card">
    <div class="card-body">
        <div class="row">
                        <div class="col-12 d-flex justify-content-between align-items-center mb-2">
                                <h5 class="m-0">Detail Pengiriman</h5>
                                <?php
                                    $status = $pengiriman['status'] ?? 'siap';
                                    $map = [ 'siap' => ['warning','Siap'], 'mengirim' => ['primary','Mengirim'], 'diterima' => ['success','Diterima'], 'gagal' => ['danger','Gagal'] ];
                                    [$cls,$lbl] = $map[$status] ?? ['secondary', ucfirst($status)];
                                ?>
                                <span class="badge bg-<?= $cls ?> px-3 py-2"><?= $lbl ?></span>
                        </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="text" name="tanggal" id="tanggal"
                        value="<?= date('d F Y', strtotime($pengiriman['tanggal'])) ?>" class="form-control" readonly>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label for="supir" class="form-label">Supir</label>
                    <input type="text" name="supir" id="supir" class="form-control" value="<?= $pengiriman['supir'] ?>"
                        readonly>
                </div>
            </div>

            <div class=" col-md-3">
                <div class="form-group">
                    <label for="kenek" class="form-label">Kenek</label>
                    <input type="text" name="kenek" id="kenek" class="form-control" value="<?= $pengiriman['kenek'] ?>"
                        readonly>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    <label for="plat_kendaraan" class="form-label">Plat Kendaraan</label>
                    <input type="text" name="plat_kendaraan" id="plat_kendaraan" class="form-control"
                        value="<?= $pengiriman['plat_kendaraan'] ?>" readonly>
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    <label for="nama_wilayah" class="form-label">Rute</label>
                    <input type="text" name="nama_wilayah" id="nama_wilayah" class="form-control" autocomplete="off"
                        value="<?= $pengiriman['kode_rute'] ?> - <?= $pengiriman['nama_wilayah'] ?>" readonly>
                </div>
            </div>



            <!-- Customer & Invoices -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">Customer</div>
                            <div class="card-body">
                                <table class="table table-sm mb-0">
                                    <tr><th class="w-25">Nama</th><td><?= esc($pengiriman['nama_customer'] ?? '-') ?></td></tr>
                                    <tr><th>Telp</th><td><?= esc($pengiriman['telp_customer'] ?? '-') ?></td></tr>
                                    <tr><th>Alamat</th><td><?= esc($pengiriman['alamat_customer'] ?? '-') ?></td></tr>
                                    <tr><th>Rute</th><td><?= esc(($pengiriman['kode_rute'] ?? '').' - '.($pengiriman['nama_wilayah'] ?? '')) ?></td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-light">Invoice pada BON ini</div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>No. Invoice</th>
                                                <th>Ref/Transaksi</th>
                                                <th>Tanggal</th>
                                                <th class="text-end">Jumlah</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $totalInv=0; foreach(($invoices ?? []) as $inv): $totalInv += (float)($inv['amount'] ?? 0); ?>
                                            <tr>
                                                <td><?= esc($inv['invoice_no'] ?? $inv['id_invoice']) ?></td>
                                                <td><?= esc($inv['transaction_no'] ?? '-') ?></td>
                                                <td><?= !empty($inv['created_at']) ? date('d/m/Y', strtotime($inv['created_at'])) : (!empty($inv['transaction_date']) ? date('d/m/Y', strtotime($inv['transaction_date'])) : '-') ?></td>
                                                <td class="text-end"><?= number_format((float)($inv['amount'] ?? 0),0,',','.') ?></td>
                                                <td><span class="badge bg-<?= ($inv['status']??'')==='paid'?'success':'secondary' ?>"><?= ucfirst($inv['status'] ?? '-') ?></span></td>
                                            </tr>
                                        <?php endforeach; if (empty($invoices)): ?>
                                            <tr><td colspan="5" class="text-center text-muted">Belum ada invoice terkait BON ini</td></tr>
                                        <?php endif; ?>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th colspan="3" class="text-end">Total</th>
                                                <th class="text-end"><?= number_format($totalInv,0,',','.') ?></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="col-12 mt-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light">Item Es yang Dipesan</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $grand=0; foreach(($items ?? []) as $it): $grand += (float)($it['total'] ?? 0); ?>
                                    <tr>
                                        <td><?= esc($it['name'] ?? '-') ?></td>
                                        <td class="text-end"><?= (int)($it['qty'] ?? 0) ?></td>
                                        <td class="text-end"><?= number_format((float)($it['harga'] ?? 0),0,',','.') ?></td>
                                        <td class="text-end"><?= number_format((float)($it['total'] ?? 0),0,',','.') ?></td>
                                    </tr>
                                <?php endforeach; if (empty($items)): ?>
                                    <tr><td colspan="4" class="text-center text-muted">Tidak ada item</td></tr>
                                <?php endif; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" class="text-end">Grand Total</th>
                                        <th class="text-end"><?= number_format($grand,0,',','.') ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <?php if(!empty($pengiriman['foto_surat_jalan']) || !empty($pengiriman['foto_penerimaan'])): ?>
            <div class="col-md-12">
                <div class="row g-3">
                    <?php if(!empty($pengiriman['foto_surat_jalan'])): ?>
                    <div class="col-md-6">
                        <label class="form-label">Foto Surat Jalan</label>
                        <div class="border rounded p-2"><img src="<?= base_url('uploads/suratjalan/'.$pengiriman['foto_surat_jalan']) ?>" class="img-fluid"/></div>
                    </div>
                    <?php endif; ?>
                    <?php if(!empty($pengiriman['foto_penerimaan'])): ?>
                    <div class="col-md-6">
                        <label class="form-label">Foto Bukti Diterima</label>
                        <div class="border rounded p-2"><img src="<?= base_url('uploads/penerimaan/'.$pengiriman['foto_penerimaan']) ?>" class="img-fluid"/></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>

    <div class="card-footer bg-white">
        <div class="row g-2 justify-content-end">
            <div class="col-12 col-md-2">
                <a href="<?= base_url('pengiriman') ?>" class="btn btn-secondary w-100">Kembali</a>
            </div>

            <div class="col-12 col-md-2">
                <a href="<?= base_url('pengiriman/edit/' . $pengiriman['id_pengiriman']) ?>"
                    class="btn btn-warning w-100">Edit</a>
            </div>


        </div>
    </div>
</div>



<script>
    $(function() {
         // ...existing code...
    });
</script>
<?= $this->endSection() ?>