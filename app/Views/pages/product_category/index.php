<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'product category']) ?>
<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover" id="table-category">
                <thead>
                    <tr>
                        <th scope="col" class="col-1 text-center">No.</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Deskripsi</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $key => $c): ?>
                    <tr>
                        <td class="text-center"><?= $key + 1 ?></td>
                        <td><?= esc($c['name']) ?></td>
                        <td><?= esc($c['description']) ?></td>
                        <td>
                            <a href="<?= base_url('/product-category/detail/'.$c['id_category']) ?>"
                                class="btn btn-sm btn-info">Detail</a>
                            <a href="<?= base_url('/product-category/edit/'.$c['id_category']) ?>"
                                class="btn btn-sm btn-warning">Edit</a>
                            <button type="button"
                                data-href="<?= base_url('/product-category/delete/'.$c['id_category']) ?>"
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
