<div class="bg-white d-none d-md-block shadow" id="sidebar">
    <div class="d-flex justify-content-center align-items-center sticky-top py-2 bg-white">
        <a class="navbar-brand fs-4 fw-bold " href="<?= base_url('/') ?>">
            <img src="<?= base_url('assets/image/logo.png') ?>" alt="Logo" class="" style="width: 120px;">
        </a>
    </div>

    <div class="py-4 px-3">
        <?= $this->include('components/partials/Menubar') ?>
    </div>
</div>