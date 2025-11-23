<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'product category', 'segment2' => 'edit']) ?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Edit Kategori</h5>
    <form id="f" method="post">
      <div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" required value="<?= esc($category['name']) ?>"></div>
      <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control"><?= esc($category['description']) ?></textarea></div>
      <button class="btn btn-primary">Simpan</button>
      <a href="<?= base_url('/product-category') ?>" class="btn btn-secondary ms-2">Batal</a>
    </form>
  </div>
</div>
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
<?= $this->endSection() ?>
