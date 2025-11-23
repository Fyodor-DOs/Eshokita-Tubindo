<?php
/** @var array $pengiriman */
/** @var array $pages */
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Surat Jalan - BON <?= esc($pengiriman['no_bon'] ?? '-') ?></title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; }
    .page { page-break-after: always; padding: 16px; }
    .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .brand { display: flex; align-items: center; gap: 12px; }
    .brand img { height: 42px; }
    .title { font-size: 16px; font-weight: bold; text-transform: uppercase; }
    .meta { font-size: 12px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #777; padding: 6px 8px; font-size: 12px; }
    th { background: #f3f3f3; text-align: left; }
    .muted { color: #666; }
    .right { text-align: right; }
    .small { font-size: 11px; }
    .sign { margin-top: 24px; display: flex; justify-content: space-between; gap: 24px; }
    .sign .box { width: 45%; text-align: center; min-height: 80px; }
    @media print { .no-print{ display: none; } }
  </style>
</head>
<body>
<?php if (empty($pages)) : ?>
  <div class="page">
    <div class="header">
      <div class="brand">
        <img src="<?= base_url('assets/image/Logo.png') ?>" alt="Logo" />
        <div>
          <div class="title">SURAT JALAN BARANG</div>
          <div class="small muted">BON: <?= esc($pengiriman['no_bon'] ?? '-') ?> | Tanggal: <?= esc(date('d M Y', strtotime($pengiriman['tanggal'] ?? date('Y-m-d')))) ?></div>
        </div>
      </div>
      <div class="meta">
        <div>Pengirim: <strong>PT. Es hokita & Es Tubindo</strong></div>
        <div>Rute: <?= esc($pengiriman['nama_wilayah'] ?? '-') ?></div>
      </div>
    </div>
    <p class="muted">Tidak ada data invoice untuk BON ini.</p>
  </div>
<?php else: foreach ($pages as $idx => $page): ?>
  <div class="page">
    <div class="header">
      <div class="brand">
        <img src="<?= base_url('assets/image/Logo.png') ?>" alt="Logo" />
        <div>
          <div class="title">SURAT JALAN BARANG</div>
          <div class="small muted">BON: <?= esc($pengiriman['no_bon'] ?? '-') ?> | Tanggal: <?= esc(date('d M Y', strtotime($pengiriman['tanggal'] ?? date('Y-m-d')))) ?></div>
        </div>
      </div>
      <div class="meta">
        <div>Pengirim: <strong>PT. Es hokita & Es Tubindo</strong></div>
        <div>Penerima (PT): <strong><?= esc($page['customer']['name'] ?? '-') ?></strong></div>
        <div class="small muted">Alamat: <?= esc($page['customer']['address'] ?? '-') ?></div>
        <div>Rute: <?= esc($pengiriman['nama_wilayah'] ?? '-') ?></div>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width: 6%">No</th>
          <th style="width: 18%">Kode Barang</th>
          <th>Nama Barang</th>
          <th style="width: 15%" class="right">Kuantitas</th>
          <th style="width: 15%" class="right">Berat Satuan (kg)</th>
          <th style="width: 18%" class="right">Total Berat (kg)</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($page['items'])): ?>
        <tr>
          <td colspan="6" class="muted">Tidak ada item</td>
        </tr>
      <?php else: 
            $__totalQty = 0; $__totalBerat = 0.0; 
            foreach ($page['items'] as $idx2 => $it): 
                $qty = (float)($it['qty'] ?? 0); $__totalQty += $qty; 
                $b = isset($it['berat_kg']) && $it['berat_kg'] !== null ? (float)$it['berat_kg'] : 0.0; 
                $tb = $qty * $b; $__totalBerat += $tb;
      ?>
        <tr>
          <td class="right"><?= $idx2+1 ?></td>
          <td><?= esc($it['kode'] ?? '-') ?></td>
          <td><?= esc($it['nama'] ?? '-') ?></td>
          <td class="right"><?= number_format($qty, 0, ',', '.') ?></td>
          <td class="right"><?= $it['berat_kg'] !== null ? number_format((float)$it['berat_kg'], 2, ',', '.') : '-' ?></td>
          <td class="right"><?= $it['berat_kg'] !== null ? number_format($tb, 2, ',', '.') : '-' ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="3" class="right">TOTAL</th>
        <th class="right"><?= number_format($__totalQty, 0, ',', '.') ?></th>
        <th></th>
        <th class="right"><?= number_format($__totalBerat, 2, ',', '.') ?></th>
      </tr>
      <?php endif; ?>
      </tbody>
    </table>
    <div class="sign">
      <div class="box">
        <div class="small">Hormat Kami</div>
        <div style="height:60px"></div>
        <div class="small muted">( ttd & nama jelas )</div>
      </div>
      <div class="box">
        <div class="small">Tanda Terima</div>
        <div style="height:60px"></div>
        <div class="small muted">( ttd & nama jelas )</div>
      </div>
    </div>
    <div class="no-print" style="margin-top:10px; text-align:right">
      <button onclick="window.print()">Print</button>
    </div>
  </div>
<?php endforeach; endif; ?>
<script>
  (function(){
    const params=new URLSearchParams(location.search);
    if(params.get('print')==='1' || params.get('print')==='true'){
      window.print();
    }
  })();
  </script>
</body>
</html>
