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
          <thead>
            <tr>
              <th>Produk</th>
              <th class="col-2">Harga</th>
              <th class="col-2">Qty</th>
              <th class="col-2">Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $p):
              $stock = (int) ($p['qty'] ?? 0);
              ?>
              <tr data-id="<?= $p['id_product'] ?>" data-stock="<?= $stock ?>">
                <td>
                  <div class="fw-semibold"><?= esc($p['name']) ?></div>
                  <div class="text-muted small">SKU: <?= esc($p['sku'] ?? '-') ?></div>
                </td>
                <td>
                  <input type="number" step="0.01" class="form-control price" value="<?= (float) $p['customer_price'] ?>">
                </td>
                <td>
                  <input type="number" min="0" max="<?= $stock ?>" class="form-control qty" placeholder="0" <?= $stock <= 0 ? 'disabled' : '' ?>>
                  <div class="form-text sisa-note <?= $stock <= 0 ? 'text-danger fw-bold' : '' ?>">
                    <?php if ($stock <= 0): ?>
                      <span class="text-danger">Stok Habis</span>
                    <?php else: ?>
                      Stok: <?= $stock ?> | Sisa: <?= $stock ?>
                    <?php endif; ?>
                  </div>
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
        <a href="<?= base_url('customer/detail/' . $customer['id_customer']) ?>" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
  function formatRupiah(n) { return 'Rp ' + (n || 0).toLocaleString('id-ID'); }
  function recalc() {
    let total = 0;
    document.querySelectorAll('tbody tr').forEach(tr => {
      const price = parseFloat(tr.querySelector('.price').value) || 0;
      const stock = parseInt(tr.getAttribute('data-stock')) || 0;
      let qty = parseFloat(tr.querySelector('.qty').value) || 0;
      // Clamp qty to stock
      if (qty > stock && stock > 0) {
        qty = stock;
        tr.querySelector('.qty').value = stock;
      }
      const subtotal = price * qty; total += subtotal;
      tr.querySelector('.subtotal').value = formatRupiah(subtotal);
      // Update sisa note
      const note = tr.querySelector('.sisa-note');
      if (note) {
        if (stock <= 0) {
          note.innerHTML = '<span class="text-danger">Stok Habis</span>';
        } else {
          const sisa = Math.max(0, stock - qty);
          note.textContent = 'Stok: ' + stock + ' | Sisa: ' + sisa;
        }
      }
    });
    document.getElementById('grandTotal').textContent = formatRupiah(total);
  }
  document.querySelectorAll('.price,.qty').forEach(inp => inp.addEventListener('input', recalc));
  recalc();

  let __orderProcessing = false;
  document.getElementById('formOrder').addEventListener('submit', async (e) => {
    e.preventDefault();
    if (__orderProcessing) return; // Prevent double submit
    __orderProcessing = true;

    const submitBtn = e.target.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    const items = [];
    let hasStockError = false;
    let stockErrorMsg = '';
    document.querySelectorAll('tbody tr').forEach(tr => {
      const id = tr.getAttribute('data-id');
      const qty = parseInt(tr.querySelector('.qty').value || '0');
      const price = parseFloat(tr.querySelector('.price').value || '0');
      const stock = parseInt(tr.getAttribute('data-stock') || '0');
      if (qty > 0) {
        if (qty > stock) {
          hasStockError = true;
          const productName = tr.querySelector('.fw-semibold')?.textContent || 'Produk';
          stockErrorMsg += productName + ': qty ' + qty + ' melebihi stok ' + stock + '\n';
        }
        items.push({ id_product: id, qty: qty, price: price });
      }
    });
    if (items.length === 0) {
      alert('Isi minimal satu produk.');
      __orderProcessing = false;
      if (submitBtn) submitBtn.disabled = false;
      return;
    }
    if (hasStockError) {
      alert('Stok Tidak Cukup:\n' + stockErrorMsg.trim());
      __orderProcessing = false;
      if (submitBtn) submitBtn.disabled = false;
      return;
    }

    const fd = new FormData();
    fd.append('items', JSON.stringify(items));
    const csrfToken = document.querySelector('input[name="<?= csrf_token() ?>"]')?.value;
    if (csrfToken) fd.append('<?= csrf_token() ?>', csrfToken);

    try {
      const res = await fetch('', { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, body: fd });
      const j = await res.json();
      if (j.success) {
        try { if (j.message) sessionStorage.setItem('success_message', j.message); } catch (_) { }
        location.href = j.url;
      } else {
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