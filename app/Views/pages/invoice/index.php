<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'invoice']) ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="payment-history">
                <thead>
                    <tr>
                        <th scope="col" class="col-1 text-center">No.</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">No. Invoice</th>
                        <th scope="col">Ref No.</th>
                        <th scope="col">Customer</th>
                        <th scope="col" class="text-end">Jumlah</th>
                        <th scope="col" class="text-end">Terbayar</th>
                        <th scope="col">Status Pembayaran</th>
                        <th scope="col">Status Pengiriman</th>
                        
                        <th scope="col" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($invoices as $key => $i): ?>
                    <tr>
                        <td class="text-center"><?= $key + 1 ?></td>
                        <td><?= date('d M Y', strtotime($i['issue_date'])) ?></td>
                        <td><strong><?= esc($i['invoice_no']) ?></strong></td>
                        <td><?= esc($i['ref_no']) ?></td>
                        <td><?= esc($i['customer_name']) ?></td>
                        <td class="text-end"><strong>Rp <?= number_format((float)$i['amount'], 0, ',', '.') ?></strong></td>
                        <td class="text-end">Rp <?= number_format((float)$i['total_paid'], 0, ',', '.') ?></td>
                        <td>
                            <?php 
                            $statusBadge = [
                                'paid' => 'success',
                                'partial' => 'warning',
                                'unpaid' => 'danger',
                                'draft' => 'secondary',
                                'void' => 'dark'
                            ];
                            $statusLabel = [
                                'paid' => 'Lunas',
                                'partial' => 'Sebagian',
                                'unpaid' => 'Belum Bayar',
                                'draft' => 'Draft',
                                'void' => 'Batal'
                            ];
                            ?>
                            <span class="badge bg-<?= $statusBadge[$i['status']] ?? 'secondary' ?>">
                                <?= $statusLabel[$i['status']] ?? ucfirst($i['status']) ?>
                            </span>
                        </td>

                        <td>
                            <?php
                            // Status pengiriman persis seperti di pengiriman
                            $statusPengiriman = $i['status_pengiriman'] ?? 'siap';
                            $mapPengiriman = [
                                'siap' => ['warning','Siap'],
                                'mengirim' => ['primary','Mengirim'],
                                'diterima' => ['success','Diterima'],
                                'gagal' => ['danger','Gagal']
                            ];
                            [$clsPengiriman, $lblPengiriman] = $mapPengiriman[$statusPengiriman] ?? ['secondary', ucfirst($statusPengiriman)];
                            ?>
                            <span class="badge bg-<?= $clsPengiriman ?>"><?= $lblPengiriman ?></span>
                        </td>

                        <td class="text-center">
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn-info" href="<?= base_url('/payment/detail/'.$i['id_invoice']) ?>" title="Lihat Detail">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <?php if($i['status'] !== 'paid'): ?>
                                <a class="btn btn-success" href="<?= base_url('/payment/create/'.$i['id_invoice']) ?>" title="Tambah Pembayaran">
                                    <i class="bi bi-plus-circle"></i> Bayar
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?php $this->section('scripts'); ?>
<script src="<?= base_url('assets/js/tables/payment.js') ?>"></script>
<?php $this->endSection(); ?>
