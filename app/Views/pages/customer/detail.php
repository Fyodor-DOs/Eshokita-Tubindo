            <div class="col-12 col-md-3">
                <a href="<?= base_url('customer') ?>" class="btn btn-secondary w-100">Kembali</a>
            </div>
<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'customer', 'segment2' => 'detail', 'segment3' => $customer['id_customer']]) ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="nama_wilayah">Rute</label>
                    <input type="text" name="nama_wilayah" id="nama_wilayah" class="form-control"
                        placeholder="Ketik Rute" autocomplete="off" value="<?= $customer['kode_rute'] ?> - <?= $customer['nama_wilayah'] ?>" readonly>

                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="nama">Nama Customer</label>
                    <input type="text" name="nama" id="nama" class="form-control" value="<?= $customer['nama'] ?>"
                        readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= $customer['email'] ?>"
                        readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="telepon">Telepon</label>
                    <input type="text" name="telepon" id="telepon" class="form-control"
                        value="<?= $customer['telepon'] ?>" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="provinsi">Provinsi</label>
                    <input type="text" name="provinsi" id="provinsi" class="form-control"
                        value="<?= $customer['provinsi'] ?>" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="kota">Kota/Kabupaten</label>
                    <input type="text" name="kabupaten" id="kabupaten" class="form-control"
                        value="<?= $customer['kabupaten'] ?>" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="kecamatan">Kecamatan</label>
                    <input type="text" name="kecamatan" id="kecamatan" class="form-control"
                        value="<?= $customer['kecamatan'] ?>" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="kelurahan">Kelurahan</label>
                    <input type="text" name="kelurahan" id="kelurahan" class="form-control"
                        value="<?= $customer['kelurahan'] ?>" readonly>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label" for="kodepos">Kode Pos</label>
                    <input type="number" name="kodepos" id="kodepos" class="form-control"
                        value="<?= $customer['kodepos'] ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label" for="alamat">Alamat</label>
                    <textarea name="alamat" id="alamat" rows="9" class="form-control"
                        readonly><?= $customer['alamat'] ?></textarea>
                </div>
            </div>

            <div class="col-md-6">
                <p class="p-0 m-0 mb-2"><strong>Invoice Customer</strong></p>
                <?php if (!empty($invoices)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Invoice</th>
                                    <th class="text-end">Jumlah</th>
                                    <th class="text-end">Terbayar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $i): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($i['issue_date'])) ?></td>
                                    <td><?= esc($i['invoice_no']) ?></td>
                                    <td class="text-end">Rp <?= number_format((float)$i['amount'], 0, ',', '.') ?></td>
                                    <td class="text-end">Rp <?= number_format((float)($i['total_paid'] ?? 0), 0, ',', '.') ?></td>
                                    <td>
                                        <?php 
                                        $statusBadge = [ 'paid'=>'success','partial'=>'warning','unpaid'=>'danger','draft'=>'secondary','void'=>'dark' ];
                                        $statusLabel = [ 'paid'=>'Lunas','partial'=>'Sebagian','unpaid'=>'Belum Bayar','draft'=>'Draft','void'=>'Batal' ];
                                        ?>
                                        <span class="badge bg-<?= $statusBadge[$i['status']] ?? 'secondary' ?>"><?= $statusLabel[$i['status']] ?? ucfirst($i['status']) ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Belum ada invoice.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card-footer bg-white">
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="<?= base_url('customer') ?>" class="btn btn-secondary">Kembali</a>
                <a href="<?= base_url('customer/edit/' . $customer['id_customer']) ?>" class="btn btn-warning">Edit</a>
                <a href="<?= base_url('customer/transaksi/' . $customer['id_customer']) ?>" class="btn btn-primary">Lihat Transaksi</a>
            </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    });
</script>

<?= $this->endSection() ?>