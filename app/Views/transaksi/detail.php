<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<?php
    // Tentukan warna tema berdasarkan jenis transaksi (No Bukti)
    $themeClass = 'primary'; // Default Biru
    $iconClass = 'file-signature';
    $prefix = explode('-', $jurnal[0]['no_bukti'])[0];

    // Logic Warna yang sama dengan Riwayat
    if(in_array($prefix, ['JPJ', 'BKM'])) {
        $themeClass = 'success'; // Hijau (Masuk)
        $iconClass = 'cash-register';
    } elseif(in_array($prefix, ['JPB', 'BKK'])) {
        $themeClass = 'danger'; // Merah (Keluar)
        $iconClass = 'shopping-cart';
    } elseif($prefix == 'AJP') {
        $themeClass = 'warning'; // Kuning
        $iconClass = 'edit';
    }
?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Transaksi</h1>
    <div>
        <a href="<?= base_url('transaksi') ?>" class="btn btn-secondary btn-sm shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
        </a>
        <a href="<?= base_url('transaksi/cetak/'.$jurnal[0]['id_jurnal']) ?>" target="_blank" class="btn btn-primary btn-sm shadow-sm">
            <i class="fas fa-print fa-sm text-white-50"></i> Cetak Bukti
        </a>
    </div>
</div>

<div class="card shadow mb-4 border-left-<?= $themeClass ?>">
    <!-- Header dengan Warna Dinamis -->
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-<?= $themeClass ?>">
            <i class="fas fa-<?= $iconClass ?> mr-2"></i> Bukti Transaksi: <?= $jurnal[0]['no_bukti'] ?>
        </h6>
        <?php if($jurnal[0]['status_posting'] == 'Posted'): ?>
            <span class="badge badge-success px-3 py-2"><i class="fas fa-check-circle mr-1"></i> POSTED</span>
        <?php else: ?>
            <span class="badge badge-secondary px-3 py-2"><i class="fas fa-clock mr-1"></i> PENDING</span>
        <?php endif; ?>
    </div>

    <div class="card-body">
        
        <!-- Informasi Header Jurnal -->
        <div class="row mb-4">
            <div class="col-md-3">
                <small class="text-uppercase text-gray-500 font-weight-bold">Tanggal Transaksi</small>
                <h5 class="font-weight-bold text-gray-800"><?= date('d F Y', strtotime($jurnal[0]['tgl_jurnal'])) ?></h5>
            </div>
            <div class="col-md-3">
                <small class="text-uppercase text-gray-500 font-weight-bold">Jenis Transaksi</small>
                <h5 class="text-gray-800"><?= $jurnal[0]['jenis_transaksi'] ?></h5>
            </div>
            <div class="col-md-6">
                <small class="text-uppercase text-gray-500 font-weight-bold">Keterangan / Uraian</small>
                <h5 class="text-gray-800 font-italic">"<?= $jurnal[0]['keterangan'] ?>"</h5>
            </div>
        </div>

        <hr>

        <!-- Tabel Rincian -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th width="15%" class="text-center">Kode Akun</th>
                        <th width="35%">Nama Akun</th>
                        <th width="25%" class="text-center bg-info text-white">Debit (Rp)</th>
                        <th width="25%" class="text-center bg-<?= $themeClass == 'success' ? 'success' : 'danger' ?> text-white">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalDebit = 0; 
                    $totalKredit = 0;
                    foreach($jurnal as $row): 
                        $totalDebit += $row['debit'];
                        $totalKredit += $row['kredit'];
                    ?>
                    <tr>
                        <td class="text-center font-weight-bold text-primary"><?= $row['kode_akun'] ?></td>
                        <td><?= $row['nama_akun'] ?></td>
                        <td class="text-right">
                            <?= $row['debit'] > 0 ? 'Rp ' . number_format($row['debit'], 0, ',', '.') : '-' ?>
                        </td>
                        <td class="text-right">
                            <?= $row['kredit'] > 0 ? 'Rp ' . number_format($row['kredit'], 0, ',', '.') : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold bg-gray-100">
                        <td colspan="2" class="text-right align-middle text-uppercase text-gray-600">Total Transaksi :</td>
                        <td class="text-right align-middle" style="border-bottom: 4px solid #36b9cc;">
                            <span class="h5 font-weight-bold text-gray-800">Rp <?= number_format($totalDebit, 0, ',', '.') ?></span>
                        </td>
                        <td class="text-right align-middle" style="border-bottom: 4px solid <?= $themeClass == 'success' ? '#1cc88a' : '#e74a3b' ?>;">
                            <span class="h5 font-weight-bold text-gray-800">Rp <?= number_format($totalKredit, 0, ',', '.') ?></span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-4 text-center">
            <small class="text-muted">Transaksi ini dibuat pada: <?= $jurnal[0]['created_at'] ?></small>
        </div>

    </div>
</div>
<?= $this->endSection(); ?>