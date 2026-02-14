<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Neraca Saldo Setelah Penutupan - <?= $filter_bln ?>/<?= $filter_thn ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; print-color-adjust: exact; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #999; padding: 6px; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bg-light { background-color: #f9f9f9; }
        
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
        <a href="<?= base_url('penutupan/export?bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-excel">📊 Export Excel</a>
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
            <h3 style="margin: 5px 0; border: 1px solid #333; display: inline-block; padding: 5px 15px;">NERACA SALDO SETELAH PENUTUPAN</h3>
            <p>Periode: <?= date('F Y', mktime(0, 0, 0, $filter_bln, 10, $filter_thn)) ?></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="15%">Kode Akun</th>
                    <th>Nama Akun</th>
                    <th width="20%">Debit</th>
                    <th width="20%">Kredit</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalDebit = 0;
                $totalKredit = 0;
                
                if(empty($neraca)): ?>
                    <tr><td colspan="4" class="text-center">Tidak ada data.</td></tr>
                <?php else:
                    foreach($neraca as $row):
                        $saldo = $row['total_debit'] - $row['total_kredit'];
                        if($saldo == 0) continue; 

                        $posisi = ($saldo > 0) ? 'Debit' : 'Kredit';
                        $nilai = abs($saldo);

                        if($posisi == 'Debit') $totalDebit += $nilai;
                        else $totalKredit += $nilai;
                ?>
                <tr>
                    <td class="text-center"><?= $row['kode_akun'] ?></td>
                    <td><?= $row['nama_akun'] ?></td>
                    <td class="text-right"><?= $posisi == 'Debit' ? number_format($nilai, 0, ',', '.') : '-' ?></td>
                    <td class="text-right"><?= $posisi == 'Kredit' ? number_format($nilai, 0, ',', '.') : '-' ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="2" class="text-center">TOTAL</td>
                    <td class="text-right">Rp <?= number_format($totalDebit, 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($totalKredit, 0, ',', '.') ?></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Tanda Tangan -->
        <div style="margin-top: 60px; display: flex; justify-content: space-around;">
            <div style="text-align: center;">
                <p>Disiapkan Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; width: 150px; margin: 0 auto;">Bagian Akuntansi</p>
            </div>
            <div style="text-align: center;">
                <p>Disetujui Oleh,</p>
                <br><br><br>
                <p style="border-bottom: 1px solid #333; width: 150px; margin: 0 auto;">Manajer Keuangan</p>
            </div>
        </div>

        <div style="margin-top: 20px; font-size: 10px; color: #666; font-style: italic; text-align: center;">
            * Dicetak pada: <?= date('d F Y H:i') ?>
        </div>

    </div>

</body>
</html>