<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-file-invoice-dollar mr-1"></i> Laporan Laba Rugi</h1>
    
    <div class="d-flex">
        <a href="<?= base_url('laporan/cetak-laba-rugi?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" target="_blank" class="btn btn-sm btn-secondary shadow-sm mr-2"><i class="fas fa-print fa-sm text-white-50"></i> Cetak PDF</a>
        <a href="<?= base_url('laporan/export-laba-rugi?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-sm btn-success shadow-sm"><i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel</a>
    </div>
</div>

<!-- Filter Periode (Sama seperti sebelumnya) -->
<div class="card shadow mb-4">
    <div class="card-body">
        <form action="" method="get" class="form-inline">
            <label class="mr-2">Periode:</label>
            <select name="bulan" class="form-control mr-2">
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
            <select name="tahun" class="form-control mr-2">
                <?php for($y=date('Y'); $y>=2020; $y--): ?>
                    <option value="<?= $y ?>" <?= $y==$filter_thn?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tampilkan</button>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 text-center bg-white">
        <h5 class="m-0 font-weight-bold text-dark text-uppercase"><?= $perusahaan ?></h5>
        <h6 class="m-0 font-weight-bold text-primary">Laporan Laba Rugi</h6>
        <!-- PERBAIKAN: Casting (int) saat mengakses array key -->
        <small class="text-muted">Periode: <?= $bulanList[(int)$filter_bln] ?? '-' ?> <?= $filter_thn ?></small>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-borderless table-sm" width="100%">
                
                <!-- 1. PENDAPATAN USAHA -->
                <tr>
                    <td colspan="3" class="font-weight-bold text-primary text-uppercase">Pendapatan Usaha</td>
                </tr>
                <?php foreach($penjualan as $p): 
                    $saldo = $p['total_kredit'] - $p['total_debit']; // Saldo Normal Kredit
                ?>
                <tr>
                    <td width="5%"></td>
                    <td><?= $p['nama_akun'] ?> <small class="text-muted">(<?= $p['kode_akun'] ?>)</small></td>
                    <td width="20%" class="text-right"><?= number_to_currency($saldo, 'IDR', 'id_ID', 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="border-top border-primary bg-light">
                    <td colspan="2" class="font-weight-bold pl-4">Total Penjualan Bersih</td>
                    <td class="font-weight-bold text-right text-primary"><?= number_to_currency($total_penjualan, 'IDR', 'id_ID', 2) ?></td>
                </tr>

                <tr><td colspan="3">&nbsp;</td></tr>

                <!-- 2. HARGA POKOK PENJUALAN (Cost of Goods Sold) -->
                <tr>
                    <td colspan="3" class="font-weight-bold text-danger text-uppercase">Harga Pokok Penjualan</td>
                </tr>
                <!-- Menampilkan Akun Pembelian di sini -->
                <?php foreach($pembelian as $p): 
                     $saldo = $p['total_debit'] - $p['total_kredit']; // Saldo Normal Debit
                ?>
                <tr>
                    <td width="5%"></td>
                    <td><?= $p['nama_akun'] ?></td>
                    <td class="text-right text-danger">(<?= number_to_currency($saldo, 'IDR', 'id_ID', 2) ?>)</td>
                </tr>
                <?php endforeach; ?>
                
                <tr class="border-top border-danger bg-light">
                    <td colspan="2" class="font-weight-bold pl-4">Total Harga Pokok Penjualan</td>
                    <td class="font-weight-bold text-right text-danger">(<?= number_to_currency($total_pembelian, 'IDR', 'id_ID', 2) ?>)</td>
                </tr>

                <!-- 3. LABA KOTOR -->
                <tr class="bg-gray-200">
                    <td colspan="2" class="font-weight-bold text-uppercase pl-3">Laba Kotor (Gross Profit)</td>
                    <td class="font-weight-bold text-right text-dark"><?= number_to_currency($laba_kotor, 'IDR', 'id_ID', 2) ?></td>
                </tr>

                <tr><td colspan="3">&nbsp;</td></tr>

                <!-- 4. BEBAN OPERASIONAL -->
                <tr>
                    <td colspan="3" class="font-weight-bold text-secondary text-uppercase">Beban Operasional</td>
                </tr>
                <?php foreach($beban as $b): 
                    $saldo = $b['total_debit'] - $b['total_kredit'];
                ?>
                <tr>
                    <td width="5%"></td>
                    <td><?= $b['nama_akun'] ?></td>
                    <td class="text-right"><?= number_to_currency($saldo, 'IDR', 'id_ID', 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="border-top">
                    <td colspan="2" class="font-weight-bold pl-4">Total Beban Operasional</td>
                    <td class="font-weight-bold text-right text-danger">(<?= number_to_currency($total_beban, 'IDR', 'id_ID', 2) ?>)</td>
                </tr>

                <!-- 5. PENDAPATAN LAIN-LAIN -->
                <?php if($total_other != 0): ?>
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr>
                    <td colspan="3" class="font-weight-bold text-success text-uppercase">Pendapatan/Beban Lainnya</td>
                </tr>
                <?php foreach($pendapatan_lain as $pl): 
                     $saldo = $pl['total_kredit'] - $pl['total_debit'];
                ?>
                <tr>
                    <td width="5%"></td>
                    <td><?= $pl['nama_akun'] ?></td>
                    <td class="text-right text-success"><?= number_to_currency($saldo, 'IDR', 'id_ID', 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>

                <!-- 6. LABA BERSIH -->
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr class="bg-primary text-white" style="font-size: 1.2em;">
                    <td colspan="2" class="font-weight-bold text-uppercase pl-3">Laba Bersih (Net Profit)</td>
                    <td class="font-weight-bold text-right"><?= number_to_currency($laba_bersih, 'IDR', 'id_ID', 2) ?></td>
                </tr>

            </table>
        </div>
    </div>
    <!-- Footer Note untuk Dosen -->
    <div class="card-footer py-3">
        <small class="text-muted font-italic">
            <i class="fas fa-info-circle"></i> 
            Catatan: Sistem menggunakan <b>Metode Pencatatan Periodik (Fisik)</b>. Nilai Persediaan Akhir harus disesuaikan melalui <b>Jurnal Penyesuaian</b> agar nilai HPP akurat.
        </small>
    </div>
</div>
<?= $this->endSection(); ?>