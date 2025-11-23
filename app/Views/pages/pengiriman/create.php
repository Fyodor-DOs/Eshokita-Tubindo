<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'pengiriman', 'segment2' => 'create']) ?>

<form method="post" action="<?= site_url('pengiriman/create') ?>">
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>"
                            class="form-control">
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label for="supir" class="form-label">Supir <span class="text-danger">*</span></label>
                        <input type="text" name="supir" id="supir" class="form-control" placeholder="Nama supir" required>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="kenek" class="form-label">Kenek</label>
                        <input type="text" name="kenek" id="kenek" class="form-control" placeholder="Nama kenek (opsional)">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="plat_kendaraan" class="form-label">Plat Kendaraan <span class="text-danger">*</span></label>
                        <input type="text" name="plat_kendaraan" id="plat_kendaraan" class="form-control" placeholder="B 1234 XYZ" required>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nama_wilayah" class="form-label">Rute Pengiriman <span class="text-danger">*</span></label>
                        <input type="hidden" name="kode_rute" id="kode_rute" required>
                        <input type="search" name="nama_wilayah" id="nama_wilayah" class="form-select"
                            placeholder="Ketik nama rute untuk mencari..." autocomplete="off" required>

                        <?php if (!empty($rutes)): ?>
                        <div id="list_rute"
                            class="d-none border my-1 rounded overflow-y-scroll position-absolute z-1 bg-white shadow"
                            style="max-height: 200px; width: 90%;">
                            <ul class="list-group list-group-flush p-2">
                                <?php foreach ($rutes as $rute) : ?>
                                <li class="list-group-item list-group-item-action" 
                                    data-id="<?= $rute['kode_rute'] ?>"
                                    data-nama="<?= $rute['nama_wilayah'] ?>"
                                    style="cursor: pointer;">
                                    <strong><?= $rute['kode_rute'] ?></strong> - <?= $rute['nama_wilayah'] ?>
                                    <br><small class="text-muted">Customer di rute ini akan mendapat pengiriman</small>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif ?>
                        <small class="text-muted">Pilih rute, lalu pilih invoice yang akan diantar di bawah</small>
                    </div>
                </div>

                <!-- Pembayaran dihilangkan: ditangani di menu Payment/Invoice -->


                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> <strong>Catatan Penting:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Pengiriman <strong>tidak bisa antar rute</strong> - hanya invoice dengan rute yang sama yang bisa dikirim bersamaan</li>
                            <li>Pilih <strong>Rute Pengiriman</strong> terlebih dahulu, maka invoice akan otomatis terfilter</li>
                            <li>Hanya invoice berstatus <strong>paid/lunas</strong> yang bisa dikirim</li>
                            <li>Semua invoice yang dipilih akan digabung ke <strong>1 BON</strong> pengiriman</li>
                        </ul>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold">Invoice Siap Dikirim</label>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small">
                            <i class="bi bi-funnel"></i> 
                            <span id="filterInfo">Menampilkan semua invoice yang belum dikirim. Pilih rute untuk filter otomatis.</span>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" id="btnSelectAllRoute" class="btn btn-sm btn-secondary">Pilih Semua</button>
                            <button type="button" id="btnClearSelection" class="btn btn-sm btn-outline-secondary">Bersihkan</button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm" id="table-invoice-candidates">
                            <thead>
                                <tr>
                                    <th style="width: 40px;" class="text-center">#</th>
                                    <th>Invoice</th>
                                    <th>Tanggal</th>
                                    <th>Customer</th>
                                    <th>Rute</th>
                                    <th class="text-end">Jumlah</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach(($invoiceCandidates ?? []) as $row): ?>
                                <tr data-koderute="<?= esc($row['kode_rute']) ?>">
                                    <td class="text-center">
                                        <input type="checkbox" name="invoice_ids[]" value="<?= (int)$row['id_invoice'] ?>" class="chk-invoice" />
                                    </td>
                                    <td><strong><?= esc($row['invoice_no']) ?></strong></td>
                                    <td><?= date('d M Y', strtotime($row['issue_date'])) ?></td>
                                    <td><?= esc($row['customer_name']) ?></td>
                                    <td><code><?= esc($row['kode_rute']) ?></code></td>
                                    <td class="text-end">Rp <?= number_format((float)$row['amount'],0,',','.') ?></td>
                                    <td>
                                        <?php $badge = $row['status']==='paid'?'success':'warning'; $label = $row['status']==='paid'?'Lunas':'Sebagian'; ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($invoiceCandidates)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada invoice siap kirim</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
