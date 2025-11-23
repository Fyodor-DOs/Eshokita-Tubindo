<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?= view('components/Breadcrumb', ['segment1' => 'user']) ?>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="user">
                <thead>
                    <tr>
                        <th scope="col" class="col-1">#</th>
                        <th scope="col">Nama</th>
                        <th scope="col">No. Telepon</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($user as $key => $value) : ?>
                        <tr>
                            <th scope="row"><?= $key + 1 ?></th>
                            <td><?= esc($value['nama']) ?></td>
                            <td><?= esc($value['telepon']) ?></td>
                            <td><?= esc($value['email']) ?></td>
                            <td><?= esc(ucwords($value['role'])) ?></td>
                            <td>
                                <a href="<?= base_url('user/detail/' . $value['id_user']) ?>"
                                    class="btn btn-sm btn-info">Detail</a>
                                <a href="<?= base_url('user/edit/' . $value['id_user']) ?>"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <button type="button" data-href="<?= base_url('user/delete/' . $value['id_user']) ?>"
                                    class="btn btn-sm btn-danger btn-delete">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<?= $this->endSection() ?>