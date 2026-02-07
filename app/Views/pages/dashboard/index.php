<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('title') ?>Dashboard - PT Eshokita<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'dashboard']) ?>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-white">Total Penjualan Bulan Ini</div>
            <div class="card-body">
                <h4 class="text-primary">Rp <?= number_format($rekap_totals['grand'] ?? 0, 0, ',', '.') ?></h4>
                <small class="text-muted">
                    <?php
                    $methodSummary = [];
                    $methodNames = ['cash'=>'Cash','qris'=>'QRIS','va'=>'VA','ewallet'=>'E-Wallet'];
                    foreach ($methodNames as $k => $label) {
                        $val = $rekap_totals[$k] ?? 0;
                        if ($val > 0) $methodSummary[] = $label . ': Rp ' . number_format($val, 0, ',', '.');
                    }
                    echo implode('<br>', $methodSummary) ?: 'Belum ada pembayaran';
                    ?>
                </small>
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

<!-- Charts Section -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span>Metode Pembayaran</span>
                <input type="month" id="filterCashVsKredit" value="<?= esc($month) ?>" class="form-control form-control-sm" style="width:140px; font-size:11px" />
            </div>
            <div class="card-body">
                <canvas id="cashVsKreditChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span>Distribusi Produk Terjual</span>
                <input type="month" id="filterProductDist" value="<?= esc($month) ?>" class="form-control form-control-sm" style="width:140px; font-size:11px" />
            </div>
            <div class="card-body">
                <canvas id="productDistributionChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span>Trend Penjualan</span>
                <select id="filterSalesTrend" class="form-control form-control-sm" style="width:100px; font-size:11px">
                    <?php for($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                    <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="card-body">
                <canvas id="salesTrendChart" style="max-height: 250px;"></canvas>
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

<?php $colSpan = 10 + count($rekap_variants ?? []); ?>
<div class="table-responsive mb-4">
    <table class="table table-sm table-bordered align-middle" style="font-size:12px">
        <thead class="table-light">
            <tr>
                <th style="width:70px">NO NOTA</th>
                <th style="width:140px">CUSTOMER</th>
                <th style="width:80px">RUTE</th>
                <?php foreach (($rekap_variants ?? []) as $v): ?>
                <th style="width:60px"><?= esc($v['label']) ?></th>
                <?php endforeach; ?>
                <th style="width:50px">JML</th>
                <th style="width:80px">HRG</th>
                <th style="width:70px">CASH</th>
                <th style="width:70px">QRIS</th>
                <th style="width:70px">VA</th>
                <th style="width:70px">E-WALLET</th>
                <th style="width:60px">KET</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rekap)): ?>
                <tr><td colspan="<?= $colSpan ?>" class="text-center text-muted">Tidak ada data</td></tr>
            <?php else: foreach ($rekap as $row): ?>
                <tr>
                    <td><?= esc($row['nota']) ?></td>
                    <td><?= esc($row['customer']) ?></td>
                    <td><?= esc($row['rute'] ?? '-') ?></td>
                    <?php foreach (($rekap_variants ?? []) as $v): ?>
                    <td><?= esc(($row['variants'][$v['key']] ?? '') ?: '') ?></td>
                    <?php endforeach; ?>
                    <td><?= esc($row['jumlah'] ?? '') ?></td>
                    <td><?= esc($row['hrg']) ?></td>
                    <td><?= esc($row['cash'] ?? '') ?></td>
                    <td><?= esc($row['qris'] ?? '') ?></td>
                    <td><?= esc($row['va'] ?? '') ?></td>
                    <td><?= esc($row['ewallet'] ?? '') ?></td>
                    <td><?= esc($row['ket']) ?></td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>


