<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'payment', 'segment2' => $invoice['invoice_no']]) ?>
<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Riwayat Pembayaran - <?= esc($invoice['invoice_no']) ?></h5>
      <div class="btn-group">
        <a class="btn btn-info" href="<?= base_url('/payment/detail/' . $invoice['id_invoice']) ?>">
          <i class="bi bi-eye"></i> Lihat Detail
        </a>
        <a class="btn btn-success" href="<?= base_url('/payment/create/' . $invoice['id_invoice']) ?>">
          <i class="bi bi-plus-circle"></i> Tambah Pembayaran
        </a>
        <a class="btn btn-secondary" href="<?= base_url('/invoice') ?>">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>
      </div>
    </div>

    <?php if (empty($payments)): ?>
      <div class="alert alert-warning">
        <i class="bi bi-exclamation-triangle"></i> Belum ada pembayaran untuk invoice ini.
      </div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-hover" id="payment-history">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal Bayar</th>
              <th>Metode</th>
              <th class="text-end">Jumlah</th>
              <th>Foto Bukti</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $key => $p): ?>
              <tr>
                <td><?= $key + 1 ?></td>
                <td><?= date('d M Y H:i', strtotime($p['paid_at'])) ?></td>
                <td>
                  <?php
                  $methodBadge = [
                    'cash' => 'success',
                    'kredit' => 'warning',
                    'transfer' => 'primary',
                    'qris' => 'danger',
                    'va' => 'info',
                    'ewallet' => 'warning',
                    'other' => 'secondary'
                  ];
                  $methodLabel = [
                    'cash' => 'Tunai',
                    'kredit' => 'Kredit',
                    'transfer' => 'Transfer',
                    'qris' => 'QRIS',
                    'va' => 'Virtual Account',
                    'ewallet' => 'E-Wallet',
                    'other' => 'Lainnya'
                  ];
                  ?>
                  <span class="badge bg-<?= $methodBadge[$p['method']] ?? 'secondary' ?>">
                    <?= $methodLabel[$p['method']] ?? ucfirst($p['method']) ?>
                  </span>
                </td>
                <td class="text-end"><strong>Rp <?= number_format((float) $p['amount'], 0, ',', '.') ?></strong></td>
                <td>
                  <?php if (!empty($p['invoice_photo'])): ?>
                    <a href="<?= base_url('uploads/invoices/' . $p['invoice_photo']) ?>" target="_blank"
                      class="btn btn-sm btn-info">
                      <i class="bi bi-image"></i> Lihat Foto
                    </a>
                  <?php else: ?>
                    <span class="text-muted">-</span>
                  <?php endif; ?>
                </td>
                <td><?= esc($p['note']) ?: '-' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
<?= $this->endSection() ?>