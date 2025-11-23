<?= $this->extend('layouts/main') ?>
<?= $this->section('main') ?>
<div class="d-flex flex-row">
    <div class="min-vh-100 col-2 d-none d-md-block" id="layoutSidebar"><?= $this->include('components/Sidebar') ?></div>

    <div class="col-md-10 col-12" id="layoutContent">
        <?= $this->include('components/Navbar') ?>
        <div class="container-fluid p-3 overflow-auto">
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>