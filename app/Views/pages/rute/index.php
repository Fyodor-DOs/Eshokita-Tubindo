<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('title') ?>Rute - PT Eshokita<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'rute']) ?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="table-rute">
                <thead>
                    <tr>
                        <th scope="col" class="col-1">No.</th>
                        <th scope="col">Kode Rute</th>
                        <th scope="col">Nama Wilayah</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rute as $key => $value) : ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= esc($value['kode_rute']) ?></td>
                        <td><?= esc($value['nama_wilayah']) ?></td>
                        <td>
                            <a href="<?= base_url('rute/detail/' . $value['id_rute']) ?>"
                                class="btn btn-sm btn-info">Detail</a>
                            <a href="<?= base_url('rute/edit/' . $value['id_rute']) ?>"
                                class="btn btn-sm btn-warning">Edit</a>
                            <button type="button" data-href="<?= base_url('rute/delete/' . $value['id_rute']) ?>"
                                class="btn btn-sm btn-danger btn-delete">Delete</button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?= $this->endSection() ?>