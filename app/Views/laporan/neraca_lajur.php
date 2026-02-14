<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    /* --- STYLING KHUSUS WORKSHEET (MODERN LOOK) --- */
    .container-worksheet {
        position: relative;
        height: 600px; /* Tinggi fix agar bisa scroll vertikal */
        overflow: auto;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }

    /* Sticky Header: Judul kolom nempel di atas saat scroll */
    .table-worksheet thead th {
        position: sticky;
        top: 0;
        z-index: 10;
        vertical-align: middle !important;
        text-align: center;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 2px solid #ddd;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.1); /* Bayangan halus */
    }

    /* Top layer untuk header grup (NS, AJP, dll) */
    .table-worksheet thead tr:first-child th {
        top: 0;
        z-index: 20;
    }
    
    /* Bottom layer untuk header D/K */
    .table-worksheet thead tr:last-child th {
        top: 36px; /* Sesuaikan dengan tinggi baris pertama */
        z-index: 15;
    }

    .table-worksheet {
        font-size: 0.8rem;
        margin-bottom: 0;
    }

    .table-worksheet td {
        padding: 6px 8px;
        vertical-align: middle;
        border-color: #f0f0f0;
    }

    /* --- COLOR PALETTE PER SECTION --- */
    /* Kode & Nama Akun (Sticky Kiri - Opsional, saat ini statis) */
    .col-account { background-color: #fff; color: #4e73df; font-weight: 600; width: 250px; min-width: 200px; }

    /* Neraca Saldo (Putih/Abu) */
    .head-ns { background-color: #f8f9fc; color: #5a5c69; }
    .bg-ns { background-color: #ffffff; }

    /* Penyesuaian (Kuning Soft) */
    .head-ajp { background-color: #fff3cd; color: #856404; }
    .bg-ajp { background-color: #fffae6; }

    /* NS Disesuaikan (Biru Soft) */
    .head-nsd { background-color: #d1ecf1; color: #0c5460; }
    .bg-nsd { background-color: #eef9fb; }

    /* Laba Rugi (Merah Soft) */
    .head-lr { background-color: #f8d7da; color: #721c24; }
    .bg-lr { background-color: #fff2f3; }

    /* Neraca (Hijau Soft) */
    .head-nr { background-color: #d4edda; color: #155724; }
    .bg-nr { background-color: #f0fff4; }

    /* Highlight Baris saat Hover */
    .table-worksheet tbody tr:hover td {
        background-color: #eaecf4 !important; /* Warna hover abu kebiruan SB Admin */
        color: #2e59d9;
        font-weight: 600;
        cursor: default;
    }

    /* Typography Angka */
    .num-cell {
        text-align: right;
        font-family: 'Consolas', 'Monaco', monospace;
        letter-spacing: -0.5px;
    }
    .zero-val { color: #d1d3e2; } /* Warna abu pudar untuk nilai 0 */

    /* Footer Totals */
    .tfoot-sticky {
        position: sticky;
        bottom: 0;
        z-index: 20;
    }
    .grand-total td {
        background-color: #2c3e50; /* Dark Blue */
        color: #fff;
        font-weight: bold;
        border-top: 2px solid #fff;
    }
    .balance-row td {
        background-color: #eaecf4;
        font-weight: bold;
        color: #333;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Neraca Lajur (Worksheet)</h1>
    <div class="btn-group">
        <a href="<?= base_url('laporan/cetak-neraca-lajur?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" target="_blank" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak Preview
        </a>
        <a href="<?= base_url('laporan/export-neraca-lajur?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4 border-left-info">
    <div class="card-body py-3">
        <form action="" method="get" class="form-inline">
            <label class="mr-2 font-weight-bold text-gray-700">Periode Laporan:</label>
            <div class="input-group mr-2">
                <div class="input-group-prepend">
                    <span class="input-group-text bg-white"><i class="far fa-calendar-alt"></i></span>
                </div>
                <select name="bulan" class="form-control custom-select">
                    <?php for($m=1; $m<=12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($filter_bln == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <input type="number" name="tahun" class="form-control mr-3" value="<?= $filter_thn ?>" style="width: 100px;">
            <button type="submit" class="btn btn-info shadow-sm px-4">
                <i class="fas fa-filter mr-1"></i> Tampilkan
            </button>
        </form>
    </div>
</div>

<!-- Main Table Container -->
<div class="card shadow mb-4">
    <div class="card-body p-0"> <!-- Padding 0 agar tabel full width -->
        <div class="container-worksheet">
            <table class="table table-bordered table-worksheet mb-0" width="100%" cellspacing="0">
                <thead>
                    <!-- HEADER BARIS 1: Grup Kolom -->
                    <tr>
                        <th rowspan="2" class="col-account bg-white" style="z-index: 30; left: 0;">Kode Akun</th>
                        <th rowspan="2" class="bg-white" style="min-width: 200px;">Nama Akun</th>
                        <th colspan="2" class="head-ns">Neraca Saldo</th>
                        <th colspan="2" class="head-ajp">Penyesuaian (AJP)</th>
                        <th colspan="2" class="head-nsd">NS Disesuaikan</th>
                        <th colspan="2" class="head-lr">Laba Rugi</th>
                        <th colspan="2" class="head-nr">Posisi Keuangan</th>
                    </tr>
                    <!-- HEADER BARIS 2: D/K -->
                    <tr>
                        <th class="head-ns">Debit</th> <th class="head-ns">Kredit</th>
                        <th class="head-ajp">Debit</th> <th class="head-ajp">Kredit</th>
                        <th class="head-nsd">Debit</th> <th class="head-nsd">Kredit</th>
                        <th class="head-lr">Debit</th>  <th class="head-lr">Kredit</th>
                        <th class="head-nr">Debit</th>  <th class="head-nr">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        // Inisialisasi Total Kolom
                        $t = array_fill(0, 10, 0); 

                        // PERBAIKAN: Fungsi didefinisikan DI LUAR loop, dan diberi nama berbeda (render_cell)
                        // Menggunakan function_exists untuk mencegah redeclare jika view diload ulang
                        if (!function_exists('render_cell')) {
                            function render_cell($val, $bg_class) {
                                $cls = ($val == 0) ? 'zero-val' : 'text-gray-900 font-weight-bold';
                                $txt = ($val == 0) ? '-' : number_format($val, 0, ',', '.');
                                echo "<td class='num-cell $bg_class $cls'>$txt</td>";
                            }
                        }
                    ?>
                    <?php foreach($worksheet as $row): ?>
                    <?php 
                        // Update Total Vertical
                        $t[0] += $row['ns_d']; $t[1] += $row['ns_k'];
                        $t[2] += $row['ajp_d']; $t[3] += $row['ajp_k'];
                        $t[4] += $row['nsd_d']; $t[5] += $row['nsd_k'];
                        $t[6] += $row['lr_d']; $t[7] += $row['lr_k'];
                        $t[8] += $row['nr_d']; $t[9] += $row['nr_k'];
                    ?>
                    <tr>
                        <!-- Kolom Akun -->
                        <td class="text-center font-weight-bold text-primary"><?= $row['kode'] ?></td>
                        <td class="text-nowrap"><?= $row['nama'] ?></td>

                        <!-- Render Kolom Data dengan Background Class -->
                        <!-- Menggunakan fungsi render_cell yang baru -->
                        <?php render_cell($row['ns_d'], 'bg-ns'); ?>
                        <?php render_cell($row['ns_k'], 'bg-ns'); ?>
                        
                        <?php render_cell($row['ajp_d'], 'bg-ajp'); ?>
                        <?php render_cell($row['ajp_k'], 'bg-ajp'); ?>

                        <?php render_cell($row['nsd_d'], 'bg-nsd'); ?>
                        <?php render_cell($row['nsd_k'], 'bg-nsd'); ?>

                        <?php render_cell($row['lr_d'], 'bg-lr'); ?>
                        <?php render_cell($row['lr_k'], 'bg-lr'); ?>

                        <?php render_cell($row['nr_d'], 'bg-nr'); ?>
                        <?php render_cell($row['nr_k'], 'bg-nr'); ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                
                <!-- FOOTER TOTAL -->
                <tfoot>
                    <!-- BARIS TOTAL UTAMA -->
                    <tr class="grand-total">
                        <td colspan="2" class="text-right">TOTAL</td>
                        <?php for($i=0; $i<10; $i++): ?>
                            <td class="num-cell"><?= number_format($t[$i], 0, ',', '.') ?></td>
                        <?php endfor; ?>
                    </tr>

                    <!-- PERHITUNGAN LABA / RUGI -->
                    <?php 
                        $selisih_lr = $t[7] - $t[6]; // Kredit - Debit
                        $laba = ($selisih_lr >= 0) ? $selisih_lr : 0;
                        $rugi = ($selisih_lr < 0) ? abs($selisih_lr) : 0;
                    ?>
                    <tr class="balance-row">
                        <td colspan="2" class="text-right font-italic text-gray-600">Laba / (Rugi) Bersih</td>
                        
                        <!-- Kosongkan NS, AJP, NSD -->
                        <td colspan="6" class="bg-light"></td>

                        <!-- Kolom Laba Rugi (Menyeimbangkan) -->
                        <td class="num-cell text-success"><?= $laba > 0 ? number_format($laba, 0, ',', '.') : '-' ?></td>
                        <td class="num-cell text-danger"><?= $rugi > 0 ? '('.number_format($rugi, 0, ',', '.').')' : '-' ?></td>
                        
                        <!-- Kolom Neraca (Menyeimbangkan Kebalikan) -->
                        <td class="num-cell text-danger"><?= $rugi > 0 ? '('.number_format($rugi, 0, ',', '.').')' : '-' ?></td>
                        <td class="num-cell text-success"><?= $laba > 0 ? number_format($laba, 0, ',', '.') : '-' ?></td>
                    </tr>

                    <!-- BALANCE CHECK AKHIR -->
                    <tr class="grand-total" style="background-color: #2c3e50;">
                        <td colspan="2" class="text-right">BALANCE AKHIR</td>
                        
                        <!-- Kosongkan NS, AJP, NSD -->
                        <td colspan="6" class="bg-gray-200 border-0"></td>
                        
                        <!-- Total LR Akhir -->
                        <td class="num-cell text-warning"><?= number_format($t[6] + $laba, 0, ',', '.') ?></td>
                        <td class="num-cell text-warning"><?= number_format($t[7] + $rugi, 0, ',', '.') ?></td>
                        
                        <!-- Total Neraca Akhir -->
                        <td class="num-cell text-warning"><?= number_format($t[8] + $rugi, 0, ',', '.') ?></td>
                        <td class="num-cell text-warning"><?= number_format($t[9] + $laba, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="card-footer py-3">
        <div class="small text-muted">
            <i class="fas fa-info-circle mr-1"></i>
            Baris berwarna kuning menandakan penyesuaian (AJP). Laba Bersih dihitung dari selisih Debit/Kredit pada kolom Laba Rugi.
        </div>
    </div>
</div>

<?= $this->endSection(); ?>