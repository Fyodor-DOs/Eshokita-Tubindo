<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'dashboard']) ?>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-white">Total Penjualan Bulan Ini</div>
            <div class="card-body">
                <h4 class="text-primary">Rp <?= number_format($rekap_totals['grand'] ?? 0, 0, ',', '.') ?></h4>
                <small class="text-muted">Cash: Rp <?= number_format($rekap_totals['cash'] ?? 0, 0, ',', '.') ?><br>
                Kredit: Rp <?= number_format($rekap_totals['kredit'] ?? 0, 0, ',', '.') ?></small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-white">Total Customer</div>
            <div class="card-body">
                <div><?= $customer ?></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-white">Total Pengiriman</div>
            <div class="card-body">
                <div><?= $pengiriman ?></div>
            </div>
        </div>
    </div>

</div>

<hr class="my-4"/>

<div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">Rekap Penjualan Bulan <?= esc($month) ?></h5>
        <form method="get" class="d-flex gap-2 align-items-center">
                <input type="month" name="month" value="<?= esc($month) ?>" class="form-control form-control-sm" style="width:180px" />
                <button class="btn btn-sm btn-outline-primary">Filter</button>
                <a target="_blank" class="btn btn-sm btn-primary" href="<?= site_url('rekap-penjualan/export?date=' . urlencode($month.'-01')) ?>">Export PDF</a>
        </form>
</div>

<div class="table-responsive mb-4">
    <table class="table table-sm table-bordered align-middle" style="font-size:12px">
        <thead class="table-light">
            <tr>
                <th style="width:70px">NO NOTA</th>
                <th style="width:150px">CUSTOMER</th>
                <?php foreach (($rekap_variants ?? []) as $v): ?>
                <th style="width:60px"><?= esc($v['label']) ?></th>
                <?php endforeach; ?>
                <th style="width:50px">JML</th>
                <th style="width:80px">HRG</th>
                <th style="width:80px">CASH</th>
                <th style="width:80px">KREDIT</th>
                <th style="width:80px">KET</th>
            </tr>
        </thead>
        <tbody>
            <tr class="table-primary"><td colspan="<?= 6 + count($rekap_variants ?? []) ?>" class="fw-bold">CASH</td></tr>
            <?php if (empty($rekap['cash'])): ?>
                <tr><td colspan="<?= 6 + count($rekap_variants ?? []) ?>" class="text-center text-muted">Tidak ada data</td></tr>
            <?php else: foreach ($rekap['cash'] as $row): ?>
                <tr>
                    <td><?= esc($row['nota']) ?></td>
                    <td><?= esc($row['customer']) ?></td>
                    <?php foreach (($rekap_variants ?? []) as $v): ?>
                    <td><?= esc(($row['variants'][$v['key']] ?? '') ?: '') ?></td>
                    <?php endforeach; ?>
                    <td><?= esc($row['jumlah'] ?? '') ?></td>
                    <td><?= esc($row['hrg']) ?></td>
                    <td><?= esc($row['cash']) ?></td>
                    <td><?= esc($row['kredit']) ?></td>
                    <td><?= esc($row['ket']) ?></td>
                </tr>
            <?php endforeach; endif; ?>

            <tr class="table-primary"><td colspan="<?= 6 + count($rekap_variants ?? []) ?>" class="fw-bold">KREDIT</td></tr>
            <?php if (empty($rekap['kredit'])): ?>
                <tr><td colspan="<?= 6 + count($rekap_variants ?? []) ?>" class="text-center text-muted">Tidak ada data</td></tr>
            <?php else: foreach ($rekap['kredit'] as $row): ?>
                <tr>
                    <td><?= esc($row['nota']) ?></td>
                    <td><?= esc($row['customer']) ?></td>
                    <?php foreach (($rekap_variants ?? []) as $v): ?>
                    <td><?= esc(($row['variants'][$v['key']] ?? '') ?: '') ?></td>
                    <?php endforeach; ?>
                    <td><?= esc($row['jumlah'] ?? '') ?></td>
                    <td><?= esc($row['hrg']) ?></td>
                    <td><?= esc($row['cash']) ?></td>
                    <td><?= esc($row['kredit']) ?></td>
                    <td><?= esc($row['ket']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>


<?= $this->endSection() ?>