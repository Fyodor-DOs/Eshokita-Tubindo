<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'penerimaan']) ?>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover" id="table-penerimaan">
        <thead>
          <tr>
            <th>No BON</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Diterima</th>
            <th>Verifikasi</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach(($penerimaan ?? []) as $row): ?>
          <tr>
            <td><?= esc($row['no_bon']) ?></td>
            <td><?= esc($row['customer_name'] ?? '-') ?></td>
            <td><span class="badge bg-<?php echo $row['status']==='received'?'success':($row['status']==='partial'?'warning':'danger'); ?>"><?= esc($row['status']) ?></span></td>
            <td><?= esc($row['received_at']) ?></td>
            <td><?= $row['verified']? '<span class="badge bg-success">Verified</span>':'<span class="badge bg-secondary">Pending</span>' ?></td>
            <td>
              <?php if(!$row['verified']): ?>
              <button class="btn btn-sm btn-primary" onclick="verify(<?= (int)$row['id_penerimaan'] ?>)">Verifikasi</button>
              <?php endif; ?>
              <?php if(!empty($row['photo_path'])): ?>
              <a class="btn btn-sm btn-outline-secondary" target="_blank" href="<?= base_url('uploads/receipts/'.$row['photo_path']) ?>">Foto</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<script>
async function verify(id){
  const res = await fetch('<?= base_url('penerimaan/verify') ?>/'+id, {method:'POST'});
  const j = await res.json();
  if(j.success) location.reload(); else alert('Gagal verifikasi');
}
</script>
<?= $this->endSection() ?>
