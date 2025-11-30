<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('title') ?>Produk - PT Eshokita<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'product']) ?>
<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover" id="table-product">
                <thead>
                    <tr>
                        <th scope="col" class="col-1 text-center">No.</th>
                        <th scope="col">SKU</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Berat Satuan (kg)</th>
                        <th scope="col">Stok</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $key => $p): ?>
                    <tr>
                        <td class="text-center"><?= $key + 1 ?></td>
                        <td><?= esc($p['sku']) ?></td>
                        <td><?= esc($p['name']) ?></td>
                        <td><?= esc($p['category_name'] ?? '-') ?></td>
                        <td><?= esc($p['unit']) ?></td>
                        <td><?= number_format($p['qty'] ?? 0) ?></td>
                        <td>Rp <?= number_format($p['price'] ?? 0) ?></td>
                        <td>
                            <a href="<?= base_url('/product/detail/'.$p['id_product']) ?>"
                                class="btn btn-sm btn-info">Detail</a>
                            <a href="<?= base_url('/product/edit/'.$p['id_product']) ?>"
                                class="btn btn-sm btn-warning">Edit</a>
                            <button type="button"
                                data-href="<?= base_url('/product/delete/'.$p['id_product']) ?>"
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
