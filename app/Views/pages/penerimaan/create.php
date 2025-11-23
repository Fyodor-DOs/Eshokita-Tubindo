<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'penerimaan', 'segment2' => 'create']) ?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Catat Penerimaan - No BON: <?= esc($pengiriman['no_bon']) ?></h5>
    <form id="formRec" method="post" enctype="multipart/form-data">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Customer</label>
          <select name="id_customer" id="customer" class="form-select">
            <option value="">- Pilih -</option>
            <?php foreach($customers as $c): ?>
            <option value="<?= $c['id_customer'] ?>"><?= esc($c['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Tanggal Terima</label>
          <input type="datetime-local" name="received_at" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Nama Penerima</label>
          <input type="text" name="receiver_name" class="form-control">
        </div>
      </div>

      <hr>
      <div class="table-responsive">
        <table class="table align-middle" id="tblItems">
          <thead><tr><th>Jenis</th><th class="col-2">Harga</th><th class="col-2">Qty</th><th class="col-2">Subtotal</th></tr></thead>
          <tbody>
            <tr data-key="besar"><td>Es Besar</td><td><input type="number" class="form-control price" step="0.01"></td><td><input type="number" class="form-control qty" min="0"></td><td><input type="text" class="form-control subtotal" readonly></td></tr>
            <tr data-key="kecil"><td>Es Kecil</td><td><input type="number" class="form-control price" step="0.01"></td><td><input type="number" class="form-control qty" min="0"></td><td><input type="text" class="form-control subtotal" readonly></td></tr>
            <tr data-key="serut"><td>Es Serut</td><td><input type="number" class="form-control price" step="0.01"></td><td><input type="number" class="form-control qty" min="0"></td><td><input type="text" class="form-control subtotal" readonly></td></tr>
          </tbody>
          <tfoot>
            <tr><th colspan="3" class="text-end">Total</th><th><span id="grand">Rp 0</span></th></tr>
          </tfoot>
        </table>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Foto Penerimaan</label>
          <input type="file" name="photo" accept="image/*" capture="environment" class="form-control">
        </div>
        <div class="col-md-6">
          <label class="form-label">Catatan</label>
          <textarea name="note" class="form-control" rows="3"></textarea>
        </div>
      </div>

      <div class="mt-3 d-flex gap-2">
        <button class="btn btn-success" type="submit">Simpan</button>
        <a href="<?= base_url('pengiriman/detail/'.$pengiriman['id_pengiriman']) ?>" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
function formatR(n){return 'Rp ' + (n||0).toLocaleString('id-ID');}
function recalc(){
  let total=0;
  document.querySelectorAll('#tblItems tbody tr').forEach(tr=>{
    const price=parseFloat(tr.querySelector('.price').value)||0;
    const qty=parseFloat(tr.querySelector('.qty').value)||0;
    const sub=price*qty; total+=sub;
    tr.querySelector('.subtotal').value = formatR(sub);
  });
  document.getElementById('grand').textContent = formatR(total);
}
document.querySelectorAll('.price,.qty').forEach(i=>i.addEventListener('input', recalc));
recalc();

// Load customer product prices via API if needed. For sekarang manual input bisa.

document.getElementById('formRec').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const items={};
  document.querySelectorAll('#tblItems tbody tr').forEach(tr=>{
    const key=tr.getAttribute('data-key');
    const price=parseFloat(tr.querySelector('.price').value)||0;
    const qty=parseInt(tr.querySelector('.qty').value||'0');
    if(qty>0){ items[key]={qty:qty, harga:price, total:qty*price}; }
  });
  const fd=new FormData(e.target);
  fd.append('items_received', JSON.stringify(items));
  const res=await fetch('', {method:'POST', body:fd});
  const j=await res.json();
  if(j.success){ alert(j.message); location.href=j.url||'<?= base_url('penerimaan') ?>'; } else { alert('Gagal: '+(j.message||'Unknown')); }
});
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'penerimaan', 'segment2' => 'create']) ?>

