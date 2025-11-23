<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'customer', 'segment2' => 'create']) ?>
<form id="formCreateCustomer" method="post" action="<?= site_url('/customer/create') ?>">
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="nama_wilayah">Rute</label>
                        <input type="hidden" name="kode_rute" id="kode_rute">
                        <input type="search" name="nama_wilayah" id="nama_wilayah" class="form-select"
                            placeholder="Ketik Rute" autocomplete="off">

                        <?php if (!empty($rutes)): ?>
                            <div id="list_rute"
                                class="d-none border my-1 rounded overflow-y-scroll position-absolute z-1 bg-white shadow"
                                style="max-height: 150px;">
                                <ul class="list-group list-group-flush p-2">
                                    <?php foreach ($rutes as $rute) : ?>
                                        <li class="list-group-item list-group-item-action" data-id="<?= $rute['kode_rute'] ?>"
                                            style="cursor: pointer;">
                                            <?= $rute['kode_rute'] ?> - <?= $rute['nama_wilayah'] ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif ?>

                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="nama">Nama Customer</label>
                        <input type="text" name="nama" id="nama" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control">
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="telepon">Telepon</label>
                        <input type="text" name="telepon" id="telepon" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="provinsi">Provinsi</label>
                        <input type="text" name="provinsi" id="provinsi" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kota">Kota/Kabupaten</label>
                        <input type="text" name="kabupaten" id="kabupaten" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kecamatan">Kecamatan</label>
                        <input type="text" name="kecamatan" id="kecamatan" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kelurahan">Kelurahan</label>
                        <input type="text" name="kelurahan" id="kelurahan" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label" for="kodepos">Kode Pos</label>
                        <input type="number" name="kodepos" id="kodepos" class="form-control" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="alamat">Alamat</label>
                        <textarea name="alamat" id="alamat" rows="9" class="form-control" required></textarea>
                    </div>
                </div>

                <div class="col-md-6">
                    <p class="p-0 m-0 mb-2"><strong>Pemesanan Es (Order Awal)</strong></p>
                    <?php if (!empty($products)): ?>
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
                                    <?php foreach ($products as $product): ?>
                                        <tr data-id="<?= $product['id_product'] ?>" data-price="<?= (float)$product['price'] ?>" data-stock="<?= (int)($product['qty'] ?? 0) ?>">
                                            <td>
                                                <div class="fw-semibold"><?= esc($product['name']) ?></div>
                                                <div class="text-muted small">SKU: <?= esc($product['sku'] ?? '-') ?></div>
                                            </td>
                                            <td class="text-end">Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                            <td>
                                                <input type="number" class="form-control qty" min="0" placeholder="0">
                                                <div class="form-text sisa-note">Stok: <?= (int)($product['qty'] ?? 0) ?> | Sisa: <?= (int)($product['qty'] ?? 0) ?></div>
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
                        <input type="hidden" name="order_items" id="order_items">
                        
                    <?php else: ?>
                        <p class="text-muted">Tidak ada produk aktif. Silakan tambahkan produk terlebih dahulu.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary col-12 col-md-3">Submit</button>
            </div>
        </div>

    </div>
</form>

<script>
    $(document).ready(function() {
        initAutoComplete('kode_rute', 'nama_wilayah', 'list_rute');

        function formatR(n){ return 'Rp ' + (n||0).toLocaleString('id-ID'); }
        function recalc(){
            let total = 0; const items=[];
            $("tbody tr[data-id]").each(function(){
                const id = $(this).data('id');
                const price = parseFloat($(this).data('price'))||0;
                const stock = parseFloat($(this).data('stock'))||0;
                const qty = parseFloat($(this).find('.qty').val())||0;
                const sub = price*qty; total += sub;
                $(this).find('.subtotal').text(formatR(sub));
                if(qty>0){ items.push({id_product:id, qty:qty, price:price, subtotal:sub}); }
                const sisa = Math.max(0, stock - qty);
                $(this).find('.sisa-note').text('Stok: '+(stock||0)+' | Sisa: '+sisa);
            });
            $('#grandTotal').text(formatR(total));
            $('#order_items').val(JSON.stringify(items));
        }
        $(document).on('input','.qty',recalc);
        recalc();

        // Intercept submit with proper error handling
        let __createProcessing = false;
        document.getElementById('formCreateCustomer').addEventListener('submit', async function(e){
            e.preventDefault();
            e.stopImmediatePropagation(); // Prevent global form handler from intercepting
            if (__createProcessing) {
                console.log('Submit already in progress, ignoring...');
                return; // prevent double submit
            }
            __createProcessing = true;
            try { recalc(); } catch(_) {}
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : '';
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
            }
            const fd = new FormData(this); // includes CSRF
            try {
                const res = await fetch(this.action, { method:'POST', headers:{ 'X-Requested-With':'XMLHttpRequest', 'Accept':'application/json' }, body: fd });
                const ct = (res.headers.get('content-type')||'').toLowerCase();
                if (ct.includes('application/json')){
                    const j = await res.json();
                    if (j && j.success){
                        // Success - save message and redirect like order_again
                        try {
                            sessionStorage.setItem('success_message', j.message || 'Customer berhasil dibuat');
                        } catch(_) {}
                        if (submitBtn) submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Berhasil! Mengalihkan...';
                        if (j.url) { 
                            window.location.href = j.url;
                            return; 
                        }
                        window.location.href = '<?= base_url('customer') ?>';
                        return;
                    } else {
                        // Error - show error alert like order_again
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: j.message || 'Terjadi kesalahan saat menyimpan customer'
                        });
                        __createProcessing = false;
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        }
                    }
                } else {
                    // Non-JSON response: follow it
                    window.location.href = res.url || window.location.href;
                    return;
                }
            } catch (err) {
                // Network error - show error like order_again
                console?.error && console.error('Submit error', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Terjadi kesalahan koneksi'
                });
                __createProcessing = false;
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        });
    });
</script>

<?= $this->endSection() ?>