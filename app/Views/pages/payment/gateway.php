<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'payment', 'segment2' => 'gateway', 'segment3' => 'Pembayaran']) ?>

<?php if ($amount <= 0): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> Invoice ini sudah <strong>LUNAS</strong>.
    </div>
    <a href="<?= base_url('/invoice') ?>" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Invoice</a>
<?php else: ?>

    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">
                        <?php if ($method === 'qris'): ?>
                            <i class="bi bi-qr-code"></i> Pembayaran QRIS
                        <?php elseif ($method === 'va'): ?>
                            <i class="bi bi-bank"></i> Virtual Account
                        <?php else: ?>
                            <i class="bi bi-wallet2"></i> E-Wallet
                        <?php endif; ?>
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Invoice Info -->
                    <div class="text-center mb-3">
                        <small class="text-muted"><?= esc($invoice['invoice_no']) ?></small>
                    </div>

                    <!-- Amount Card -->
                    <div class="alert alert-primary text-center mb-4">
                        <small class="text-muted">Total yang harus dibayar</small>
                        <h2 class="mb-0">Rp <?= number_format($amount, 0, ',', '.') ?></h2>
                    </div>

                    <!-- Timer -->
                    <div class="alert alert-warning text-center mb-4">
                        <small>Selesaikan pembayaran dalam</small>
                        <h4 class="mb-0" id="timer">15:00</h4>
                    </div>

                    <?php if ($method === 'qris'): ?>
                        <!-- QRIS Content -->
                        <?php
                        $qrData = 'ESHOKITA-PAY-' . $invoice['id_invoice'] . '-' . $amount . '-' . time();
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=' . urlencode($qrData);
                        ?>
                        <div class="text-center mb-4">
                            <div class="border rounded p-3 d-inline-block bg-white">
                                <img src="<?= $qrUrl ?>" alt="QR Code" width="180" height="180" style="display: block;">
                            </div>
                            <p class="text-muted small mt-2">Scan menggunakan aplikasi e-wallet atau mobile banking</p>
                        </div>

                        <div class="alert alert-light">
                            <strong>Cara Pembayaran:</strong>
                            <ol class="mb-0 small">
                                <li>Buka aplikasi e-wallet atau mobile banking</li>
                                <li>Pilih menu "Scan" atau "QRIS"</li>
                                <li>Scan kode QR di atas</li>
                                <li>Konfirmasi dan selesaikan pembayaran</li>
                            </ol>
                        </div>

                    <?php elseif ($method === 'va'): ?>
                        <!-- Virtual Account Content -->
                        <div class="mb-4">
                            <div class="btn-group w-100 mb-3" role="group">
                                <button type="button" class="btn btn-outline-primary va-bank active"
                                    data-bank="bca">BCA</button>
                                <button type="button" class="btn btn-outline-primary va-bank" data-bank="bni">BNI</button>
                                <button type="button" class="btn btn-outline-primary va-bank"
                                    data-bank="mandiri">Mandiri</button>
                                <button type="button" class="btn btn-outline-primary va-bank" data-bank="bri">BRI</button>
                            </div>

                            <div class="border rounded p-3 text-center bg-light">
                                <small class="text-muted">Nomor Virtual Account</small>
                                <h4 class="font-monospace mb-2" id="va-number">8277
                                    0<?= str_pad($invoice['id_invoice'], 10, '0', STR_PAD_LEFT) ?></h4>
                                <button type="button" class="btn btn-sm btn-primary" id="btn-copy-va">
                                    <i class="bi bi-clipboard"></i> Salin
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-light">
                            <strong>Cara Pembayaran:</strong>
                            <ol class="mb-0 small">
                                <li>Buka aplikasi mobile banking atau ATM</li>
                                <li>Pilih menu Transfer > Virtual Account</li>
                                <li>Masukkan nomor VA di atas</li>
                                <li>Periksa detail dan konfirmasi pembayaran</li>
                            </ol>
                        </div>

                    <?php elseif ($method === 'ewallet'): ?>
                        <!-- E-Wallet Content -->
                        <div class="mb-4">
                            <div class="list-group ewallet-list">
                                <label class="list-group-item list-group-item-action d-flex align-items-center">
                                    <input type="radio" name="ewallet" value="gopay" class="d-none" checked>
                                    <img src="<?= base_url('assets/image/gopay.webp') ?>" alt="GoPay" height="28" class="me-3">
                                    <span>GoPay</span>
                                    <i class="bi bi-check-circle-fill text-success ms-auto check-icon"></i>
                                </label>
                                <label class="list-group-item list-group-item-action d-flex align-items-center">
                                    <input type="radio" name="ewallet" value="ovo" class="d-none">
                                    <img src="<?= base_url('assets/image/ovo.png') ?>" alt="OVO" height="28" class="me-3">
                                    <span>OVO</span>
                                    <i class="bi bi-check-circle-fill text-success ms-auto check-icon"></i>
                                </label>
                                <label class="list-group-item list-group-item-action d-flex align-items-center">
                                    <input type="radio" name="ewallet" value="dana" class="d-none">
                                    <img src="<?= base_url('assets/image/dana.jpg') ?>" alt="DANA" height="28" class="me-3">
                                    <span>DANA</span>
                                    <i class="bi bi-check-circle-fill text-success ms-auto check-icon"></i>
                                </label>
                                <label class="list-group-item list-group-item-action d-flex align-items-center">
                                    <input type="radio" name="ewallet" value="shopeepay" class="d-none">
                                    <img src="<?= base_url('assets/image/shopeepay.png') ?>" alt="ShopeePay" height="28"
                                        class="me-3">
                                    <span>ShopeePay</span>
                                    <i class="bi bi-check-circle-fill text-success ms-auto check-icon"></i>
                                </label>
                                <label class="list-group-item list-group-item-action d-flex align-items-center">
                                    <input type="radio" name="ewallet" value="linkaja" class="d-none">
                                    <img src="<?= base_url('assets/image/linkaja.png') ?>" alt="LinkAja" height="28"
                                        class="me-3">
                                    <span>LinkAja</span>
                                    <i class="bi bi-check-circle-fill text-success ms-auto check-icon"></i>
                                </label>
                            </div>
                        </div>

                        <style>
                            .ewallet-list .list-group-item {
                                cursor: pointer;
                            }

                            .ewallet-list .check-icon {
                                display: none;
                            }

                            .ewallet-list input:checked~.check-icon {
                                display: inline;
                            }

                            .ewallet-list .list-group-item:has(input:checked) {
                                background-color: #e8f5e9;
                                border-color: #4caf50;
                            }
                        </style>

                        <div class="alert alert-light">
                            <strong>Cara Pembayaran:</strong>
                            <ol class="mb-0 small">
                                <li>Pilih e-wallet yang ingin digunakan</li>
                                <li>Klik tombol "Bayar Sekarang" di bawah</li>
                                <li>Konfirmasi pembayaran di aplikasi e-wallet</li>
                            </ol>
                        </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <button type="button" class="btn btn-success w-100 mb-2" id="btn-pay">
                        <i class="bi bi-check-circle"></i> Bayar Sekarang
                    </button>
                    <a href="<?= base_url('/payment/create/' . $invoice['id_invoice']) ?>"
                        class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left"></i> Ganti Metode Pembayaran
                    </a>

                    <!-- Status message -->
                    <div id="status-message" class="mt-3" style="display: none;"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Timer countdown
            let timeLeft = 15 * 60;
            const timerEl = document.getElementById('timer');
            const timerInterval = setInterval(function () {
                timeLeft--;
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerEl.textContent = String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    Swal.fire({
                        icon: 'warning',
                        title: 'Waktu Habis',
                        text: 'Sesi pembayaran telah berakhir. Silakan coba lagi.',
                        confirmButtonText: 'OK'
                    }).then(function () {
                        window.location.href = '<?= base_url('/payment/create/' . $invoice['id_invoice']) ?>';
                    });
                }
            }, 1000);

            // VA Bank tabs
            const bankTabs = document.querySelectorAll('.va-bank');
            const vaPrefixes = { bca: '8277', bni: '8810', mandiri: '8900', bri: '8880' };
            bankTabs.forEach(function (tab) {
                tab.addEventListener('click', function () {
                    bankTabs.forEach(function (t) { t.classList.remove('active'); });
                    this.classList.add('active');
                    const bank = this.dataset.bank;
                    document.getElementById('va-number').textContent =
                        vaPrefixes[bank] + ' 0<?= str_pad($invoice['id_invoice'], 10, '0', STR_PAD_LEFT) ?>';
                });
            });

            // Copy VA
            const btnCopyVa = document.getElementById('btn-copy-va');
            if (btnCopyVa) {
                btnCopyVa.addEventListener('click', function () {
                    const vaNumber = document.getElementById('va-number').textContent.replace(/\s/g, '');
                    navigator.clipboard.writeText(vaNumber).then(function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Nomor VA berhasil disalin',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }).catch(function () {
                        Swal.fire({
                            icon: 'info',
                            title: 'Salin Manual',
                            text: vaNumber,
                            confirmButtonText: 'OK'
                        });
                    });
                });
            }

            // Pay button
            const btnPay = document.getElementById('btn-pay');
            const statusMessage = document.getElementById('status-message');

            btnPay.addEventListener('click', async function () {
                btnPay.disabled = true;
                btnPay.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Memproses...';

                statusMessage.style.display = 'block';
                statusMessage.className = 'mt-3 alert alert-info';
                statusMessage.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses pembayaran...';

                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                formData.append('method', '<?= $method ?>');
                formData.append('amount', '<?= $amount ?>');
                formData.append('paid_at', new Date().toISOString().slice(0, 19).replace('T', ' '));
                formData.append('note', 'Pembayaran via <?= strtoupper($method) ?>');

                try {
                    const response = await fetch('<?= base_url('/payment/process/' . $invoice['id_invoice']) ?>', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const result = await response.json();
                    clearInterval(timerInterval);

                    if (result.success) {
                        statusMessage.className = 'mt-3 alert alert-success';
                        statusMessage.innerHTML = '<i class="bi bi-check-circle-fill"></i> <strong>Pembayaran Berhasil!</strong>';

                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil!',
                            text: 'Pembayaran sebesar Rp <?= number_format($amount, 0, ',', '.') ?> telah diterima.',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.href = '<?= base_url('/invoice') ?>';
                        });
                    } else {
                        // Jika gagal, langsung redirect tanpa alert
                        statusMessage.className = 'mt-3 alert alert-warning';
                        statusMessage.innerHTML = '<i class="bi bi-exclamation-triangle"></i> ' + (result.message || 'Silakan coba lagi');
                        btnPay.disabled = false;
                        btnPay.innerHTML = '<i class="bi bi-check-circle"></i> Bayar Sekarang';
                    }
                } catch (err) {
                    // Jika network error, tampilkan pesan di status saja
                    statusMessage.className = 'mt-3 alert alert-warning';
                    statusMessage.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Koneksi bermasalah, silakan coba lagi';
                    btnPay.disabled = false;
                    btnPay.innerHTML = '<i class="bi bi-check-circle"></i> Bayar Sekarang';
                }
            });
        });
    </script>

<?php endif; ?>

<?= $this->endSection() ?>