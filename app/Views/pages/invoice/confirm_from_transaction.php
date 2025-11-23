<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'invoice', 'segment2' => 'create']) ?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Konfirmasi Buat Invoice dari Transaksi</h5>
    <p>No Transaksi: <strong><?= esc($transaction['transaction_no']) ?></strong></p>
    <p>Total: <strong>Rp <?= number_format($transaction['total_amount'],0,',','.') ?></strong></p>
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
        <a href="<?= base_url('customer/transaksi/'.$transaction['id_customer']) ?>" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>
  <script>
  // Show success alert from sessionStorage or flashdata
  (function(){
    let msg = null;
    // Cek sessionStorage dulu (dari order_again)
    try { 
      msg = sessionStorage.getItem('success_message');
      if (msg) sessionStorage.removeItem('success_message');
    } catch(_) {}
    
    // Fallback ke flashdata jika ada
    <?php if (session()->getFlashdata('success_message')): ?>
    if (!msg) msg = <?= json_encode(session()->getFlashdata('success_message')) ?>;
    <?php endif; ?>
    
    // Tampilkan sweet alert jika ada pesan
    if (msg) {
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: msg,
        timer: 2500,
        timerProgressBar: true,
        showConfirmButton: true,
        confirmButtonText: 'OK'
      });
    }
  })();
  </script>
<?= $this->endSection() ?>
