<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'product', 'segment2' => 'edit']) ?>
<div class="card"><div class="card-body">
<h5 class="mb-3">Edit Produk</h5>
<form id="f" method="post">
  <div class="row g-3">
    <div class="col-md-4"><label class="form-label">SKU</label><input name="sku" class="form-control" required value="<?= esc($product['sku']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Nama</label><input name="name" class="form-control" required value="<?= esc($product['name']) ?>"></div>
    <div class="col-md-4"><label class="form-label">Kategori</label>
      <select name="id_category" class="form-select">
        <option value="">-</option>
        <?php foreach($categories as $c): ?>
        <option value="<?= (int)$c['id_category'] ?>" <?= $product['id_category']==$c['id_category']?'selected':'' ?>><?= esc($c['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3"><label class="form-label">Berat Satuan (kg)</label><input name="unit" class="form-control" value="<?= esc($product['unit']) ?>" required></div>
    <div class="col-md-3">
      <label class="form-label">Stok (QTY)</label>
      <input name="qty" type="number" min="0" class="form-control" value="<?= esc($product['qty'] ?? 0) ?>" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Harga</label>
      <input name="price" type="number" step="0.01" class="form-control" value="<?= esc($product['price'] ?? 0) ?>">
    </div>
    <div class="col-12">
      <label class="form-label">Catatan</label>
      <textarea name="notes" class="form-control" rows="2"><?= esc($product['notes'] ?? '') ?></textarea>
    </div>
  </div>
  <div class="mt-3 d-flex gap-2">
    <button class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('/product') ?>" class="btn btn-secondary">Batal</a>
  </div>
</form>
  </div></div>
<?= $this->endSection() ?>
<script>
document.getElementById('f').addEventListener('submit', async (e)=>{
  e.preventDefault();
  const fd=new FormData(e.target);
  const r= await fetch('',{method:'POST', body:fd});
  const j= await r.json();
  if(j.success){ 
    location.href=j.url;
  } else { 
    let errorMsg = '';
    if(typeof j.message === 'object' && !Array.isArray(j.message)){
      errorMsg = Object.values(j.message).join("\n");
    } else if(Array.isArray(j.message)){
      errorMsg = j.message.join("\n");
    } else {
      errorMsg = j.message;
    }
    alert(errorMsg);
  }
});
</script>
 
