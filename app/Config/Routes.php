<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'DashboardController::index', ['filters' => ['auth', 'role:admin,distributor,produksi']]);

// Route untuk UserController
$routes->group('user', ['filters' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->get('detail/(:num)', 'UserController::detail/$1');
    $routes->match(['GET', 'POST'], 'create', 'UserController::create');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'UserController::edit/$1');
    $routes->post('delete/(:num)', 'UserController::delete/$1');
});

$routes->match(['GET', 'POST'], '/login', 'UserController::login');
$routes->match(['GET', 'POST'], '/forgot-password', 'UserController::forgotPassword');
$routes->match(['GET', 'POST'], '/reset-password/(:any)', 'UserController::resetPassword/$1');
$routes->get('/logout', 'UserController::logout', ['filter' => 'auth']);
// End UserController

// Export Rekap Penjualan (dari dashboard)
$routes->get('rekap-penjualan/export', 'DashboardController::rekapExport', ['filters' => ['auth','role:admin,distributor,produksi']]);

// Route untuk CustomerController
$routes->group('customer', ['filters' => ['auth', 'role:admin,produksi']], function ($routes) {
    $routes->get('/', 'CustomerController::index');
    $routes->get('detail/(:num)', 'CustomerController::detail/$1');
    $routes->get('detail-modal/(:num)', 'CustomerController::detailModal/$1');
    $routes->match(['GET','POST'], 'order/(:num)', 'CustomerController::order/$1');
    $routes->match(['GET','POST'], 'order-again/(:num)', 'CustomerController::orderAgain/$1');
    $routes->get('transaksi/(:num)', 'CustomerController::transactions/$1');
    $routes->match(['GET', 'POST'], 'create', 'CustomerController::create');
    $routes->match(['GET', 'POST'], 'edit/(:num)', 'CustomerController::edit/$1');
    $routes->post('delete/(:num)', 'CustomerController::delete/$1');
    $routes->get('get-customer-by-id/(:num)', 'CustomerController::getCustomerById/$1');
    $routes->get('get-customer-by-rute/(:any)', 'CustomerController::getCustomerByRute/$1');
});
// End CustomerController

// Product & Category
$routes->group('product', ['filters' => ['auth', 'role:admin,produksi']], function ($routes) {
    $routes->get('/', 'ProductController::index');
    $routes->get('detail/(:num)', 'ProductController::detail/$1');
    $routes->match(['GET','POST'], 'create', 'ProductController::create');
    $routes->match(['GET','POST'], 'edit/(:num)', 'ProductController::edit/$1');
    $routes->post('delete/(:num)', 'ProductController::delete/$1');
});
$routes->group('product-category', ['filters' => ['auth', 'role:admin,produksi']], function ($routes) {
    $routes->get('/', 'ProductCategoryController::index');
    $routes->get('detail/(:num)', 'ProductCategoryController::detail/$1');
    $routes->match(['GET','POST'], 'create', 'ProductCategoryController::create');
    $routes->match(['GET','POST'], 'edit/(:num)', 'ProductCategoryController::edit/$1');
    $routes->post('delete/(:num)', 'ProductCategoryController::delete/$1');
});

// Stock Management
$routes->group('stock', ['filters' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'StockController::index');
    $routes->get('detail/(:num)', 'StockController::detail/$1');
    $routes->match(['GET','POST'], 'transaction', 'StockController::transaction');
    $routes->get('report', 'StockController::report');
});

// Rute (Routes Management)
$routes->group('rute', ['filters' => ['auth', 'role:admin,distributor']], function ($routes) {
    $routes->get('/', 'RuteController::index');
    $routes->get('get-rute', 'RuteController::getRute');
    $routes->get('detail/(:num)', 'RuteController::detail/$1');
    $routes->match(['GET','POST'], 'create', 'RuteController::create');
    $routes->match(['GET','POST'], 'edit/(:num)', 'RuteController::edit/$1');
    $routes->post('delete/(:num)', 'RuteController::delete/$1');
});

// Surat Jalan
$routes->group('surat-jalan', ['filters' => ['auth', 'role:admin,distributor']], function ($routes) {
    $routes->get('/', 'SuratJalanController::index');
    $routes->get('detail/(:num)', 'SuratJalanController::detail/$1');
    $routes->match(['GET','POST'], 'create', 'SuratJalanController::create');
    $routes->post('create-quick/(:num)', 'SuratJalanController::createQuick/$1');
    $routes->match(['GET','POST'], 'edit/(:num)', 'SuratJalanController::edit/$1');
    $routes->post('delete/(:num)', 'SuratJalanController::delete/$1');
        $routes->get('print/(:num)', 'SuratJalanController::printSuratJalan/$1');
    $routes->get('print-batch/(:num)', 'SuratJalanController::printBatchByPengiriman/$1');
});

// Pengiriman (Delivery/Distribution)
$routes->group('pengiriman', ['filters' => ['auth', 'role:admin,distributor']], function ($routes) {
    $routes->get('/', 'PengirimanController::index');
    $routes->get('detail/(:num)', 'PengirimanController::detail/$1');
    $routes->match(['GET','POST'], 'create', 'PengirimanController::create');
    $routes->post('create-from-invoice/(:num)', 'PengirimanController::createFromInvoice/$1');
    $routes->match(['GET','POST'], 'edit/(:num)', 'PengirimanController::edit/$1');
    $routes->post('upload-surat-jalan/(:num)', 'PengirimanController::uploadSuratJalan/$1');
    $routes->post('upload-penerimaan/(:num)', 'PengirimanController::uploadPenerimaan/$1');
    // Per-invoice operations
    $routes->get('invoices/(:num)', 'PengirimanController::invoices/$1');
    $routes->post('uploadSuratJalanInvoice/(:num)', 'PengirimanController::uploadSuratJalanInvoice/$1');
    $routes->post('uploadPenerimaanInvoice/(:num)', 'PengirimanController::uploadPenerimaanInvoice/$1');
    $routes->post('delete/(:num)', 'PengirimanController::delete/$1');
    $routes->post('update-status/(:num)', 'PengirimanController::updateStatus/$1');
});

// Tracking
$routes->group('tracking', ['filters' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'ShipmentTrackingController::index');
    $routes->get('pengiriman/(:num)', 'ShipmentTrackingController::list/$1');
    $routes->post('pengiriman/(:num)', 'ShipmentTrackingController::create/$1');
});

// Penerimaan (Receipt)
$routes->group('penerimaan', ['filters' => ['auth', 'role:admin']], function ($routes) {
    $routes->get('/', 'PenerimaanController::index');
    $routes->match(['GET','POST'], 'create/(:num)', 'PenerimaanController::create/$1');
    $routes->post('verify/(:num)', 'PenerimaanController::verify/$1');
});

// Finance (Invoice & Payment)
$routes->group('invoice', ['filters' => ['auth', 'role:admin,produksi']], function ($routes) {
    $routes->get('/', 'InvoiceController::index');
    $routes->match(['GET','POST'], 'create-from-pengiriman/(:num)', 'InvoiceController::createFromPengiriman/$1');
    $routes->match(['GET','POST'], 'create-from-transaction/(:num)', 'InvoiceController::createFromTransaction/$1');
});
$routes->group('payment', ['filters' => ['auth', 'role:admin,produksi']], function ($routes) {
    $routes->get('invoice/(:num)', 'PaymentController::listByInvoice/$1');
    $routes->get('detail/(:num)', 'PaymentController::detail/$1');
    $routes->match(['GET','POST'], 'create/(:num)', 'PaymentController::create/$1');
});