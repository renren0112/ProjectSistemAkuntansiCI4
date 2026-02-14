<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Laporan Arus Kas</h1>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body py-3">
        <form action="" method="get" class="form-inline justify-content-between">
            <div class="d-flex align-items-center">
                <label class="mr-2 font-weight-bold text-gray-700">Periode:</label>
                <select name="bulan" class="form-control mr-2 shadow-sm" style="min-width: 150px;">
                    <?php 
                    $months = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                    foreach($months as $k => $v): 
                    ?>
                        <option value="<?= $k ?>" <?= ($filter_bln == $k) ? 'selected' : '' ?>><?= $v ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" name="tahun" class="form-control mr-3 shadow-sm" value="<?= $filter_thn ?>" style="width: 100px;">
                <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-search mr-1"></i> Tampilkan</button>
            </div>
            
            <div class="dropdown mt-2 mt-sm-0">
                <button class="btn btn-success dropdown-toggle shadow-sm" type="button" id="dropdownExport" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-download mr-1"></i> Export Laporan
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownExport">
                    <div class="dropdown-header">Pilih Format:</div>
                    <a class="dropdown-item" target="_blank" href="<?= base_url("laporan/cetak-arus-kas?bulan=$filter_bln&tahun=$filter_thn") ?>">
                        <i class="fas fa-file-pdf text-danger mr-2"></i> Cetak PDF / Print
                    </a>
                    <a class="dropdown-item" target="_blank" href="<?= base_url("laporan/export-arus-kas?bulan=$filter_bln&tahun=$filter_thn") ?>">
                        <i class="fas fa-file-excel text-success mr-2"></i> Export Excel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<?php 
    $total_masuk = 0; 
    foreach($arus_masuk as $m) $total_masuk += $m['debit'];
    
    $total_keluar = 0; 
    foreach($arus_keluar as $k) $total_keluar += $k['kredit'];
    
    $kenaikan = $total_masuk - $total_keluar;
    $saldo_akhir = $saldo_awal + $kenaikan;
?>

<div class="row mb-4">
    <!-- Saldo Awal -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Saldo Awal</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-wallet fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Masuk -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Arus Masuk</div>
                        <div class="h5 mb-0 font-weight-bold text-success">Rp <?= number_format($total_masuk, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-arrow-down fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Keluar -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Arus Keluar</div>
                        <div class="h5 mb-0 font-weight-bold text-danger">Rp <?= number_format($total_keluar, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-arrow-up fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Saldo Akhir -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Saldo Akhir</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Rp <?= number_format($saldo_akhir, 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-coins fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Report -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-file-alt mr-2"></i> Rincian Arus Kas</h6>
        <span class="badge badge-light border">Periode: <?= $months[(int)$filter_bln] ?> <?= $filter_thn ?></span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" width="100%" cellspacing="0">
                <thead class="bg-gray-100 text-dark">
                    <tr>
                        <th width="60%" class="pl-4">Keterangan</th>
                        <th width="20%" class="text-center">Ref. Akun</th>
                        <th width="20%" class="text-right pr-4">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- HEADER SALDO AWAL -->
                    <tr class="bg-light">
                        <td colspan="2" class="font-weight-bold text-gray-800 pl-4">SALDO KAS AWAL</td>
                        <td class="text-right font-weight-bold pr-4">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></td>
                    </tr>

                    <!-- SECTION ARUS MASUK -->
                    <tr>
                        <td colspan="3" class="text-success font-weight-bold text-uppercase bg-light pl-4 py-3">
                            <i class="fas fa-plus-circle mr-2"></i> Arus Kas Masuk (Penerimaan)
                        </td>
                    </tr>
                    <?php if(empty($arus_masuk)): ?>
                        <tr><td colspan="3" class="text-center text-muted font-italic py-3">Tidak ada transaksi penerimaan kas.</td></tr>
                    <?php else: ?>
                        <?php foreach($arus_masuk as $m): ?>
                        <tr>
                            <td class="pl-5">
                                <?= $m['keterangan'] ?> 
                                <br><small class="text-muted"><?= date('d/m/Y', strtotime($m['tgl_jurnal'])) ?> - <?= $m['no_bukti'] ?></small>
                            </td>
                            <td class="text-center align-middle"><span class="badge badge-secondary"><?= $m['nama_akun'] ?></span></td>
                            <td class="text-right align-middle pr-4 text-gray-700">Rp <?= number_format($m['debit'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="bg-gray-100">
                        <td colspan="2" class="text-right font-weight-bold pr-3">Total Penerimaan:</td>
                        <td class="text-right font-weight-bold text-success pr-4">Rp <?= number_format($total_masuk, 0, ',', '.') ?></td>
                    </tr>

                    <!-- SECTION ARUS KELUAR -->
                    <tr>
                        <td colspan="3" class="text-danger font-weight-bold text-uppercase bg-light pl-4 py-3 mt-2">
                            <i class="fas fa-minus-circle mr-2"></i> Arus Kas Keluar (Pengeluaran)
                        </td>
                    </tr>
                    <?php if(empty($arus_keluar)): ?>
                        <tr><td colspan="3" class="text-center text-muted font-italic py-3">Tidak ada transaksi pengeluaran kas.</td></tr>
                    <?php else: ?>
                        <?php foreach($arus_keluar as $k): ?>
                        <tr>
                            <td class="pl-5">
                                <?= $k['keterangan'] ?>
                                <br><small class="text-muted"><?= date('d/m/Y', strtotime($k['tgl_jurnal'])) ?> - <?= $k['no_bukti'] ?></small>
                            </td>
                            <td class="text-center align-middle"><span class="badge badge-secondary"><?= $k['nama_akun'] ?></span></td>
                            <td class="text-right align-middle pr-4 text-gray-700">(Rp <?= number_format($k['kredit'], 0, ',', '.') ?>)</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <tr class="bg-gray-100">
                        <td colspan="2" class="text-right font-weight-bold pr-3">Total Pengeluaran:</td>
                        <td class="text-right font-weight-bold text-danger pr-4">(Rp <?= number_format($total_keluar, 0, ',', '.') ?>)</td>
                    </tr>

                    <!-- SECTION BERSIH -->
                    <tr>
                        <td colspan="2" class="text-right font-weight-bold pr-3 py-3">Kenaikan / (Penurunan) Kas Bersih:</td>
                        <td class="text-right font-weight-bold py-3 pr-4 <?= $kenaikan >= 0 ? 'text-success' : 'text-danger' ?>">
                            Rp <?= number_format($kenaikan, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-primary text-white">
                    <tr>
                        <td colspan="2" class="text-right font-weight-bold text-uppercase pr-3 py-3" style="font-size: 1.1em;">Saldo Kas Akhir</td>
                        <td class="text-right font-weight-bold pr-4 py-3" style="font-size: 1.1em;">
                            Rp <?= number_format($saldo_akhir, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>