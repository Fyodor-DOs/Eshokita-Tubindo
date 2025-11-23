<?= $this->extend('layouts/main') ?>

<?= $this->section('main') ?>
<div class="d-flex justify-content-center align-items-center bg-primary bg-gradient flex-column min-vh-100">

    <?= $this->renderSection('auth') ?>
</div>
<?= $this->endSection() ?>