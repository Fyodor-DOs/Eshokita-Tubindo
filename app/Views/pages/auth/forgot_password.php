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
                <h4>Lupa Password</h4>
                <p class="text-muted">Masukkan email Anda untuk reset password</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('/forgot-password') ?>">
                    <?= csrf_field() ?>

                    <div class="form-floating mb-3">
                        <input type="email" name="email" id="email" class="form-control bg-light"
                            value="<?= old('email') ?>" placeholder="Email" autofocus required>
                        <label for="email" class="form-label">Email</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Kirim Link Reset Password</button>
                    
                    <a href="<?= site_url('/login') ?>" class="d-flex justify-content-center text-decoration-none">Kembali ke Login</a>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
