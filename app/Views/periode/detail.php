<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Detail Periode: <?= $periode['nama_periode'] ?></h1>
    <a href="<?= base_url('periode') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="alert alert-warning border-left-warning shadow-sm">
    <i class="fas fa-lock mr-2"></i> 
    Periode ini berstatus <b>CLOSED</b>. Anda hanya dapat melihat data transaksi historis tanpa bisa mengubahnya.
</div>

<!-- Laporan Cepat -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="<?= base_url('laporan/neraca-saldo?bulan='.$periode['bulan'].'&tahun='.$periode['tahun']) ?>" class="btn btn-primary btn-block text-left shadow-sm">
            <i class="fas fa-balance-scale mr-2"></i> Lihat Neraca Saldo
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= base_url('laporan/laba-rugi?bulan='.$periode['bulan'].'&tahun='.$periode['tahun']) ?>" class="btn btn-success btn-block text-left shadow-sm">
            <i class="fas fa-file-invoice-dollar mr-2"></i> Lihat Laba Rugi
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= base_url('laporan/neraca?bulan='.$periode['bulan'].'&tahun='.$periode['tahun']) ?>" class="btn btn-info btn-block text-left shadow-sm">
            <i class="fas fa-building mr-2"></i> Lihat Neraca
        </a>
    </div>
    <div class="col-md-3">
        <a href="<?= base_url('laporan/neraca-saldo-penutup?bulan='.$periode['bulan'].'&tahun='.$periode['tahun']) ?>" class="btn btn-dark btn-block text-left shadow-sm">
            <i class="fas fa-check-double mr-2"></i> Neraca Penutup
        </a>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-gray-800">Riwayat Transaksi (<?= date('F Y', mktime(0,0,0,$periode['bulan'], 1, $periode['tahun'])) ?>)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th>Tanggal</th>
                        <th>No Bukti</th>
                        <th>Keterangan</th>
                        <th>Jenis</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($jurnal)): ?>
                        <tr><td colspan="6" class="text-center text-muted font-italic py-4">Tidak ada transaksi tercatat.</td></tr>
                    <?php else: ?>
                        <?php $no=1; foreach($jurnal as $j): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= date('d/m/Y', strtotime($j['tgl_jurnal'])) ?></td>
                            <td><?= $j['no_bukti'] ?></td>
                            <td><?= $j['keterangan'] ?></td>
                            <td><?= $j['jenis_transaksi'] ?></td>
                            <td class="text-center">
                                <span class="badge badge-secondary">Arsip</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>