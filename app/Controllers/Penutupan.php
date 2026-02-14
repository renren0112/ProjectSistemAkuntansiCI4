<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JurnalModel;
use App\Models\CoaModel;
use App\Models\PengaturanModel; // TAMBAHAN: Untuk data perusahaan di cetak

class Penutupan extends BaseController
{
    protected $jurnalModel;
    protected $coaModel;
    protected $pengaturanModel; // TAMBAHAN

    public function __construct()
    {
        $this->jurnalModel = new JurnalModel();
        $this->coaModel = new CoaModel();
        $this->pengaturanModel = new PengaturanModel(); // TAMBAHAN
    }

    // Halaman Form Penutupan
    public function index()
    {
        $data = [
            'title'     => 'Proses Tutup Buku',
            'akun_ekuitas' => $this->coaModel->where('tipe_akun', 'Ekuitas')->findAll(),
            'all_akun' => $this->coaModel->orderBy('kode_akun', 'ASC')->findAll(),
        ];
        return view('penutupan/index', $data);
    }

    // Proses Generate Jurnal Penutup
    public function proses()
    {
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        
        $akun_modal = $this->request->getPost('akun_modal');
        $akun_prive = $this->request->getPost('akun_prive'); 
        $akun_ikhtisar = $this->request->getPost('akun_ikhtisar');

        // 1. Cek Status Penutupan
        if ($this->jurnalModel->cekStatusPenutupan($bulan, $tahun)) {
            return redirect()->back()->with('error', "Periode $bulan-$tahun sudah ditutup! Hapus jurnal penutup di Riwayat Jurnal jika ingin mengulang.");
        }

        // --- REVISI: AMBIL SEMUA AKUN NOMINAL (Jasa & Dagang) ---
        
        // KELOMPOK KREDIT: Pendapatan & Penjualan
        $kelompok_kredit = array_merge(
            $this->jurnalModel->getLaporanByTipe(['Pendapatan'], $bulan, $tahun),
            $this->jurnalModel->getLaporanByTipe(['Penjualan'], $bulan, $tahun) // PENTING: Tambahkan ini
        );

        // KELOMPOK DEBIT: Beban & Pembelian
        $kelompok_debit = array_merge(
            $this->jurnalModel->getLaporanByTipe(['Beban'], $bulan, $tahun),
            $this->jurnalModel->getLaporanByTipe(['Pembelian'], $bulan, $tahun) // PENTING: Tambahkan ini
        );

        $db = \Config\Database::connect();
        // Set tanggal ke akhir bulan
        $tgl_jurnal = date('Y-m-t', mktime(0, 0, 0, $bulan, 1, $tahun)); 
        
        $db->transStart();

        // ---------------------------------------------------------
        // TAHAP 1: MENUTUP PENDAPATAN & PENJUALAN
        // (Debit Akun Pendapatan/Penjualan, Kredit Ikhtisar L/R)
        // ---------------------------------------------------------
        $total_pendapatan = 0;
        $detailJurnal1 = [];
        
        foreach ($kelompok_kredit as $row) {
            // Hitung Saldo (Kredit - Debit)
            // Jika ada Retur Penjualan (Saldo Debit), nilainya akan minus di sini
            $saldo = $row['total_kredit'] - $row['total_debit'];
            
            if ($saldo > 0) {
                // Jika Saldo Normal (Kredit), Debitkan agar 0
                $detailJurnal1[] = ['kode_akun' => $row['kode_akun'], 'debit' => $saldo, 'kredit' => 0];
                $total_pendapatan += $saldo;
            } 
            // Note: Akun Kontra (Retur) yang minus akan ditangani di Tahap 2 (Logic Debit)
        }
        
        if ($total_pendapatan > 0) {
            $detailJurnal1[] = ['kode_akun' => $akun_ikhtisar, 'debit' => 0, 'kredit' => $total_pendapatan];
            
            $this->jurnalModel->simpanJurnalLengkap([
                'no_bukti' => 'JP-' . date('ym') . '-01',
                'tgl_jurnal' => $tgl_jurnal,
                'keterangan' => 'Menutup Akun Pendapatan & Penjualan',
                'jenis_transaksi' => 'Penutup'
            ], $detailJurnal1);
        }

        // ---------------------------------------------------------
        // TAHAP 2: MENUTUP BEBAN & PEMBELIAN
        // (Debit Ikhtisar L/R, Kredit Akun Beban/Pembelian)
        // ---------------------------------------------------------
        $total_beban = 0;
        $detailJurnal2 = []; // Array Kredit
        
        foreach ($kelompok_debit as $row) {
            // Hitung Saldo (Debit - Kredit)
            $saldo = $row['total_debit'] - $row['total_kredit'];
            
            if ($saldo > 0) {
                // Jika Saldo Normal (Debit), Kreditkan agar 0
                $detailJurnal2[] = ['kode_akun' => $row['kode_akun'], 'debit' => 0, 'kredit' => $saldo];
                $total_beban += $saldo;
            }
        }

        // TAMBAHAN: Cek Akun Kontra Pendapatan (Retur Penjualan/Potongan)
        // Akun ini ada di kelompok Pendapatan/Penjualan tapi saldonya Debit
        foreach ($kelompok_kredit as $row) {
            $saldo = $row['total_kredit'] - $row['total_debit'];
            if ($saldo < 0) { // Saldo Debit
                $saldo_abs = abs($saldo);
                $detailJurnal2[] = ['kode_akun' => $row['kode_akun'], 'debit' => 0, 'kredit' => $saldo_abs];
                $total_beban += $saldo_abs; // Menambah pengurang laba
            }
        }

        if ($total_beban > 0) {
            // Debitkan Ikhtisar (Taruh di urutan pertama array)
            array_unshift($detailJurnal2, ['kode_akun' => $akun_ikhtisar, 'debit' => $total_beban, 'kredit' => 0]);
            
            $this->jurnalModel->simpanJurnalLengkap([
                'no_bukti' => 'JP-' . date('ym') . '-02',
                'tgl_jurnal' => $tgl_jurnal,
                'keterangan' => 'Menutup Akun Beban & Pembelian',
                'jenis_transaksi' => 'Penutup'
            ], $detailJurnal2);
        }

        // ---------------------------------------------------------
        // TAHAP 3: MENUTUP IKHTISAR L/R KE MODAL (LABA/RUGI)
        // ---------------------------------------------------------
        $laba_bersih = $total_pendapatan - $total_beban;
        
        if ($laba_bersih != 0) {
            $detailJurnal3 = [];
            if ($laba_bersih > 0) {
                // LABA: Debit Ikhtisar, Kredit Modal
                $detailJurnal3[] = ['kode_akun' => $akun_ikhtisar, 'debit' => $laba_bersih, 'kredit' => 0];
                $detailJurnal3[] = ['kode_akun' => $akun_modal, 'debit' => 0, 'kredit' => $laba_bersih];
                $ket = 'Menutup Laba Bersih ke Modal';
            } else {
                // RUGI: Debit Modal, Kredit Ikhtisar
                $rugi = abs($laba_bersih);
                $detailJurnal3[] = ['kode_akun' => $akun_modal, 'debit' => $rugi, 'kredit' => 0];
                $detailJurnal3[] = ['kode_akun' => $akun_ikhtisar, 'debit' => 0, 'kredit' => $rugi];
                $ket = 'Menutup Rugi Bersih ke Modal';
            }

            $this->jurnalModel->simpanJurnalLengkap([
                'no_bukti' => 'JP-' . date('ym') . '-03',
                'tgl_jurnal' => $tgl_jurnal,
                'keterangan' => $ket,
                'jenis_transaksi' => 'Penutup'
            ], $detailJurnal3);
        }

        // ---------------------------------------------------------
        // TAHAP 4: MENUTUP PRIVE (Jika Ada)
        // ---------------------------------------------------------
        if (!empty($akun_prive)) {
            $saldoPriveRaw = $this->jurnalModel->getBukuBesar($akun_prive, $bulan, $tahun);
            $totalPrive = 0;
            foreach ($saldoPriveRaw as $t) {
                $totalPrive += ($t['debit'] - $t['kredit']);
            }

            if ($totalPrive > 0) {
                $detailJurnal4 = [
                    ['kode_akun' => $akun_modal, 'debit' => $totalPrive, 'kredit' => 0],
                    ['kode_akun' => $akun_prive, 'debit' => 0, 'kredit' => $totalPrive]
                ];

                $this->jurnalModel->simpanJurnalLengkap([
                    'no_bukti' => 'JP-' . date('ym') . '-04',
                    'tgl_jurnal' => $tgl_jurnal,
                    'keterangan' => 'Menutup Prive ke Modal',
                    'jenis_transaksi' => 'Penutup'
                ], $detailJurnal4);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === FALSE) {
            return redirect()->back()->with('error', 'Gagal melakukan tutup buku.');
        } else {
            // Update Status Periode
            $db->table('periode_akuntansi')
                ->where('bulan', $bulan)
                ->where('tahun', $tahun)
                ->update(['status' => 'CLOSED']);

            return redirect()->to('laporan/neraca-saldo-penutup')->with('success', 'Tutup Buku Berhasil! Saldo nominal telah dinolkan.');
        }
    }

    // --- NERACA SALDO SETELAH PENUTUPAN ---
    public function laporanPenutup()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $neraca = $this->jurnalModel->getNeracaSaldo($bulan, $tahun, true);

        $data = [
            'title'      => 'Neraca Saldo Setelah Penutupan',
            'neraca'     => $neraca,
            'filter_bln' => $bulan,
            'filter_thn' => $tahun,
            // Data Perusahaan
            'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan')
        ];

        return view('laporan/neraca_saldo_penutup', $data);
    }

    public function cetak()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $neraca = $this->jurnalModel->getNeracaSaldo($bulan, $tahun, true);

        $data = [
            'neraca'     => $neraca,
            'filter_bln' => $bulan,
            'filter_thn' => $tahun,
            'is_excel'   => false,
            // Data Perusahaan Lengkap
            'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'     => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'    => $this->pengaturanModel->getNilai('no_telp'),
            'email'      => $this->pengaturanModel->getNilai('email_perusahaan')
        ];

        return view('laporan/cetak_neraca_penutup', $data);
    }

    public function export()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $neraca = $this->jurnalModel->getNeracaSaldo($bulan, $tahun, true);

        $data = [
            'neraca'     => $neraca,
            'filter_bln' => $bulan,
            'filter_thn' => $tahun,
            'is_excel'   => true,
            // Data Perusahaan Lengkap
            'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'     => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'    => $this->pengaturanModel->getNilai('no_telp'),
            'email'      => $this->pengaturanModel->getNilai('email_perusahaan')
        ];

        $namaFile = "Neraca_Saldo_Penutup_{$bulan}_{$tahun}.xls";
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");

        return view('laporan/cetak_neraca_penutup', $data);
    }
}