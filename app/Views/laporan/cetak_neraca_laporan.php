<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neraca - <?= $filter_bln ?>/<?= $filter_thn ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; print-color-adjust: exact; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        .content-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .content-table td { padding: 5px; vertical-align: top; }
        
        .section-title { font-weight: bold; text-transform: uppercase; padding-top: 10px; text-decoration: underline; }
        .sub-total { font-weight: bold; border-top: 1px solid #000; }
        .grand-total { font-weight: bold; border-top: 1px solid #000; border-bottom: 3px double #000; padding: 10px 0; }
        
        .text-right { text-align: right; }
        
        /* Navigasi Tombol */
        .nav-buttons { text-align: center; margin-bottom: 20px; padding: 10px; background: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; margin: 0 5px; color: white; text-decoration: none; display: inline-block; font-family: sans-serif; }
        .btn-print { background-color: #4e73df; }
        .btn-close { background-color: #e74a3b; }
        .btn-excel { background-color: #1cc88a; }
        .btn:hover { opacity: 0.9; }

        @media print {
            body { margin: 0; padding: 0; }
            .container { width: 100%; max-width: 100%; border: none; }
            .no-print, .nav-buttons { display: none !important; }
        }
    </style>
</head>
<body <?php if(!isset($is_excel)) echo 'onload="window.print()"'; ?>>

    <!-- TOMBOL NAVIGASI -->
    <?php if(!isset($is_excel)): ?>
    <div class="nav-buttons no-print">
        <button onclick="window.print()" class="btn btn-print">🖨️ Preview Cetak</button>
        <a href="<?= base_url('laporan/export-neraca-laporan?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-excel">📊 Export Excel</a>
        <button onclick="window.close()" class="btn btn-close">❌ Tutup</button>
    </div>
    <?php endif; ?>

    <div class="container">
        <!-- KOP SURAT DINAMIS -->
        <div class="header">
            <h2><?= $perusahaan ?? 'PERUSAHAAN ANDA' ?></h2>
            <p><?= $alamat ?? 'Alamat belum diatur' ?></p>
            <p>Telp: <?= $telepon ?? '-' ?> | Email: <?= $email ?? '-' ?></p>
            <br>
            <h3 style="margin: 5px 0; border: 1px solid #333; display: inline-block; padding: 5px 15px;">LAPORAN POSISI KEUANGAN</h3>
            <p>Per <?= date('d F Y', mktime(0, 0, 0, $filter_bln + 1, 0, $filter_thn)) ?></p>
        </div>

        <table width="100%">
            <tr>
                <!-- SISI KIRI (ASET) -->
                <td width="50%" valign="top" style="padding-right: 20px;">
                    <table class="content-table">
                        <tr><td colspan="2" class="section-title">ASET</td></tr>
                        
                        <?php 
                        $totalAset = 0;
                        foreach($aset as $a): 
                            $saldo = $a['total_debit'] - $a['total_kredit'];
                            $totalAset += $saldo;
                        ?>
                        <tr>
                            <td><?= $a['nama_akun'] ?></td>
                            <td class="text-right">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td class="grand-total">TOTAL ASET</td>
                            <td class="text-right grand-total">Rp <?= number_format($totalAset, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </td>

                <!-- SISI KANAN (LIABILITAS & EKUITAS) -->
                <td width="50%" valign="top" style="padding-left: 20px;">
                    <table class="content-table">
                        <!-- LIABILITAS -->
                        <tr><td colspan="2" class="section-title">LIABILITAS</td></tr>
                        <?php 
                        $totalLiabilitas = 0;
                        foreach($liabilitas as $l): 
                            $saldo = $l['total_kredit'] - $l['total_debit'];
                            $totalLiabilitas += $saldo;
                        ?>
                        <tr>
                            <td><?= $l['nama_akun'] ?></td>
                            <td class="text-right">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td class="sub-total">Total Liabilitas</td>
                            <td class="text-right sub-total">Rp <?= number_format($totalLiabilitas, 0, ',', '.') ?></td>
                        </tr>

                        <tr><td colspan="2">&nbsp;</td></tr>

                        <!-- EKUITAS -->
                        <tr><td colspan="2" class="section-title">EKUITAS</td></tr>
                        <?php 
                        $totalEkuitas = 0;
                        foreach($ekuitas as $e): 
                            $saldo = $e['total_kredit'] - $e['total_debit'];
                            if(stripos($e['nama_akun'], 'Prive') !== false) {
                                $saldo = -abs($e['total_debit'] - $e['total_kredit']);
                            }
                            $totalEkuitas += $saldo;
                        ?>
                        <tr>
                            <td><?= $e['nama_akun'] ?></td>
                            <td class="text-right">
                                <?= $saldo < 0 ? '(Rp '.number_format(abs($saldo), 0, ',', '.').')' : 'Rp '.number_format($saldo, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <!-- Laba Berjalan -->
                        <tr>
                            <td>Laba/Rugi Tahun Berjalan</td>
                            <td class="text-right">
                                <?= $laba_bersih < 0 ? '(Rp '.number_format(abs($laba_bersih), 0, ',', '.').')' : 'Rp '.number_format($laba_bersih, 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php $totalEkuitas += $laba_bersih; ?>

                        <tr>
                            <td class="sub-total">Total Ekuitas</td>
                            <td class="text-right sub-total">Rp <?= number_format($totalEkuitas, 0, ',', '.') ?></td>
                        </tr>

                        <tr><td colspan="2">&nbsp;</td></tr>
                        <tr>
                            <td class="grand-total">TOTAL LIABILITAS & EKUITAS</td>
                            <td class="text-right grand-total">Rp <?= number_format($totalLiabilitas + $totalEkuitas, 0, ',', '.') ?></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan -->
        <div style="margin-top: 60px; display: flex; justify-content: space-around;">
            <div style="text-align: center;">
                <p>Disiapkan Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; width: 150px; margin: 0 auto;">Accounting</p>
            </div>
            <div style="text-align: center;">
                <p>Disetujui Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; width: 150px; margin: 0 auto;">Direktur</p>
            </div>
        </div>
        
        <div style="margin-top: 30px; font-size: 10px; color: #666; font-style: italic; text-align: center;">
            * Dicetak pada: <?= date('d F Y H:i') ?>
        </div>

    </div>

</body>
</html>