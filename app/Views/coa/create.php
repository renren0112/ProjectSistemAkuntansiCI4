<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<!-- Header Halaman -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Akun Baru</h1>
    <a href="<?= base_url('akun') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4 border-left-success">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Formulir Akun Baru</h6>
    </div>
    <div class="card-body">
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('akun/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <!-- Kolom Kiri: Identitas Akun -->
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Kode Akun</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-hashtag"></i></span>
                            </div>
                            <input type="text" name="kode_akun" class="form-control" placeholder="Contoh: 111" required autofocus>
                        </div>
                        <small class="form-text text-muted">Kode akun harus unik (belum pernah digunakan).</small>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Nama Akun</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-pen-nib"></i></span>
                            </div>
                            <input type="text" name="nama_akun" class="form-control" placeholder="Contoh: Kas Kecil" required>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Klasifikasi Akun -->
                <div class="col-lg-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Kategori / Tipe Akun</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-info text-white"><i class="fas fa-layer-group"></i></span>
                            </div>
                            <select name="tipe_akun" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Aset">Aset (Harta)</option>
                                <option value="Liabilitas">Liabilitas (Utang)</option>
                                <option value="Ekuitas">Ekuitas (Modal)</option>
                                <option value="Pendapatan">Pendapatan (Jasa/Lainnya)</option>
                                <option value="Beban">Beban (Operasional)</option>
                                <!-- TAMBAHAN KHUSUS DAGANG -->
                                <option value="Penjualan" class="font-weight-bold text-success">Penjualan (Dagang)</option>
                                <option value="Pembelian" class="font-weight-bold text-danger">Pembelian (HPP/Dagang)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Posisi Saldo Normal</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-info text-white"><i class="fas fa-balance-scale"></i></span>
                            </div>
                            <select name="posisi_saldo_normal" class="form-control" required>
                                <option value="">-- Pilih Posisi --</option>
                                <option value="D">Debit (Bertambah di Debit)</option>
                                <option value="K">Kredit (Bertambah di Kredit)</option>
                            </select>
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle text-info"></i> Debit: Aset, Beban, Pembelian. Kredit: Utang, Modal, Pendapatan, Penjualan.
                        </small>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end">
                <a href="<?= base_url('akun') ?>" class="btn btn-secondary mr-2">
                    <i class="fas fa-times fa-sm text-white-50"></i> Batal
                </a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save fa-sm text-white-50"></i> Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection(); ?>