<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<style>
    .card-header-actions { background-color: #f8f9fc; border-bottom: 1px solid #e3e6f0; }
    .action-btn { 
        width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;
        border-radius: 50%; transition: all 0.2s ease; border: none; text-decoration: none !important; margin: 0 2px;
    }
    .btn-edit-modern { background: #f6c23e; color: #fff; }
    .btn-edit-modern:hover { background: #dfa510; color: white; transform: translateY(-2px); box-shadow: 0 3px 5px rgba(246, 194, 62, 0.3); }
    .btn-delete-modern { background: #e74a3b; color: #fff; }
    .btn-delete-modern:hover { background: #be2617; color: white; transform: translateY(-2px); box-shadow: 0 3px 5px rgba(231, 74, 59, 0.3); }
    
    /* UPDATE: Warna Badge Role */
    .badge-admin { background-color: #4e73df; color: white; }     /* Biru */
    .badge-accounting { background-color: #36b9cc; color: white; } /* Cyan/Turquoise */
    .badge-kasir { background-color: #1cc88a; color: white; }      /* Hijau */
    .badge-secondary { background-color: #858796; color: white; }
</style>

<div class="card shadow mb-4">
    <!-- Header Card -->
    <div class="card-header py-3 d-flex flex-column flex-md-row justify-content-between align-items-center card-header-actions">
        <h6 class="m-0 font-weight-bold text-primary mb-2 mb-md-0 d-flex align-items-center">
            <i class="fas fa-users-cog mr-2"></i> Manajemen Pengguna
        </h6>
        
        <a href="<?= base_url('user/create') ?>" class="btn btn-primary btn-sm shadow-sm btn-icon-split rounded-pill">
            <span class="icon text-white-50">
                <i class="fas fa-user-plus"></i>
            </span>
            <span class="text">Tambah User Baru</span>
        </a>
    </div>

    <div class="card-body">
        
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success border-left-success alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-left-danger alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-dark">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th width="20%" class="text-center">Role / Hak Akses</th>
                        <th width="15%">Terdaftar Sejak</th>
                        <th width="10%" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no=1; foreach($users as $u): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td class="font-weight-bold text-primary">
                            <img class="img-profile rounded-circle mr-2" src="https://ui-avatars.com/api/?name=<?= urlencode($u['nama_lengkap']) ?>&size=30&background=random" width="30">
                            <?= $u['username'] ?>
                        </td>
                        <td class="text-gray-800"><?= $u['nama_lengkap'] ?></td>
                        <td class="text-center">
                            <?php 
                                $badgeClass = 'badge-secondary';
                                $icon = '';
                                
                                // UPDATE: Logic Badge Baru
                                if ($u['role'] == 'admin') {
                                    $badgeClass = 'badge-admin'; 
                                    $icon='<i class="fas fa-user-shield mr-1"></i>'; 
                                } elseif ($u['role'] == 'accounting') {
                                    $badgeClass = 'badge-accounting'; 
                                    $icon='<i class="fas fa-file-invoice-dollar mr-1"></i>'; 
                                } elseif ($u['role'] == 'kasir') {
                                    $badgeClass = 'badge-kasir'; 
                                    $icon='<i class="fas fa-cash-register mr-1"></i>'; 
                                }
                            ?>
                            <span class="badge <?= $badgeClass ?> px-3 py-2 shadow-sm rounded-pill">
                                <?= $icon . strtoupper($u['role']) ?>
                            </span>
                        </td>
                        <td class="small text-muted">
                            <i class="far fa-calendar-alt mr-1"></i> <?= date('d M Y', strtotime($u['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('user/edit/'.$u['id_user']) ?>" class="action-btn btn-edit-modern shadow-sm" title="Edit User">
                                <i class="fas fa-pen fa-xs"></i>
                            </a>
                            
                            <!-- Proteksi: Jangan hapus akun sendiri -->
                            <?php if(session()->get('id_user') != $u['id_user']): ?>
                                <form action="<?= base_url('user/delete/'.$u['id_user']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user <?= $u['username'] ?>?');">
                                    <button type="submit" class="action-btn btn-delete-modern shadow-sm" title="Hapus User">
                                        <i class="fas fa-trash-alt fa-xs"></i>
                                    </button>
                                </form>
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