<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tambah Barang Baru</h1>
    <a href="<?= base_url('barang') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left mr-2"></i> Kembali
    </a>
</div>

<div class="card shadow mb-4 border-left-success">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-box-open mr-2"></i>Formulir Data Barang</h6>
    </div>
    <div class="card-body">
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('barang/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <!-- Kolom Kiri: Identitas -->
                <div class="col-lg-6 border-right">
                    <h6 class="text-gray-900 font-weight-bold mb-3">Identitas Produk</h6>
                    
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Kode Barang</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-barcode"></i></span>
                            </div>
                            <input type="text" name="kode_barang" class="form-control" placeholder="Contoh: B001" required autofocus>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Nama Barang</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-box"></i></span>
                            </div>
                            <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Laptop Asus..." required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Kategori</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-tags"></i></span>
                            </div>
                            <select name="kategori" class="form-control" required>
                                <option value="">-- Pilih Kategori --</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Komponen PC">Komponen PC</option>
                                <option value="Aksesoris">Aksesoris</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail Harga & Stok -->
                <div class="col-lg-6 pl-lg-4">
                    <h6 class="text-gray-900 font-weight-bold mb-3">Detail Harga & Stok</h6>

                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Stok Awal</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-secondary text-white"><i class="fas fa-cubes"></i></span>
                            </div>
                            <input type="number" name="stok" class="form-control" value="0" min="0">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-primary">Harga Beli (HPP)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-primary text-white">Rp</span>
                                    </div>
                                    <input type="number" name="harga_beli" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold text-success">Harga Jual</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-success text-white">Rp</span>
                                    </div>
                                    <input type="number" name="harga_jual" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end">
                <a href="<?= base_url('barang') ?>" class="btn btn-secondary mr-2">
                    <i class="fas fa-times fa-sm text-white-50"></i> Batal
                </a>
                <button type="submit" class="btn btn-success px-4 shadow">
                    <i class="fas fa-save fa-sm text-white-50"></i> Simpan Barang
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?>