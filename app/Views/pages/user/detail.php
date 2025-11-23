<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'user', 'segment2' => 'detail', 'segment3' => $user['id_user']]) ?>

<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Detail User</h5>
      <div class="d-flex gap-2">
        <a href="<?= base_url('user/edit/' . $user['id_user']) ?>" class="btn btn-warning">Edit</a>
        <a href="<?= base_url('user') ?>" class="btn btn-secondary">Kembali</a>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nama</label>
        <input class="form-control" value="<?= esc($user['nama']) ?>" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Role</label>
        <input class="form-control" value="<?= esc(ucwords($user['role'])) ?>" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Telepon</label>
        <input class="form-control" value="<?= esc($user['telepon']) ?>" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input class="form-control" value="<?= esc($user['email']) ?>" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Dibuat</label>
        <input class="form-control" value="<?= isset($user['created_at']) ? esc($user['created_at']) : '-' ?>" readonly>
      </div>
      <div class="col-md-6">
        <label class="form-label">Diubah</label>
        <input class="form-control" value="<?= isset($user['updated_at']) ? esc($user['updated_at']) : '-' ?>" readonly>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>