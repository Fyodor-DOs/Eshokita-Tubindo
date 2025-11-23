<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'surat-jalan', 'segment2' => 'detail']) ?>

<?php $sj = $surat_jalan ?? []; ?>
<div class="card">
  <div class="card-body">
    <?php 
      $tgl = isset($sj['tanggal']) ? date('Ymd', strtotime($sj['tanggal'])) : date('Ymd');
      $noSurat = 'SJ-' . $tgl . '-' . str_pad((string)($sj['id_surat_jalan'] ?? 0), 4, '0', STR_PAD_LEFT);
    ?>
    <div class="d-flex align-items-center gap-3 mb-2">
      <img src="<?= base_url('assets/image/Logo.png') ?>" alt="Logo" style="height:50px" onerror="this.style.display='none'"/>
      <div>
        <h5 class="mb-0">SURAT JALAN BARANG</h5>
        <small class="text-muted">No: <?= esc($noSurat) ?> | Tanggal: <?= esc(date('d M Y', strtotime($sj['tanggal'] ?? date('Y-m-d')))) ?></small>
      </div>
      <div class="ms-auto text-end">
        <div><small>Pengirim:</small> <strong>PT. Es hokita &amp; Es Tubindo</strong></div>
        <div><small>Rute:</small> <?= esc($sj['rute_name'] ?? $sj['kode_rute'] ?? '-') ?></div>
        <div><small>Penerima (PT):</small> <strong><?= esc($sj['customer_name'] ?? '-') ?></strong></div>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-sm table-bordered align-middle">
        <thead class="table-light">
          <tr>
            <th class="text-center" style="width:6%">No</th>
            <th style="width:18%">SKU</th>
            <th>Nama</th>
            <th class="text-end" style="width:12%">Kuantitas</th>
            <th class="text-end" style="width:14%">Berat Satuan (kg)</th>
            <th class="text-end" style="width:16%">Total Berat (kg)</th>
          </tr>
        </thead>
        <tbody>
          <?php $totalQty=0; $totalBerat=0.0; foreach(($items ?? []) as $i=>$it): $totalQty += (float)($it['qty']??0); $totalBerat += ($it['total_berat']??0); ?>
          <tr>
            <td class="text-center"><?= $i+1 ?></td>
            <td><?= esc($it['sku'] ?? '-') ?></td>
            <td><?= esc($it['name'] ?? '-') ?></td>
            <td class="text-end"><?= number_format((float)($it['qty'] ?? 0), 0, ',', '.') ?></td>
            <td class="text-end"><?= isset($it['berat_kg']) && $it['berat_kg']!==null ? number_format((float)$it['berat_kg'], 2, ',', '.') : '-' ?></td>
            <td class="text-end"><?= isset($it['total_berat']) && $it['total_berat']!==null ? number_format((float)$it['total_berat'], 2, ',', '.') : '-' ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr>
            <th colspan="3" class="text-end">TOTAL</th>
            <th class="text-end"><?= number_format($totalQty, 0, ',', '.') ?></th>
            <th></th>
            <th class="text-end"><?= number_format($totalBerat, 2, ',', '.') ?></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex justify-content-between mt-4">
      <div class="text-center" style="width:45%">
        <div class="small">Hormat Kami</div>
        <div style="height:70px"></div>
        <div class="text-muted small">( ttd &amp; nama jelas )</div>
      </div>
      <div class="text-center" style="width:45%">
        <div class="small">Tanda Terima</div>
        <div style="height:70px"></div>
        <div class="text-muted small">( ttd &amp; nama jelas )</div>
      </div>
    </div>

    <div class="mt-3 d-flex gap-2 justify-content-end">
      <a href="<?= base_url('/surat-jalan/print/'.($sj['id_surat_jalan'])) ?>" class="btn btn-secondary" target="_blank"><i class="bi bi-printer"></i> Print</a>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
