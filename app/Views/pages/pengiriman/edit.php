<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>

<?= view('components/Breadcrumb', ['segment1' => 'pengiriman', 'segment2' => 'edit', 'segment3' => $pengiriman['id_pengiriman']]) ?>

<form method="post" action="<?= site_url('pengiriman/edit/' . $pengiriman['id_pengiriman']) ?>">
    <?= csrf_field() ?>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal</label>
                        <input type="text" name="tanggal" id="tanggal"
                            value="<?= date('d F Y', strtotime($pengiriman['tanggal'])) ?>" class="form-control"
                            readonly>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label for="supir" class="form-label">Supir</label>
                        <input type="text" name="supir" id="supir" class="form-control"
                            value="<?= $pengiriman['supir'] ?>" readonly>
                    </div>
                </div>

                <div class=" col-md-3">
                    <div class="form-group">
                        <label for="kenek" class="form-label">Kenek</label>
                        <input type="text" name="kenek" id="kenek" class="form-control"
                            value="<?= $pengiriman['kenek'] ?>" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="plat_kendaraan" class="form-label">Plat Kendaraan</label>
                        <input type="text" name="plat_kendaraan" id="plat_kendaraan" class="form-control"
                            value="<?= $pengiriman['plat_kendaraan'] ?>" readonly>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nama_wilayah" class="form-label">Rute</label>
                        <input type="hidden" name="kode_rute" id="kode_rute" value="<?= $pengiriman['kode_rute'] ?>">
                        <input type="text" name="nama_wilayah" id="nama_wilayah" class="form-control" autocomplete="off"
                            value="<?= $pengiriman['kode_rute'] ?> - <?= $pengiriman['nama_wilayah'] ?>" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="rute" class="form-label">Nama Customer</label>
                        <input type="hidden" name="id_customer" id="id_customer"
                            value="<?= $pengiriman['id_customer'] ?>">
                        <input type="text" name="nama" id="nama" class="form-control" autocomplete="off"
                            value="<?= $pengiriman['nama_customer'] ?>" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="nama_penerima" class="form-label">Nama Penerima</label>
                        <input type="text" name="nama_penerima" id="nama_penerima" class="form-control"
                            value="<?= $pengiriman['nama_penerima'] ?>" readonly>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="pembayaran" class="form-label">Pembayaran</label>
                        <input type="text" name="pembayaran" id="pembayaran" class="form-control"
                            value="<?= ucwords($pengiriman['pembayaran']) ?>" readonly>
                    </div>
                </div>


                <div class="col-md-12">
                    <p class="p-0 m-0">Pemesanan Es</p>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="es_besar">
                            <label class="form-check-label user-select-none" for="es_besar">
                                Es Besar
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="qty_es_besar">Qty:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="qty_es_besar" name="qty_es_besar"
                                            min="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="harga_es_besar">Harga:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="harga_es_besar"
                                            name="harga_es_besar" min="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="total_es_besar">Total:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="total_es_besar"
                                            name="total_es_besar" min="0" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="es_kecil">
                            <label class="form-check-label user-select-none" for="es_kecil">
                                Es Kecil
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="qty_es_kecil">Qty:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="qty_es_kecil" name="qty_es_kecil"
                                            min="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="harga_es_kecil">Harga:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="harga_es_kecil"
                                            name="harga_es_kecil" min="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="total_es_kecil">Total:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="total_es_kecil"
                                            name="total_es_kecil" min="0" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="es_serut">
                            <label class="form-check-label user-select-none" for="es_serut">
                                Es Serut
                            </label>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="qty_es_serut">Qty:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="qty_es_serut" name="qty_es_serut"
                                            min="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="harga_es_serut">Harga:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="harga_es_serut"
                                            name="harga_es_serut" min="0" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="row align-items-center">
                                    <div class="col-12 col-md-auto"><label for="total_es_serut">Total:</label></div>
                                    <div class="col">
                                        <input type="number" class="form-control" id="total_es_serut"
                                            name="total_es_serut" min="0" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-12 d-flex justify-content-end">
                    <div class="form-group">
                        <label for="ttd_penerima" class="form-label">TTD Penerima</label>
                        <input type="hidden" name="ttd_penerima" id="ttd_penerima">
                        <div class="border rounded position-relative" style="height: 250px;">
                            <img src="<?= $pengiriman['ttd_penerima'] ?>" alt="TTD Penerima" class="w-100 h-100">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="card-footer bg-white">
            <div class="d-flex justify-content-end align-items-center">
                <button type="submit" class="btn btn-primary col-12 col-md-3">Update</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        var today = new Date().toISOString().split("T")[0];
        $('#tanggal').attr('min', today);

        initAutoComplete('kode_rute', 'nama_wilayah', 'list_rute');
        initAutoComplete('id_customer', 'nama', 'list_customer');


    // Safely pass decoded pemesanan JSON into JS
    var pemesanan = <?= json_encode(json_decode($pengiriman['pemesanan'] ?? '{}', true) ?: new stdClass()) ?>;

        // Cek dan aktifkan produk jika tersedia
        if ('besar' in pemesanan) {
            $('#es_besar').prop('checked', true); // Disable checkbox agar tidak bisa diuncheck
            $("#qty_es_besar").val(pemesanan.besar.qty).removeAttr('disabled');
            $("#harga_es_besar").val(pemesanan.besar.harga).removeAttr('disabled').prop('readonly', true);
            $("#total_es_besar").val(pemesanan.besar.total).removeAttr('disabled').prop('readonly', true);
        } else {
            $('#es_besar').prop('disabled', true); // Disable checkbox jika produk tidak ada
            $("#qty_es_besar").prop('disabled', true);
            $("#harga_es_besar").prop('disabled', true);
            $("#total_es_besar").prop('disabled', true);
        }

        if ('kecil' in pemesanan) {
            $('#es_kecil').prop('checked', true); // Disable checkbox agar tidak bisa diuncheck
            $("#qty_es_kecil").val(pemesanan.kecil.qty).removeAttr('disabled');
            $("#harga_es_kecil").val(pemesanan.kecil.harga).removeAttr('disabled').prop('readonly', true);
            $("#total_es_kecil").val(pemesanan.kecil.total).removeAttr('disabled').prop('readonly', true);
        } else {
            $('#es_kecil').prop('disabled', true); // Disable checkbox jika produk tidak ada
            $("#qty_es_kecil").prop('disabled', true);
            $("#harga_es_kecil").prop('disabled', true);
            $("#total_es_kecil").prop('disabled', true);
        }

        if ('serut' in pemesanan) {
            $('#es_serut').prop('checked', true); // Disable checkbox agar tidak bisa diuncheck
            $("#qty_es_serut").val(pemesanan.serut.qty).removeAttr('disabled');
            $("#harga_es_serut").val(pemesanan.serut.harga).removeAttr('disabled').prop('readonly', true);
            $("#total_es_serut").val(pemesanan.serut.total).removeAttr('disabled');
        } else {
            $('#es_serut').prop('disabled', true); // Disable checkbox jika produk tidak ada
            $("#qty_es_serut").prop('disabled', true);
            $("#harga_es_serut").prop('disabled', true);
            $("#total_es_serut").prop('disabled', true);
        }

        // Menangani perubahan checkbox untuk setiap produk jika checkbox aktif
        $('#es_besar').on('change', function() {
            if ($(this).is(':checked')) {
                $('#qty_es_besar').removeAttr('disabled');
                $('#harga_es_besar').removeAttr('disabled').prop('readonly', true);
                $('#total_es_besar').removeAttr('disabled').prop('readonly', true);
            } else {
                $('#qty_es_besar').prop('disabled', true);
                $('#harga_es_besar').prop('disabled', true).prop('readonly', true);
                $('#total_es_besar').removeAttr('readonly').prop('disabled', true);
            }
        });

        $('#es_kecil').on('change', function() {
            if ($(this).is(':checked')) {
                $('#qty_es_kecil').removeAttr('disabled');
                $('#harga_es_kecil').removeAttr('disabled').prop('readonly', true);
                $('#total_es_kecil').removeAttr('disabled').prop('readonly', true);
            } else {
                $('#qty_es_kecil').prop('disabled', true);
                $('#harga_es_kecil').prop('disabled', true).prop('readonly', true);
                $('#total_es_kecil').removeAttr('readonly').prop('disabled', true);
            }
        });

        $('#es_serut').on('change', function() {
            if ($(this).is(':checked')) {
                $('#qty_es_serut').removeAttr('disabled');
                $('#harga_es_serut').removeAttr('disabled').prop('readonly', true);
                $('#total_es_serut').removeAttr('disabled').prop('readonly', true);
            } else {
                $('#qty_es_serut').prop('disabled', true);
                $('#harga_es_serut').prop('disabled', true).prop('readonly', true);
                $('#total_es_serut').removeAttr('readonly').prop('disabled', true);
            }
        });


        $(document).on('change', '#qty_es_besar, #harga_es_besar', function() {
            var qty = parseFloat($('#qty_es_besar').val());
            var harga = parseFloat($('#harga_es_besar').val());
            var total = qty * harga;
            $('#total_es_besar').val(total);
        });

        $(document).on('change', '#qty_es_kecil, #harga_es_kecil', function() {
            var qty = parseFloat($('#qty_es_kecil').val());
            var harga = parseFloat($('#harga_es_kecil').val());
            var total = qty * harga;
            $('#total_es_kecil').val(total);
        });

        $(document).on('change', '#qty_es_serut, #harga_es_serut', function() {
            var qty = parseFloat($('#qty_es_serut').val());
            var harga = parseFloat($('#harga_es_serut').val());
            var total = qty * harga;
            $('#total_es_serut').val(total);
        });

    });
</script>
<?= $this->endSection() ?>