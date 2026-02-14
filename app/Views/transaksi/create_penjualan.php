<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Input Jurnal Penjualan (Kredit)</h1>
    <a href="<?= base_url('transaksi') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Kembali ke Riwayat
    </a>
</div>

<div class="card shadow mb-4 border-left-success">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-cash-register mr-1"></i> Form Penjualan Kredit (Sales Journal)</h6>
    </div>
    <div class="card-body">
        
        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle mr-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>
        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('transaksi/store') ?>" method="post" id="formPenjualan">
            <?= csrf_field() ?>
            <input type="hidden" name="jenis_transaksi" value="Penjualan">

            <!-- HEADER JURNAL -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">No. Bukti</label>
                        <input type="text" name="no_bukti" class="form-control bg-light" value="<?= $no_bukti_auto ?>" readonly>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Tanggal Transaksi</label>
                        <input type="date" name="tgl_jurnal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="font-weight-bold text-gray-700">Keterangan</label>
                        <input type="text" name="keterangan" class="form-control" placeholder="Contoh: Penjualan kredit kepada Toko Sejahtera..." required>
                    </div>
                </div>
            </div>

            <div class="alert alert-success py-2 small" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724;">
                <i class="fas fa-info-circle mr-1"></i> 
                Transaksi <b>Non-Tunai (Kredit)</b>. Akun Debit otomatis diarahkan ke <b>Piutang</b>, dan Kredit ke <b>Penjualan</b>.
            </div>

            <!-- DETAIL JURNAL -->
            <div class="table-responsive">
                <table class="table table-bordered" id="tableDetail">
                    <thead class="thead-light">
                        <tr>
                            <th width="40%">Akun</th>
                            <th width="25%" class="text-center bg-info text-white">Debit (Rp)</th>
                            <th width="25%" class="text-center bg-success text-white">Kredit (Rp)</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- BARIS 1: DEBIT (PIUTANG DAGANG) -->
                        <tr>
                            <td>
                                <select name="kode_akun[]" class="form-control select-akun" required>
                                    <option value="">-- Pilih Akun Piutang --</option>
                                    <?php foreach($akun_aset as $a): ?>
                                        <!-- Auto Select jika nama akun mengandung 'Piutang' -->
                                        <option value="<?= $a['kode_akun'] ?>" <?= (stripos($a['nama_akun'], 'Piutang') !== false) ? 'selected' : '' ?>>
                                            <?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="text" name="debit[]" class="form-control text-right input-rupiah" id="inputDebit1" placeholder="0" onkeyup="copyToCredit()" required>
                                </div>
                            </td>
                            <td>
                                <input type="hidden" name="kredit[]" value="0">
                                <input type="text" class="form-control text-right bg-light" value="0" disabled>
                            </td>
                            <td></td>
                        </tr>

                        <!-- BARIS 2: KREDIT (PENJUALAN) -->
                        <tr>
                            <td>
                                <select name="kode_akun[]" class="form-control select-akun" required>
                                    <option value="">-- Pilih Akun Penjualan --</option>
                                    <?php foreach($akun_pendapatan as $a): ?>
                                        <!-- Auto Select jika nama akun mengandung 'Penjualan' -->
                                        <option value="<?= $a['kode_akun'] ?>" <?= (stripos($a['nama_akun'], 'Penjualan') !== false) ? 'selected' : '' ?>>
                                            <?= $a['kode_akun'] ?> - <?= $a['nama_akun'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="debit[]" value="0">
                                <input type="text" class="form-control text-right bg-light" value="0" disabled>
                            </td>
                            <td>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text">Rp</span></div>
                                    <input type="text" name="kredit[]" class="form-control text-right input-rupiah" id="inputKredit1" placeholder="0" onkeyup="hitungTotal()" required>
                                </div>
                            </td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td class="text-right align-middle text-uppercase text-gray-600" style="font-size: 1rem;">
                                Total Transaksi :
                            </td>
                            <!-- Style Keren untuk Total Debit -->
                            <td class="text-right align-middle bg-gray-100 pr-4" style="border-bottom: 4px solid #36b9cc;">
                                <span class="text-info small d-block font-weight-bold">TOTAL DEBIT</span>
                                <span id="labelTotalDebit" class="h4 font-weight-bold text-gray-800">0</span>
                            </td>
                            <!-- Style Keren untuk Total Kredit -->
                            <td class="text-right align-middle bg-gray-100 pr-4" style="border-bottom: 4px solid #1cc88a;">
                                <span class="text-success small d-block font-weight-bold">TOTAL KREDIT</span>
                                <span id="labelTotalKredit" class="h4 font-weight-bold text-gray-800">0</span>
                            </td>
                            <td class="bg-gray-100"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-between align-items-center">
                <span id="statusBalance" class="badge badge-secondary px-3 py-2">Belum Input</span>
                
                <div>
                    <a href="<?= base_url('transaksi') ?>" class="btn btn-secondary mr-2">Batal</a>
                    <button type="submit" class="btn btn-success px-5 shadow font-weight-bold" id="btnSimpan" disabled>
                        <i class="fas fa-save mr-2"></i> SIMPAN PENJUALAN
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // 1. FORMAT RUPIAH
    document.addEventListener('keyup', function(e) {
        if (e.target.classList.contains('input-rupiah')) {
            e.target.value = formatRupiah(e.target.value);
        }
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return rupiah;
    }

    function cleanNumber(str) {
        if(!str) return 0;
        return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
    }

    // 2. LOGIKA KHUSUS PENJUALAN (AUTO COPY)
    function copyToCredit() {
        var val = document.getElementById('inputDebit1').value;
        document.getElementById('inputKredit1').value = val;
        hitungTotal();
    }

    // 3. HITUNG TOTAL & VALIDASI
    function hitungTotal() {
        var debits = document.getElementsByName('debit[]');
        var kredits = document.getElementsByName('kredit[]');
        var totalD = 0;
        var totalK = 0;

        for (var i = 0; i < debits.length; i++) {
            totalD += cleanNumber(debits[i].value);
            totalK += cleanNumber(kredits[i].value);
        }

        document.getElementById('labelTotalDebit').innerText = formatRupiah(totalD.toString());
        document.getElementById('labelTotalKredit').innerText = formatRupiah(totalK.toString());

        var balanceBadge = document.getElementById('statusBalance');
        var btnSimpan = document.getElementById('btnSimpan');

        if (totalD > 0 && Math.abs(totalD - totalK) < 1) {
            balanceBadge.className = 'badge badge-success px-3 py-2';
            balanceBadge.innerHTML = '<i class="fas fa-check-circle"></i> BALANCE';
            btnSimpan.disabled = false;
        } else {
            balanceBadge.className = 'badge badge-danger px-3 py-2';
            balanceBadge.innerHTML = '<i class="fas fa-times-circle"></i> TIDAK BALANCE';
            btnSimpan.disabled = true;
        }
    }
</script>

<?= $this->endSection(); ?>