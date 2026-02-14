<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<!-- STYLE TAMBAHAN UNTUK PAGINATION -->
<style>
    .pagination {
        display: flex;
        padding-left: 0;
        list-style: none;
        border-radius: 0.35rem;
        justify-content: center; /* Tengah */
    }
    .pagination li {
        margin: 0 5px; /* Jarak antar nomor halaman */
    }
    .pagination li a, .pagination li span {
        position: relative;
        display: block;
        padding: 0.5rem 0.75rem;
        margin-left: -1px;
        line-height: 1.25;
        color: #4e73df;
        background-color: #fff;
        border: 1px solid #dddfeb;
        border-radius: 0.35rem;
        text-decoration: none;
    }
    .pagination li.active span {
        z-index: 3;
        color: #fff;
        background-color: #4e73df;
        border-color: #4e73df;
    }
    .pagination li.disabled span {
        color: #858796;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dddfeb;
    }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Riwayat Transaksi Jurnal</h1>
    
    <div class="dropdown no-arrow">
        <button class="btn btn-primary btn-sm shadow-sm dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown">
            <i class="fas fa-plus fa-sm text-white-50 mr-1"></i> Input Transaksi Baru
        </button>
        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
            <div class="dropdown-header">Pilih Jenis Transaksi:</div>
            <a class="dropdown-item" href="<?= base_url('transaksi/create') ?>"><i class="fas fa-file-signature text-primary mr-2"></i> Jurnal Umum</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?= base_url('transaksi/create-penjualan') ?>"><i class="fas fa-cash-register text-success mr-2"></i> Jurnal Penjualan</a>
            <a class="dropdown-item" href="<?= base_url('transaksi/create-penerimaan') ?>"><i class="fas fa-hand-holding-usd text-success mr-2"></i> Penerimaan Kas</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?= base_url('transaksi/create-pembelian') ?>"><i class="fas fa-shopping-cart text-danger mr-2"></i> Jurnal Pembelian</a>
            <a class="dropdown-item" href="<?= base_url('transaksi/create-pengeluaran') ?>"><i class="fas fa-file-invoice-dollar text-danger mr-2"></i> Pengeluaran Kas</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?= base_url('transaksi/penyesuaian') ?>"><i class="fas fa-edit text-warning mr-2"></i> Jurnal Penyesuaian</a>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow mb-4 border-left-info">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-filter mr-1"></i> Filter & Mode Tampilan</h6>
    </div>
    <div class="card-body">
        <form action="" method="get">
            <div class="form-row align-items-end">
                <div class="col-md-3 mb-2">
                    <label class="small font-weight-bold">Kata Kunci</label>
                    <input type="text" name="keyword" class="form-control" placeholder="Cari No Bukti / Ket..." value="<?= $filter['keyword'] ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Dari Tanggal</label>
                    <input type="date" name="start" class="form-control" value="<?= $filter['start'] ?>">
                </div>
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Sampai Tanggal</label>
                    <input type="date" name="end" class="form-control" value="<?= $filter['end'] ?>">
                </div>
                
                <!-- FILTER JENIS JURNAL -->
                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Jenis Jurnal</label>
                    <select name="jenis" class="form-control">
                        <option value="">Semua Jenis</option>
                        <option value="Umum" <?= $filter['jenis'] == 'Umum' ? 'selected' : '' ?>>Jurnal Umum</option>
                        <option value="Penjualan" <?= $filter['jenis'] == 'Penjualan' ? 'selected' : '' ?>>Penjualan</option>
                        <option value="Pembelian" <?= $filter['jenis'] == 'Pembelian' ? 'selected' : '' ?>>Pembelian</option>
                        <option value="PenerimaanKas" <?= $filter['jenis'] == 'PenerimaanKas' ? 'selected' : '' ?>>Penerimaan Kas</option>
                        <option value="PengeluaranKas" <?= $filter['jenis'] == 'PengeluaranKas' ? 'selected' : '' ?>>Pengeluaran Kas</option>
                        <option value="Penyesuaian" <?= $filter['jenis'] == 'Penyesuaian' ? 'selected' : '' ?>>Penyesuaian (AJP)</option>
                        <option value="Penutup" <?= $filter['jenis'] == 'Penutup' ? 'selected' : '' ?>>Jurnal Penutup</option>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <label class="small font-weight-bold">Mode Tampilan</label>
                    <select name="mode" class="form-control">
                        <option value="ringkas" <?= $filter['mode'] == 'ringkas' ? 'selected' : '' ?>>Ringkas (Per Transaksi)</option>
                        <option value="detail" <?= $filter['mode'] == 'detail' ? 'selected' : '' ?>>Detail (Rincian Akun)</option>
                    </select>
                </div>
                
                <div class="col-md-1 mb-2">
                    <button type="submit" class="btn btn-info btn-block shadow-sm"><i class="fas fa-search"></i></button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('info')): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle mr-2"></i><?= session()->getFlashdata('info') ?>
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<!-- Tabel Data -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
        <h6 class="m-0 font-weight-bold text-primary">Data Transaksi (Mode: <?= ucfirst($filter['mode']) ?>)</h6>
        
        <?php if($filter['mode'] == 'ringkas'): 
            $grandTotal = 0;
            if(!empty($jurnal)) {
                foreach($jurnal as $j) $grandTotal += $j['total_nilai'];
            }
        ?>
            <span class="badge badge-primary px-3 py-2" style="font-size: 0.9rem;">
                Total Nilai: Rp <?= number_format($grandTotal, 0, ',', '.') ?>
            </span>
        <?php endif; ?>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th width="12%">Tanggal</th>
                        <th width="15%">No. Bukti</th>
                        
                        <?php if($filter['mode'] == 'detail'): ?>
                            <th>Akun / Keterangan</th>
                            <th width="12%" class="text-right">Debit</th>
                            <th width="12%" class="text-right">Kredit</th>
                        <?php else: ?>
                            <th>Keterangan</th>
                            <th width="15%" class="text-right">Total Nilai</th>
                        <?php endif; ?>

                        <th width="10%" class="text-center">Status</th>
                        <th width="8%" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if(empty($jurnal)): ?>
                        <tr><td colspan="<?= $filter['mode'] == 'detail' ? 8 : 7 ?>" class="text-center py-4 text-muted font-italic">Tidak ada data transaksi ditemukan.</td></tr>
                    <?php else: ?>
                        <?php 
                        $no = 1 + (10 * ($pager_links['currentPage'] ?? 0)); 
                        $globalDebit = 0;
                        $globalKredit = 0;

                        foreach($jurnal as $j): 
                            $badgeClass = 'badge-secondary';
                            $prefix = explode('-', $j['no_bukti'])[0];
                            
                            switch($prefix) {
                                case 'JU':  $badgeClass = 'badge-primary'; break; 
                                case 'JPJ': 
                                case 'BKM': $badgeClass = 'badge-success'; break; 
                                case 'JPB': 
                                case 'BKK': $badgeClass = 'badge-danger'; break; 
                                case 'AJP': $badgeClass = 'badge-warning text-dark'; break; 
                                case 'JP':  $badgeClass = 'badge-dark'; break; 
                            }
                        ?>
                        
                        <!-- MODE DETAIL -->
                        <?php if($filter['mode'] == 'detail'): 
                            $globalDebit += $j['debit'];
                            $globalKredit += $j['kredit'];
                        ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle"><?= date('d/m/Y', strtotime($j['tgl_jurnal'])) ?></td>
                                <td class="align-middle">
                                    <span class="badge <?= $badgeClass ?> px-2 py-1 shadow-sm">
                                        <?= $j['no_bukti'] ?>
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <span class="font-weight-bold text-primary"><?= $j['kode_akun'] ?></span> - <?= $j['nama_akun'] ?>
                                    <br><small class="text-muted font-italic"><?= $j['keterangan'] ?></small>
                                </td>
                                <td class="text-right align-middle text-primary">
                                    <?= $j['debit'] > 0 ? number_format($j['debit'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-right align-middle text-success">
                                    <?= $j['kredit'] > 0 ? number_format($j['kredit'], 0, ',', '.') : '-' ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if($j['status_posting'] == 'Posted'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check"></i> Posted</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <a href="<?= base_url('transaksi/detail/'.$j['id_jurnal']) ?>" class="btn btn-info btn-circle btn-sm" title="Detail"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        
                        <!-- MODE RINGKAS -->
                        <?php else: ?>
                            <tr>
                                <td class="text-center align-middle"><?= $no++ ?></td>
                                <td class="align-middle"><?= date('d/m/Y', strtotime($j['tgl_jurnal'])) ?></td>
                                <td class="align-middle">
                                    <span class="badge <?= $badgeClass ?> px-2 py-1 shadow-sm">
                                        <?= $j['no_bukti'] ?>
                                    </span>
                                    <div class="small text-muted mt-1"><?= $j['jenis_transaksi'] ?></div>
                                </td>
                                <td class="align-middle"><?= $j['keterangan'] ?></td>
                                <td class="text-right align-middle font-weight-bold">
                                    Rp <?= number_format($j['total_nilai'] ?? 0, 0, ',', '.') ?>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if($j['status_posting'] == 'Posted'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Posted</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><i class="fas fa-clock mr-1"></i> Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle btn btn-sm btn-light border shadow-sm" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                                            <a class="dropdown-item" href="<?= base_url('transaksi/detail/'.$j['id_jurnal']) ?>"><i class="fas fa-eye text-info mr-2"></i> Lihat Detail</a>
                                            <a class="dropdown-item" href="<?= base_url('transaksi/cetak/'.$j['id_jurnal']) ?>" target="_blank"><i class="fas fa-print text-secondary mr-2"></i> Cetak Bukti</a>
                                            
                                            <?php if($j['status_posting'] == 'Pending'): ?>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="<?= base_url('transaksi/posting/'.$j['id_jurnal']) ?>"><i class="fas fa-check text-success mr-2"></i> Posting Sekarang</a>
                                                <a class="dropdown-item" href="<?= base_url('transaksi/delete/'.$j['id_jurnal']) ?>" onclick="return confirm('Hapus transaksi ini?')"><i class="fas fa-trash text-danger mr-2"></i> Hapus</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>

                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- FOOTER TOTAL (KHUSUS MODE DETAIL) -->
                <?php if($filter['mode'] == 'detail' && !empty($jurnal)): ?>
                <tfoot class="bg-gray-200 font-weight-bold">
                    <tr>
                        <td colspan="4" class="text-right text-uppercase">Total Halaman Ini :</td>
                        <td class="text-right text-primary">Rp <?= number_format($globalDebit, 0, ',', '.') ?></td>
                        <td class="text-right text-success">Rp <?= number_format($globalKredit, 0, ',', '.') ?></td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>

            </table>
        </div>
        
        <!-- PAGINATION DENGAN JARAK -->
        <div class="mt-4 d-flex justify-content-center">
            <?= $pager_links ?? '' ?>
        </div>
    </div>
</div>

<!-- TOMBOL POSTING SEMUA (Selalu Muncul jika ada transaksi) -->
<?php if(isset($jurnal) && count($jurnal) > 0): ?>
<div class="text-center mb-5">
    <a href="<?= base_url('transaksi/posting-otomatis') ?>" class="btn btn-success shadow-lg rounded-pill px-4" onclick="return confirm('Yakin ingin memposting semua jurnal yang masih Pending?');">
        <i class="fas fa-rocket mr-2"></i> Posting Semua Transaksi Pending
    </a>
</div>
<?php endif; ?>

<?= $this->endSection(); ?>