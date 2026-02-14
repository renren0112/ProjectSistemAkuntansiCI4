<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Kelola Periode Akuntansi</h1>
    <a href="<?= base_url('periode/create') ?>" class="btn btn-primary btn-sm shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Buat Periode Baru
    </a>
</div>

<!-- Info Card -->
<div class="row mb-4">
    <div class="col-xl-12 col-md-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Periode Aktif Saat Ini</div>
                        <?php 
                            // Cari periode yang statusnya OPEN
                            $periodeAktif = array_filter($periode, function($p) { return $p['status'] == 'OPEN'; });
                            $aktif = reset($periodeAktif); 
                        ?>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $aktif ? $aktif['nama_periode'] . " (" . date('M Y', mktime(0,0,0,$aktif['bulan'], 1, $aktif['tahun'])) . ")" : 'Belum ada periode aktif' ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Periode</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%">No</th>
                        <th>Nama Periode</th>
                        <th>Bulan/Tahun</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th class="text-center">Status</th>
                        <th class="text-center" width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($periode as $p): ?>
                    <tr class="<?= $p['status'] == 'OPEN' ? 'bg-light font-weight-bold' : '' ?>">
                        <td class="text-center align-middle"><?= $no++ ?></td>
                        <td class="align-middle"><?= $p['nama_periode'] ?></td>
                        <td class="align-middle">
                            <?= date('F', mktime(0, 0, 0, $p['bulan'], 10)) ?> <?= $p['tahun'] ?>
                        </td>
                        <td class="align-middle"><?= date('d-m-Y', strtotime($p['tgl_mulai'])) ?></td>
                        <td class="align-middle"><?= date('d-m-Y', strtotime($p['tgl_selesai'])) ?></td>
                        <td class="text-center align-middle">
                            <?php if($p['status'] == 'OPEN'): ?>
                                <span class="badge badge-success px-3 py-2">AKTIF (OPEN)</span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-3 py-2">CLOSED</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center align-middle">
                            <?php if($p['status'] == 'CLOSED'): ?>
                                <!-- Tombol Buka Kembali -->
                                <a href="<?= base_url('periode/open/'.$p['id']) ?>" class="btn btn-warning btn-sm" onclick="return confirm('Yakin ingin membuka kembali periode ini? Periode lain akan otomatis ditutup.')" title="Buka Periode">
                                    <i class="fas fa-lock-open"></i>
                                </a>
                                <!-- Tombol Lihat Detail (Read Only) -->
                                <a href="<?= base_url('periode/detail/'.$p['id']) ?>" class="btn btn-info btn-sm" title="Lihat Transaksi">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-sm" disabled title="Sedang Aktif"><i class="fas fa-check"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>