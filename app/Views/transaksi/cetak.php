<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Bukti Transaksi - <?= $jurnal[0]['no_bukti'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; -webkit-print-color-adjust: exact; }
        .container { width: 100%; max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; background: white; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px; }
        .font-bold { font-weight: bold; }

        .transaksi-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .transaksi-table th, .transaksi-table td { border: 1px solid #999; padding: 8px; }
        .transaksi-table th { background-color: #f0f0f0; text-align: center; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .footer { margin-top: 40px; display: flex; justify-content: space-between; }
        .ttd-box { width: 150px; text-align: center; }
        .ttd-line { border-bottom: 1px solid #333; margin-top: 50px; }
        
        /* Tombol Navigasi (Hanya tampil di layar) */
        .nav-buttons { text-align: center; margin-bottom: 20px; padding: 10px; background: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; margin: 0 5px; color: white; text-decoration: none; display: inline-block; }
        .btn-print { background-color: #4e73df; }
        .btn-excel { background-color: #1cc88a; }
        .btn-close { background-color: #e74a3b; }
        .btn:hover { opacity: 0.9; }

        @media print {
            body { margin: 0; padding: 0; background: white; }
            .container { border: none; width: 100%; max-width: 100%; padding: 0; }
            .no-print, .nav-buttons { display: none !important; }
        }
    </style>
</head>
<!-- Auto print jika bukan excel -->
<body <?php if(!isset($is_excel)) echo 'onload="window.print()"'; ?>>

    <!-- TOMBOL NAVIGASI -->
    <?php if(!isset($is_excel)): ?>
    <div class="nav-buttons no-print">
        <button onclick="window.print()" class="btn btn-print">🖨️ Preview Cetak</button>
        <a href="<?= base_url('transaksi/export/'.$jurnal[0]['id_jurnal']) ?>" class="btn btn-excel">📊 Export Excel</a>
        <button onclick="window.close()" class="btn btn-close">❌ Tutup</button>
    </div>
    <?php endif; ?>

    <div class="container">
        <!-- KOP SURAT DINAMIS -->
        <?php 
            // Ambil data pengaturan langsung dari DB jika belum dikirim controller
            $db = \Config\Database::connect();
            $settings = $db->table('pengaturan')->get()->getResultArray();
            $info = [];
            foreach($settings as $s) $info[$s['kunci']] = $s['nilai'];
        ?>
        
        <div class="header">
            <h2><?= $info['nama_perusahaan'] ?? 'SIA PERUSAHAAN' ?></h2>
            <p><?= $info['alamat_perusahaan'] ?? 'Alamat belum diatur' ?></p>
            <p>Telp: <?= $info['no_telp'] ?? '-' ?> | Email: <?= $info['email_perusahaan'] ?? '-' ?></p>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin:0; border: 1px solid #333; display: inline-block; padding: 5px 15px;">BUKTI TRANSAKSI</h3>
        </div>

        <table class="info-table">
            <tr>
                <td width="15%" class="font-bold">No. Bukti</td>
                <td width="35%">: <?= $jurnal[0]['no_bukti'] ?></td>
                <td width="15%" class="font-bold">Tanggal</td>
                <td width="35%">: <?= date('d F Y', strtotime($jurnal[0]['tgl_jurnal'])) ?></td>
            </tr>
            <tr>
                <td class="font-bold">Jenis</td>
                <td>: <?= strtoupper($jurnal[0]['jenis_transaksi']) ?></td>
                <td class="font-bold">Status</td>
                <td>: <?= strtoupper($jurnal[0]['status_posting']) ?></td>
            </tr>
            <tr>
                <td class="font-bold">Keterangan</td>
                <td colspan="3">: <?= $jurnal[0]['keterangan'] ?></td>
            </tr>
        </table>

        <!-- Tabel Rincian -->
        <table class="transaksi-table" border="1">
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
                foreach($jurnal as $d): 
                    $totalDebit += $d['debit'];
                    $totalKredit += $d['kredit'];
                ?>
                <tr>
                    <td class="text-center"><?= $d['kode_akun'] ?></td>
                    <td><?= $d['nama_akun'] ?></td>
                    <td class="text-right">
                        <?= $d['debit'] > 0 ? 'Rp '.number_format($d['debit'], 0, ',', '.') : '-' ?>
                    </td>
                    <td class="text-right">
                        <?= $d['kredit'] > 0 ? 'Rp '.number_format($d['kredit'], 0, ',', '.') : '-' ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">TOTAL</th>
                    <th class="text-right">Rp <?= number_format($totalDebit, 0, ',', '.') ?></th>
                    <th class="text-right">Rp <?= number_format($totalKredit, 0, ',', '.') ?></th>
                </tr>
            </tfoot>
        </table>

        <!-- Tanda Tangan -->
        <div class="footer">
            <div class="ttd-box">
                <p>Dibuat Oleh,</p>
                <div class="ttd-line"></div>
                <p>Admin / Kasir</p>
            </div>
            <div class="ttd-box">
                <p>Disetujui Oleh,</p>
                <div class="ttd-line"></div>
                <p>Manajer Keuangan</p>
            </div>
        </div>
    </div>

</body>
</html>