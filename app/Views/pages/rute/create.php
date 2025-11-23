<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'rute', 'segment2' => 'create']) ?>

<form method="post" action="<?= site_url('rute/create') ?>">
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="kode_rute" class="form-label">Kode Rute</label>
                        <input type="text" name="kode_rute" id="kode_rute" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_wilayah" class="form-label">Nama Wilayah</label>
                        <input type="text" name="nama_wilayah" id="nama_wilayah" class="form-control">
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary col-12 col-md-3">Submit</button>
            </div>
        </div>
    </div>

</form>

<?= $this->endSection() ?>