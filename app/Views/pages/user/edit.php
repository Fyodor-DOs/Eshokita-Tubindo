<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'user', 'segment2' => 'edit', 'segment3' => $user['id_user']]) ?>
<form method="post" action="<?= site_url('user/edit/' . $user['id_user']) ?>">
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" name="nama" id="nama" class="form-control" value="<?= $user['nama'] ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="telepon" class="form-label">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control"
                            value="<?= $user['telepon'] ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $user['email'] ?>">
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
                            <option value="distributor" <?= $user['role'] == 'distributor' ? 'selected' : '' ?>>
                                Distributor</option>
                            <option value="produksi" <?= $user['role'] == 'produksi' ? 'selected' : '' ?>>Produksi
                            </option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary col-12 col-md-3">Update</button>
            </div>
        </div>
    </div>

</form>

<?= $this->endSection() ?>