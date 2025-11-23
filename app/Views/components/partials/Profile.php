<?php
$profileName = current_user_name();
$profileId   = current_user_id();
?>

<button class="btn dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false"
    id="btnProfile">
    <span class="fw-bold text-white ms-1 d-none d-md-inline"><?= esc($profileName) ?></span>
</button>

<div class="dropdown-menu my-3 py-0 col-md-3 col-11 shadow me-md-3" id="dropdownProfile">
    <div class="position-relative">
        <img src="<?= base_url('assets/image/bg/banner.jpg') ?>" class="rounded-top" style="width: 100%; height: 80px;"
            alt="Banner">

        <div class="gradient-overlay rounded-top"></div>

        <div class="title-overlay position-absolute top-50 start-50 translate-middle">
            <h5 class="text-light text-truncate" style="width: 200px;"><?= esc($profileName) ?></h5>
        </div>
    </div>

    <div class="p-3 d-flex gap-1 justify-content-between">
        <a href="<?= $profileId ? site_url('user/detail/' . $profileId) : '#' ?>"
            class="btn p-2 w-100 btn-outline-success text-decoration-none">
            <i class="bi bi-person"></i> Profile</a>

        <a href="<?= site_url('logout') ?>" class="btn p-2 w-100 btn-outline-danger text-decoration-none">
            <i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

</div>