<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'product category', 'segment2' => 'create']) ?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Tambah Kategori</h5>
    <form id="f" method="post">
      <div class="mb-3"><label class="form-label">Nama</label><input name="name" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Deskripsi</label><textarea name="description" class="form-control"></textarea></div>
      <button class="btn btn-primary">Simpan</button>
      <a href="<?= base_url('/product-category') ?>" class="btn btn-secondary ms-2">Batal</a>
    </form>
  </div>
</div>
<script>
(() => {
  const form = document.getElementById('f');
  const submitBtn = form.querySelector('button[type="submit"]') || form.querySelector('button');
  let inProgress = false;

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    if (typeof e.stopImmediatePropagation === 'function') e.stopImmediatePropagation();
    if (inProgress) return; 
    inProgress = true;
    if (submitBtn) submitBtn.setAttribute('disabled', 'disabled');

    try {
      const fd = new FormData(form);
      const r = await fetch('', { method: 'POST', body: fd });
      const j = await r.json();

      if (j.success) {
        try {
          Swal.fire({
            icon: 'success',
            title: 'Sukses',
            text: j.message || 'Kategori berhasil ditambahkan',
            showConfirmButton: true,
            confirmButtonText: 'OK',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: true,
            timer: 3000,
            timerProgressBar: true,
          }).then((res) => {
            if (res.isConfirmed || (res.dismiss && res.dismiss === Swal.DismissReason.timer)) {
              if (j.url) {
                window.location.href = j.url;
              } else {
                window.location.reload();
              }
            }
          });
        } catch (e) {
          if (j.url) window.location.href = j.url;
        }
        return;
      }

      const msg = j.message && typeof j.message === 'string' ? j.message : JSON.stringify(j.message || {});
      if (msg.toLowerCase().includes('sudah ada') || (j.id && Number.isInteger(j.id))) {
        location.href = '/product-category';
        return;
      }

      try { alert(msg); } catch (err) { console.error('Alert failed', err); }
    } catch (err) {
      console.error('Product category create error', err);
      try { alert('Gagal menyimpan kategori.'); } catch (e) {}
    } finally {
      inProgress = false;
      if (submitBtn) submitBtn.removeAttribute('disabled');
    }
  });
})();
</script>
<?= $this->endSection() ?>
