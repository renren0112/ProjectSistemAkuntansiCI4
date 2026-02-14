<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Proses Tutup Buku (Closing)</h1>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-left-danger shadow-sm alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="card shadow mb-4 border-left-danger">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-danger"><i class="fas fa-gavel mr-2"></i>Wizard Penutupan Akhir Periode</h6>
            </div>
            <div class="card-body">
                
                <div class="alert alert-warning text-dark mb-4 border-0 bg-warning-light" style="background-color: #fff3cd;">
                    <h5 class="alert-heading font-weight-bold"><i class="fas fa-info-circle mr-1"></i> Perhatian!</h5>
                    <p class="mb-0">
                        Proses ini akan secara otomatis membuat <b>Jurnal Penutup</b> untuk me-nol-kan saldo Pendapatan dan Beban, serta memindahkan Laba/Rugi ke Modal.
                        <br>Pastikan Anda telah menyelesaikan semua <b>Jurnal Penyesuaian (AJP)</b> sebelum melanjutkan.
                    </p>
                </div>

                <form action="<?= base_url('penutupan/proses') ?>" method="post" onsubmit="return confirm('⚠️ PERINGATAN FINAL:\n\nApakah Anda yakin ingin melakukan Tutup Buku untuk periode ini?\n\nPastikan semua data sudah benar.');">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <!-- KOLOM KIRI: PERIODE -->
                        <div class="col-md-5 border-right">
                            <h6 class="text-primary font-weight-bold mb-3 text-uppercase small">1. Pilih Periode Tutup Buku</h6>
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Bulan</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-danger text-white"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <select name="bulan" class="form-control">
                                        <?php for($m=1; $m<=12; $m++): ?>
                                            <option value="<?= $m ?>" <?= (date('m') == $m) ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold text-gray-700">Tahun</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-danger text-white"><i class="fas fa-calendar"></i></span>
                                    </div>
                                    <input type="number" name="tahun" class="form-control" value="<?= date('Y') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN: AKUN TARGET -->
                        <div class="col-md-7 pl-md-4">
                            <h6 class="text-primary font-weight-bold mb-3 text-uppercase small">2. Konfigurasi Akun Target</h6>
                            
                            <div class="form-group">
                                <label class="text-gray-800 font-weight-bold">Akun Ikhtisar Laba Rugi (Perantara)</label>
                                <select name="akun_ikhtisar" class="form-control select-akun" required>
                                    <option value="">-- Pilih Akun Ikhtisar LR --</option>
                                    <?php foreach($all_akun as $a): ?>
                                        <option value="<?= $a['kode_akun'] ?>"><?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Akun sementara untuk menampung selisih pendapatan dan beban sebelum ke modal.</small>
                            </div>

                            <div class="form-group">
                                <label class="text-gray-800 font-weight-bold">Akun Modal Pemilik (Target Akhir)</label>
                                <select name="akun_modal" class="form-control" required>
                                    <option value="">-- Pilih Akun Modal --</option>
                                    <?php foreach($akun_ekuitas as $e): ?>
                                        <option value="<?= $e['kode_akun'] ?>"><?= $e['kode_akun'] ?> - <?= $e['nama_akun'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Laba/Rugi Bersih akan ditambahkan/dikurangkan ke akun ini.</small>
                            </div>

                            <div class="form-group">
                                <label class="text-gray-800 font-weight-bold">Akun Prive / Drawing (Opsional)</label>
                                <select name="akun_prive" class="form-control">
                                    <option value="">-- Tidak Ada / Jangan Tutup Prive --</option>
                                    <?php foreach($akun_ekuitas as $e): ?>
                                        <option value="<?= $e['kode_akun'] ?>"><?= $e['kode_akun'] ?> - <?= $e['nama_akun'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="form-text text-muted">Jika dipilih, saldo Prive akan dipindahkan ke Modal (Mengurangi Modal).</small>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="text-muted small mb-3">Klik tombol di bawah untuk memproses Jurnal Penutup secara otomatis.</p>
                        <button type="submit" class="btn btn-danger btn-lg shadow px-5 rounded-pill">
                            <i class="fas fa-check-circle mr-2"></i> JALANKAN PROSES TUTUP BUKU
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>