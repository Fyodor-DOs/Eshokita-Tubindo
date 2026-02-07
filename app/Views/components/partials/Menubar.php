<?php
$uri = service('uri');
$segment = $uri->getSegment(1);
$role = current_role() ?? 'guest';
?>

<ul class="nav nav-pills d-flex flex-column">
    <li class="nav-item">
        <a class="nav-link <?= ($segment === '') ? 'bg-primary bg-gradient active' : '' ?>" href="<?= base_url('/') ?>">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>
    </li>

    <?php if (in_array($role, ["admin", "super-admin", "produksi"])) : ?>
    <!-- Manajemen Produk -->
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'product') ? 'bg-primary active' : '' ?>"
            href="<?= base_url('/product') ?>">
            <i class="bi bi-box-seam"></i> Produk
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'product-category') ? 'bg-primary active' : '' ?>"
            href="<?= base_url('/product-category') ?>">
            <i class="bi bi-tags"></i> Kategori
        </a>
    </li>
    
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'customer') ? 'bg-primary active' : '' ?>"
            href="<?= base_url('/customer') ?>">
            <i class="bi bi-people-fill"></i> Customer
        </a>
    </li>
    <?php endif; ?>

    <?php if (in_array($role, ["admin", "super-admin"])) : ?>
    <!-- Manajemen Distribusi (Admin) -->
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'rute') ? 'bg-primary active' : '' ?>"
            href="<?= base_url('/rute') ?>">
            <i class="bi bi-signpost-2"></i> Rute
        </a>
    </li>
    <?php endif; ?>

    <?php if (in_array($role, ["distributor", "admin", "super-admin"])) : ?>
    <!-- Manajemen Distribusi -->
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'nota') ? 'bg-primary active' : '' ?>"
            href="<?= base_url('/nota') ?>">
            <i class="bi bi-file-earmark-text"></i> Nota
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'pengiriman') ? 'bg-primary active' : '' ?>"
            href="<?= base_url('/pengiriman') ?>">
            <i class="bi bi-truck"></i> Pengiriman
        </a>
    </li>
    <!-- Penerimaan & Tracking dipusatkan di Pengiriman -->
    <?php endif; ?>

    <?php if (in_array($role, ["admin", "super-admin", "produksi"])) : ?>
    <!-- Keuangan -->
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'invoice') ? 'bg-primary active' : '' ?>"
            href="<?= base_url('/invoice') ?>">
            <i class="bi bi-cash-coin"></i> Pembayaran
        </a>
    </li>
    <?php endif; ?>

    <?php if (in_array($role, ["admin", "super-admin"])) : ?>
    <!--  Cek apakah pengguna adalah admin  -->
    <li class="nav-item">
        <a class="nav-link <?= ($segment === 'user') ? 'bg-primary active' : '' ?>" href="<?= base_url('/user') ?>">
            <i class="bi bi-person-fill-gear"></i> Manage Users
        </a>
    </li>
    <?php endif; ?>
</ul>