$(function() {
    // Proteksi double submit
    $('form').on('submit', function(e){
        var btn = $(this).find('button[type="submit"]');
        btn.prop('disabled', true);
        btn.text('Memproses...');

        // If using AJAX, handle error to re-enable button
        $(this).off('ajaxError.pengiriman').on('ajaxError.pengiriman', function(){
            btn.prop('disabled', false);
            btn.text('Submit');
        });
    });

    // If using AJAX submit, handle error globally
    $(document).ajaxError(function(event, jqxhr, settings, thrownError){
        var btn = $('form').find('button[type="submit"]');
        btn.prop('disabled', false);
        btn.text('Submit');
    });
    var today = new Date().toISOString().split("T")[0];
    $('#tanggal').attr('min', today);

    // Autocomplete untuk rute saja
    initAutoComplete('kode_rute', 'nama_wilayah', 'list_rute');

    // Filter kandidat invoice berdasarkan kode rute yang dipilih
    function applyRouteFilter(){
        var r = $('#kode_rute').val();
        var ruteName = $('#nama_wilayah').val();
        var visibleCount = 0;
        var totalCount = 0;
        
        $('#table-invoice-candidates tbody tr').each(function(){
            // Skip jika baris kosong
            if($(this).find('td').length === 1) return;
            
            totalCount++;
            var kr = $(this).data('koderute');
            
            // Jika rute belum dipilih (kosong), tampilkan semua
            // Jika rute sudah dipilih, hanya tampilkan yang sesuai dengan rute tersebut
            if(!r || r==='' || r===kr){ 
                $(this).show(); 
                visibleCount++;
            }
            else { 
                $(this).hide(); 
                // Uncheck invoice yang disembunyikan
                $(this).find('.chk-invoice').prop('checked', false); 
            }
        });
        
        // Update info filter
        if(r && r!==''){
            $('#filterInfo').html('<strong class="text-primary">Filter aktif:</strong> Menampilkan ' + visibleCount + ' invoice untuk rute <strong>' + r + '</strong> (' + ruteName + ')');
            $('#btnSelectAllRoute').html('<i class="bi bi-check-square"></i> Pilih Semua Rute ' + r);
        } else {
            $('#filterInfo').html('Menampilkan <strong>semua ' + visibleCount + ' invoice</strong> yang belum dikirim. <span class="text-warning">Pilih rute untuk filter otomatis.</span>');
            $('#btnSelectAllRoute').html('<i class="bi bi-check-square"></i> Pilih Semua');
        }
        
        // Tampilkan peringatan jika tidak ada invoice untuk rute yang dipilih
        if(r && visibleCount === 0){
            $('#filterInfo').html('<strong class="text-danger">Tidak ada invoice siap kirim untuk rute ' + r + '</strong>');
        }
    }
    
    // Awalnya tampilkan semua invoice (belum ada filter)
    applyRouteFilter();
    
    // Apply filter setiap kali rute berubah
    $('#kode_rute').on('change', function(){
        applyRouteFilter();
        // Scroll ke tabel invoice untuk memudahkan user melihat hasil filter
        $('html, body').animate({
            scrollTop: $('#table-invoice-candidates').offset().top - 100
        }, 500);
    });

    $('#btnSelectAllRoute').on('click', function(){
        var r = $('#kode_rute').val();
        $('#table-invoice-candidates tbody tr').each(function(){
            // Pilih semua yang visible saja
            if($(this).is(':visible')){
                $(this).find('.chk-invoice').prop('checked', true);
            }
        });
    });
    $('#btnClearSelection').on('click', function(){
        $('.chk-invoice').prop('checked', false);
    });
});
</script>
<?= $this->endSection() ?>