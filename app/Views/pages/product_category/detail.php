<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'product category', 'segment2' => 'detail', 'segment3' => $category['id_category']]) ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="form-label" for="name">Nama Kategori</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= esc($category['name']) ?>" readonly>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group mb-3">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea name="description" id="description" rows="4" class="form-control" readonly><?= esc($category['description'] ?? '') ?></textarea>
                </div>
            </div>

            <div class="col-md-12">
                <a href="<?= base_url('/product-category') ?>" class="btn btn-secondary">Kembali</a>
                <a href="<?= base_url('/product-category/edit/' . $category['id_category']) ?>" class="btn btn-warning">Edit</a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
