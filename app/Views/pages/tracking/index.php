<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'tracking']) ?>

<div class="card">
	<div class="card-body">
		<div class="table-responsive">
                        <table class="table table-hover" id="table-tracking">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>No. Bon</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Lokasi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($tracking)): ?>
                    <tr>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">Belum ada tracking</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                        <td class="text-center text-muted">-</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach($tracking as $key => $t): ?>
                    <tr>
                        <td><?= $key + 1 ?></td>
                        <td><?= date('d M Y H:i', strtotime($t['created_at'])) ?></td>
                        <td><strong><?= esc($t['no_bon']) ?></strong></td>
                        <td><?= esc($t['customer_name']) ?></td>
                        <td>
                            <?php
                            $badgeClass = match($t['status']) {
                                'delivered' => 'bg-success',
                                'on_delivery' => 'bg-warning',
                                'picked_up' => 'bg-info',
                                default => 'bg-secondary'
                            };
                            $statusText = match($t['status']) {
                                'delivered' => 'Terkirim',
                                'on_delivery' => 'Dalam Perjalanan',
                                'picked_up' => 'Diambil',
                                default => ucfirst($t['status'])
                            };
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $statusText ?></span>
                        </td>
                        <td><?= esc($t['location'] ?? '-') ?></td>
                        <td><?= esc($t['note'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/tables/tracking.js') ?>"></script>

<?= $this->endSection() ?>
