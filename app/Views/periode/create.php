<?= $this->extend('layout/template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <h4 class="mb-4">
        <i class="fas fa-calendar-plus"></i> Tambah Periode Akuntansi
    </h4>

    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <strong>Form Periode Baru</strong>
        </div>

        <div class="card-body">
            <form action="<?= base_url('periode/store') ?>" method="post">

                <div class="row">
                    <!-- Tahun -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tahun</label>
                        <input type="number" name="tahun" class="form-control"
                               value="<?= date('Y') ?>" required>
                    </div>

                    <!-- Bulan -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Bulan</label>
                        <select name="bulan" class="form-control" required>
                            <?php
                            $bulan = [
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ];
                            foreach ($bulan as $key => $val):
                            ?>
                                <option value="<?= $key ?>"><?= $val ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <!-- Nama Periode -->
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama Periode</label>
                        <input type="text" name="nama_periode"
                               class="form-control"
                               placeholder="Contoh: Januari 2026"
                               required>
                    </div>
                </div>

                <div class="row">
                    <!-- Tanggal Mulai -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tgl_mulai" class="form-control" required>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tgl_selesai" class="form-control" required>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Periode baru akan dibuat dengan status <strong>CLOSED</strong>.
                    Gunakan tombol <strong>Open</strong> untuk mengaktifkan periode.
                </div>

                <div class="text-right">
                    <a href="<?= base_url('periode') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Periode
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<?= $this->endSection(); ?>
