<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'customer', 'segment2' => 'order', 'segment3' => $customer['nama']]) ?>

<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Buat Pesanan untuk: <?= esc($customer['nama']) ?></h5>
    <form id="formOrder" method="post">
      <?= csrf_field() ?>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead><tr><th>Produk</th><th class="col-2">Harga</th><th class="col-2">Qty</th><th class="col-2">Subtotal</th></tr></thead>
          <tbody>
          <?php foreach($products as $p): ?>
            <tr data-id="<?= $p['id_product'] ?>">
              <td>
                <div class="fw-semibold"><?= esc($p['name']) ?></div>
              </td>
              <td>
                <input type="number" step="0.01" class="form-control price" value="<?= (float)$p['customer_price'] ?>">
              </td>
              <td>
                <input type="number" min="0" class="form-control qty" placeholder="0">
              </td>
              <td>
                <input type="text" class="form-control subtotal" readonly value="Rp 0">
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <th colspan="3" class="text-end">Total</th>
              <th><span id="grandTotal">Rp 0</span></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="d-flex gap-2">
        <button class="btn btn-primary" type="submit">Next: Buat Invoice</button>
        <a href="<?= base_url('customer/detail/'.$customer['id_customer']) ?>" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
function formatRupiah(n){return 'Rp ' + (n||0).toLocaleString('id-ID');}
function recalc(){
  let total=0;
  document.querySelectorAll('tbody tr').forEach(tr=>{
    const price=parseFloat(tr.querySelector('.price').value)||0;
    const qty=parseFloat(tr.querySelector('.qty').value)||0;
    const subtotal=price*qty; total+=subtotal;
    tr.querySelector('.subtotal').value = formatRupiah(subtotal);
  });
  document.getElementById('grandTotal').textContent = formatRupiah(total);
}
document.querySelectorAll('.price,.qty').forEach(inp=>inp.addEventListener('input', recalc));
recalc();

let __orderProcessing = false;
document.getElementById('formOrder').addEventListener('submit', async (e)=>{
  e.preventDefault();
  if (__orderProcessing) return; // Prevent double submit
  __orderProcessing = true;
  
  const submitBtn = e.target.querySelector('button[type="submit"]');
  if (submitBtn) submitBtn.disabled = true;
  
  const items=[];
  document.querySelectorAll('tbody tr').forEach(tr=>{
    const id = tr.getAttribute('data-id');
    const qty = parseInt(tr.querySelector('.qty').value||'0');
    const price = parseFloat(tr.querySelector('.price').value||'0');
    if(qty>0){ items.push({id_product:id, qty:qty, price:price}); }
  });
  if(items.length===0){ 
    alert('Isi minimal satu produk.'); 
    __orderProcessing = false;
    if (submitBtn) submitBtn.disabled = false;
    return; 
  }
  
  const fd = new FormData();
  fd.append('items', JSON.stringify(items));
  const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]')?.value;
  if (csrfToken) fd.append('<?= csrf_token() ?>', csrfToken);
  
  try {
    const res = await fetch('', {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}, body:fd});
    const j = await res.json();
    if(j.success){
      try { if (j.message) sessionStorage.setItem('success_message', j.message); } catch(_) {}
      location.href = j.url;
    }else{
      // Supress alert gagal untuk konsistensi UI; log saja
      console?.warn && console.warn('Order submit gagal', j);
      __orderProcessing = false;
      if (submitBtn) submitBtn.disabled = false;
    }
  } catch (err) {
    console?.error && console.error('Order submit error', err);
    __orderProcessing = false;
    if (submitBtn) submitBtn.disabled = false;
  }
});
</script>

<?= $this->endSection() ?>
