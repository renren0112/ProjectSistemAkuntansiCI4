<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .card-header-actions { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
    .search-input { border-radius: 20px 0 0 20px !important; border: 1px solid #d1d3e2; padding-left: 15px; }
    .search-btn { border-radius: 0 20px 20px 0 !important; background-color: #4e73df; color: white; }
    
    /* STYLING TOMBOL AKSI BARU (KEREN & MODERN) */
    .action-btn { 
        width: 32px; 
        height: 32px; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center;
        border-radius: 50%; 
        transition: all 0.2s ease;
        border: none;
        text-decoration: none !important;
        margin: 0 2px;
    }
    /* Warna Edit: Biru Soft -> Biru Solid */
    .btn-edit-modern { background: #eaecf4; color: #4e73df; }
    .btn-edit-modern:hover { background: #4e73df; color: white; transform: translateY(-2px); box-shadow: 0 3px 5px rgba(78, 115, 223, 0.3); }
    
    /* Warna Hapus: Merah Soft -> Merah Solid */
    .btn-delete-modern { background: #fceceb; color: #e74a3b; }
    .btn-delete-modern:hover { background: #e74a3b; color: white; transform: translateY(-2px); box-shadow: 0 3px 5px rgba(231, 74, 59, 0.3); }
</style>

<div class="card shadow mb-4">
    <!-- Header Card -->
    <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-center card-header-actions">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0 d-flex align-items-center">
            <i class="fas fa-list-ul mr-2"></i> Daftar Akun (COA)
        </h6>
        
        <div class="d-flex align-items-center flex-wrap">
            <!-- Form Pencarian -->
            <form action="" method="get" class="form-inline mr-2 mb-2 mb-md-0">
                <div class="input-group input-group-sm">
                    <input type="text" name="keyword" class="form-control search-input bg-white small" 
                           placeholder="Cari akun..." value="<?= $keyword ?? '' ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary search-btn" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <!-- GROUP TOMBOL AKSI -->
            <div class="btn-group btn-group-sm shadow-sm mb-2 mb-md-0">
                <a href="<?= base_url('akun/export') ?>" target="_blank" class="btn btn-success" title="Download Excel">
                    <i class="fas fa-file-excel mr-1"></i> Export
                </a>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#importModal" title="Upload CSV">
                    <i class="fas fa-file-upload mr-1"></i> Import
                </button>
                <a href="<?= base_url('akun/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus mr-1"></i> Tambah
                </a>
            </div>
        </div>
    </div>

    <div class="card-body">
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-left-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th width="15%">Kode</th>
                        <th>Nama Akun</th>
                        <th width="15%">Tipe</th>
                        <th width="15%">Saldo Normal</th>
                        <th width="12%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($akun as $row): ?>
                    <tr>
                        <td class="font-weight-bold text-primary"><?= $row['kode_akun'] ?></td>
                        <td class="font-weight-bold text-gray-800"><?= $row['nama_akun'] ?></td>
                        <td>
                            <?php 
                                $badgeColor = 'secondary';
                                if(in_array($row['tipe_akun'], ['Aset'])) $badgeColor = 'success';
                                elseif(in_array($row['tipe_akun'], ['Liabilitas', 'Ekuitas'])) $badgeColor = 'warning text-dark';
                                elseif(in_array($row['tipe_akun'], ['Pendapatan'])) $badgeColor = 'info';
                                elseif(in_array($row['tipe_akun'], ['Beban'])) $badgeColor = 'danger';
                            ?>
                            <span class="badge badge-<?= $badgeColor ?> badge-pill shadow-sm px-2 py-1"><?= $row['tipe_akun'] ?></span>
                        </td>
                        <td>
                            <?php if($row['posisi_saldo_normal'] == 'D'): ?>
                                <span class="text-success font-weight-bold"><i class="fas fa-arrow-up mr-1"></i> Debit</span>
                            <?php else: ?>
                                <span class="text-danger font-weight-bold"><i class="fas fa-arrow-down mr-1"></i> Kredit</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <!-- TOMBOL EDIT BARU -->
                            <a href="<?= base_url('akun/edit/'.$row['kode_akun']) ?>" class="action-btn btn-edit-modern shadow-sm" title="Edit Akun" data-toggle="tooltip">
                                <i class="fas fa-pen fa-xs"></i>
                            </a>
                            
                            <!-- TOMBOL HAPUS BARU -->
                            <form action="<?= base_url('akun/delete/'.$row['kode_akun']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus akun <?= $row['nama_akun'] ?>?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="action-btn btn-delete-modern shadow-sm" title="Hapus Akun" data-toggle="tooltip">
                                    <i class="fas fa-trash-alt fa-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($akun)): ?>
                        <tr><td colspan="5" class="text-center py-5 text-gray-500">Data akun tidak ditemukan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL IMPORT CSV -->
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-file-upload mr-2"></i>Import Akun dari CSV</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('akun/import') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="small text-muted">
                        Format file wajib <b>.CSV</b> (Comma Separated Values).<br>
                        Urutan kolom: <b>Kode, Nama, Tipe, Posisi(D/K)</b>.
                        <br>Baris pertama (Header) akan diabaikan.
                    </p>
                    <div class="form-group">
                        <label>Pilih File CSV</label>
                        <input type="file" name="file_excel" class="form-control-file" required accept=".csv">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload & Proses</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>