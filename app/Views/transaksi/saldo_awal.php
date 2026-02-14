<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Setup Saldo Awal Akun</h1>
    <a href="<?= base_url('transaksi') ?>" class="btn btn-secondary btn-sm shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> Batal
    </a>
</div>

<div class="alert alert-info border-left-info shadow-sm">
    <i class="fas fa-info-circle mr-2"></i> 
    Silakan masukkan saldo akhir dari periode sebelumnya (Neraca Akhir). 
    <br><b>Tips:</b> Klik 2x pada kolom yang terkunci jika Anda perlu menginput saldo tidak normal (Kontra Akun).
</div>

<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i><?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span></button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4 border-left-primary">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Formulir Saldo Awal</h6>
    </div>
    <div class="card-body">

        <form action="<?= base_url('saldoawal/store') ?>" method="post" id="formSaldo">
            <?= csrf_field() ?>

            <!-- Setting Tanggal Mulai -->
            <div class="form-group row">
                <label class="col-sm-2 col-form-label font-weight-bold">Tanggal Saldo</label>
                <div class="col-sm-3">
                    <input type="date" name="tgl_jurnal" class="form-control" value="<?= date('Y-01-01') ?>" required>
                    <small class="text-muted">Biasanya 1 Januari tahun berjalan.</small>
                </div>
            </div>

            <div class="table-responsive mt-3">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="thead-dark text-center">
                        <tr>
                            <th width="10%">Kode</th>
                            <th width="30%">Nama Akun</th>
                            <th width="10%">Normal</th>
                            <th width="25%" class="bg-primary">Debit (Rp)</th>
                            <th width="25%" class="bg-success">Kredit (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($akun as $a): 
                            $kode = $a['kode_akun'];
                        ?>
                        <tr>
                            <td class="text-center font-weight-bold align-middle text-primary">
                                <?= $kode ?>
                            </td>
                            <td class="align-middle">
                                <?= $a['nama_akun'] ?> 
                                <span class="badge badge-light border ml-1"><?= $a['tipe_akun'] ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-secondary"><?= $a['posisi_saldo_normal'] ?></span>
                            </td>
                            
                            <!-- KOLOM DEBIT -->
                            <td class="p-1">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text border-0 bg-transparent">Rp</span></div>
                                    <!-- PERBAIKAN: Gunakan Array Key berdasarkan Kode Akun -->
                                    <input type="text" 
                                           name="akun[<?= $kode ?>][debit]" 
                                           class="form-control text-right input-rupiah debit-field border-0" 
                                           placeholder="0" 
                                           value="" 
                                           onkeyup="hitungTotal()"
                                           <?= $a['posisi_saldo_normal'] == 'K' ? 'disabled style="background-color:#f1f3f6;"' : '' ?>> 
                                </div>
                            </td>

                            <!-- KOLOM KREDIT -->
                            <td class="p-1">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend"><span class="input-group-text border-0 bg-transparent">Rp</span></div>
                                    <!-- PERBAIKAN: Gunakan Array Key berdasarkan Kode Akun -->
                                    <input type="text" 
                                           name="akun[<?= $kode ?>][kredit]" 
                                           class="form-control text-right input-rupiah kredit-field border-0" 
                                           placeholder="0" 
                                           value="" 
                                           onkeyup="hitungTotal()"
                                           <?= $a['posisi_saldo_normal'] == 'D' ? 'disabled style="background-color:#f1f3f6;"' : '' ?>>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="font-weight-bold" style="font-size: 1.1em;">
                            <td colspan="3" class="text-right align-middle text-uppercase">Total Saldo Awal :</td>
                            <td class="text-right p-2">
                                <div id="lblTotalDebit" class="text-primary h5 font-weight-bold mb-0">0</div>
                            </td>
                            <td class="text-right p-2">
                                <div id="lblTotalKredit" class="text-success h5 font-weight-bold mb-0">0</div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="p-0">
                                <div id="statusBalance" class="alert alert-secondary m-0 text-center rounded-0 font-weight-bold border-0">
                                    Silakan input nominal
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row mt-4 mb-4">
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary btn-lg shadow px-5" id="btnSimpan" disabled>
                        <i class="fas fa-save mr-2"></i> SIMPAN & POSTING
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

    // 2. ENABLE FIELD SAAT DOUBLE CLICK (Untuk akun kontra)
    document.querySelectorAll('input.input-rupiah').forEach(item => {
        item.addEventListener('dblclick', event => {
            if(event.target.disabled) {
                event.target.disabled = false;
                event.target.style.backgroundColor = "#fff";
                event.target.style.fontWeight = "bold";
                event.target.focus();
            }
        })
    });

    function formatRupiah(angka) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return rupiah;
    }

    function cleanNumber(str) {
        if(!str) return 0;
        return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
    }

    // 3. HITUNG TOTAL REALTIME
    function hitungTotal() {
        let totalD = 0;
        let totalK = 0;

        document.querySelectorAll('.debit-field').forEach(el => {
            if(!el.disabled) totalD += cleanNumber(el.value);
        });

        document.querySelectorAll('.kredit-field').forEach(el => {
            if(!el.disabled) totalK += cleanNumber(el.value);
        });

        document.getElementById('lblTotalDebit').innerText = formatRupiah(totalD.toString());
        document.getElementById('lblTotalKredit').innerText = formatRupiah(totalK.toString());

        let statusBox = document.getElementById('statusBalance');
        let btn = document.getElementById('btnSimpan');

        // Logic Balance
        if (totalD > 0 && Math.abs(totalD - totalK) < 1) {
            statusBox.className = 'alert alert-success m-0 text-center rounded-0 font-weight-bold';
            statusBox.innerHTML = '<i class="fas fa-check-circle mr-2"></i> SEIMBANG (BALANCE)';
            btn.disabled = false;
        } else {
            let selisih = Math.abs(totalD - totalK);
            statusBox.className = 'alert alert-danger m-0 text-center rounded-0 font-weight-bold';
            statusBox.innerHTML = '<i class="fas fa-times-circle mr-2"></i> TIDAK SEIMBANG (Selisih: ' + formatRupiah(selisih.toString()) + ')';
            btn.disabled = true;
        }
    }
</script>

<?= $this->endSection(); ?>