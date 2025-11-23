<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'customer', 'segment2' => 'order-ulang', 'segment3' => $customer['nama']]) ?>

<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Order Ulang untuk: <?= esc($customer['nama']) ?></h5>

    <div class="row g-3 mb-3">
      <div class="col-md-3">
        <label class="form-label">Rute</label>
        <input type="text" class="form-control" value="<?= esc(($customer['kode_rute']??'').' - '.($customer['nama_wilayah']??'')) ?>" readonly>
      </div>
      <div class="col-md-3">
        <label class="form-label">Nama</label>
        <input type="text" class="form-control" value="<?= esc($customer['nama']) ?>" readonly>
      </div>
      <div class="col-md-3">
        <label class="form-label">Email</label>
        <input type="text" class="form-control" value="<?= esc($customer['email']) ?>" readonly>
      </div>
      <div class="col-md-3">
        <label class="form-label">Telepon</label>
        <input type="text" class="form-control" value="<?= esc($customer['telepon']) ?>" readonly>
      </div>
      <div class="col-12">
        <label class="form-label">Alamat</label>
        <textarea class="form-control" rows="2" readonly><?= esc($customer['alamat']) ?></textarea>
      </div>
    </div>

    <form id="formOrderAgain" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="items" id="itemsField" value="[]">
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Produk</th>
              <th class="col-3 text-end">Harga</th>
              <th class="col-3">Qty</th>
              <th class="col-3 text-end">Subtotal</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($products as $p): ?>
            <tr data-id="<?= $p['id_product'] ?>" data-price="<?= (float)$p['customer_price'] ?>" data-stock="<?= (int)($p['qty'] ?? 0) ?>">
              <td>
                <div class="fw-semibold"><?= esc($p['name']) ?></div>
                <div class="text-muted small">SKU: <?= esc($p['sku'] ?? '-') ?></div>
              </td>
              <td class="text-end">Rp <?= number_format((float)$p['customer_price'], 0, ',', '.') ?></td>
              <td>
                <input type="number" class="form-control qty" min="0" placeholder="0">
                <div class="form-text sisa-note">Stok: <?= (int)($p['qty'] ?? 0) ?> | Sisa: <?= (int)($p['qty'] ?? 0) ?></div>
              </td>
              <td class="text-end subtotal">Rp 0</td>
            </tr>
          <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">Total Bayar</th>
              <th class="text-end" id="grandTotal">Rp 0</th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="d-flex justify-content-end align-items-center gap-2">
        <a href="<?= base_url('customer') ?>" class="btn btn-secondary">Kembali</a>
        <button class="btn btn-primary" type="submit">Submit</button>
      </div>
    </form>
  </div>
</div>

<script>
function formatRupiah(n){return 'Rp ' + (n||0).toLocaleString('id-ID');}
function recalc(){
  let total=0;
  document.querySelectorAll('tbody tr').forEach(tr=>{
    const price=parseFloat(tr.getAttribute('data-price'))||0;
    const qty=parseFloat(tr.querySelector('.qty').value)||0;
    const stock=parseFloat(tr.getAttribute('data-stock'))||0;
    const subtotal=price*qty; total+=subtotal;
    tr.querySelector('.subtotal').textContent = formatRupiah(subtotal);
    const sisa = Math.max(0, stock - qty);
    const note = tr.querySelector('.sisa-note');
    if (note) note.textContent = 'Stok: '+(stock||0)+' | Sisa: '+sisa;
  });
  document.getElementById('grandTotal').textContent = formatRupiah(total);
}
document.querySelectorAll('.qty').forEach(inp=>inp.addEventListener('input', recalc));
recalc();

let __oaProcessing = false;
document.getElementById('formOrderAgain').addEventListener('submit', async (e)=>{
  e.preventDefault();
  if (__oaProcessing) return; // prevent double submit
  __oaProcessing = true;
  const submitBtn = e.currentTarget.querySelector('button[type="submit"]');
  submitBtn && (submitBtn.disabled = true);
  const form = e.currentTarget;
  const items=[];
  document.querySelectorAll('tbody tr').forEach(tr=>{
    const id = tr.getAttribute('data-id');
    const qty = parseInt(tr.querySelector('.qty').value||'0');
    const price = parseFloat(tr.getAttribute('data-price')||'0');
    if(qty>0){ items.push({id_product:id, qty:qty, price:price}); }
  });
  if(items.length===0){
    Swal.fire({icon:'warning', title:'Perhatian', text:'Isi minimal satu produk.'});
    __oaProcessing = false;
    submitBtn && (submitBtn.disabled = false);
    return;
  }
  document.getElementById('itemsField').value = JSON.stringify(items);

  const fd = new FormData(form); // includes CSRF automatically
  try {
    const res = await fetch('', {
      method:'POST',
      headers: { 'X-Requested-With':'XMLHttpRequest' },
      body: fd
    });
    const ct = res.headers.get('content-type') || '';
    if (ct.includes('application/json')) {
      const j = await res.json();
      if (j && j.success) {
        // Simpan pesan ke sessionStorage untuk ditampilkan di halaman berikutnya
        try {
          sessionStorage.setItem('success_message', j.message || 'Pesanan berhasil dibuat');
        } catch(_) {}
        // Langsung redirect, sweet alert akan tampil di halaman tujuan
        if (j.url) {
          window.location.href = j.url;
        } else {
          window.location.reload();
        }
      } else {
        // Tampilkan error jika ada
        Swal.fire({
          icon: 'error',
          title: 'Gagal',
          text: j.message || 'Terjadi kesalahan saat membuat pesanan'
        });
        __oaProcessing = false;
        submitBtn && (submitBtn.disabled = false);
      }
    } else {
      // Server melakukan redirect HTML normal; ikuti URL tujuan
      window.location.href = res.url || window.location.href;
    }
  } catch (err) {
    console?.error && console.error('Order again submit error', err);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Terjadi kesalahan koneksi'
    });
    __oaProcessing = false;
    submitBtn && (submitBtn.disabled = false);
  }
});
</script>

<?= $this->endSection() ?>
