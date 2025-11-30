<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('title') ?>Customer - PT Eshokita<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'customer']) ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="customer">
                <thead>
                    <tr>
                        <th scope="col" class="col-1">No.</th>
                        <th scope="col">Nama</th>
                        <th scope="col">Email</th>
                        <th scope="col">No. Telepon</th>
                        <th scope="col">Rute</th>
                        <th scope="col" style="width: 420px; white-space: nowrap;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customer as $key => $value) : ?>
                    <tr>
                        <th scope="row"><?= $key + 1 ?></th>
                        <td><?= esc($value['nama']) ?></td>
                        <td><?= esc($value['email']) ?></td>
                        <td><?= esc($value['telepon']) ?></td>
                        <td><?= esc($value['nama_wilayah']) ?></td>
                        <td style="white-space: nowrap;">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <a href="<?= base_url('customer/detail/' . $value['id_customer']) ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                                <a href="<?= base_url('customer/edit/' . $value['id_customer']) ?>" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <a href="<?= base_url('customer/order-again/' . $value['id_customer']) ?>" class="btn btn-sm btn-success">
                                    <i class="bi bi-cart-plus"></i> Order Ulang
                                </a>
                                <button type="button" data-href="<?= base_url('customer/delete/' . $value['id_customer']) ?>" class="btn btn-sm btn-danger btn-delete">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
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