<script>
// Metode Pembayaran Chart (Bar Chart)
const cashVsKreditCtx = document.getElementById('cashVsKreditChart').getContext('2d');
const allMethodLabels = ['Cash', 'QRIS', 'VA', 'E-Wallet'];
const allMethodKeys = ['cash', 'qris', 'va', 'ewallet'];
const allMethodColors = [
    'rgba(75, 192, 192, 0.6)',   // Cash - teal
    'rgba(255, 99, 132, 0.6)',   // QRIS - red
    'rgba(153, 102, 255, 0.6)',  // VA - purple
    'rgba(255, 206, 86, 0.6)'   // E-Wallet - yellow
];
const allMethodBorders = [
    'rgba(75, 192, 192, 1)',
    'rgba(255, 99, 132, 1)',
    'rgba(153, 102, 255, 1)',
    'rgba(255, 206, 86, 1)'
];
const initialMethodData = [
    <?= $rekap_totals['cash'] ?? 0 ?>,
    <?= $rekap_totals['qris'] ?? 0 ?>,
    <?= $rekap_totals['va'] ?? 0 ?>,
    <?= $rekap_totals['ewallet'] ?? 0 ?>
];
const cashVsKreditChart = new Chart(cashVsKreditCtx, {
    type: 'bar',
    data: {
        labels: allMethodLabels,
        datasets: [{
            label: 'Total Penjualan (Rp)',
            data: initialMethodData,
            backgroundColor: allMethodColors,
            borderColor: allMethodBorders,
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Product Distribution Chart (Pie Chart)
const productDistCtx = document.getElementById('productDistributionChart').getContext('2d');
const productDistChart = new Chart(productDistCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode($chart_product_distribution['labels'] ?? []) ?>,
        datasets: [{
            data: <?= json_encode($chart_product_distribution['data'] ?? []) ?>,
            backgroundColor: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(255, 206, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 159, 64, 0.6)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    boxWidth: 12,
                    font: {
                        size: 10
                    }
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.label + ': ' + context.parsed + ' unit';
                    }
                }
            }
        }
    }
});

// Sales Trend Chart (Line Chart)
const salesTrendCtx = document.getElementById('salesTrendChart').getContext('2d');
const salesTrendChart = new Chart(salesTrendCtx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chart_sales_trend['labels'] ?? []) ?>,
        datasets: [{
            label: 'Total Penjualan',
            data: <?= json_encode($chart_sales_trend['data'] ?? []) ?>,
            fill: true,
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 2,
            tension: 0.4,
            pointBackgroundColor: 'rgba(54, 162, 235, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        if (value >= 1000000) {
                            return 'Rp ' + (value / 1000000).toFixed(1) + 'jt';
                        } else if (value >= 1000) {
                            return 'Rp ' + (value / 1000).toFixed(0) + 'rb';
                        } else {
                            return 'Rp ' + value.toLocaleString('id-ID');
                        }
                    }
                }
            }
        }
    }
});

// AJAX Filters
document.getElementById('filterCashVsKredit').addEventListener('change', function() {
    const month = this.value;
    fetch(`<?= site_url('dashboard/chart/cash-vs-kredit') ?>?month=${month}`)
        .then(response => response.json())
        .then(data => {
            cashVsKreditChart.data.datasets[0].data = allMethodKeys.map(k => data[k] || 0);
            cashVsKreditChart.update();
        });
});

document.getElementById('filterProductDist').addEventListener('change', function() {
    const month = this.value;
    fetch(`<?= site_url('dashboard/chart/product-distribution') ?>?month=${month}`)
        .then(response => response.json())
        .then(data => {
            productDistChart.data.labels = data.labels;
            productDistChart.data.datasets[0].data = data.data;
            productDistChart.update();
        });
});

document.getElementById('filterSalesTrend').addEventListener('change', function() {
    const year = this.value;
    fetch(`<?= site_url('dashboard/chart/sales-trend') ?>?year=${year}`)
        .then(response => response.json())
        .then(data => {
            salesTrendChart.data.labels = data.labels;
            salesTrendChart.data.datasets[0].data = data.data;
            salesTrendChart.update();
        });
});
</script>

<?= $this->endSection() ?>