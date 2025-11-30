<div class="modal-header">
  <h5 class="modal-title">Detail Customer</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
  <div class="row g-2">
    <div class="col-md-6">
      <div class="mb-2"><strong>Nama</strong><div><?= esc($customer['nama']) ?></div></div>
      <div class="mb-2"><strong>Email</strong><div><?= esc($customer['email']) ?></div></div>
      <div class="mb-2"><strong>Telepon</strong><div><?= esc($customer['telepon']) ?></div></div>
      <div class="mb-2"><strong>Rute</strong><div><?= esc(($customer['kode_rute']??''). ' - ' . ($customer['nama_wilayah']??'')) ?></div></div>
    </div>
    <div class="col-md-6">
      <div class="mb-2"><strong>Alamat</strong><div><?= esc($customer['alamat']) ?></div></div>
      <div class="mb-2"><strong>Kecamatan</strong><div><?= esc($customer['kecamatan']) ?></div></div>
      <div class="mb-2"><strong>Kabupaten</strong><div><?= esc($customer['kabupaten']) ?></div></div>
      <div class="mb-2"><strong>Provinsi</strong><div><?= esc($customer['provinsi']) ?></div></div>
    </div>
  </div>
  <?php $orderItems = json_decode($customer['order_items'] ?? '[]', true) ?: []; ?>
  <hr>
  <h6>Order Awal</h6>
  <?php if(!empty($orderItems)): ?>
  <div class="table-responsive">
    <table class="table table-sm">
      <thead><tr><th>Produk</th><th class="text-end">Harga</th><th class="text-end">Qty</th><th class="text-end">Subtotal</th></tr></thead>
      <tbody>
        <?php $gt=0; foreach($orderItems as $it): $gt += (float)($it['subtotal']??0); ?>
          <tr>
            <td><?= esc($it['name'] ?? ('Produk #'.$it['id_product'])) ?></td>
            <td class="text-end">Rp <?= number_format((float)($it['price'] ?? 0),0,',','.') ?></td>
            <td class="text-end"><?= (int)($it['qty'] ?? 0) ?></td>
            <td class="text-end">Rp <?= number_format((float)($it['subtotal'] ?? 0),0,',','.') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><th colspan="3" class="text-end">Total</th><th class="text-end">Rp <?= number_format($gt,0,',','.') ?></th></tr>
      </tfoot>
    </table>
  </div>
  <?php else: ?>
  <p class="text-muted">Belum ada order awal.</p>
  <?php endif; ?>

  <?php if(!empty($customer['foto_surat_jalan']) || !empty($customer['foto_penerimaan'])): ?>
  <hr>
  <h6>Foto Dokumen</h6>
  <div class="row g-3">
    <?php if(!empty($customer['foto_surat_jalan'])): ?>
    <div class="col-6">
      <div class="mb-1"><strong>Nota</strong></div>
      <a href="<?= base_url('uploads/suratjalan/'.$customer['foto_surat_jalan']) ?>" target="_blank" class="d-block border rounded p-2 text-center">
        <img src="<?= base_url('uploads/suratjalan/'.$customer['foto_surat_jalan']) ?>" alt="Foto Nota" class="img-fluid" style="max-height:200px; object-fit:contain;"/>
      </a>
    </div>
    <?php endif; ?>
    <?php if(!empty($customer['foto_penerimaan'])): ?>
    <div class="col-6">
      <div class="mb-1"><strong>Foto Penerimaan</strong></div>
      <a href="<?= base_url('uploads/penerimaan/'.$customer['foto_penerimaan']) ?>" target="_blank" class="d-block border rounded p-2 text-center">
        <img src="<?= base_url('uploads/penerimaan/'.$customer['foto_penerimaan']) ?>" alt="Foto Penerimaan" class="img-fluid" style="max-height:200px; object-fit:contain;"/>
      </a>
    </div>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<div class="modal-footer">
  <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
