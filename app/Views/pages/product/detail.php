<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'product', 'segment2' => 'detail']) ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label" for="sku">SKU</label>
                    <input type="text" name="sku" id="sku" class="form-control" value="<?= esc($product['sku']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label" for="name">Nama Produk</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= esc($product['name']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label" for="category">Kategori</label>
                    <input type="text" name="category" id="category" class="form-control" value="<?= esc($product['category_name'] ?? '-') ?>" readonly>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group mb-3">
                    <label class="form-label" for="unit">Berat Satuan (kg)</label>
                    <input type="text" name="unit" id="unit" class="form-control" value="<?= esc($product['unit']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label" for="price">Harga</label>
                    <input type="text" name="price" id="price" class="form-control" value="Rp <?= number_format($product['price'], 0, ',', '.') ?>" readonly>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group mb-3">
                    <label class="form-label" for="qty">Stok (QTY)</label>
                    <input type="text" name="qty" id="qty" class="form-control" value="<?= number_format($product['qty'] ?? 0) ?> unit" readonly>
                </div>
            </div>

            <div class="col-12">
                <div class="form-group mb-3">
                    <label class="form-label" for="notes">Catatan</label>
                    <textarea name="notes" id="notes" rows="4" class="form-control" readonly><?= esc($product['notes'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="col-12">
                <a href="<?= base_url('/product') ?>" class="btn btn-secondary">Kembali</a>
                <a href="<?= base_url('/product/edit/' . $product['id_product']) ?>" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
