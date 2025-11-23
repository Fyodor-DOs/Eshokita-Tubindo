<nav class="navbar dark:bg-dark bg-primary sticky-top shadow-md">
    <div class="container-fluid px-3">
        <!-- Button Sidebar -->
        <button class="btn border-0 d-none d-md-block" id="btnSidebar" type="button">
            <i class="bi bi-list text-light fs-5"></i>
        </button>

        <!-- Button Offcanvas Mobile -->
        <button class="btn d-md-none border-0" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar" aria-label="Toggle navigation">
            <i class="bi bi-list text-light fs-5"></i>
        </button>


        <a class="navbar-brand d-md-none bg-white px-5 rounded" href="<?= base_url('/') ?>">
            <img src="<?= base_url('assets/image/logo.png') ?>" alt="Logo" class="" style="height: 50px;">
        </a>

        <div class="d-flex justify-self-start align-items-center d-none d-md-block">
            <?= $this->include('components/partials/Profile') ?>
        </div>

        <?= $this->include('components/Offcanvas') ?>
    </div>
</nav>