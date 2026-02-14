<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Besar - <?= $akun['nama_akun'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; print-color-adjust: exact; }
        .container { width: 100%; max-width: 900px; margin: 0 auto; padding: 20px; }
        
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        .info-akun { width: 100%; margin-bottom: 15px; font-weight: bold; font-size: 12px; }
        .info-akun td { padding: 3px 0; }

        table.data-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data-table th, table.data-table td { border: 1px solid #999; padding: 6px; }
        table.data-table th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        
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

    <!-- TOMBOL NAVIGASI (Hanya di Layar, Hilang di Excel & Print) -->
    <?php if(!isset($is_excel)): ?>
    <div class="nav-buttons no-print">
        <button onclick="window.print()" class="btn btn-print">🖨️ Preview Cetak</button>
        <!-- Tombol Export Excel -->
        <a href="<?= base_url('laporan/export-buku-besar?akun='.$akun['kode_akun'].'&bulan='.$filter_bln.'&tahun='.$filter_thn) ?>" class="btn btn-excel">📊 Export Excel</a>
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
            <h3 style="margin: 5px 0; border: 1px solid #333; display: inline-block; padding: 5px 15px;">BUKU BESAR (GENERAL LEDGER)</h3>
            <p>Periode: <?= date('F Y', mktime(0, 0, 0, $filter_bln, 10, $filter_thn)) ?></p>
        </div>

        <table class="info-akun">
            <tr>
                <td width="15%">Kode Akun</td>
                <td width="40%">: <?= $akun['kode_akun'] ?></td>
                <td width="15%">Kategori</td>
                <td width="30%">: <?= strtoupper($akun['tipe_akun']) ?></td>
            </tr>
            <tr>
                <td>Nama Akun</td>
                <td>: <?= $akun['nama_akun'] ?></td>
                <td>Saldo Normal</td>
                <td>: <?= $akun['posisi_saldo_normal'] == 'D' ? 'DEBIT' : 'KREDIT' ?></td>
            </tr>
        </table>

        <table class="data-table" border="1">
            <thead>
                <tr>
                    <th rowspan="2" width="10%">Tanggal</th>
                    <th rowspan="2" width="12%">No. Bukti</th>
                    <th rowspan="2">Keterangan</th>
                    <th colspan="2">Mutasi Transaksi</th>
                    <th colspan="2">Saldo Berjalan</th>
                </tr>
                <tr>
                    <th width="13%">Debit</th>
                    <th width="13%">Kredit</th>
                    <th width="13%">Debit</th>
                    <th width="13%">Kredit</th>
                </tr>
            </thead>
            <tbody>
                <!-- Saldo Awal (Dummy 0 karena filter bulanan) -->
                <tr class="bg-light">
                    <td colspan="3" class="text-center font-bold">Saldo Awal</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">0</td>
                    <td class="text-right">0</td>
                </tr>

                <?php 
                $saldo = 0;
                $normal = $akun['posisi_saldo_normal'];
                $totalD = 0; $totalK = 0;

                if(!empty($transaksi)):
                    foreach($transaksi as $t): 
                        $d = $t['debit'];
                        $k = $t['kredit'];
                        $totalD += $d;
                        $totalK += $k;

                        // Logic Saldo Berjalan
                        if ($normal == 'D') $saldo += ($d - $k);
                        else $saldo += ($k - $d);
                ?>
                <tr>
                    <td class="text-center"><?= date('d/m/Y', strtotime($t['tgl_jurnal'])) ?></td>
                    <td class="text-center"><?= $t['no_bukti'] ?></td>
                    <td><?= $t['keterangan'] ?></td>
                    <td class="text-right"><?= $d!=0 ? number_format($d,0,',','.') : '-' ?></td>
                    <td class="text-right"><?= $k!=0 ? number_format($k,0,',','.') : '-' ?></td>
                    
                    <!-- Kolom Saldo -->
                    <?php if($normal == 'D'): ?>
                        <td class="text-right"><?= $saldo >= 0 ? number_format($saldo,0,',','.') : '-' ?></td>
                        <td class="text-right"><?= $saldo < 0 ? '('.number_format(abs($saldo),0,',','.').')' : '-' ?></td>
                    <?php else: ?>
                        <td class="text-right"><?= $saldo < 0 ? '('.number_format(abs($saldo),0,',','.').')' : '-' ?></td>
                        <td class="text-right"><?= $saldo >= 0 ? number_format($saldo,0,',','.') : '-' ?></td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                    <td colspan="7" class="text-center" style="padding: 20px;">Tidak ada transaksi pada periode ini.</td>
                </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="background-color: #e9ecef; font-weight: bold;">
                    <td colspan="3" class="text-right">Total Perubahan Periode Ini :</td>
                    <td class="text-right"><?= number_format($totalD,0,',','.') ?></td>
                    <td class="text-right"><?= number_format($totalK,0,',','.') ?></td>
                    
                    <!-- Saldo Akhir -->
                    <?php if($normal == 'D'): ?>
                        <td class="text-right"><?= $saldo >= 0 ? number_format($saldo,0,',','.') : '-' ?></td>
                        <td class="text-right"><?= $saldo < 0 ? '('.number_format(abs($saldo),0,',','.').')' : '-' ?></td>
                    <?php else: ?>
                        <td class="text-right"><?= $saldo < 0 ? '('.number_format(abs($saldo),0,',','.').')' : '-' ?></td>
                        <td class="text-right"><?= $saldo >= 0 ? number_format($saldo,0,',','.') : '-' ?></td>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
        
        <div style="margin-top: 10px; font-size: 10px; color: #666; font-style: italic;">
            * Dicetak pada: <?= date('d F Y H:i') ?> oleh Sistem Akuntansi
        </div>

    </div>

</body>
</html>