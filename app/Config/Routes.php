<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Dashboard');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();

// =======================================================================
// ROUTES PUBLIK (Bisa diakses siapa saja tanpa login)
// =======================================================================
$routes->get('login', 'Auth::index');
$routes->post('auth/login', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('auth/setup', 'Auth::setup');

// =======================================================================
// ROUTES TERPROTEKSI (Wajib Login)
// =======================================================================
$routes->group('', ['filter' => 'auth'], function($routes) {
    
    // DASHBOARD (Semua User Boleh Akses)
    $routes->get('/', 'Dashboard::index');

    // ===================================================================
    // 1. AREA KHUSUS ADMIN & ACCOUNTING
    // (Kasir DILARANG masuk sini)
    // ===================================================================
    
    // Master Data Akun (COA)
    $routes->group('akun', ['filter' => 'role:admin,accounting'], function($routes) {
        $routes->get('/', 'Coa::index');
        $routes->get('create', 'Coa::create');
        $routes->post('store', 'Coa::store');
        $routes->get('edit/(:segment)', 'Coa::edit/$1');
        $routes->post('update/(:segment)', 'Coa::update/$1');
        $routes->post('delete/(:segment)', 'Coa::delete/$1');
        $routes->get('export', 'Coa::export');
        $routes->post('import', 'Coa::import');
    });

    // Saldo Awal (Hanya Admin & Accounting yang boleh set modal awal)
    $routes->group('', ['filter' => 'role:admin,accounting'], function($routes) {
        $routes->get('saldo-awal', 'SaldoAwal::index');
        $routes->post('saldoawal/store', 'SaldoAwal::store');
    });

    // Laporan Keuangan (Kasir Dilarang Lihat Laporan)
    $routes->group('laporan', ['filter' => 'role:admin,accounting'], function($routes) {
        $routes->get('buku-besar', 'Laporan::bukuBesar');
        $routes->get('cetak-buku-besar', 'Laporan::cetakBukuBesar');
        $routes->get('export-buku-besar', 'Laporan::exportBukuBesar');
        
        $routes->get('neraca-saldo', 'Laporan::neracaSaldo');
        $routes->get('cetak-neraca', 'Laporan::cetakNeraca');
        $routes->get('export-neraca', 'Laporan::exportNeracaExcel');
        
        $routes->get('neraca-lajur', 'Laporan::neracaLajur');
        $routes->get('cetak-neraca-lajur', 'Laporan::cetakNeracaLajur');
        $routes->get('export-neraca-lajur', 'Laporan::exportNeracaLajur');
        
        $routes->get('laba-rugi', 'Laporan::labaRugi');
        $routes->get('cetak-laba-rugi', 'Laporan::cetakLabaRugi');
        $routes->get('export-laba-rugi', 'Laporan::exportLabaRugi');
        
        $routes->get('perubahan-modal', 'Laporan::perubahanModal');
        $routes->get('cetak-perubahan-modal', 'Laporan::cetakPerubahanModal');
        $routes->get('export-perubahan-modal', 'Laporan::exportPerubahanModal');

        $routes->get('neraca', 'Laporan::neraca');
        $routes->get('cetak-neraca-laporan', 'Laporan::cetakNeracaLaporan');
        $routes->get('export-neraca-laporan', 'Laporan::exportNeracaLaporan');

        $routes->get('arus-kas', 'Laporan::arusKas');
        $routes->get('cetak-arus-kas', 'Laporan::cetakArusKas');
        $routes->get('export-arus-kas', 'Laporan::exportArusKas');
        
        $routes->get('neraca-saldo-penutup', 'Penutupan::laporanPenutup');
        $routes->get('cetak-neraca-penutup', 'Penutupan::cetak');
        $routes->get('export-neraca-penutup', 'Penutupan::export');
    });

    // Proses Tutup Buku (Closing)
    $routes->group('penutupan', ['filter' => 'role:admin,accounting'], function($routes) {
        $routes->get('/', 'Penutupan::index');
        $routes->post('proses', 'Penutupan::proses');
    });

    // Periode Akuntansi
    $routes->group('periode', ['filter' => 'role:admin,accounting'], function($routes) {
        $routes->get('/', 'PeriodeController::index');
        $routes->get('create', 'PeriodeController::create');
        $routes->post('store', 'PeriodeController::store');
        $routes->get('detail/(:num)', 'PeriodeController::detail/$1');
        $routes->get('open/(:num)', 'PeriodeController::open/$1');
    });

    // ===================================================================
    // 2. AREA KHUSUS ADMIN (Accounting & Kasir DILARANG Masuk)
    // ===================================================================
    
    // User Management (Hanya Admin)
    $routes->group('user', ['filter' => 'role:admin'], function($routes) {
        $routes->get('/', 'User::index');
        $routes->get('create', 'User::create');
        $routes->post('store', 'User::store');
        $routes->get('edit/(:num)', 'User::edit/$1');
        $routes->post('update/(:num)', 'User::update/$1');
        $routes->post('delete/(:num)', 'User::delete/$1');
    });

    // Pengaturan Perusahaan (Hanya Admin)
    $routes->group('pengaturan', ['filter' => 'role:admin'], function($routes) {
        $routes->get('/', 'Pengaturan::index');
        $routes->post('update', 'Pengaturan::update');
    });
     // Backup & Restore
    $routes->group('backup', ['filter' => 'role:admin'], function($routes) {
        $routes->get('/', 'Backup::index');
        $routes->get('download', 'Backup::download');
        $routes->post('restore', 'Backup::restore');
    });

     // LOG AKTIVITAS (AUDIT TRAIL)
    $routes->group('log', ['filter' => 'role:admin'], function($routes) {
        $routes->get('/', 'Log::index');
    });

    // Reset Database (Hanya Admin & di Development)
    $routes->get('reset-db', 'Reset::index', ['filter' => 'role:admin']);


    // ===================================================================
    // 3. AREA UMUM (ADMIN, ACCOUNTING, KASIR BOLEH MASUK)
    // ===================================================================
    
    // Transaksi (Semua role butuh akses ini untuk operasional)
    // Filter role:admin,accounting,kasir memastikan role 'gudang' dll tidak bisa masuk jika ada
    $routes->group('transaksi', ['filter' => 'role:admin,accounting,kasir'], function($routes) {
        $routes->get('/', 'Transaksi::index');           
        
        $routes->get('create', 'Transaksi::create');               
        $routes->get('create-penjualan', 'Transaksi::create_penjualan'); 
        $routes->get('create-pembelian', 'Transaksi::create_pembelian'); 
        
        $routes->get('create-penerimaan', 'Transaksi::create_penerimaan'); 
        $routes->get('create-pengeluaran', 'Transaksi::create_pengeluaran'); 
        
        $routes->get('penyesuaian', 'Transaksi::penyesuaian');     
        $routes->get('create-koreksi', 'Transaksi::create_koreksi'); 
        
        $routes->post('store', 'Transaksi::store');
        
        $routes->get('delete/(:num)', 'Transaksi::delete/$1');   
        $routes->get('restore/(:num)', 'Transaksi::restore/$1'); 
        $routes->get('purge/(:num)', 'Transaksi::purge/$1');     
        
        $routes->get('detail/(:num)', 'Transaksi::detail/$1');
        $routes->get('cetak/(:num)', 'Transaksi::cetak/$1');
        $routes->get('export/(:num)', 'Transaksi::export/$1');

        $routes->get('posting/(:num)', 'Transaksi::posting/$1');
        $routes->get('posting-otomatis', 'Transaksi::posting_otomatis');
    });

    // Barang (Jika ada, diakses semua)
    $routes->group('barang', function($routes) {
        $routes->get('/', 'Barang::index');
    });
});

if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}