<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Pengaturan Perusahaan</h1>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-building mr-2"></i>Identitas Instansi</h6>
            </div>
            <div class="card-body">
                
                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form action="<?= base_url('pengaturan/update') ?>" method="post">
                    <?= csrf_field(); ?>
                    
                    <div class="form-group row">
                        <label for="nama_perusahaan" class="col-sm-3 col-form-label font-weight-bold text-gray-700">Nama Perusahaan</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="nama_perusahaan" name="nama_perusahaan" value="<?= $nama_perusahaan ?>" required>
                            <small class="form-text text-muted">Akan muncul di Sidebar dan Kop Laporan.</small>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="alamat_perusahaan" class="col-sm-3 col-form-label font-weight-bold text-gray-700">Alamat Lengkap</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="alamat_perusahaan" name="alamat_perusahaan" rows="3"><?= $alamat_perusahaan ?></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="no_telp" class="col-sm-3 col-form-label font-weight-bold text-gray-700">No. Telepon</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="no_telp" name="no_telp" value="<?= $no_telp ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email_perusahaan" class="col-sm-3 col-form-label font-weight-bold text-gray-700">Email Resmi</label>
                        <div class="col-sm-9">
                            <input type="email" class="form-control" id="email_perusahaan" name="email_perusahaan" value="<?= $email_perusahaan ?>">
                        </div>
                    </div>

                    <div class="form-group row mt-4">
                        <div class="col-sm-9 offset-sm-3">
                            <button type="submit" class="btn btn-primary shadow-sm px-4">
                                <i class="fas fa-save mr-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow mb-4 border-left-info">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info"><i class="fas fa-info-circle mr-1"></i> Informasi</h6>
            </div>
            <div class="card-body">
                <p class="text-gray-700">
                    Pengaturan ini bersifat global:
                </p>
                <ul class="pl-3 small text-gray-600">
                    <li>Nama Perusahaan akan mengganti teks di Sidebar.</li>
                    <li>Nama & Alamat akan menjadi Kop Surat Laporan PDF.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>