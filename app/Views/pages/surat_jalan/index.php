<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'surat-jalan']) ?>

<div class="card">
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-hover" id="surat_jalan">
				<thead>
					<tr>
						<th>No</th>
						<th>No. Surat Jalan</th>
						<th>Tanggal</th>
						<th>Customer</th>
						<th>Rute</th>
						<th class="text-center">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php if(empty($suratJalan)): ?>
					<tr>
						<td class="text-center text-muted">-</td>
						<td class="text-center text-muted">-</td>
						<td class="text-center text-muted">-</td>
						<td class="text-center text-muted">Belum ada surat jalan</td>
						<td class="text-center text-muted">-</td>
						<td class="text-center text-muted">-</td>
					</tr>
					<?php else: ?>
					<?php foreach($suratJalan as $key => $sj): ?>
					<tr>
						<td><?= $key + 1 ?></td>
						<?php 
						  $tgl = isset($sj['tanggal']) ? date('Ymd', strtotime($sj['tanggal'])) : date('Ymd');
						  $noSurat = 'SJ-' . $tgl . '-' . str_pad((string)$sj['id_surat_jalan'], 4, '0', STR_PAD_LEFT);
						?>
					<td><?= esc($noSurat) ?></td>
					<td><?= esc($sj['tanggal']) ?></td>
					<td><?= esc($sj['customer_name'] ?? '-') ?></td>
					<td><?= esc($sj['rute_name'] ?? $sj['kode_rute']) ?></td>
						<td class="text-center">
							<a href="<?= base_url('/surat-jalan/detail/'.$sj['id_surat_jalan']) ?>" class="btn btn-sm btn-info">Detail</a>
							<?php if (!empty($sj['id_pengiriman'])): ?>
							<a href="<?= base_url('/surat-jalan/print-batch/'.$sj['id_pengiriman']) ?>" class="btn btn-sm btn-secondary" target="_blank">Print Batch BON</a>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?= $this->endSection() ?>