<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Perubahan Modal - <?= $filter_bln ?>/<?= $filter_thn ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; print-color-adjust: exact; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; }
        
        .font-bold { font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-uppercase { text-transform: uppercase; }
        
        /* Warna Baris Khusus */
        .bg-light { background-color: #f8f9fc; }
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
        <a href="<?= base_url('laporan/export-perubahan-modal?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-excel">📊 Export Excel</a>
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
            <h3 style="margin: 5px 0; border: 1px solid #333; display: inline-block; padding: 5px 15px;">LAPORAN PERUBAHAN MODAL</h3>
            <p>Periode Berakhir: <?= date('d F Y', mktime(0, 0, 0, $filter_bln + 1, 0, $filter_thn)) ?></p>
        </div>

        <?php 
        // HITUNG LOGIC MODAL
        $modalAwal = 0;
        $prive = 0;
        
        foreach($ekuitas as $e) {
            $saldo = $e['total_kredit'] - $e['total_debit'];
            if (stripos($e['nama_akun'], 'Modal') !== false) {
                $modalAwal += $saldo;
            } elseif (stripos($e['nama_akun'], 'Prive') !== false) {
                $prive += abs($e['total_debit'] - $e['total_kredit']);
            }
        }
        
        $perubahan = $laba_bersih - $prive;
        $modalAkhir = $modalAwal + $perubahan;
        ?>

        <table width="100%">
            <!-- MODAL AWAL -->
            <tr class="bg-light">
                <td colspan="2" class="font-bold text-uppercase">Modal Awal</td>
                <td class="text-right font-bold" style="font-size: 1.1em;">Rp <?= number_format($modalAwal, 0, ',', '.') ?></td>
            </tr>

            <!-- ISI PERUBAHAN -->
            <tr>
                <td width="5%"></td>
                <td>Ditambah: Laba Bersih (atau Dikurang jika Rugi)</td>
                <td width="25%" class="text-right">Rp <?= number_format($laba_bersih, 0, ',', '.') ?></td>
            </tr>
            <?php if($prive > 0): ?>
            <tr>
                <td></td>
                <td>Dikurang: Prive / Penarikan</td>
                <td class="text-right">(Rp <?= number_format($prive, 0, ',', '.') ?>)</td>
            </tr>
            <?php endif; ?>

            <!-- TOTAL PERUBAHAN -->
            <tr>
                <td></td>
                <td class="font-bold text-italic border-top">
                    <?= $perubahan >= 0 ? 'Kenaikan Modal' : 'Penurunan Modal' ?>
                </td>
                <td class="text-right font-bold border-top">
                    Rp <?= number_format($perubahan, 0, ',', '.') ?>
                </td>
            </tr>

            <!-- MODAL AKHIR -->
            <tr><td colspan="3" style="padding: 15px;"></td></tr>
            <tr class="font-bold text-uppercase" style="background-color: #f0f0f0;">
                <td colspan="2" style="padding: 10px;">Modal Akhir (<?= date('d/m/Y', mktime(0, 0, 0, $filter_bln + 1, 0, $filter_thn)) ?>)</td>
                <td class="text-right double-underline" style="padding: 10px;">Rp <?= number_format($modalAkhir, 0, ',', '.') ?></td>
            </tr>
        </table>

        <!-- Tanda Tangan -->
        <div style="margin-top: 60px; display: flex; justify-content: space-between;">
            <div style="width: 200px; text-align: center;">
                <p>Disiapkan Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; margin-bottom: 5px;">Bag. Akuntansi</p>
            </div>
            <div style="width: 200px; text-align: center;">
                <p>Disetujui Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; margin-bottom: 5px;">Pemilik / Direktur</p>
            </div>
        </div>
        
        <div style="margin-top: 30px; font-size: 10px; color: #666; font-style: italic; text-align: center;">
            * Dicetak pada: <?= date('d F Y H:i') ?>
        </div>

    </div>

</body>
</html>