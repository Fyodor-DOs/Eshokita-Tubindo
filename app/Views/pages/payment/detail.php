<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('title') ?>Detail Pembayaran - PT Eshokita<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'invoice', 'segment2' => 'detail-pembayaran']) ?>

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Detail Pembayaran Invoice</h4>
    <div class="btn-group">
      <?php if (!$isPaid): ?>
      <a href="<?= base_url('/payment/create/'.$invoice['id_invoice']) ?>" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Tambah Pembayaran
      </a>
      <?php endif; ?>
      <a href="<?= base_url('/invoice') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
      </a>
    </div>
  </div>

  <div class="row g-3">
    <!-- Invoice Info Card -->
    <div class="col-lg-4">
      <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
          <h6 class="mb-0"><i class="bi bi-receipt"></i> Informasi Invoice</h6>
        </div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr>
              <td class="text-muted" style="width: 40%;">No. Invoice</td>
              <td><strong><?= esc($invoice['invoice_no']) ?></strong></td>
            </tr>
            <tr>
              <td class="text-muted">No. Transaksi</td>
              <td><?= esc($invoice['transaction_no'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">Tanggal Terbit</td>
              <td><?= date('d M Y', strtotime($invoice['issue_date'])) ?></td>
            </tr>
            <tr>
              <td class="text-muted">Jatuh Tempo</td>
              <td><?= date('d M Y', strtotime($invoice['due_date'])) ?></td>
            </tr>
            <tr>
              <td class="text-muted">BON</td>
              <td><?= !empty($invoice['no_bon']) ? '<span class="badge bg-info">'.esc($invoice['no_bon']).'</span>' : '<span class="text-muted">Belum dikirim</span>' ?></td>
            </tr>
            <tr>
              <td class="text-muted">Status</td>
              <td>
                <?php if ($isPaid): ?>
                  <span class="badge bg-success">Lunas</span>
                <?php elseif ($totalPaid > 0): ?>
                  <span class="badge bg-warning">Dibayar Sebagian</span>
                <?php else: ?>
                  <span class="badge bg-danger">Belum Dibayar</span>
                <?php endif; ?>
              </td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Customer Info Card -->
      <div class="card shadow-sm mt-3">
        <div class="card-header bg-info text-white">
          <h6 class="mb-0"><i class="bi bi-person"></i> Informasi Customer</h6>
        </div>
        <div class="card-body">
          <table class="table table-sm table-borderless mb-0">
            <tr>
              <td class="text-muted" style="width: 35%;">Nama</td>
              <td><strong><?= esc($invoice['customer_name'] ?? '-') ?></strong></td>
            </tr>
            <tr>
              <td class="text-muted">Telepon</td>
              <td><?= esc($invoice['telepon'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">Email</td>
              <td><?= esc($invoice['email'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted">Rute</td>
              <td><?= esc($invoice['rute_name'] ?? '-') ?></td>
            </tr>
            <tr>
              <td class="text-muted" colspan="2">Alamat</td>
            </tr>
            <tr>
              <td colspan="2"><?= nl2br(esc($invoice['alamat'] ?? '-')) ?></td>
            </tr>
          </table>
        </div>
      </div>

      <!-- Bukti Penerimaan Card -->
      <div class="card shadow-sm mt-3">
        <div class="card-header bg-success text-white">
          <h6 class="mb-0"><i class="bi bi-check-circle"></i> Bukti Penerimaan Barang</h6>
        </div>
        <div class="card-body text-center">
          <?php if (!empty($invoice['foto_penerimaan'])): ?>
            <div class="mb-2">
              <span class="badge bg-success mb-2">
                <i class="bi bi-check-circle-fill"></i> Sudah Diterima
              </span>
            </div>
            <a href="<?= base_url('uploads/penerimaan/'.$invoice['foto_penerimaan']) ?>" target="_blank">
              <img src="<?= base_url('uploads/penerimaan/'.$invoice['foto_penerimaan']) ?>" 
                   alt="Bukti Penerimaan" 
                   class="img-fluid rounded shadow-sm" 
                   style="max-height: 200px; cursor: pointer;">
            </a>
            <div class="mt-2">
              <a href="<?= base_url('uploads/penerimaan/'.$invoice['foto_penerimaan']) ?>" 
                 target="_blank" 
                 class="btn btn-sm btn-outline-success">
                <i class="bi bi-download"></i> Download Bukti
              </a>
            </div>
          <?php else: ?>
            <div class="text-muted py-4">
              <i class="bi bi-x-circle" style="font-size: 2rem;"></i>
              <p class="mb-0 mt-2">Belum ada bukti penerimaan</p>
              <?php if (!empty($invoice['no_bon'])): ?>
                <?php
                $statusBadge = [
                  'siap' => ['warning', 'Siap Kirim'],
                  'mengirim' => ['primary', 'Dalam Pengiriman'],
                  'diterima' => ['success', 'Sudah Diterima'],
                  'gagal' => ['danger', 'Pengiriman Gagal']
                ];
                $status = $invoice['pengiriman_status'] ?? 'siap';
                [$badgeClass, $statusLabel] = $statusBadge[$status] ?? ['secondary', 'Unknown'];
                ?>
                <small class="text-muted">
                  Status Pengiriman: <span class="badge bg-<?= $badgeClass ?>"><?= $statusLabel ?></span>
                </small>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- Order Items & Payment Details -->
    <div class="col-lg-8">
      <!-- Order Items -->
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-secondary text-white">
          <h6 class="mb-0"><i class="bi bi-box-seam"></i> Item Pesanan</h6>
        </div>
        <div class="card-body">
          <?php if (empty($items)): ?>
            <div class="alert alert-warning mb-0">
              <i class="bi bi-exclamation-triangle"></i> Tidak ada data item pesanan
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Produk</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($items as $idx => $item): ?>
                  <tr>
                    <td><?= $idx + 1 ?></td>
                    <td>
                      <strong><?= esc($item['name'] ?? 'Unknown') ?></strong>
                      <?php if (!empty($item['sku'])): ?>
                        <br><small class="text-muted">SKU: <?= esc($item['sku']) ?></small>
                      <?php endif; ?>
                    </td>
                    <td class="text-center"><?= number_format((float)($item['qty'] ?? 0), 0, ',', '.') ?></td>
                    <td class="text-end">Rp <?= number_format((float)($item['price'] ?? 0), 0, ',', '.') ?></td>
                    <td class="text-end"><strong>Rp <?= number_format((float)($item['subtotal'] ?? 0), 0, ',', '.') ?></strong></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                  <tr>
                    <th colspan="4" class="text-end">Total Invoice:</th>
                    <th class="text-end">Rp <?= number_format((float)$invoice['amount'], 0, ',', '.') ?></th>
                  </tr>
                </tfoot>
              </table>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Payment Summary -->
      <div class="card shadow-sm mb-3">
        <div class="card-header bg-success text-white">
          <h6 class="mb-0"><i class="bi bi-cash-stack"></i> Ringkasan Pembayaran</h6>
        </div>
        <div class="card-body">
          <div class="row text-center">
            <div class="col-md-4">
              <div class="border rounded p-3">
                <div class="text-muted small">Total Invoice</div>
                <h5 class="mb-0">Rp <?= number_format((float)$invoice['amount'], 0, ',', '.') ?></h5>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3 bg-light">
                <div class="text-muted small">Sudah Dibayar</div>
                <h5 class="mb-0 text-success">Rp <?= number_format($totalPaid, 0, ',', '.') ?></h5>
              </div>
            </div>
            <div class="col-md-4">
              <div class="border rounded p-3 <?= $isPaid ? 'bg-success text-white' : 'bg-warning' ?>">
                <div class="small <?= $isPaid ? 'text-white' : 'text-muted' ?>">Sisa Tagihan</div>
                <h5 class="mb-0 <?= $isPaid ? 'text-white' : 'text-danger' ?>">
                  Rp <?= number_format($remaining, 0, ',', '.') ?>
                </h5>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment History -->
      <div class="card shadow-sm">
        <div class="card-header bg-warning">
          <h6 class="mb-0"><i class="bi bi-clock-history"></i> Riwayat Pembayaran</h6>
        </div>
        <div class="card-body">
          <?php if (empty($payments)): ?>
            <div class="alert alert-info mb-0">
              <i class="bi bi-info-circle"></i> Belum ada pembayaran untuk invoice ini
            </div>
          <?php else: ?>
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>No</th>
                    <th>Tanggal Bayar</th>
                    <th>Metode</th>
                    <th class="text-end">Jumlah</th>
                    <th>Bukti Transfer</th>
                    <th>Catatan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($payments as $idx => $p): ?>
                  <tr>
                    <td><?= $idx + 1 ?></td>
                    <td><?= date('d M Y H:i', strtotime($p['paid_at'])) ?></td>
                    <td>
                      <?php
                      $methodBadge = [
                        'cash' => 'success',
                        'kredit' => 'warning',
                        'transfer' => 'primary',
                        'other' => 'secondary'
                      ];
                      $methodLabel = [
                        'cash' => 'Tunai',
                        'kredit' => 'Kredit',
                        'transfer' => 'Transfer',
                        'other' => 'Lainnya'
                      ];
                      ?>
                      <span class="badge bg-<?= $methodBadge[$p['method']] ?? 'secondary' ?>">
                        <?= $methodLabel[$p['method']] ?? ucfirst($p['method']) ?>
                      </span>
                    </td>
                    <td class="text-end"><strong>Rp <?= number_format((float)$p['amount'], 0, ',', '.') ?></strong></td>
                    <td>
                      <?php if (!empty($p['invoice_photo'])): ?>
                        <a href="<?= base_url('uploads/invoices/'.$p['invoice_photo']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                          <i class="bi bi-image"></i> Lihat Bukti
                        </a>
                      <?php else: ?>
                        <span class="text-muted small">Tidak ada bukti</span>
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
    </div>
  </div>
</div>

<?= $this->endSection() ?>
