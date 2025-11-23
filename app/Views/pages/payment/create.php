<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'payment', 'segment2' => 'create', 'segment3' => $invoice['invoice_no']]) ?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Tambah Pembayaran - <?= esc($invoice['invoice_no']) ?></h5>
    <div class="alert alert-info">
      <i class="bi bi-info-circle"></i> <strong>Wajib:</strong> Upload foto invoice/bukti pembayaran (struk transfer, nota, dll)
    </div>
    <form id="f" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Tanggal Bayar</label>
          <input type="datetime-local" name="paid_at" class="form-control" value="<?= date('Y-m-d\\TH:i') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
          <select name="method" class="form-select" required>
            <option value="">-- Pilih Metode --</option>
            <option value="cash">Cash (Tunai)</option>
            <option value="kredit">Kredit</option>
            <option value="transfer">Transfer Bank</option>
            <option value="other">Lainnya</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Jumlah Bayar <span class="text-danger">*</span></label>
          <?php 
            $total = (float)$invoice['amount'];
            $db = \Config\Database::connect();
            $paidRow = $db->table('payment')->where('id_invoice', $invoice['id_invoice'])->selectSum('amount', 'total_paid')->get()->getRowArray();
            $paid = (float)($paidRow['total_paid'] ?? 0);
            $outstanding = max(0, $total - $paid);
          ?>
          <input type="number" step="0.01" name="amount" class="form-control" value="<?= $outstanding ?>" required readonly>
          <small class="text-muted">Sisa yang harus dibayar: Rp <?= number_format($outstanding,0,',','.') ?></small>
        </div>
        
        <div class="col-12">
          <label class="form-label">Foto Invoice / Bukti Pembayaran <span class="text-danger">*</span></label>
          <input type="file" name="invoice_photo" class="form-control" accept="image/*" required>
          <small class="text-muted">Upload foto struk transfer, nota, atau bukti pembayaran (JPG, PNG, max 5MB)</small>
        </div>
        
        <div class="col-12">
          <label class="form-label">Catatan</label>
          <textarea name="note" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
        </div>
      </div>
      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan Pembayaran</button>
        <a href="<?= base_url('/invoice') ?>" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Batal</a>
      </div>
    </form>
  </div>
</div>
<script>
let __payProcessing = false;
document.getElementById('f').addEventListener('submit', async (e)=>{
  e.preventDefault();
  e.stopImmediatePropagation(); // Prevent global form handler
  if (__payProcessing) return; // debounce double submit
  __payProcessing = true;
  const submitBtn = e.target.querySelector('button[type="submit"]');
  submitBtn && (submitBtn.disabled = true);
  // Validate file upload
  const fileInput = document.querySelector('input[name="invoice_photo"]');
  if (!fileInput.files || fileInput.files.length === 0) {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Foto invoice/bukti pembayaran wajib diupload!' });
      __payProcessing = false; submitBtn && (submitBtn.disabled = false);
      return;
  }
  // Validate file size (max 5MB)
  const file = fileInput.files[0];
  if (file.size > 5 * 1024 * 1024) {
      Swal.fire({ icon: 'error', title: 'Gagal', text: 'Ukuran file terlalu besar! Maksimal 5MB.' });
      __payProcessing = false; submitBtn && (submitBtn.disabled = false);
      return;
  }
  const fd=new FormData(e.target); // includes CSRF
  try {
    const r= await fetch('',{method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body:fd});
    const j= await r.json();
    if(j && j.success){
      // Tutup semua swal yang mungkin masih terbuka
      if (typeof Swal !== 'undefined') {
        try { Swal.close(); } catch(_e) {}
      }
      // Tampilkan hanya sweet alert sukses
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon:'success',
          title:'Berhasil',
          text: j.message || 'Pembayaran berhasil disimpan!',
          timer: 2500,
          timerProgressBar: true,
          showConfirmButton: true,
          confirmButtonText: 'OK',
          allowOutsideClick: false
        }).then(()=>{ 
          window.location.href = j.url || '<?= base_url('/invoice') ?>'; 
        });
      } else {
        // Fallback jika Swal tidak ada
        window.location.href = j.url || '<?= base_url('/invoice') ?>';
      }
      return; // Stop execution di sini, tidak ada code lain yang dijalankan
    }
    // Jika tidak success, redirect ke invoice list tanpa alert
    console?.warn && console.warn('Create payment response:', j);
    window.location.href = '<?= base_url('/invoice') ?>';
  } catch(err){
    // Network error, redirect tanpa alert
    console?.error && console.error('Submit payment error', err);
    window.location.href = '<?= base_url('/invoice') ?>';
  }
});
</script>
<?= $this->endSection() ?>
