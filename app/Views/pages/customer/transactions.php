<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'customer', 'segment2' => 'transaksi', 'segment3' => $customer['nama'] ?? '' ]) ?>

<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Transaksi Customer: <?= esc($customer['nama']) ?></h5>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="border rounded p-3 bg-light">
          <div><strong>Alamat:</strong> <?= esc($customer['alamat'] ?? '-') ?></div>
          <div><strong>Telp:</strong> <?= esc($customer['telepon'] ?? '-') ?></div>
          <div><strong>Rute:</strong> <?= esc($customer['kode_rute'] ?? '-') ?></div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="row text-center">
          <div class="col">
            <div class="fw-bold">Total Tagihan</div>
            <div class="h5 text-primary">Rp <?= number_format($summary['invoiced'] ?? 0,0,',','.') ?></div>
          </div>
          <div class="col">
            <div class="fw-bold">Terbayar</div>
            <div class="h5 text-success">Rp <?= number_format($summary['paid'] ?? 0,0,',','.') ?></div>
          </div>
          <div class="col">
            <div class="fw-bold">Sisa</div>
            <div class="h5 text-danger">Rp <?= number_format(($summary['invoiced']??0)-($summary['paid']??0),0,',','.') ?></div>
          </div>
        </div>
      </div>
    </div>

    <hr>
    <h6>Invoice</h6>
    <div class="table-responsive mb-4">
      <table class="table table-bordered">
        <thead><tr><th>No</th><th>Tanggal</th><th>Amount</th><th>Status</th></tr></thead>
        <tbody>
          <?php foreach(($invoices ?? []) as $inv): ?>
          <tr>
            <td><?= esc($inv['invoice_no']) ?></td>
            <td><?= esc($inv['issue_date']) ?></td>
            <td>Rp <?= number_format($inv['amount'],0,',','.') ?></td>
            <td><span class="badge bg-secondary"><?= esc($inv['status']) ?></span></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <h6>Pembayaran</h6>
    <div class="table-responsive mb-4">
      <table class="table table-bordered">
        <thead><tr><th>Tanggal</th><th>Method</th><th>Amount</th><th>Bukti</th></tr></thead>
        <tbody>
          <?php foreach(($payments ?? []) as $p): ?>
          <tr>
            <td><?= esc($p['paid_at']) ?></td>
            <td><?= esc($p['method']) ?></td>
            <td>Rp <?= number_format($p['amount'],0,',','.') ?></td>
            <td><?php if(!empty($p['invoice_photo'])): ?><a target="_blank" href="<?= base_url('uploads/invoices/'.$p['invoice_photo']) ?>">Lihat</a><?php endif; ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <h6>Pengiriman & Penerimaan</h6>
    <div class="table-responsive">
      <table class="table table-bordered">
  <thead><tr><th>No BON</th><th>Tanggal</th><th>Status Penerimaan</th><th>Foto</th></tr></thead>
        <tbody>
          <?php foreach(($shipments ?? []) as $s): ?>
          <tr>
            <td><?= esc($s['no_bon']) ?></td>
            <td><?= esc($s['tanggal']) ?></td>
            <?php $status = $s['pr_status'] ?? $s['pg_status'] ?? '-'; ?>
            <td><?= esc($status) ?></td>
            <td>
              <?php if (!empty($s['pg_photo'])): ?>
                <a target="_blank" rel="noopener" href="<?= base_url('uploads/penerimaan/'.$s['pg_photo']) ?>">Lihat</a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
