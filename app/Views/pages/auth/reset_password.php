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
                <h4>Reset Password</h4>
                <p class="text-muted">Masukkan password baru untuk <?= esc($email) ?></p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('/reset-password/' . $token) ?>">
                    <?= csrf_field() ?>

                    <div class="form-floating mb-3">
                        <input type="password" name="password" id="password" class="form-control bg-light"
                            placeholder="Password Baru" required minlength="6">
                        <label for="password" class="form-label">Password Baru</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control bg-light"
                            placeholder="Konfirmasi Password" required minlength="6">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Reset Password</button>
                    
                    <a href="<?= site_url('/login') ?>" class="d-flex justify-content-center text-decoration-none">Kembali ke Login</a>
                </form>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
