<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('title') ?>Buat Nota - PT Eshokita<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'nota', 'segment2' => 'create']) ?>

<div class="alert alert-info">
    <i class="bi bi-info-circle"></i>
    <strong>Cara Kerja Nota:</strong>
    <ul class="mb-0 mt-2">
        <li>Pilih No. BON Pengiriman yang belum dibuat notanya</li>
        <li>Sistem akan otomatis membuat nota terpisah untuk setiap customer berbeda dalam 1 BON tersebut</li>
        <li>Setiap customer akan mendapatkan halaman nota sendiri dengan produk mereka masing-masing</li>
        <li>Anda bisa download/print nota per customer secara terpisah</li>
    </ul>
</div>

<?php if (empty($pengirimanList)): ?>
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle"></i>
    Semua pengiriman sudah dibuatkan nota. Silakan buat pengiriman baru terlebih dahulu.
</div>
<?php else: ?>
<form method="post" action="<?= site_url('nota/create') ?>" id="formNota">
    <?= csrf_field() ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="no_bon" class="form-label">No. BON Pengiriman <span class="text-danger">*</span></label>
                        <select name="id_pengiriman" id="id_pengiriman" class="form-select" required>
                            <option value="">Pilih No. BON</option>
                            <?php foreach ($pengirimanList ?? [] as $p): ?>
                                <option value="<?= $p['id_pengiriman'] ?>" data-kode-rute="<?= esc($p['kode_rute']) ?>" data-tanggal="<?= esc($p['tanggal']) ?>">
                                    <?= esc($p['no_bon']) ?> - <?= esc($p['kode_rute']) ?> (<?= date('d M Y', strtotime($p['tanggal'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Hanya menampilkan BON yang belum dibuat notanya</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal Nota <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal" id="tanggal" value="<?= date('Y-m-d') ?>" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-12">
                    <div id="info-customers" class="alert alert-light" style="display:none;">
                        <strong>Preview Customer:</strong>
                        <div id="customer-list" class="mt-2"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <a href="<?= base_url('/nota') ?>" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Buat Nota</button>
            </div>
        </div>
    </div>
</form>
<?php endif; ?>

<script>
$(function() {
    $('#id_pengiriman').on('change', async function(){
        const idPengiriman = $(this).val();
        if (!idPengiriman) {
            $('#info-customers').hide();
            return;
        }
        
        // Load invoice/customer info for this pengiriman
        try {
            const response = await fetch('<?= base_url('pengiriman/invoices') ?>/' + idPengiriman);
            const data = await response.json();
            
            if (data.success && data.data && data.data.length > 0) {
                let html = '<p class=\"mb-2\">BON ini memiliki <strong>' + data.data.length + ' invoice</strong></p>';
                html += '<p class="text-muted small">Sistem akan membuat nota terpisah untuk setiap customer berbeda dalam BON ini</p>';
                
                $('#customer-list').html(html);
                $('#info-customers').show();
            } else {
                $('#customer-list').html('<p class=\"text-danger\">Tidak ada invoice untuk BON ini</p>');
                $('#info-customers').show();
            }
        } catch (error) {
            console.error('Error loading customers:', error);
            $('#info-customers').hide();
        }
    });
    
    $('#formSuratJalan').on('submit', function(e) {
        const idPengiriman = $('#id_pengiriman').val();
        if (!idPengiriman) {
            e.preventDefault();
            alert('Pilih No. BON terlebih dahulu');
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
