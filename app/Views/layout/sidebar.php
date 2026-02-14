<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- SIDEBAR BRAND -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url() ?>">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-calculator"></i>
        </div>
        <div class="sidebar-brand-text mx-3">
            <div class="small text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Sistem Akuntansi</div>
            <?= isset($company['nama_perusahaan']) ? $company['nama_perusahaan'] : 'UD. MAJU' ?>
        </div>
    </a>

    <!-- INFO PERIODE AKTIF (WIDGET BARU) -->
    <?php 
        // Ambil Data Periode Aktif (Quick Query di View agar praktis, atau pass dari BaseController)
        $db = \Config\Database::connect();
        $periodeAktif = $db->table('periode_akuntansi')->where('status', 'OPEN')->get()->getRowArray();
    ?>
    <div class="sidebar-heading mt-2 text-center">
        <div class="bg-white text-primary rounded p-2 mx-2 shadow-sm">
            <small class="d-block font-weight-bold text-uppercase" style="font-size: 0.65rem;">Periode Aktif</small>
            <span class="h6 font-weight-bold">
                <?= $periodeAktif ? date('M Y', mktime(0,0,0,$periodeAktif['bulan'],1,$periodeAktif['tahun'])) : 'TIDAK ADA' ?>
            </span>
        </div>
    </div>

    <hr class="sidebar-divider my-3">

    <!-- DASHBOARD -->
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('/') ?>">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard Overview</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <!-- KELOMPOK: OPERASIONAL (TRANSAKSI) -->
    <div class="sidebar-heading">
        Operasional
    </div>

    <!-- Menu Transaksi (Dropdown Rapih) -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTrx" aria-expanded="true">
            <i class="fas fa-fw fa-cash-register"></i>
            <span>Transaksi Harian</span>
        </a>
        <div id="collapseTrx" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Input Jurnal:</h6>
                <a class="collapse-item" href="<?= base_url('transaksi/create-penjualan') ?>">Penjualan (Sales)</a>
                <a class="collapse-item" href="<?= base_url('transaksi/create-pembelian') ?>">Pembelian (Purchase)</a>
                <a class="collapse-item" href="<?= base_url('transaksi/create-penerimaan') ?>">Penerimaan Kas</a>
                <a class="collapse-item" href="<?= base_url('transaksi/create-pengeluaran') ?>">Pengeluaran Kas</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Lainnya:</h6>
                <a class="collapse-item" href="<?= base_url('transaksi/create') ?>">Jurnal Umum (Memo)</a>
                <a class="collapse-item" href="<?= base_url('transaksi/penyesuaian') ?>">Jurnal Penyesuaian</a>
                <a class="collapse-item" href="<?= base_url('transaksi') ?>"><b>Riwayat Jurnal</b></a>
            </div>
        </div>
    </li>

    <!-- KELOMPOK: LAPORAN (FINANCIAL) -->
    <div class="sidebar-heading mt-3">
        Financial Report
    </div>

    <!-- Menu Laporan -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLaporan" aria-expanded="true">
            <i class="fas fa-fw fa-file-invoice-dollar"></i>
            <span>Laporan Keuangan</span>
        </a>
        <div id="collapseLaporan" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Buku Besar & Neraca:</h6>
                <a class="collapse-item" href="<?= base_url('laporan/buku-besar') ?>">Buku Besar (Ledger)</a>
                <a class="collapse-item" href="<?= base_url('laporan/neraca-saldo') ?>">Neraca Saldo</a>
                <a class="collapse-item" href="<?= base_url('laporan/neraca-lajur') ?>">Neraca Lajur (Worksheet)</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Laporan Utama:</h6>
                <a class="collapse-item" href="<?= base_url('laporan/laba-rugi') ?>">Laba Rugi (P/L)</a>
                <a class="collapse-item" href="<?= base_url('laporan/perubahan-modal') ?>">Perubahan Modal</a>
                <a class="collapse-item" href="<?= base_url('laporan/neraca') ?>">Posisi Keuangan (BS)</a>
                <a class="collapse-item" href="<?= base_url('laporan/arus-kas') ?>">Arus Kas</a>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">Closing:</h6>
                <a class="collapse-item" href="<?= base_url('penutupan') ?>">Proses Tutup Buku</a>
                <a class="collapse-item" href="<?= base_url('laporan/neraca-saldo-penutup') ?>">NS Setelah Penutupan</a>
            </div>
        </div>
    </li>

    <hr class="sidebar-divider">

    <!-- KELOMPOK: KONFIGURASI (MASTER DATA) -->
    <?php if(session()->get('role') == 'admin' || session()->get('role') == 'accounting'): ?>
    <div class="sidebar-heading">
        Konfigurasi
    </div>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster" aria-expanded="true">
            <i class="fas fa-fw fa-cogs"></i>
            <span>Master Data</span>
        </a>
        <div id="collapseMaster" class="collapse" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Referensi:</h6>
                <a class="collapse-item" href="<?= base_url('akun') ?>">Chart of Accounts (COA)</a>
                <a class="collapse-item" href="<?= base_url('saldo-awal') ?>">Input Saldo Awal</a>
                <a class="collapse-item" href="<?= base_url('periode') ?>">Periode Akuntansi</a>
                
                <?php if(session()->get('role') == 'admin'): ?>
                <div class="collapse-divider"></div>
                <h6 class="collapse-header">System:</h6>
                <a class="collapse-item" href="<?= base_url('user') ?>">Manajemen User</a>
                <a class="collapse-item" href="<?= base_url('pengaturan') ?>">Identitas Perusahaan</a>
                <!-- Di bagian 'Sistem' atau 'Konfigurasi' -->
                <a class="collapse-item" href="<?= base_url('backup') ?>">Backup & Restore</a>
                <a class="collapse-item" href="<?= base_url('log') ?>"><b>Audit Trail (Log Aktivitas)</b></a>
                <?php endif; ?>
            </div>
        </div>
    </li>
    <?php endif; ?>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

    <!-- Tombol Logout -->
    <div class="text-center mt-3">
        <a href="<?= base_url('logout') ?>" class="btn btn-danger btn-sm shadow-sm" style="width: 80%;">
            <i class="fas fa-sign-out-alt mr-1"></i> Logout
        </a>
    </div>

</ul>