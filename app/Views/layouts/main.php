<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= base_url('assets/image/favicon.ico') ?>" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('assets/image/apple-touch-icon.png') ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= base_url('assets/image/favicon-32x32.png') ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url('assets/image/favicon-16x16.png') ?>">
    <link rel="icon" type="image/png" sizes="192x192" href="<?= base_url('assets/image/android-chrome-192x192.png') ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= base_url('assets/image/android-chrome-512x512.png') ?>">
    <link rel="manifest" href="<?= base_url('assets/image/site.webmanifest') ?>">


    <link rel="stylesheet" type="text/css" href="<?= base_url('vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css"
        href="<?= base_url('vendor/bootstrap-icons/font/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('vendor/apexcharts/apexcharts.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('vendor/sweetalert2/sweetalert2.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('vendor/datatables/datatables.min.css') ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?= base_url('vendor/jqsignature/css/jquery.signature.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/style.css') ?>">

</head>

<body class="bg-body-tertiary">
    <script src="<?= base_url('vendor/jquery/jquery.min.js') ?>" type="text/javascript"></script>

    <!-- Datatables -->
    <script src="<?= base_url('vendor/datatables/datatables.min.js') ?>" type="text/javascript"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

    <script src="<?= base_url('vendor/apexcharts/apexcharts.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('vendor/signature/signature_pad.min.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('vendor/sweetalert2/sweetalert2.all.min.js') ?>" type="text/javascript"></script>


    <?= $this->renderSection('main') ?>

    <script src="<?= base_url('assets/js/script.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/user.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/rute.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/customer.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/pengiriman.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/surat_jalan.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/product.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/product_category.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/stock.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/invoice.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/penerimaan.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/payment.js') ?>" type="text/javascript"></script>
    <script src="<?= base_url('assets/js/tables/tracking.js') ?>" type="text/javascript"></script>


</body>

</html>