<?= $this->extend('layouts/auth') ?>

<?= $this->section('auth') ?>
<div class="card col-md-5 col-11 p-3">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 d-flex justify-content-center align-items-center mb-3">
                <img src="<?= base_url('assets/image/logo.png') ?>" alt="Image" class="img-fluid"
                    style="max-width: 200px;">
            </div>

            <div class="col-md-6">
                <h4>Selamat Datang di <span class="text-primary fw-bold">Eshokita</span>! </h4>
                <p class="text-muted">Masuk ke akun anda</p>

                <form method="post" action="<?= site_url('/login') ?>">
                    <?= csrf_field() ?>

                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="email" class="form-control bg-light"
                            value="<?= old('email') ?>" placeholder="Email" autofocus required>
                        <label for="email" class="form-label">Email</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="password" id="password" class="form-control bg-light"
                            placeholder="Password" required>
                        <label for="password" class="form-label">Password</label>
                    </div>

                    <a href="<?= site_url('/forgot-password') ?>" class="d-flex justify-content-end text-decoration-none mb-3">Lupa Password?</a>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Masuk</button>
                </form>

            </div>
        </div>
    </div>
    <?= $this->endSection() ?>