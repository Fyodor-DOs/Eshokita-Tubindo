<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'rute', 'segment2' => 'detail', 'segment3' => $rute['id_rute']]) ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label" for="kode_rute">Kode Rute</label>
                    <input type="text" name="kode_rute" id="kode_rute" class="form-control" value="<?= esc($rute['kode_rute']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label" for="nama_wilayah">Nama Wilayah</label>
                    <input type="text" name="nama_wilayah" id="nama_wilayah" class="form-control" value="<?= esc($rute['nama_wilayah']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-12">
                <a href="<?= base_url('/rute') ?>" class="btn btn-secondary">Kembali</a>
                <a href="<?= base_url('/rute/edit/' . $rute['id_rute']) ?>" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