<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Terima Barang - No BON <?= esc($pengiriman['no_bon']) ?></h5>
    <form id="formPenerimaan" enctype="multipart/form-data" method="post">
      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">Customer</label>
          <select name="id_customer" id="customer" class="form-select">
            <option value="">- Pilih Customer -</option>
            <?php foreach(($customers ?? []) as $c): ?>
              <option value="<?= $c['id_customer'] ?>"><?= esc($c['nama']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Nama Penerima</label>
          <input type="text" class="form-control" name="receiver_name" placeholder="Nama penerima">
        </div>
        <div class="col-md-4">
          <label class="form-label">Waktu Diterima</label>
          <input type="datetime-local" class="form-control" name="received_at" value="<?= date('Y-m-d\TH:i') ?>">
        </div>

        <div class="col-12"><hr><strong>Item Diterima</strong></div>
        <div class="col-md-3">
          <label class="form-label">Es Besar (qty)</label>
          <input type="number" min="0" class="form-control qty" data-key="besar" placeholder="0">
        </div>
        <div class="col-md-3">
          <label class="form-label">Es Kecil (qty)</label>
          <input type="number" min="0" class="form-control qty" data-key="kecil" placeholder="0">
        </div>
        <div class="col-md-3">
          <label class="form-label">Es Serut (qty)</label>
          <input type="number" min="0" class="form-control qty" data-key="serut" placeholder="0">
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="received">Selesai</option>
            <option value="partial">Sebagian</option>
            <option value="failed">Gagal</option>
          </select>
        </div>

        <div class="col-12"><hr><strong>Upload Foto Penerimaan (driver)</strong></div>
        <div class="col-md-6">
          <input type="file" class="form-control" name="photo" accept="image/*" capture="environment">
        </div>
        <div class="col-md-6">
          <label class="form-label">Catatan</label>
          <input type="text" class="form-control" name="note" placeholder="Catatan opsional">
        </div>

        <div class="col-12"><hr><div class="d-flex justify-content-end align-items-center gap-3">
          <div>Total Bayar:</div>
          <h4 id="total" class="text-primary mb-0">Rp 0</h4>
        </div></div>
      </div>

      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="<?= base_url('penerimaan') ?>" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
// Price mapping pulled dynamically could be added; for now compute from selected customer via ajax
let priceMap = {};
document.getElementById('customer').addEventListener('change', async function(){
  const id = this.value;
  if(!id){ priceMap={}; updateTotal(); return; }
  const res = await fetch('<?= base_url('customer/get-customer-by-id') ?>/'+id);
  const c = await res.json();
  priceMap = {};
  (c.products||[]).forEach(p=>{
    // normalize keys by name contains 'Besar/Kecil/Serut'
    const name = (p.name||'').toLowerCase();
    if(name.includes('besar')) priceMap.besar = p.price;
    else if(name.includes('kecil')) priceMap.kecil = p.price;
    else if(name.includes('serut')) priceMap.serut = p.price;
  });
  updateTotal();
});

document.querySelectorAll('.qty').forEach(inp=>{
  inp.addEventListener('input', updateTotal);
});

function updateTotal(){
  let total = 0;
  document.querySelectorAll('.qty').forEach(inp=>{
    const k = inp.dataset.key;
    const qty = parseFloat(inp.value||'0');
    const price = priceMap[k] || 0;
    total += qty*price;
  });
  document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

document.getElementById('formPenerimaan').addEventListener('submit', async function(e){
  e.preventDefault();
  const items = {};
  document.querySelectorAll('.qty').forEach(inp=>{
    const k = inp.dataset.key; const v = parseInt(inp.value||'0'); if(v>0) items[k] = v;
  });
  const fd = new FormData(this);
  fd.append('items_received', JSON.stringify(items));
  try{
    const res = await fetch('', {method:'POST', body: fd});
    const j = await res.json();
    if(j.success){ alert(j.message); location.href=j.url; } else { alert('Gagal: '+JSON.stringify(j.message)); }
  }catch(err){ alert('Error: '+err.message); }
});
</script>

<?= $this->endSection() ?>
