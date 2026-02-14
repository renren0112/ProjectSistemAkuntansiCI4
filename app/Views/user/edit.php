<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Data User</h1>
    <a href="<?= base_url('user') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">Edit Pengguna: <?= $user['username'] ?></h6>
            </div>
            <div class="card-body">
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger border-left-danger shadow-sm">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('user/update/'.$user['id_user']) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-gray-700">Username</label>
                            <input type="text" class="form-control bg-light" value="<?= $user['username'] ?>" readonly>
                            <small class="text-muted"><i class="fas fa-lock mr-1"></i> Username tidak dapat diubah.</small>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-gray-700">Password Baru</label>
                            <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ubah password">
                            <small class="text-muted">Isi hanya jika ingin mereset password.</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="form-control" value="<?= $user['nama_lengkap'] ?>" required>
                    </div>

                    <!-- UPDATE: Opsi Role Sesuai Revisi -->
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Hak Akses (Role)</label>
                        <select name="role" class="form-control">
                            <option value="kasir" <?= $user['role'] == 'kasir' ? 'selected' : '' ?>>Kasir</option>
                            <option value="accounting" <?= $user['role'] == 'accounting' ? 'selected' : '' ?>>Accounting</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Administrator</option>
                        </select>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="<?= base_url('user') ?>" class="btn btn-secondary mr-2">Batal</a>
                        <button type="submit" class="btn btn-warning px-4 shadow text-white">
                            <i class="fas fa-save mr-2"></i> Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>