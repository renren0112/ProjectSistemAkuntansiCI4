<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worksheet - <?= $perusahaan ?></title>
    <style>
        /* Base Setup */
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 9px; 
            color: #333;
            margin: 0;
            padding: 20px;
            line-height: 1.4;
        }

        /* Kop Surat / Header */
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            position: relative;
        }
        .header h2 { 
            margin: 0; 
            text-transform: uppercase; 
            font-size: 20px; 
            color: #1a202c;
            letter-spacing: 1px;
        }
        .header p { 
            margin: 3px 0; 
            font-size: 10px;
            color: #4a5568; 
        }
        .report-title {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
        }
        .report-title h3 {
            margin: 0;
            font-size: 14px;
            color: #2d3748;
            text-decoration: none;
        }

        /* Table Styling */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
            table-layout: fixed;
        }
        th, td { 
            border: 1px solid #a0aec0; 
            padding: 5px 4px; 
            word-wrap: break-word;
        }
        
        /* Header Table */
        thead th { 
            background-color: #edf2f7; 
            text-transform: uppercase; 
            font-size: 8px; 
            color: #2d3748;
            font-weight: 700;
        }

        /* Color Grouping for Columns */
        .col-ns { background-color: #fcfcfc; }
        .col-ajp { background-color: #fffaf0; }
        .col-nsd { background-color: #f0fff4; }
        .col-lr { background-color: #ebf8ff; }
        .col-nr { background-color: #fff5f5; }

        /* Alignment & Numbers */
        .num { 
            text-align: right; 
            font-family: 'Consolas', 'Courier New', monospace; 
            font-size: 9px;
        }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .italic { font-style: italic; }

        /* Footer / Summary Rows */
        .row-total { background-color: #edf2f7 !important; font-weight: bold; }
        .row-laba { background-color: #f7fafc !important; }
        .row-balance { background-color: #2d3748 !important; color: #fff !important; }
        .row-balance td { border-color: #2d3748; }

        /* Print Settings */
        @media print {
            @page { 
                size: landscape; 
                margin: 0.5cm; 
            }
            body { padding: 0; }
            .no-print { display: none; }
            th { -webkit-print-color-adjust: exact; }
            td { -webkit-print-color-adjust: exact; }
        }

        /* Utilities */
        .btn-close {
            background: #e53e3e;
            color: white;
            border: none;
            padding: 6px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            font-size: 11px;
        }
    </style>
</head>
<body onload="window.print()">
    <!-- Navigation for Screen -->
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <span style="float: left; color: #718096; font-style: italic;">* Gunakan mode Landscape untuk hasil terbaik</span>
        <button onclick="window.close()" class="btn-close">Tutup Halaman</button>
    </div>

    <!-- Header Perusahaan -->
    <div class="header">
        <h2><?= $perusahaan ?></h2>
        <p><?= $alamat ?></p>
        <p>Hubungi: <?= $telepon ?> | <?= $email ?></p>
        
        <div class="report-title">
            <h3>NERACA LAJUR (WORKSHEET)</h3>
            <p style="font-weight: bold; color: #2d3748;">Periode Laporan: <?= date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2" style="width: 40px;">Kode</th>
                <th rowspan="2" style="width: 150px;">Nama Akun</th>
                <th colspan="2" class="col-ns">Neraca Saldo</th>
                <th colspan="2" class="col-ajp">Penyesuaian</th>
                <th colspan="2" class="col-nsd">NS Disesuaikan</th>
                <th colspan="2" class="col-lr">Laba Rugi</th>
                <th colspan="2" class="col-nr">Neraca</th>
            </tr>
            <tr>
                <th class="col-ns">Debit</th><th class="col-ns">Kredit</th>
                <th class="col-ajp">Debit</th><th class="col-ajp">Kredit</th>
                <th class="col-nsd">Debit</th><th class="col-nsd">Kredit</th>
                <th class="col-lr">Debit</th><th class="col-lr">Kredit</th>
                <th class="col-nr">Debit</th><th class="col-nr">Kredit</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $t = array_fill(0, 10, 0); // Penampung total
            foreach($worksheet as $row): 
                // Akumulasi
                $t[0] += $row['ns_d']; $t[1] += $row['ns_k'];
                $t[2] += $row['ajp_d']; $t[3] += $row['ajp_k'];
                $t[4] += $row['nsd_d']; $t[5] += $row['nsd_k'];
                $t[6] += $row['lr_d']; $t[7] += $row['lr_k'];
                $t[8] += $row['nr_d']; $t[9] += $row['nr_k'];
            ?>
            <tr>
                <td class="text-center font-bold" style="color: #4a5568;"><?= $row['kode'] ?></td>
                <td style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= $row['nama'] ?></td>
                
                <td class="num col-ns"><?= $row['ns_d'] != 0 ? number_format($row['ns_d'], 0, ',', '.') : '-' ?></td>
                <td class="num col-ns"><?= $row['ns_k'] != 0 ? number_format($row['ns_k'], 0, ',', '.') : '-' ?></td>
                
                <td class="num col-ajp"><?= $row['ajp_d'] != 0 ? number_format($row['ajp_d'], 0, ',', '.') : '-' ?></td>
                <td class="num col-ajp"><?= $row['ajp_k'] != 0 ? number_format($row['ajp_k'], 0, ',', '.') : '-' ?></td>
                
                <td class="num col-nsd"><?= $row['nsd_d'] != 0 ? number_format($row['nsd_d'], 0, ',', '.') : '-' ?></td>
                <td class="num col-nsd"><?= $row['nsd_k'] != 0 ? number_format($row['nsd_k'], 0, ',', '.') : '-' ?></td>
                
                <td class="num col-lr"><?= $row['lr_d'] != 0 ? number_format($row['lr_d'], 0, ',', '.') : '-' ?></td>
                <td class="num col-lr"><?= $row['lr_k'] != 0 ? number_format($row['lr_k'], 0, ',', '.') : '-' ?></td>
                
                <td class="num col-nr"><?= $row['nr_d'] != 0 ? number_format($row['nr_d'], 0, ',', '.') : '-' ?></td>
                <td class="num col-nr"><?= $row['nr_k'] != 0 ? number_format($row['nr_k'], 0, ',', '.') : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot class="font-bold">
            <!-- Total Sebelum Laba/Rugi -->
            <tr class="row-total">
                <td colspan="2" class="text-center">TOTAL SALDO</td>
                <td class="num"><?= number_format($t[0], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[1], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[2], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[3], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[4], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[5], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[6], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[7], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[8], 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[9], 0, ',', '.') ?></td>
            </tr>

            <?php 
                $laba = $t[7] - $t[6]; 
                $label = ($laba >= 0) ? "LABA BERSIH" : "RUGI BERSIH";
                $abs_laba = abs($laba);
                $color_laba = ($laba >= 0) ? "#2f855a" : "#c53030";
            ?>
            <!-- Baris Laba/Rugi Bersih -->
            <tr class="row-laba">
                <td colspan="2" class="text-center italic" style="color: <?= $color_laba ?>;"><?= $label ?></td>
                <td colspan="6"></td>
                <!-- Laba Rugi Column -->
                <td class="num" style="color: <?= $color_laba ?>;"><?= ($laba < 0) ? number_format($abs_laba, 0, ',', '.') : '-' ?></td>
                <td class="num" style="color: <?= $color_laba ?>;"><?= ($laba >= 0) ? number_format($abs_laba, 0, ',', '.') : '-' ?></td>
                <!-- Neraca Column (Kebalikan) -->
                <td class="num" style="color: <?= $color_laba ?>;"><?= ($laba >= 0) ? number_format($abs_laba, 0, ',', '.') : '-' ?></td>
                <td class="num" style="color: <?= $color_laba ?>;"><?= ($laba < 0) ? number_format($abs_laba, 0, ',', '.') : '-' ?></td>
            </tr>

            <!-- Balance Check Akhir -->
            <tr class="row-balance">
                <td colspan="2" class="text-center">JUMLAH BALANCE</td>
                <td colspan="6" class="text-center italic" style="font-size: 8px;">(Verify All Columns Are Balanced)</td>
                <!-- Sum Laba Rugi -->
                <td class="num"><?= number_format($t[6] + ($laba < 0 ? $abs_laba : 0), 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[7] + ($laba >= 0 ? 0 : $abs_laba), 0, ',', '.') ?></td>
                <!-- Sum Neraca -->
                <td class="num"><?= number_format($t[8] + ($laba >= 0 ? $abs_laba : 0), 0, ',', '.') ?></td>
                <td class="num"><?= number_format($t[9] + ($laba < 0 ? $abs_laba : 0), 0, ',', '.') ?></td>
            </tr>
        </tfoot>
    </table>

    <!-- Tanda Tangan Section -->
    <div style="margin-top: 40px; width: 100%; display: flex; justify-content: flex-end;">
        <div style="width: 250px; text-align: center;">
            <p>Dicetak pada: <?= date('d/m/Y H:i') ?></p>
            <br><br><br><br>
            <p><strong>( __________________________ )</strong></p>
            <p>Bagian Akuntansi / Manager</p>
        </div>
    </div>
</body>
</html>