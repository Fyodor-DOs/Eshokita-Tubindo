<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'user', 'segment2' => 'create']) ?>

<form method="post" action="<?= site_url('user/create') ?>">
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select">
                            <option value="">Pilih Role</option>
                            <option value="distributor">Distributor</option>
                            <option value="produksi">Produksi</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary col-12 col-md-3">Submit</button>
            </div>
        </div>
    </div>

</form>

<?= $this->endSection() ?>