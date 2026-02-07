<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Rekap Penjualan</title>
<style>
  body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8px; }
  table { border-collapse: collapse; width: 100%; }
  th, td { border:1px solid #555; padding:2px; font-size:8px; }
  th { background:#f0f0f0; }
  .title { text-align:center; font-weight:700; font-size:12px; border:none; }
  .no-border { border:none !important; }
  .section { font-weight:700; background:#eef5ff; }
</style>
</head>
<body>
<table>
  <tr><td class="title" colspan="<?= 10 + count($variants ?? []) ?>">REKAP PENJUALAN</td></tr>
  <tr>
    <td class="no-border" colspan="<?= 10 + count($variants ?? []) ?>">Hari / Tgl : <?php 
      // If date is YYYY-MM format, show as 01-MM-YYYY, else show as d-m-Y
      if (preg_match('/^\d{4}-\d{2}$/', $date)) {
        echo esc('01-' . date('m-Y', strtotime($date . '-01')));
      } else {
        echo esc(date('d-m-Y', strtotime($date)));
      }
    ?></td>
  </tr>
  <?php $pdfColSpan = 10 + count($variants ?? []); ?>
  <tr>
    <th style="width:40px">NO NOTA</th>
    <th style="width:60px">CUSTOMER</th>
    <th style="width:40px">RUTE</th>
    <?php foreach (($variants ?? []) as $v): ?>
    <th style="width:25px"><?= esc($v['label']) ?></th>
    <?php endforeach; ?>
    <th style="width:22px">JML</th>
    <th style="width:42px">HRG</th>
    <th style="width:42px">CASH</th>
    <th style="width:38px">QRIS</th>
    <th style="width:38px">VA</th>
    <th style="width:42px">E-WALLET</th>
    <th style="width:30px">KET</th>
  </tr>
  <?php if (empty($rows)): ?>
    <tr><td colspan="<?= $pdfColSpan ?>" class="text-center">(Tidak ada data)</td></tr>
  <?php else: foreach ($rows as $r): ?>
    <tr>
      <td><?= esc($r['nota']) ?></td>
      <td><?= esc($r['customer']) ?></td>
      <td><?= esc($r['rute'] ?? '-') ?></td>
      <?php foreach (($variants ?? []) as $v): ?>
      <td><?= esc(($r['variants'][$v['key']] ?? '') ?: '') ?></td>
      <?php endforeach; ?>
      <td><?= esc($r['jumlah'] ?? '') ?></td>
      <td><?= esc($r['hrg']) ?></td>
      <td><?= esc($r['cash'] ?? '') ?></td>
      <td><?= esc($r['qris'] ?? '') ?></td>
      <td><?= esc($r['va'] ?? '') ?></td>
      <td><?= esc($r['ewallet'] ?? '') ?></td>
      <td><?= esc($r['ket']) ?></td>
    </tr>
  <?php endforeach; endif; ?>
  <tr style="font-weight:bold; background:#f0f0f0">
    <td colspan="<?= 4 + count($variants ?? []) ?>" class="text-right" style="text-align:right">TOTAL</td>
    <td><?= number_format($totals['total_hrg'] ?? 0, 0, ',', '.') ?></td>
    <td><?= number_format($totals['cash'] ?? 0, 0, ',', '.') ?></td>
    <td><?= number_format($totals['qris'] ?? 0, 0, ',', '.') ?></td>
    <td><?= number_format($totals['va'] ?? 0, 0, ',', '.') ?></td>
    <td><?= number_format($totals['ewallet'] ?? 0, 0, ',', '.') ?></td>
    <td></td>
  </tr>
  <tr><td colspan="<?= 10 + count($variants ?? []) ?>" class="no-border" style="height:6px"></td></tr>
  <tr>
    <td colspan="<?= 10 + count($variants ?? []) ?>" class="no-border">
      <table style="width:100%">
        <tr>
          <th style="width:25%">keterangan</th>
          <th style="width:25%">Sisa</th>
          <th style="width:25%">Laku</th>
          <th style="width:25%">KET</th>
        </tr>
        <?php foreach ($summary as $label => $vals): ?>
        <tr>
          <td><?= esc($label) ?></td>
          <td><?= esc($vals['sisa']) ?></td>
          <td><?= esc($vals['laku']) ?></td>
          <td></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </td>
  </tr>
  <tr><td colspan="<?= 10 + count($variants ?? []) ?>" class="no-border" style="height:8px"></td></tr>
  <tr>
    <td colspan="<?= 10 + count($variants ?? []) ?>" class="no-border">
      <div>* Cash = Rp. <?= number_format($totals['cash'] ?? 0,0,',','.') ?></div>
      <div>* QRIS = Rp. <?= number_format($totals['qris'] ?? 0,0,',','.') ?></div>
      <div>* VA = Rp. <?= number_format($totals['va'] ?? 0,0,',','.') ?></div>
      <div>* E-Wallet = Rp. <?= number_format($totals['ewallet'] ?? 0,0,',','.') ?></div>
      <div>* Total = Rp. <?= number_format($totals['grand'] ?? 0,0,',','.') ?></div>
    </td>
  </tr>
</table>
</body>
</html>