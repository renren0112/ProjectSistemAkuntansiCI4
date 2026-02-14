<?= $this->extend('layout/template'); ?>

<?= $this->section('content'); ?>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-history mr-2"></i> Audit Trail (Log Aktivitas)</h1>
</div>

<div class="card shadow mb-4 border-left-secondary">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-secondary">Rekam Jejak Pengguna (200 Transaksi Terakhir)</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm" id="dataTable" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th width="15%">Waktu</th>
                        <th width="15%">User</th>
                        <th width="10%">Role</th>
                        <th width="15%">Aksi</th>
                        <th>Keterangan Detail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted font-italic">Belum ada aktivitas terekam.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($logs as $l): ?>
                        <tr>
                            <td class="small"><?= date('d/m/Y H:i:s', strtotime($l['tgl_log'])) ?></td>
                            <td class="font-weight-bold"><?= esc($l['username']) ?></td>
                            <td><span class="badge badge-light border"><?= esc($l['role']) ?></span></td>
                            <td>
                                <?php 
                                    // Logic Warna Badge berdasarkan Aksi
                                    $warna = 'primary';
                                    $aksi = strtoupper($l['aksi']);
                                    
                                    if(strpos($aksi, 'HAPUS') !== false || strpos($aksi, 'DELETE') !== false) {
                                        $warna = 'danger'; // Merah untuk Hapus
                                    } elseif(strpos($aksi, 'EDIT') !== false || strpos($aksi, 'UPDATE') !== false) {
                                        $warna = 'warning text-dark'; // Kuning untuk Edit
                                    } elseif(strpos($aksi, 'LOGIN') !== false) {
                                        $warna = 'info'; // Biru muda untuk Login
                                    } elseif(strpos($aksi, 'TUTUP') !== false) {
                                        $warna = 'dark'; // Hitam untuk Tutup Buku
                                    } elseif(strpos($aksi, 'INPUT') !== false) {
                                        $warna = 'success'; // Hijau untuk Input
                                    }
                                ?>
                                <span class="badge badge-<?= $warna ?> px-2 py-1"><?= esc($l['aksi']) ?></span>
                            </td>
                            <td class="small text-gray-700"><?= esc($l['keterangan']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            <small class="text-muted font-italic">* Data log ini dibuat otomatis oleh sistem dan tidak dapat dihapus manual demi keamanan audit.</small>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>