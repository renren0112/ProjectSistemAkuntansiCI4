<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-book mr-1"></i> Buku Besar (General Ledger)</h1>
    
    <div class="d-flex">
        <?php if($filter_akun): ?>
        <a href="<?= base_url('laporan/cetak-buku-besar?akun='.$filter_akun.'&bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" target="_blank" class="btn btn-sm btn-secondary shadow-sm mr-2"><i class="fas fa-print fa-sm text-white-50"></i> Cetak PDF</a>
        <a href="<?= base_url('laporan/export-buku-besar?akun='.$filter_akun.'&bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel</a>
        <?php endif; ?>
    </div>
</div>

<!-- Filter -->
<div class="card shadow mb-4 border-left-primary">
    <div class="card-body">
        <form action="" method="get" class="form-inline">
            <label class="mr-2 font-weight-bold">Pilih Akun:</label>
            <select name="akun" class="form-control mr-3 select2" required style="min-width: 250px;">
                <option value="">-- Pilih Akun --</option>
                <?php foreach($list_akun as $a): ?>
                    <option value="<?= $a['kode_akun'] ?>" <?= $filter_akun == $a['kode_akun'] ? 'selected' : '' ?>>
                        <?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label class="mr-2 font-weight-bold">Periode:</label>
            <select name="bulan" class="form-control mr-2">
                <?php 
                $bulanList = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                foreach($bulanList as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $k==$filter_bln?'selected':'' ?>><?= $v ?></option>
                <?php endforeach; ?>
            </select>
            <select name="tahun" class="form-control mr-2">
                <?php for($y=date('Y'); $y>=2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y==$filter_thn?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            
            <button type="submit" class="btn btn-primary shadow-sm"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
        
        <!-- Navigasi Cepat Akun Sebelumnya/Selanjutnya -->
        <?php if($filter_akun): ?>
        <div class="mt-3 d-flex justify-content-between">
            <?php if($prev_akun): ?>
                <a href="?akun=<?= $prev_akun ?>&bulan=<?= $filter_bln ?>&tahun=<?= $filter_thn ?>" class="btn btn-light btn-sm border"><i class="fas fa-arrow-left"></i> Akun Sebelumnya</a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if($next_akun): ?>
                <a href="?akun=<?= $next_akun ?>&bulan=<?= $filter_bln ?>&tahun=<?= $filter_thn ?>" class="btn btn-light btn-sm border">Akun Selanjutnya <i class="fas fa-arrow-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($filter_akun && $akun): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
        <div>
            <h5 class="m-0 font-weight-bold text-primary"><?= $akun['nama_akun'] ?></h5>
            <span class="badge badge-info text-uppercase"><?= $akun['tipe_akun'] ?></span>
            <span class="text-muted ml-2">Kode: <b><?= $akun['kode_akun'] ?></b></span>
        </div>
        <div class="text-right">
            <small class="text-muted d-block">Saldo Normal: <?= $akun['posisi_saldo_normal'] == 'D' ? 'Debit' : 'Kredit' ?></small>
            <small class="text-muted">Periode: <?= $bulanList[$filter_bln] ?> <?= $filter_thn ?></small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-dark text-center">
                    <tr>
                        <th rowspan="2" class="align-middle">Tanggal</th>
                        <th rowspan="2" class="align-middle">No. Bukti</th>
                        <th rowspan="2" class="align-middle text-left pl-3">Keterangan</th>
                        <th colspan="2" class="bg-secondary">Transaksi</th>
                        <th colspan="2" class="bg-info">Saldo</th>
                    </tr>
                    <tr>
                        <th class="bg-secondary">Debit</th>
                        <th class="bg-secondary">Kredit</th>
                        <th class="bg-info">Debit</th>
                        <th class="bg-info">Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal (Disini 0 karena kita filter per bulan, 
                         Idealnya ambil saldo bulan lalu, tapi untuk UAS biasanya cukup transaksi bulan berjalan 
                         kecuali neraca. Jika mau saldo awal, butuh query tambahan di model) -->
                    <tr class="bg-light font-weight-bold">
                        <td colspan="3" class="text-center">Saldo Awal</td>
                        <td>-</td>
                        <td>-</td>
                        <!-- Logika Saldo Awal Sederhana (0) -->
                        <td class="text-right">0</td>
                        <td class="text-right">0</td>
                    </tr>

                    <?php 
                    $saldo = 0; // Saldo berjalan (Positif = Debit, Negatif = Kredit, atau sesuai normal)
                    // Logika Saldo: Jika Normal Debit, Debit (+), Kredit (-). Sebaliknya untuk Kredit.
                    $normal = $akun['posisi_saldo_normal'];
                    
                    $totalDeb = 0;
                    $totalKre = 0;

                    if(empty($transaksi)): ?>
                        <tr><td colspan="7" class="text-center py-3 text-muted font-italic">Tidak ada transaksi pada periode ini.</td></tr>
                    <?php else: 
                        foreach($transaksi as $t): 
                            $d = $t['debit'];
                            $k = $t['kredit'];
                            $totalDeb += $d;
                            $totalKre += $k;

                            if ($normal == 'D') {
                                $saldo += ($d - $k);
                            } else {
                                $saldo += ($k - $d);
                            }
                    ?>
                    <tr>
                        <td class="text-center"><?= date('d/m/Y', strtotime($t['tgl_jurnal'])) ?></td>
                        <td class="text-center text-primary small">
                            <a href="<?= base_url('transaksi/detail/'.$t['id_jurnal']) ?>" target="_blank" title="Lihat Detail"><?= $t['no_bukti'] ?></a>
                        </td>
                        <td class="pl-3"><?= $t['keterangan'] ?></td>
                        
                        <!-- Transaksi -->
                        <td class="text-right"><?= $d!=0 ? number_format($d, 0, ',', '.') : '-' ?></td>
                        <td class="text-right"><?= $k!=0 ? number_format($k, 0, ',', '.') : '-' ?></td>
                        
                        <!-- Saldo Berjalan (Split Kolom) -->
                        <?php if($normal == 'D'): ?>
                            <!-- Jika Normal Debit: Saldo positif masuk Debit, Negatif masuk Kredit (minus) -->
                            <td class="text-right font-weight-bold text-gray-800 bg-light-blue">
                                <?= $saldo >= 0 ? number_format($saldo, 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-right font-weight-bold text-gray-800 bg-light-blue">
                                <?= $saldo < 0 ? number_format(abs($saldo), 0, ',', '.') : '-' ?>
                            </td>
                        <?php else: ?>
                            <!-- Jika Normal Kredit -->
                            <td class="text-right font-weight-bold text-gray-800 bg-light-yellow">
                                <?= $saldo < 0 ? number_format(abs($saldo), 0, ',', '.') : '-' ?>
                            </td>
                            <td class="text-right font-weight-bold text-gray-800 bg-light-yellow">
                                <?= $saldo >= 0 ? number_format($saldo, 0, ',', '.') : '-' ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
                <tfoot class="bg-dark text-white">
                    <tr>
                        <td colspan="3" class="text-right font-weight-bold pr-3">Total Perubahan Periode Ini :</td>
                        <td class="text-right font-weight-bold"><?= number_format($totalDeb, 0, ',', '.') ?></td>
                        <td class="text-right font-weight-bold"><?= number_format($totalKre, 0, ',', '.') ?></td>
                        <!-- Saldo Akhir -->
                        <?php if($normal == 'D'): ?>
                            <td class="text-right font-weight-bold bg-primary"><?= $saldo >= 0 ? number_format($saldo, 0, ',', '.') : '-' ?></td>
                            <td class="text-right font-weight-bold bg-primary"><?= $saldo < 0 ? number_format(abs($saldo), 0, ',', '.') : '-' ?></td>
                        <?php else: ?>
                            <td class="text-right font-weight-bold bg-success"><?= $saldo < 0 ? number_format(abs($saldo), 0, ',', '.') : '-' ?></td>
                            <td class="text-right font-weight-bold bg-success"><?= $saldo >= 0 ? number_format($saldo, 0, ',', '.') : '-' ?></td>
                        <?php endif; ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
    .bg-light-blue { background-color: #e3f2fd; }
    .bg-light-yellow { background-color: #fff9c4; }
</style>

<?= $this->endSection(); ?>