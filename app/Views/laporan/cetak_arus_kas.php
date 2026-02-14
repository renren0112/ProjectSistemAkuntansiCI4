<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Arus Kas - <?= $filter_thn ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 5px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .section-header { background-color: #f0f0f0; text-transform: uppercase; font-weight: bold; }
        .total-row { border-top: 1px solid #000; font-weight: bold; font-style: italic;}
        .grand-total { background-color: #ddd; font-size: 14px; font-weight: bold; border-top: 2px solid #000; border-bottom: 2px double #000; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: right; margin-bottom: 10px;">
        <button onclick="window.close()" style="cursor: pointer; padding: 5px 10px;">Tutup</button>
    </div>

    <div class="header">
        <!-- Mengambil Nama Perusahaan dari Controller -->
        <h2 style="margin:0; text-transform: uppercase;"><?= $perusahaan ?? 'SISTEM INFORMASI AKUNTANSI' ?></h2>
        <h4 style="margin:5px 0">LAPORAN ARUS KAS</h4>
        <p style="margin:0">Periode: <?= date('F Y', mktime(0,0,0,$filter_bln, 1, $filter_thn)) ?></p>
    </div>

    <table>
        <tr class="grand-total">
            <td>SALDO KAS AWAL</td>
            <td class="text-right">Rp <?= number_format($saldo_awal, 0, ',', '.') ?></td>
        </tr>
        <tr><td colspan="2">&nbsp;</td></tr>

        <!-- ARUS MASUK -->
        <tr class="section-header"><td colspan="2">ARUS KAS MASUK</td></tr>
        <?php $total_masuk = 0; foreach($arus_masuk as $m): $total_masuk += $m['debit']; ?>
        <tr>
            <td style="padding-left: 20px;"><?= $m['keterangan'] ?> <span style="font-size:10px;color:#666;">(<?= $m['nama_akun'] ?>)</span></td>
            <td class="text-right"><?= number_format($m['debit'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($arus_masuk)): ?>
        <tr><td colspan="2" style="padding-left: 20px; font-style:italic; color:#666;">- Tidak ada penerimaan kas -</td></tr>
        <?php endif; ?>
        <tr class="total-row">
            <td style="padding-left: 20px;">Total Penerimaan</td>
            <td class="text-right">Rp <?= number_format($total_masuk, 0, ',', '.') ?></td>
        </tr>

        <tr><td colspan="2">&nbsp;</td></tr>

        <!-- ARUS KELUAR -->
        <tr class="section-header"><td colspan="2">ARUS KAS KELUAR</td></tr>
        <?php $total_keluar = 0; foreach($arus_keluar as $k): $total_keluar += $k['kredit']; ?>
        <tr>
            <td style="padding-left: 20px;"><?= $k['keterangan'] ?> <span style="font-size:10px;color:#666;">(<?= $k['nama_akun'] ?>)</span></td>
            <td class="text-right">(<?= number_format($k['kredit'], 0, ',', '.') ?>)</td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($arus_keluar)): ?>
        <tr><td colspan="2" style="padding-left: 20px; font-style:italic; color:#666;">- Tidak ada pengeluaran kas -</td></tr>
        <?php endif; ?>
        <tr class="total-row">
            <td style="padding-left: 20px;">Total Pengeluaran</td>
            <td class="text-right">(Rp <?= number_format($total_keluar, 0, ',', '.') ?>)</td>
        </tr>

        <tr><td colspan="2">&nbsp;</td></tr>

        <!-- SALDO AKHIR -->
        <?php $kenaikan = $total_masuk - $total_keluar; ?>
        <tr>
            <td class="font-bold">Kenaikan / (Penurunan) Kas Bersih</td>
            <td class="text-right font-bold">Rp <?= number_format($kenaikan, 0, ',', '.') ?></td>
        </tr>
        <tr class="grand-total">
            <td style="padding: 10px;">SALDO KAS AKHIR</td>
            <td class="text-right" style="padding: 10px;">Rp <?= number_format($saldo_awal + $kenaikan, 0, ',', '.') ?></td>
        </tr>
    </table>
</body>
</html>