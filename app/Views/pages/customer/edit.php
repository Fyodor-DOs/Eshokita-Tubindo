<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'customer', 'segment2' => 'edit', 'segment3' => $customer['id_customer']]) ?>
<form id="formEditCustomer" method="post" action="<?= site_url('/customer/edit/' . $customer['id_customer']) ?>">
    <?= csrf_field() ?>
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="nama_wilayah">Rute</label>
                        <input type="hidden" name="kode_rute" id="kode_rute" value="<?= $customer['kode_rute'] ?>">
                        <input type="search" name="nama_wilayah" id="nama_wilayah" class="form-select"
                            placeholder="Ketik Rute" autocomplete="off"
                            value="<?= $customer['kode_rute'] ?> - <?= $customer['nama_wilayah'] ?>">

                        <?php if (!empty($rutes)): ?>
                        <div id="list_rute"
                            class="d-none border my-1 rounded overflow-y-scroll position-absolute z-1 bg-white shadow"
                            style="max-height: 150px;">
                            <ul class="list-group list-group-flush p-2">
                                <?php foreach ($rutes as $rute) : ?>
                                <li class="list-group-item list-group-item-action" data-id="<?= $rute['kode_rute'] ?>"
                                    style="cursor: pointer;">
                                    <?= $rute['kode_rute'] ?> - <?= $rute['nama_wilayah'] ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif ?>

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="nama">Nama Customer</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?= $customer['nama'] ?>"
                            required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="<?= $customer['email'] ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="telepon">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control"
                            value="<?= $customer['telepon'] ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="provinsi">Provinsi</label>
                        <input type="text" name="provinsi" id="provinsi" class="form-control"
                            value="<?= $customer['provinsi'] ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kota">Kota/Kabupaten</label>
                        <input type="text" name="kabupaten" id="kabupaten" class="form-control"
                            value="<?= $customer['kabupaten'] ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kecamatan">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan" class="form-control"
                            value="<?= $customer['kecamatan'] ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kelurahan">Kelurahan</label>
                        <input type="text" name="kelurahan" id="kelurahan" class="form-control"
                            value="<?= $customer['kelurahan'] ?>" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kodepos">Kode Pos</label>
                        <input type="number" name="kodepos" id="kodepos" class="form-control"
                            value="<?= $customer['kodepos'] ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="9" class="form-control"
                            required><?= $customer['alamat'] ?></textarea>
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
            <div class="d-flex justify-content-end gap-2">
                <a href="<?= base_url('customer') ?>" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    initAutoComplete('kode_rute', 'nama_wilayah', 'list_rute');
    // Intercept submit to handle JSON response from controller
    document.getElementById('formEditCustomer').addEventListener('submit', async function(e){
        e.preventDefault();
        const fd = new FormData(this); // includes CSRF
        try{
            const r = await fetch(this.action, { method:'POST', body: fd });
            const j = await r.json();
            if(j && j.success){
                Swal.fire({
                    icon:'success',
                    title:'Berhasil',
                    text: j.message || 'Data customer berhasil diperbarui',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                }).then(()=>{ window.location.href = '<?= base_url('/customer') ?>'; });
            } else {
                // Jangan tampilkan alert gagal agar tidak dobel; bisa log ke console.
                console?.warn && console.warn('Update customer tidak berhasil', j);
            }
        }catch(err){
            // Supress alert gagal sesuai permintaan; cukup log saja
            console?.error && console.error('Submit edit customer error', err);
        }
    });
});
</script>

<?= $this->endSection() ?>