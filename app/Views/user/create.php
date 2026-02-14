<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah User Baru</h1>
    <a href="<?= base_url('user') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Pendaftaran User</h6>
            </div>
            <div class="card-body">
                
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger border-left-danger shadow-sm">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('user/store') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-gray-700">Username (Login)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="username" class="form-control" placeholder="Contoh: kasir01" required autofocus>
                            </div>
                        </div>

                        <div class="form-group col-md-6">
                            <label class="font-weight-bold text-gray-700">Password</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="password" class="form-control" placeholder="********" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Nama Lengkap</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-secondary text-white"><i class="fas fa-id-card"></i></span>
                            </div>
                            <input type="text" name="nama_lengkap" class="form-control" placeholder="Nama Pengguna" required>
                        </div>
                    </div>

                    <!-- UPDATE: Opsi Role Sesuai Revisi -->
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Hak Akses (Role)</label>
                        <select name="role" class="form-control">
                            <option value="kasir">Kasir (Hanya Transaksi)</option>
                            <option value="accounting">Accounting (Laporan & Transaksi)</option>
                            <option value="admin">Administrator (Full Access)</option>
                        </select>
                        <small class="form-text text-muted">
                            <ul>
                                <li><b>Kasir:</b> Input Jurnal saja.</li>
                                <li><b>Accounting:</b> Input Jurnal + Lihat Laporan.</li>
                                <li><b>Admin:</b> Semua fitur termasuk kelola user.</li>
                            </ul>
                        </small>
                    </div>

                    <hr class="mt-4">
                    <div class="d-flex justify-content-end">
                        <a href="<?= base_url('user') ?>" class="btn btn-secondary mr-2">Batal</a>
                        <button type="submit" class="btn btn-primary px-4 shadow">
                            <i class="fas fa-save mr-2"></i> Simpan User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>