<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'invoice', 'segment2' => 'create']) ?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Konfirmasi Buat Invoice dari Pengiriman</h5>
    <p>No BON: <strong><?= esc($pengiriman['no_bon']) ?></strong></p>
    <p>Rute: <strong><?= esc($pengiriman['kode_rute']) ?></strong></p>
  <form method="post">
      <div class="row g-2">
        <div class="col-md-4">
          <label class="form-label">Tanggal Terbit</label>
          <input type="date" name="issue_date" class="form-control" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Jatuh Tempo</label>
          <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+7 days')) ?>">
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Buat Invoice</button>
        <a href="<?= base_url('pengiriman/detail/'.$pengiriman['id_pengiriman']) ?>" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>
