<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'surat-jalan', 'segment2' => 'edit']) ?>

<form method="post" action="<?= site_url('nota/edit/'.($surat_jalan['id_surat_jalan'] ?? 0)) ?>">
  <?= csrf_field() ?>
  <div class="card">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Tanggal</label>
          <input type="date" name="tanggal" class="form-control" value="<?= esc($surat_jalan['tanggal'] ?? date('Y-m-d')) ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Rute</label>
          <select name="kode_rute" class="form-select">
            <?php foreach (($rutes ?? []) as $r): ?>
              <option value="<?= esc($r['kode_rute']) ?>" <?= ($surat_jalan['kode_rute'] ?? '') === $r['kode_rute'] ? 'selected' : '' ?>><?= esc($r['nama_wilayah']) ?> (<?= esc($r['kode_rute']) ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Nama Penerima</label>
          <input type="text" name="nama_penerima" class="form-control" value="<?= esc($surat_jalan['nama_penerima'] ?? '') ?>">
        </div>
        <div class="col-md-12">
          <label class="form-label">TTD Penerima</label>
          <input type="hidden" name="ttd_penerima" id="ttd_penerima">
          <div class="border rounded position-relative" style="height:200px">
            <canvas id="ttd" class="w-100 h-100"></canvas>
            <button type="button" class="btn btn-outline-danger position-absolute top-0 end-0 m-2" id="clear"><i class="bi bi-arrow-clockwise"></i></button>
          </div>
          <small class="text-muted">Kosongkan bila tidak ingin mengubah tanda tangan.</small>
        </div>
      </div>
    </div>
    <div class="card-footer bg-white d-flex justify-content-end">
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
  </div>
</form>
<script>
$(function(){
  signature('ttd','ttd_penerima');
  $('#clear').on('click', function(){ signature('ttd','ttd_penerima', true); });
});
</script>
<?= $this->endSection() ?>
