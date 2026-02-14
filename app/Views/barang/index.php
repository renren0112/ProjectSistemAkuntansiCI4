<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .card-header-actions { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
    .search-input { border-radius: 20px 0 0 20px !important; border: 1px solid #d1d3e2; padding-left: 15px; }
    .search-btn { border-radius: 0 20px 20px 0 !important; background-color: #4e73df; color: white; }
    
    /* Tombol Aksi Bulat Modern */
    .action-btn { 
        width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: all 0.2s ease; border: none; text-decoration: none !important; margin: 0 2px;
    }
    .btn-edit-modern { background: #f6c23e; color: #fff; } /* Kuning */
    .btn-edit-modern:hover { background: #dfa510; color: white; transform: translateY(-2px); box-shadow: 0 3px 5px rgba(246, 194, 62, 0.3); }
    
    .btn-delete-modern { background: #e74a3b; color: #fff; } /* Merah */
    .btn-delete-modern:hover { background: #be2617; color: white; transform: translateY(-2px); box-shadow: 0 3px 5px rgba(231, 74, 59, 0.3); }

    .badge-stock { font-size: 0.85rem; padding: 0.4em 0.8em; border-radius: 10rem; }
    
    /* Badge Kategori */
    .badge-laptop { background-color: #4e73df; color: white; }
    .badge-komponen { background-color: #36b9cc; color: white; }
    .badge-aksesoris { background-color: #858796; color: white; }
</style>

<div class="card shadow mb-4">
    <!-- Header Card -->
    <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-center card-header-actions">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0 d-flex align-items-center">
            <i class="fas fa-boxes mr-2"></i> Master Data Barang
        </h6>
        
        <div class="d-flex align-items-center">
            <!-- Form Pencarian -->
            <form action="" method="get" class="form-inline mr-2">
                <div class="input-group input-group-sm">
                    <input type="text" name="keyword" class="form-control search-input bg-white small" 
                           placeholder="Cari barang..." value="<?= $keyword ?? '' ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary search-btn" type="submit"><i class="fas fa-search fa-sm"></i></button>
                    </div>
                </div>
            </form>

            <a href="<?= base_url('barang/create') ?>" class="btn btn-success btn-sm shadow-sm btn-icon-split rounded-pill">
                <span class="icon text-white-50">
                    <i class="fas fa-plus"></i>
                </span>
                <span class="text">Tambah Barang</span>
            </a>
        </div>
    </div>

    <div class="card-body">
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success border-left-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th width="12%">Kode</th>
                        <th>Nama Barang</th>
                        <th width="15%">Kategori</th>
                        <th width="10%" class="text-center">Stok</th>
                        <th width="15%" class="text-right">Harga Beli</th>
                        <th width="15%" class="text-right">Harga Jual</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($barang as $b): ?>
                    <tr>
                        <td class="font-weight-bold text-primary">
                            <?= $b['kode_barang'] ?>
                        </td>
                        <td class="font-weight-bold text-gray-800"><?= $b['nama_barang'] ?></td>
                        
                        <!-- Kolom Kategori Baru -->
                        <td>
                            <?php 
                                $catClass = 'badge-secondary';
                                if($b['kategori'] == 'Laptop') $catClass = 'badge-laptop';
                                elseif($b['kategori'] == 'Komponen PC') $catClass = 'badge-komponen';
                                elseif($b['kategori'] == 'Aksesoris') $catClass = 'badge-aksesoris';
                            ?>
                            <span class="badge <?= $catClass ?> px-2 py-1 shadow-sm">
                                <?= $b['kategori'] ?>
                            </span>
                        </td>

                        <!-- Logika Badge Stok -->
                        <td class="text-center">
                            <?php if($b['stok'] == 0): ?>
                                <span class="badge badge-danger badge-stock">Habis</span>
                            <?php elseif($b['stok'] <= 5): ?>
                                <span class="badge badge-warning badge-stock text-white">Menipis (<?= $b['stok'] ?>)</span>
                            <?php else: ?>
                                <span class="badge badge-success badge-stock"><?= $b['stok'] ?></span>
                            <?php endif; ?>
                        </td>

                        <td class="text-right text-gray-600">Rp <?= number_format($b['harga_beli'], 0, ',', '.') ?></td>
                        <td class="text-right font-weight-bold text-success">Rp <?= number_format($b['harga_jual'], 0, ',', '.') ?></td>
                        
                        <td class="text-center">
                            <a href="<?= base_url('barang/edit/'.$b['id']) ?>" class="action-btn btn-edit-modern shadow-sm" title="Edit Barang" data-toggle="tooltip">
                                <i class="fas fa-pen fa-xs"></i>
                            </a>
                            <form action="<?= base_url('barang/delete/'.$b['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus barang <?= $b['nama_barang'] ?>?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="action-btn btn-delete-modern shadow-sm" title="Hapus Barang" data-toggle="tooltip">
                                    <i class="fas fa-trash-alt fa-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($barang)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <img src="https://startbootstrap.github.io/startbootstrap-sb-admin-2/img/undraw_empty.svg" alt="Empty" style="width: 150px; opacity: 0.5;">
                                <p class="text-gray-500 mt-3 mb-0">Belum ada data barang.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>