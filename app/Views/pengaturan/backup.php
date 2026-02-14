<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Backup & Restore Database</h1>
</div>

<div class="row">
    <!-- Card Backup -->
    <div class="col-lg-6">
        <div class="card shadow mb-4 border-left-primary">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Backup Data (Export)</h6>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-database fa-4x text-gray-300 mb-3"></i>
                <p>Unduh seluruh data database (Jurnal, Akun, Pengaturan, dll) ke dalam file <b>.SQL</b>. Simpan file ini di tempat aman.</p>
                <a href="<?= base_url('backup/download') ?>" class="btn btn-primary btn-lg shadow-sm">
                    <i class="fas fa-download mr-2"></i> Download Database
                </a>
            </div>
        </div>
    </div>

    <!-- Card Restore -->
    <div class="col-lg-6">
        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Restore Data (Import)</h6>
            </div>
            <div class="card-body">
                <?php if(session()->getFlashdata('success')): ?>
                    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                <?php endif; ?>
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                <?php endif; ?>

                <div class="text-center mb-3">
                    <i class="fas fa-upload fa-4x text-gray-300"></i>
                </div>
                <p class="small text-danger font-weight-bold text-center">PERINGATAN: Restore akan MENIMPA/MENGHAPUS seluruh data saat ini dengan data dari file backup.</p>
                
                <form action="<?= base_url('backup/restore') ?>" method="post" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="file_backup" name="file_backup" accept=".sql" required>
                            <label class="custom-file-label" for="file_backup">Pilih file .sql...</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-danger btn-block shadow-sm" onclick="return confirm('Yakin ingin merestore? Data saat ini akan hilang!')">
                        <i class="fas fa-history mr-2"></i> Restore Database
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Script agar nama file muncul saat dipilih
document.querySelector('.custom-file-input').addEventListener('change', function(e){
    var fileName = document.getElementById("file_backup").files[0].name;
    var nextSibling = e.target.nextElementSibling;
    nextSibling.innerText = fileName;
});
</script>

<?= $this->endSection(); ?>