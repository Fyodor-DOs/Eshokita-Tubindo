<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4>Rekap Penjualan</h4>
  <form class="d-flex gap-2" method="get" action="<?= current_url() ?>">
    <input type="date" class="form-control" name="date" value="<?= esc($date) ?>">
    <input type="text" class="form-control" name="sopir" placeholder="Sopir" value="<?= esc($sopir) ?>">
    <input type="text" class="form-control" name="kenek" placeholder="Kenek" value="<?= esc($kenek) ?>">
    <button class="btn btn-outline-primary" type="submit">Terapkan</button>
    <a class="btn btn-primary" target="_blank" href="<?= site_url('rekap-penjualan/export?date=' . urlencode($date) . '&sopir=' . urlencode($sopir) . '&kenek=' . urlencode($kenek)) ?>">
      Export PDF
    </a>
  </form>
</div>

<style>
  .rekap-table { font-size: 12px; }
  .rekap-table th, .rekap-table td { border: 1px solid #ddd; padding: 4px; }
  .rekap-table th { background: #f8f9fa; }
  .section-title { font-weight: 700; background: #eef5ff; }
  .no-border { border: none !important; }
</style>

<div class="table-responsive">
  <table class="table rekap-table w-100">
    <thead>
      <tr>
        <th class="no-border" colspan="9" style="text-align:center; font-size:16px">REKAP PENJUALAN</th>
      </tr>
      <tr>
        <th class="no-border" colspan="7"></th>
        <th class="no-border" colspan="2" style="width:320px">
          <div class="d-flex flex-column gap-1">
            <div>Hari / Tgl &nbsp;: <?= esc(date('d-m-Y', strtotime($date))) ?></div>
            <div>Sopir / Kenek : <?= esc($sopir) ?><?= $sopir && $kenek ? ' / ' : '' ?><?= esc($kenek) ?></div>
          </div>
        </th>
      </tr>
      <tr>
        <th style="width:40px">NO NOTA</th>
        <th>NAMA CUSTOMER</th>
        <th style="width:60px">TB</th>
        <th style="width:60px">KRP</th>
        <th style="width:60px">10KG</th>
        <th style="width:80px">HRG</th>
        <th style="width:80px">CASH</th>
        <th style="width:80px">KREDIT</th>
        <th style="width:120px">KET</th>
      </tr>
    </thead>
    <tbody>
      <tr><td colspan="9" class="section-title">CASH</td></tr>
      <?php for ($i=0; $i<15; $i++): ?>
      <tr>
        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
      </tr>
      <?php endfor; ?>

      <tr><td colspan="9" class="section-title">KREDIT</td></tr>
      <?php for ($i=0; $i<25; $i++): ?>
      <tr>
        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
      </tr>
      <?php endfor; ?>

      <tr><td colspan="9" class="section-title">PT</td></tr>
      <?php for ($i=0; $i<2; $i++): ?>
      <tr>
        <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
      </tr>
      <?php endfor; ?>

      <tr><td colspan="8" class="no-border"></td><td>= Rp.</td></tr>
      <tr><td colspan="9" class="no-border" style="height:6px"></td></tr>
      <tr>
        <td class="no-border" colspan="9" style="text-align:center">JUMLAH ( Q ) :</td>
      </tr>
      <tr><td colspan="9" class="no-border" style="height:6px"></td></tr>
      <tr>
        <td class="no-border" colspan="9">
          <table class="w-100 rekap-table">
            <tr>
              <th style="width:120px">keterangan</th>
              <th style="width:80px">Bawa</th>
              <th style="width:80px">Sisa</th>
              <th style="width:80px">Laku</th>
              <th style="width:80px">Susut</th>
              <th style="width:120px">KET</th>
            </tr>
            <?php foreach (["Kristal", "Serut", "Kt.10 kg"] as $ket): ?>
            <tr>
              <td><?= esc($ket) ?></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            <?php endforeach; ?>
          </table>
        </td>
      </tr>
      <tr><td colspan="9" class="no-border" style="height:8px"></td></tr>
      <tr>
        <td class="no-border" colspan="9">
          <table class="w-100 rekap-table">
            <tr>
              <td class="no-border" style="width:50%">
                <div>* Cash &nbsp;&nbsp;= Rp.</div>
                <div>* Bayar Bon = Rp.</div>
                <div>* Total &nbsp;&nbsp;&nbsp;&nbsp;= Rp.</div>
                <div>* Parkir &nbsp;&nbsp;= Rp.</div>
                <div>* Sisa &nbsp;&nbsp;&nbsp;&nbsp;= Rp.</div>
              </td>
              <td class="no-border" style="width:50%; vertical-align:bottom">
                <div>KM PULANG = </div>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<?= $this->endSection() ?>
