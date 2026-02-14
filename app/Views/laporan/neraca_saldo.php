<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-balance-scale mr-2"></i> Neraca Saldo</h1>
    
    <div class="d-flex">
        <a href="<?= base_url('laporan/cetak-neraca?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" target="_blank" class="btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak PDF
        </a>
        <a href="<?= base_url('laporan/export-neraca?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-sm btn-success shadow-sm">
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
                // PERBAIKAN: Gunakan Key Integer (1, 2, 3...) agar tipe data konsisten
                $bulanList = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                foreach($bulanList as $k => $v): ?>
                    <!-- PERBAIKAN: Casting (int) saat membandingkan -->
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
    <div class="card-header py-3 bg-white d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary"><?= $perusahaan ?> - Neraca Saldo</h6>
        <!-- PERBAIKAN: Casting (int) saat mengakses array key -->
        <span class="badge badge-primary"><?= $bulanList[(int)$filter_bln] ?? '-' ?> <?= $filter_thn ?></span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="10%" class="text-center">Kode Akun</th>
                        <th>Nama Akun</th>
                        <th width="10%" class="text-center">Ref</th>
                        <th width="20%" class="text-center bg-info text-white">Debit</th>
                        <th width="20%" class="text-center bg-success text-white">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalDebit = 0;
                    $totalKredit = 0;
                    
                    if(empty($neraca)): ?>
                        <tr><td colspan="5" class="text-center py-4 text-muted font-italic">Belum ada data transaksi untuk periode ini.</td></tr>
                    <?php else:
                        foreach($neraca as $row):
                            $saldo = $row['total_debit'] - $row['total_kredit'];
                            // Skip akun yang saldonya 0
                            if($saldo == 0) continue; 

                            $posisi = ($saldo > 0) ? 'Debit' : 'Kredit';
                            $nilai = abs($saldo);

                            if($posisi == 'Debit') $totalDebit += $nilai;
                            else $totalKredit += $nilai;
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold text-primary"><?= $row['kode_akun'] ?></td>
                        <td><?= $row['nama_akun'] ?></td>
                        <td class="text-center text-muted"><?= $row['posisi_saldo_normal'] ?></td>
                        
                        <!-- Kolom Debit -->
                        <td class="text-right">
                            <?= $posisi == 'Debit' ? number_format($nilai, 0, ',', '.') : '-' ?>
                        </td>
                        
                        <!-- Kolom Kredit -->
                        <td class="text-right">
                            <?= $posisi == 'Kredit' ? number_format($nilai, 0, ',', '.') : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold" style="font-size: 1.1em;">
                        <td colspan="3" class="text-right text-uppercase align-middle">Total Neraca Saldo :</td>
                        <td class="text-right text-white bg-info">
                            Rp <?= number_format($totalDebit, 0, ',', '.') ?>
                        </td>
                        <td class="text-right text-white bg-success">
                            Rp <?= number_format($totalKredit, 0, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Indikator Balance -->
        <div class="mt-3 text-center">
            <?php if($totalDebit == $totalKredit && $totalDebit > 0): ?>
                <div class="alert alert-success d-inline-block px-5 py-2 shadow-sm font-weight-bold">
                    <i class="fas fa-check-circle mr-2"></i> SEIMBANG (BALANCE)
                </div>
            <?php elseif($totalDebit > 0): ?>
                <div class="alert alert-danger d-inline-block px-5 py-2 shadow-sm font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> TIDAK SEIMBANG (Selisih: Rp <?= number_format(abs($totalDebit - $totalKredit), 0, ',', '.') ?>)
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
<?= $this->endSection(); ?>