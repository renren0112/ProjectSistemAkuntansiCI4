<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-chart-line mr-2"></i> Laporan Perubahan Modal</h1>
    
    <div class="d-flex">
        <a href="<?= base_url('laporan/cetak-perubahan-modal?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" target="_blank" class="btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak PDF
        </a>
        <a href="<?= base_url('laporan/export-perubahan-modal?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-sm btn-success shadow-sm">
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
                $bulanList = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'];
                foreach($bulanList as $k => $v): ?>
                    <option value="<?= $k ?>" <?= $k==$filter_bln?'selected':'' ?>><?= $v ?></option>
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
        <h5 class="m-0 font-weight-bold text-dark text-uppercase"><?= $perusahaan ?></h5>
        <h6 class="m-0 font-weight-bold text-primary">Laporan Perubahan Modal</h6>
        <small class="text-muted">Periode Berakhir: <?= date('d F Y', mktime(0, 0, 0, $filter_bln + 1, 0, $filter_thn)) ?></small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-hover" width="100%">
                
                <?php 
                // 1. MODAL AWAL
                // Asumsi: Akun Modal (311) adalah Modal Awal sebelum Laba/Rugi periode berjalan
                $modalAwal = 0;
                $prive = 0;
                $penambahanModal = 0; // Jika ada setoran modal tambahan bulan ini (bisa dilacak via jurnal jika mau detail)

                foreach($ekuitas as $e) {
                    // Ekuitas Saldo Normal Kredit
                    $saldo = $e['total_kredit'] - $e['total_debit'];
                    
                    if (stripos($e['nama_akun'], 'Modal') !== false) {
                        $modalAwal += $saldo;
                    } elseif (stripos($e['nama_akun'], 'Prive') !== false) {
                        // Prive saldo normal Debit (mengurangi modal)
                        // Karena di getLaporanByTipe kita hitung Kredit - Debit, maka Prive akan otomatis negatif di sini jika ada saldo debit.
                        // Tapi mari kita pastikan nilainya Absolut untuk ditampilkan sebagai pengurang.
                        $prive += abs($e['total_debit'] - $e['total_kredit']);
                    }
                }
                ?>

                <!-- MODAL AWAL -->
                <tr class="bg-light">
                    <td colspan="2" class="font-weight-bold pl-4 py-3">Modal Awal (<?= date('F Y', mktime(0, 0, 0, $filter_bln, 1, $filter_thn)) ?>)</td>
                    <td class="text-right font-weight-bold py-3 text-dark" style="font-size: 1.1em;">
                        Rp <?= number_format($modalAwal, 0, ',', '.') ?>
                    </td>
                </tr>

                <!-- PENAMBAHAN / PENGURANGAN -->
                <tr>
                    <td colspan="3" class="font-weight-bold text-primary text-uppercase pt-4 pl-3">Penambahan / (Pengurangan) Modal</td>
                </tr>
                
                <!-- Laba / Rugi Bersih -->
                <tr>
                    <td width="5%"></td>
                    <td>
                        <?= $laba_bersih >= 0 ? 'Laba Bersih Tahun Berjalan' : 'Rugi Bersih Tahun Berjalan' ?>
                    </td>
                    <td class="text-right <?= $laba_bersih >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= $laba_bersih >= 0 ? 'Rp '.number_format($laba_bersih, 0, ',', '.') : '(Rp '.number_format(abs($laba_bersih), 0, ',', '.').')' ?>
                    </td>
                </tr>

                <!-- Prive -->
                <?php if($prive > 0): ?>
                <tr>
                    <td></td>
                    <td>Prive / Penarikan Pribadi</td>
                    <td class="text-right text-danger">(Rp <?= number_format($prive, 0, ',', '.') ?>)</td>
                </tr>
                <?php endif; ?>

                <!-- Setoran Modal (Optional Logic) -->
                <!-- Jika ingin menampilkan setoran modal tambahan terpisah, butuh query jurnal detail. 
                     Untuk simplifikasi UAS, diasumsikan Modal Awal sudah termasuk setoran. -->

                <!-- Kenaikan / Penurunan Modal -->
                <?php 
                    $perubahan = $laba_bersih - $prive;
                    $modalAkhir = $modalAwal + $perubahan;
                ?>
                <tr class="border-top border-bottom font-italic text-muted">
                    <td colspan="2" class="pl-5">
                        <?= $perubahan >= 0 ? 'Kenaikan Modal Bersih' : 'Penurunan Modal Bersih' ?>
                    </td>
                    <td class="text-right font-weight-bold">
                        <?= $perubahan >= 0 ? 'Rp '.number_format($perubahan, 0, ',', '.') : '(Rp '.number_format(abs($perubahan), 0, ',', '.').')' ?>
                    </td>
                </tr>

                <!-- MODAL AKHIR -->
                <tr><td colspan="3" class="py-2"></td></tr>
                <tr class="bg-primary text-white" style="font-size: 1.2em;">
                    <td colspan="2" class="font-weight-bold text-uppercase pl-4 py-3">Modal Akhir (<?= date('d F Y', mktime(0, 0, 0, $filter_bln + 1, 0, $filter_thn)) ?>)</td>
                    <td class="text-right font-weight-bold py-3">
                        Rp <?= number_format($modalAkhir, 0, ',', '.') ?>
                    </td>
                </tr>

            </table>
        </div>
    </div>
    <div class="card-footer py-3 text-center">
        <small class="text-muted font-italic">
            * Laporan ini menunjukkan perubahan ekuitas pemilik selama periode berjalan.
        </small>
    </div>
</div>
<?= $this->endSection(); ?>