<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-building mr-2"></i> Laporan Posisi Keuangan (Neraca)</h1>
    
    <div class="d-flex">
        <a href="<?= base_url('laporan/cetak-neraca-laporan?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" target="_blank" class="btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak PDF
        </a>
        <a href="<?= base_url('laporan/export-neraca-laporan?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-sm btn-success shadow-sm">
            <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
        </a>
    </div>
</div>

<!-- Filter Periode -->
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body py-3">
        <form action="" method="get" class="form-inline">
            <label class="mr-2 font-weight-bold text-gray-700">Periode:</label>
            <select name="bulan" class="form-control mr-2 shadow-sm border-0 bg-light">
                <?php 
                // PERBAIKAN: Gunakan Key Integer (1, 2, 3...) untuk konsistensi data
                $bulanList = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                foreach($bulanList as $k => $v): ?>
                    <!-- PERBAIKAN: Casting (int) -->
                    <option value="<?= $k ?>" <?= $k == (int)$filter_bln ? 'selected' : '' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tahun" class="form-control mr-2 shadow-sm border-0 bg-light">
                <?php for($y=date('Y'); $y>=2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y==$filter_thn?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-white text-center">
        <!-- HEADER DINAMIS -->
        <h5 class="m-0 font-weight-bold text-dark text-uppercase"><?= $perusahaan ?></h5>
        <h6 class="m-0 font-weight-bold text-primary">LAPORAN POSISI KEUANGAN</h6>
        <!-- PERBAIKAN: Casting (int) untuk array key -->
        <small class="text-muted">Per Akhir <?= $bulanList[(int)$filter_bln] ?? '-' ?> <?= $filter_thn ?></small>
    </div>
    <div class="card-body">
        <div class="row">
            <!-- KOLOM KIRI: ASET -->
            <div class="col-md-6">
                <table class="table table-sm table-borderless table-hover">
                    <tr class="bg-light border-bottom"><td colspan="2" class="font-weight-bold text-primary py-2">ASET</td></tr>
                    
                    <?php 
                    $totalAset = 0;
                    foreach($aset as $a): 
                        $saldo = $a['total_debit'] - $a['total_kredit'];
                        $totalAset += $saldo;
                    ?>
                    <tr>
                        <td class="pl-3"><?= $a['nama_akun'] ?></td>
                        <td class="text-right">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <tr class="border-top border-primary font-weight-bold bg-white text-primary" style="border-top-width: 2px !important;">
                        <td class="pl-3 py-2">TOTAL ASET</td>
                        <td class="text-right py-2">Rp <?= number_format($totalAset, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>

            <!-- KOLOM KANAN: LIABILITAS & EKUITAS -->
            <div class="col-md-6">
                <table class="table table-sm table-borderless table-hover">
                    <!-- LIABILITAS -->
                    <tr class="bg-light border-bottom"><td colspan="2" class="font-weight-bold text-danger py-2">LIABILITAS</td></tr>
                    <?php 
                    $totalLiabilitas = 0;
                    foreach($liabilitas as $l): 
                        $saldo = $l['total_kredit'] - $l['total_debit'];
                        $totalLiabilitas += $saldo;
                    ?>
                    <tr>
                        <td class="pl-3"><?= $l['nama_akun'] ?></td>
                        <td class="text-right">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="font-weight-bold text-danger border-top">
                        <td class="pl-3">Total Liabilitas</td>
                        <td class="text-right">Rp <?= number_format($totalLiabilitas, 0, ',', '.') ?></td>
                    </tr>

                    <tr><td>&nbsp;</td><td></td></tr>

                    <!-- EKUITAS -->
                    <tr class="bg-light border-bottom"><td colspan="2" class="font-weight-bold text-success py-2">EKUITAS</td></tr>
                    <?php 
                    $totalEkuitas = 0;
                    foreach($ekuitas as $e): 
                        $saldo = $e['total_kredit'] - $e['total_debit'];
                        
                        // Khusus Prive (Saldo Normal Debit), jadi pengurang
                        if(stripos($e['nama_akun'], 'Prive') !== false) {
                             $saldo = -abs($e['total_debit'] - $e['total_kredit']);
                        }
                        
                        $totalEkuitas += $saldo;
                    ?>
                    <tr>
                        <td class="pl-3"><?= $e['nama_akun'] ?></td>
                        <td class="text-right">
                            <?= $saldo < 0 ? '(Rp '.number_format(abs($saldo), 0, ',', '.').')' : 'Rp '.number_format($saldo, 0, ',', '.') ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <!-- Laba Bersih Tahun Berjalan -->
                    <tr>
                        <td class="pl-3 font-italic text-muted">Laba/Rugi Tahun Berjalan</td>
                        <td class="text-right font-italic <?= $laba_bersih >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $laba_bersih >= 0 ? 'Rp '.number_format($laba_bersih, 0, ',', '.') : '(Rp '.number_format(abs($laba_bersih), 0, ',', '.').')' ?>
                        </td>
                    </tr>
                    <?php $totalEkuitas += $laba_bersih; ?>

                    <tr class="font-weight-bold text-success border-top">
                        <td class="pl-3">Total Ekuitas</td>
                        <td class="text-right">Rp <?= number_format($totalEkuitas, 0, ',', '.') ?></td>
                    </tr>

                    <!-- TOTAL PASIVA -->
                    <tr class="border-top border-success font-weight-bold bg-white text-success mt-3" style="border-top-width: 2px !important;">
                        <td class="pl-3 py-2">TOTAL LIABILITAS & EKUITAS</td>
                        <td class="text-right py-2">Rp <?= number_format($totalLiabilitas + $totalEkuitas, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>