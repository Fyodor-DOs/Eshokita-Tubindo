<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'rute', 'segment2' => 'edit', 'segment3' => $rute['kode_rute']]) ?>


<form method="post" action="<?= site_url('rute/edit/' . $rute['id_rute']) ?>">
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode_rute" class="form-label">Kode Rute</label>
                        <input type="text" id="kode_rute" class="form-control" name="kode_rute"
                            value="<?= $rute['kode_rute'] ?>" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_wilayah" class="form-label">Nama Wilayah</label>
                        <input type="text" name="nama_wilayah" id="nama_wilayah" class="form-control"
                            value="<?= $rute['nama_wilayah'] ?>">
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary col-12 col-md-3">Update</button>
            </div>
        </div>
    </div>

</form>

<?= $this->endSection() ?>