<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <title>Surat Jalan Barang</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; font-size: 12px; color: #222; }
    .page { padding: 16px; }
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
  <div class="page">
    <div class="header">
      <div class="brand">
        <img src="<?= base_url('assets/image/Logo.png') ?>" alt="Logo" />
        <div>
          <div class="title">SURAT JALAN BARANG</div>
          <div class="small muted">Tanggal: <?= esc(date('d M Y')) ?></div>
        </div>
      </div>
      <div class="meta">
        <div>Pengirim: <strong><?= esc($pengirim) ?></strong></div>
        <div>Penerima (PT): <strong><?= esc($penerima) ?></strong></div>
        <?php if (!empty($rute)): ?>
        <div>Rute: <?= esc($rute) ?></div>
        <?php endif; ?>
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
      <?php 
        $__totalQty = 0; $__totalBerat = 0.0;
        foreach ($barang as $i => $row):
            $qty = (float)($row['kuantitas'] ?? 0); $__totalQty += $qty;
            $berat = isset($row['berat_kg']) && $row['berat_kg'] !== null ? (float)$row['berat_kg'] : null;
            $tb = $berat !== null ? $qty * $berat : null;
            if ($tb !== null) $__totalBerat += $tb;
      ?>
        <tr>
          <td class="right"><?= $i+1 ?></td>
          <td><?= esc($row['kode'] ?? $row['nama_barang'] ?? '-') ?></td>
          <td><?= esc($row['nama_barang']) ?></td>
          <td class="right"><?= number_format($qty, 0, ',', '.') ?></td>
          <td class="right"><?= $berat !== null ? number_format($berat, 2, ',', '.') : '-' ?></td>
          <td class="right"><?= $tb !== null ? number_format($tb, 2, ',', '.') : '-' ?></td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <th colspan="3" class="right">TOTAL</th>
        <th class="right"><?= number_format($__totalQty, 0, ',', '.') ?></th>
        <th></th>
        <th class="right"><?= number_format($__totalBerat, 2, ',', '.') ?></th>
      </tr>
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
  <script>
    window.onload = function() { window.print(); };
  </script>
</body>
</html>
