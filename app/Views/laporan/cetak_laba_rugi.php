<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - <?= $filter_bln ?>/<?= $filter_thn ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; print-color-adjust: exact; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 5px; }
        
        .font-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-uppercase { text-transform: uppercase; }
        
        /* Warna Baris Khusus */
        .bg-primary { background-color: #4e73df !important; color: white !important; }
        .bg-light { background-color: #f8f9fc !important; }
        .border-top { border-top: 1px solid #333; }
        .double-underline { border-bottom: 3px double #333; }

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
        <a href="<?= base_url('laporan/export-laba-rugi?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-excel">📊 Export Excel</a>
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
            <h3 style="margin: 5px 0; border: 1px solid #333; display: inline-block; padding: 5px 15px;">LAPORAN LABA RUGI</h3>
            <p>Periode: <?= date('F Y', mktime(0, 0, 0, $filter_bln, 10, $filter_thn)) ?></p>
        </div>

        <table width="100%">
            <!-- 1. PENDAPATAN USAHA -->
            <tr>
                <td colspan="3" class="font-bold text-uppercase" style="padding-top: 15px;">I. Pendapatan Usaha</td>
            </tr>
            <?php foreach($penjualan as $p): 
                $saldo = $p['total_kredit'] - $p['total_debit'];
            ?>
            <tr>
                <td width="5%"></td>
                <td><?= $p['nama_akun'] ?></td>
                <td width="20%" class="text-right">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
            <tr class="font-bold bg-light">
                <td colspan="2" style="padding-left: 20px;">Total Penjualan Bersih</td>
                <td class="text-right border-top">Rp <?= number_format($total_penjualan, 0, ',', '.') ?></td>
            </tr>

            <!-- 2. HPP -->
            <tr>
                <td colspan="3" class="font-bold text-uppercase" style="padding-top: 15px; color: #e74a3b;">II. Harga Pokok Penjualan</td>
            </tr>
            <?php foreach($pembelian as $p): 
                 $saldo = $p['total_debit'] - $p['total_kredit'];
            ?>
            <tr>
                <td></td>
                <td><?= $p['nama_akun'] ?></td>
                <td class="text-right">(Rp <?= number_format($saldo, 0, ',', '.') ?>)</td>
            </tr>
            <?php endforeach; ?>
            <tr class="font-bold bg-light">
                <td colspan="2" style="padding-left: 20px;">Total HPP</td>
                <td class="text-right border-top" style="color: #e74a3b;">(Rp <?= number_format($total_pembelian, 0, ',', '.') ?>)</td>
            </tr>

            <!-- LABA KOTOR -->
            <tr class="font-bold" style="background-color: #eaecf4;">
                <td colspan="2" class="text-uppercase" style="padding: 10px;">Laba Kotor (Gross Profit)</td>
                <td class="text-right" style="padding: 10px;">Rp <?= number_format($laba_kotor, 0, ',', '.') ?></td>
            </tr>

            <!-- 3. BEBAN OPERASIONAL -->
            <tr>
                <td colspan="3" class="font-bold text-uppercase" style="padding-top: 15px;">III. Beban Operasional</td>
            </tr>
            <?php foreach($beban as $b): 
                $saldo = $b['total_debit'] - $b['total_kredit'];
            ?>
            <tr>
                <td></td>
                <td><?= $b['nama_akun'] ?></td>
                <td class="text-right">(Rp <?= number_format($saldo, 0, ',', '.') ?>)</td>
            </tr>
            <?php endforeach; ?>
            <tr class="font-bold bg-light">
                <td colspan="2" style="padding-left: 20px;">Total Beban Operasional</td>
                <td class="text-right border-top" style="color: #e74a3b;">(Rp <?= number_format($total_beban, 0, ',', '.') ?>)</td>
            </tr>

            <!-- 4. PENDAPATAN LAIN -->
            <?php if($total_other != 0): ?>
            <tr>
                <td colspan="3" class="font-bold text-uppercase" style="padding-top: 15px;">IV. Pendapatan/Beban Lainnya</td>
            </tr>
            <?php foreach($pendapatan_lain as $pl): 
                 $saldo = $pl['total_kredit'] - $pl['total_debit'];
            ?>
            <tr>
                <td></td>
                <td><?= $pl['nama_akun'] ?></td>
                <td class="text-right">Rp <?= number_format($saldo, 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; endif; ?>

            <!-- LABA BERSIH -->
            <tr><td colspan="3" style="padding: 10px;"></td></tr>
            <tr class="font-bold bg-primary text-uppercase" style="color: white;">
                <td colspan="2" style="padding: 10px;">Laba Bersih (Net Profit)</td>
                <td class="text-right double-underline" style="padding: 10px;">Rp <?= number_format($laba_bersih, 0, ',', '.') ?></td>
            </tr>
        </table>

        <!-- Tanda Tangan -->
        <div style="margin-top: 50px; display: flex; justify-content: space-between;">
            <div style="width: 200px; text-align: center;">
                <p>Disiapkan Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; margin-bottom: 5px;">Admin Keuangan</p>
            </div>
            <div style="width: 200px; text-align: center;">
                <p>Disetujui Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; margin-bottom: 5px;">Pimpinan</p>
            </div>
        </div>
        
        <div style="margin-top: 20px; font-size: 10px; color: #666; font-style: italic; text-align: center;">
            * Dicetak pada: <?= date('d F Y H:i') ?>
        </div>

    </div>

</body>
</html>