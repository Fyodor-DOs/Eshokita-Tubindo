<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'payment', 'segment2' => 'create', 'segment3' => $invoice['invoice_no']]) ?>

<?php
$total = (float) $invoice['amount'];
$db = \Config\Database::connect();
$paidRow = $db->table('payment')->where('id_invoice', $invoice['id_invoice'])->selectSum('amount', 'total_paid')->get()->getRowArray();
$paid = (float) ($paidRow['total_paid'] ?? 0);
$outstanding = max(0, $total - $paid);
?>

<?php if ($outstanding <= 0): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> Invoice ini sudah <strong>LUNAS</strong>. Tidak perlu pembayaran lagi.
    </div>
    <a href="<?= base_url('/invoice') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Invoice</a>
<?php else: ?>

    <div class="row">
        <!-- Left Column: Summary -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-receipt"></i> Ringkasan Pembayaran</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted">No. Invoice</td>
                            <td><strong><?= esc($invoice['invoice_no']) ?></strong></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Tagihan</td>
                            <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sudah Dibayar</td>
                            <td class="text-success">Rp <?= number_format($paid, 0, ',', '.') ?></td>
                        </tr>
                        <tr class="border-top">
                            <td class="text-muted"><strong>Sisa Tagihan</strong></td>
                            <td class="text-danger"><strong>Rp <?= number_format($outstanding, 0, ',', '.') ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Payment Form -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-credit-card"></i> Pilih Metode Pembayaran</h5>
                </div>
                <div class="card-body">
                    <!-- Payment Methods -->
                    <div class="row g-3 mb-4">
                        <!-- Cash -->
                        <div class="col-md-6">
                            <div class="card h-100 payment-card" data-method="cash">
                                <div class="card-body text-center">
                                    <i class="bi bi-cash-stack text-success" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">Cash (Tunai)</h6>
                                    <small class="text-muted">Pembayaran langsung</small>
                                </div>
                            </div>
                        </div>

                        <!-- QRIS -->
                        <div class="col-md-6">
                            <div class="card h-100 payment-card" data-method="qris">
                                <div class="card-body text-center">
                                    <i class="bi bi-qr-code text-danger" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">QRIS</h6>
                                    <small class="text-muted">Scan untuk bayar</small>
                                </div>
                            </div>
                        </div>

                        <!-- Virtual Account -->
                        <div class="col-md-6">
                            <div class="card h-100 payment-card" data-method="va">
                                <div class="card-body text-center">
                                    <i class="bi bi-bank text-primary" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">Virtual Account</h6>
                                    <small class="text-muted">BCA, BNI, Mandiri, BRI</small>
                                </div>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div class="col-md-6">
                            <div class="card h-100 payment-card" data-method="ewallet">
                                <div class="card-body text-center">
                                    <i class="bi bi-wallet2 text-warning" style="font-size: 2rem;"></i>
                                    <h6 class="mt-2 mb-1">E-Wallet</h6>
                                    <small class="text-muted">GoPay, ShopeePay, LinkAja</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cash Form Section (Hidden by default) -->
                    <div id="cash-section" style="display: none;">
                        <form id="cash-form" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <input type="hidden" name="paid_at" value="<?= date('Y-m-d H:i:s') ?>">
                            <input type="hidden" name="amount" value="<?= $outstanding ?>">
                            <input type="hidden" name="method" value="cash">

                            <div class="border rounded p-3 mb-3">
                                <h6 class="mb-3"><i class="bi bi-camera"></i> Upload Bukti Pembayaran</h6>

                                <div class="mb-3">
                                    <label class="form-label">Foto Bukti Pembayaran <span
                                            class="text-danger">*</span></label>
                                    <input type="file" name="invoice_photo" id="invoice-photo" class="form-control"
                                        accept="image/*" required>
                                    <small class="text-muted">JPG, PNG (Max 5MB)</small>
                                </div>

                                <div id="preview-container" class="mb-3" style="display: none;">
                                    <img id="upload-preview" class="img-thumbnail" style="max-height: 150px;">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Catatan (Opsional)</label>
                                    <textarea name="note" class="form-control" rows="2"
                                        placeholder="Catatan tambahan"></textarea>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" id="btn-cash-submit" class="btn btn-success">
                                    <i class="bi bi-check"></i> Simpan Pembayaran
                                </button>
                                <a href="<?= base_url('/invoice') ?>" class="btn btn-secondary">
                                    <i class="bi bi-x"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Digital Payment Section (Hidden by default) -->
                    <div id="digital-section" style="display: none;">
                        <div class="d-flex gap-2">
                            <a id="btn-gateway" href="#" class="btn btn-primary">
                                <i class="bi bi-arrow-right"></i> Lanjutkan
                            </a>
                            <a href="<?= base_url('/invoice') ?>" class="btn btn-secondary">
                                <i class="bi bi-x"></i> Batal
                            </a>
                        </div>
                    </div>

                    <!-- Initial state - no method selected -->
                    <div id="no-selection" class="text-muted">
                        <i class="bi bi-info-circle"></i> Pilih metode pembayaran di atas untuk melanjutkan.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .payment-card {
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid #dee2e6;
        }

        .payment-card:hover {
            border-color: #0d6efd;
        }

        .payment-card.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var paymentCards = document.querySelectorAll('.payment-card');
            var cashSection = document.getElementById('cash-section');
            var digitalSection = document.getElementById('digital-section');
            var noSelection = document.getElementById('no-selection');
            var btnGateway = document.getElementById('btn-gateway');
            var selectedMethod = null;

            // Handle method selection
            paymentCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    paymentCards.forEach(function (c) { c.classList.remove('selected'); });
                    this.classList.add('selected');

                    selectedMethod = this.dataset.method;

                    if (selectedMethod === 'cash') {
                        cashSection.style.display = 'block';
                        digitalSection.style.display = 'none';
                        noSelection.style.display = 'none';
                    } else {
                        cashSection.style.display = 'none';
                        digitalSection.style.display = 'block';
                        noSelection.style.display = 'none';
                        // Update gateway link - NO form submit, just a regular link
                        btnGateway.href = '<?= base_url('/payment/gateway/' . $invoice['id_invoice']) ?>?method=' + selectedMethod;
                    }
                });
            });

            // File preview
            var invoicePhotoInput = document.getElementById('invoice-photo');
            var previewContainer = document.getElementById('preview-container');
            var uploadPreview = document.getElementById('upload-preview');

            invoicePhotoInput.addEventListener('change', function () {
                if (this.files.length) {
                    var file = this.files[0];
                    if (file.size > 5 * 1024 * 1024) {
                        alert('File terlalu besar! Maksimal 5MB');
                        this.value = '';
                        return;
                    }
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        uploadPreview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Cash form submit
            var cashForm = document.getElementById('cash-form');
            var btnCashSubmit = document.getElementById('btn-cash-submit');

            cashForm.addEventListener('submit', function (e) {
                e.preventDefault();

                btnCashSubmit.disabled = true;
                btnCashSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

                var fd = new FormData(cashForm);

                fetch('<?= base_url('/payment/create/' . $invoice['id_invoice']) ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                })
                    .then(function (r) { return r.json(); })
                    .then(function (j) {
                        if (j.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pembayaran berhasil disimpan',
                                timer: 2000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(function() {
                                window.location.href = '<?= base_url('/invoice') ?>';
                            });
                        } else {
                            throw new Error(j.message || 'Gagal menyimpan pembayaran');
                        }
                    })
                    .catch(function (err) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: err.message
                        });
                        btnCashSubmit.disabled = false;
                        btnCashSubmit.innerHTML = '<i class="bi bi-check"></i> Simpan Pembayaran';
                    });
            });
        });
    </script>

<?php endif; ?>

<?= $this->endSection() ?>