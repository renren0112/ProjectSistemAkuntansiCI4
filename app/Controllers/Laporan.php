<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JurnalModel;
use App\Models\CoaModel;
use App\Models\PengaturanModel;

class Laporan extends BaseController
{
    protected $jurnalModel;
    protected $coaModel;
    protected $pengaturanModel;

    public function __construct()
    {
        helper('number');
        $this->jurnalModel = new JurnalModel();
        $this->coaModel = new CoaModel();
        $this->pengaturanModel = new PengaturanModel();
    }

    // ... (Method bukuBesar s/d neracaSaldo TETAP SAMA) ...
    public function bukuBesar()
    {
        $kode_akun = $this->request->getGet('akun');
        $bulan     = $this->request->getGet('bulan') ?? date('m');
        $tahun     = $this->request->getGet('tahun') ?? date('Y');

        $allAccounts = $this->coaModel->orderBy('kode_akun', 'ASC')->findAll();
        
        $transaksi = [];
        $akunInfo  = null;
        $prevAkun  = null;
        $nextAkun  = null;

        if ($kode_akun) {
            $transaksi = $this->jurnalModel->getBukuBesar($kode_akun, $bulan, $tahun);
            $akunInfo  = $this->coaModel->find($kode_akun);

            $ids = array_column($allAccounts, 'kode_akun');
            $currentIdx = array_search($kode_akun, $ids);

            if ($currentIdx !== false) {
                if (isset($ids[$currentIdx - 1])) $prevAkun = $ids[$currentIdx - 1];
                if (isset($ids[$currentIdx + 1])) $nextAkun = $ids[$currentIdx + 1];
            }
        }

        $data = [
            'title'       => 'Buku Besar',
            'list_akun'   => $allAccounts,
            'transaksi'   => $transaksi,
            'akun'        => $akunInfo,
            'filter_akun' => $kode_akun,
            'filter_bln'  => $bulan,
            'filter_thn'  => $tahun,
            'prev_akun'   => $prevAkun,
            'next_akun'   => $nextAkun,
            'perusahaan'  => $this->pengaturanModel->getNilai('nama_perusahaan')
        ];

        return view('laporan/buku_besar', $data);
    }

    public function cetakBukuBesar()
    {
        $kode_akun = $this->request->getGet('akun');
        $bulan     = $this->request->getGet('bulan') ?? date('m');
        $tahun     = $this->request->getGet('tahun') ?? date('Y');

        if (!$kode_akun) return redirect()->to('laporan/buku-besar');

        $transaksi = $this->jurnalModel->getBukuBesar($kode_akun, $bulan, $tahun);
        $akunInfo  = $this->coaModel->find($kode_akun);

        $data = [
            'transaksi'   => $transaksi,
            'akun'        => $akunInfo,
            'filter_bln'  => $bulan,
            'filter_thn'  => $tahun,
            'is_excel'    => false,
            // FIX: Data Perusahaan Lengkap
            'perusahaan'  => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'      => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'     => $this->pengaturanModel->getNilai('no_telp'),
            'email'       => $this->pengaturanModel->getNilai('email_perusahaan')
        ];

        return view('laporan/cetak_buku_besar', $data);
    }

    public function exportBukuBesar()
    {
        $kode_akun = $this->request->getGet('akun');
        $bulan     = $this->request->getGet('bulan') ?? date('m');
        $tahun     = $this->request->getGet('tahun') ?? date('Y');

        if (!$kode_akun) return redirect()->to('laporan/buku-besar');

        $transaksi = $this->jurnalModel->getBukuBesar($kode_akun, $bulan, $tahun);
        $akunInfo  = $this->coaModel->find($kode_akun);

        $data = [
            'transaksi'   => $transaksi,
            'akun'        => $akunInfo,
            'filter_bln'  => $bulan,
            'filter_thn'  => $tahun,
            'is_excel'    => true,
            // FIX: Data Perusahaan Lengkap
            'perusahaan'  => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'      => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'     => $this->pengaturanModel->getNilai('no_telp'),
            'email'       => $this->pengaturanModel->getNilai('email_perusahaan')
        ];

        $namaFile = 'BukuBesar_' . $kode_akun . '_' . $bulan . '-' . $tahun . '.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");

        return view('laporan/cetak_buku_besar', $data);
    }

    public function neracaSaldo()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        
        // Ambil data neraca (tanpa jurnal penutup untuk melihat saldo akhir sebelum tutup buku)
        $neraca = $this->jurnalModel->getNeracaSaldo($bulan, $tahun, false); 

        $data = [
            'title'      => 'Neraca Saldo',
            'neraca'     => $neraca,
            'filter_bln' => $bulan,
            'filter_thn' => $tahun,
            'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan')
        ];

        return view('laporan/neraca_saldo', $data);
    }

   public function cetakNeraca()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $neraca = $this->jurnalModel->getNeracaSaldo($bulan, $tahun, false);
        
        $data = [
            'neraca'     => $neraca, 
            'tahun'      => $tahun, 
            'bulan'      => $bulan,
            'is_excel'   => false,
            // Data Perusahaan Lengkap untuk Kop
            'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'     => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'    => $this->pengaturanModel->getNilai('no_telp'),
            'email'      => $this->pengaturanModel->getNilai('email_perusahaan')
        ];
        return view('laporan/cetak_neraca', $data);
    }

     public function exportNeracaExcel()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $neraca = $this->jurnalModel->getNeracaSaldo($bulan, $tahun, false);
        
        $data = [
            'neraca'     => $neraca, 
            'tahun'      => $tahun, 
            'bulan'      => $bulan,
            'is_excel'   => true,
            'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'     => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'    => $this->pengaturanModel->getNilai('no_telp'),
            'email'      => $this->pengaturanModel->getNilai('email_perusahaan')
        ];
        
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Neraca_Saldo_$tahun.xls");
        
        return view('laporan/cetak_neraca', $data);
    }

    public function labaRugi()
    {
        $data = $this->getLabaRugiData(); 
        return view('laporan/laba_rugi', $data);
    }

    public function cetakLabaRugi()
    {
        $data = $this->getLabaRugiData();
        return view('laporan/cetak_laba_rugi', $data);
    }

    public function exportLabaRugi()
    {
        $data = $this->getLabaRugiData();
        $namaFile = "Laba_Rugi_" . $data['filter_bln'] . "_" . $data['filter_thn'] . ".xls";
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");
        return view('laporan/cetak_laba_rugi', $data);
    }

    // --- REVISI: LOGIC LABA RUGI YANG LEBIH CERDAS ---
    private function getLabaRugiData()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // 1. Ambil Penjualan (Hanya tipe Penjualan)
        $penjualan = $this->jurnalModel->getLaporanByTipe(['Penjualan'], $bulan, $tahun);
        
        // 2. Ambil Pembelian (Hanya tipe Pembelian)
        $pembelian = $this->jurnalModel->getLaporanByTipe(['Pembelian'], $bulan, $tahun);
        
        // 3. Ambil Beban (Tipe Beban)
        $beban = $this->jurnalModel->getLaporanByTipe(['Beban'], $bulan, $tahun);
        
        // 4. Ambil Pendapatan Lain (Tipe Pendapatan biasa)
        // Kita exclude akun Penjualan jika tipe akunnya sama-sama 'Pendapatan' (jaga-jaga salah input)
        // Tapi di database Anda sudah dipisah, jadi aman pakai 'Pendapatan'.
        $pendapatanLain = $this->jurnalModel->getLaporanByTipe(['Pendapatan'], $bulan, $tahun); 

        // 5. HITUNG TOTAL (Looping)
        
        // Penjualan: Normal Kredit
        $totalPenjualan = 0;
        foreach($penjualan as $p) $totalPenjualan += ($p['total_kredit'] - $p['total_debit']);

        // Pembelian: Normal Debit
        $totalPembelian = 0;
        foreach($pembelian as $p) $totalPembelian += ($p['total_debit'] - $p['total_kredit']);

        // Beban: Normal Debit
        $totalBeban = 0;
        foreach($beban as $b) $totalBeban += ($b['total_debit'] - $b['total_kredit']);
        
        // Pendapatan Lain: Normal Kredit
        $totalPendapatanLain = 0;
        foreach($pendapatanLain as $pl) $totalPendapatanLain += ($pl['total_kredit'] - $pl['total_debit']);

        // 6. Hitung Laba Berjenjang
        $labaKotor  = $totalPenjualan - $totalPembelian; 
        $labaBersih = $labaKotor - $totalBeban + $totalPendapatanLain;

        return [
            'title'           => 'Laporan Laba Rugi',
            'penjualan'       => $penjualan,
            'pembelian'       => $pembelian,
            'beban'           => $beban,
            'pendapatan_lain' => $pendapatanLain,
            
            // Total
            'total_penjualan' => $totalPenjualan,
            'total_pembelian' => $totalPembelian,
            'total_beban'     => $totalBeban,
            'total_other'     => $totalPendapatanLain,
            'laba_kotor'      => $labaKotor,
            'laba_bersih'     => $labaBersih,

            'filter_bln'      => $bulan,
            'filter_thn'      => $tahun,
            'perusahaan'      => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'          => $this->pengaturanModel->getNilai('alamat_perusahaan')
        ];
    }

    // ... (Method neraca, arus kas, perubahan modal TETAP SAMA, paste ulang dari file lama Anda jika hilang) ...
    public function perubahanModal()
    {
        $data = $this->getPerubahanModalData();
        return view('laporan/perubahan_modal', $data);
    }

    public function cetakPerubahanModal()
    {
        $data = $this->getPerubahanModalData();
        return view('laporan/cetak_perubahan_modal', $data);
    }

    public function exportPerubahanModal()
    {
        $data = $this->getPerubahanModalData();
        $namaFile = "Perubahan_Modal_" . $data['filter_bln'] . "_" . $data['filter_thn'] . ".xls";
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");
        return view('laporan/cetak_perubahan_modal', $data);
    }

    private function getPerubahanModalData()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Ambil Laba Bersih dari Neraca Lajur agar konsisten
        $nlData = $this->getNeracaLajurData()['worksheet'];
        $labaBersih = 0;
        foreach ($nlData as $row) {
            $labaBersih += ($row['lr_kredit'] - $row['lr_debit']);
        }

        return [
            'title'       => 'Laporan Perubahan Modal',
            'ekuitas'     => $this->jurnalModel->getLaporanByTipe('Ekuitas', $bulan, $tahun),
            'laba_bersih' => $labaBersih,
            'filter_bln'  => $bulan,
            'filter_thn'  => $tahun,
            'perusahaan'  => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'      => $this->pengaturanModel->getNilai('alamat_perusahaan')
        ];
    }

    public function neraca()
    {
        $data = $this->getNeracaData();
        return view('laporan/neraca', $data);
    }

    public function cetakNeracaLaporan()
    {
        $data = $this->getNeracaData();
        return view('laporan/cetak_neraca_laporan', $data);
    }

    public function exportNeracaLaporan()
    {
        $data = $this->getNeracaData();
        $namaFile = "Neraca_" . $data['filter_bln'] . "_" . $data['filter_thn'] . ".xls";
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");
        return view('laporan/cetak_neraca_laporan', $data);
    }

     private function getNeracaData()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        // Ambil Laba Bersih dari Neraca Lajur agar konsisten (sumber kebenaran tunggal)
        $nlData = $this->jurnalModel->getNeracaLajur($bulan, $tahun);
        $labaBersih = 0;
        // Hitung Laba Bersih (Kredit - Debit di kolom Laba Rugi)
        foreach ($nlData as $row) {
            $labaBersih += ($row['ajp_kredit'] - $row['ajp_debit']); // Menggunakan logic AJP/NSD yang masuk ke LR
            // Atau jika menggunakan hasil return array sebelumnya:
            // $labaBersih += ($row['lr_kredit'] - $row['lr_debit']);
        }
        
        // Agar lebih akurat, kita gunakan perhitungan manual dari getLabaRugiData saja jika mau,
        // tapi logic Neraca Lajur di model sudah memisahkan LR dan NR (Neraca).
        // Kita ambil saldo Laba Bersih dari selisih kolom Laba Rugi di Neraca Lajur.
        $totalLR_Debit = 0;
        $totalLR_Kredit = 0;
        foreach($nlData as $row) {
            if (in_array($row['tipe_akun'], ['Pendapatan', 'Beban', 'Penjualan', 'Pembelian', 'Beban Lain-lain', 'Pendapatan Lain-lain'])) {
               // Hitung saldo akhir (NSD)
               $nsd_d = 0; $nsd_k = 0;
               // ... logic hitung NSD sama dengan di view ...
               // Simplifikasi: Kita ambil data Neraca saja dari Model
            }
        }
        
        // REVISI: Cara paling aman ambil Laba Bersih adalah panggil getLabaRugiData
        $lrData = $this->getLabaRugiData(); // Re-use logic Laba Rugi
        $labaBersih = $lrData['laba_bersih'];

        return [
            'title'       => 'Laporan Posisi Keuangan (Neraca)',
            // Aset
            'aset_lancar' => $this->jurnalModel->getLaporanByTipe(['Aset'], $bulan, $tahun), 
            // Kita ambil semua Aset dulu, nanti di View dipisah (atau bisa filter lebih detail di model jika ada sub-kategori)
            
            'aset'        => $this->jurnalModel->getLaporanByTipe(['Aset'], $bulan, $tahun),
            'liabilitas'  => $this->jurnalModel->getLaporanByTipe(['Liabilitas'], $bulan, $tahun),
            'ekuitas'     => $this->jurnalModel->getLaporanByTipe(['Ekuitas'], $bulan, $tahun),
            
            'laba_bersih' => $labaBersih,
            
            'filter_bln'  => $bulan,
            'filter_thn'  => $tahun,
            
            // DATA DINAMIS PERUSAHAAN (LENGKAP)
            'perusahaan'  => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'      => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'     => $this->pengaturanModel->getNilai('no_telp'),
            'email'       => $this->pengaturanModel->getNilai('email_perusahaan')
        ];
    }

    public function arusKas()
    {
        $data = $this->getArusKasData();
        return view('laporan/arus_kas', $data);
    }

    public function cetakArusKas()
    {
        $data = $this->getArusKasData();
        return view('laporan/cetak_arus_kas', $data);
    }

    public function exportArusKas()
    {
        $data = $this->getArusKasData();
        $namaFile = "Arus_Kas_" . $data['filter_bln'] . "_" . $data['filter_thn'] . ".xls";
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");
        return view('laporan/cetak_arus_kas', $data);
    }

    private function getArusKasData()
    {
        $db = \Config\Database::connect();
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $akunKas = $this->coaModel->like('nama_akun', 'Kas')->orLike('nama_akun', 'Bank')->findAll();
        $kodeAkunKas = array_column($akunKas, 'kode_akun');

        if (empty($kodeAkunKas)) {
            return [
                'title' => 'Laporan Arus Kas', 'arus_masuk' => [], 'arus_keluar' => [],
                'saldo_awal' => 0, 'filter_bln' => $bulan, 'filter_thn' => $tahun,
                'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan'),
                'alamat'     => $this->pengaturanModel->getNilai('alamat_perusahaan')
            ];
        }

        $builderAwal = $db->table('jurnal_detail');
        $builderAwal->join('jurnal', 'jurnal.id_jurnal = jurnal_detail.id_jurnal');
        $builderAwal->whereIn('kode_akun', $kodeAkunKas);
        $builderAwal->where("(YEAR(jurnal.tgl_jurnal) < $tahun OR (YEAR(jurnal.tgl_jurnal) = $tahun AND MONTH(jurnal.tgl_jurnal) < $bulan))");
        $builderAwal->where('jurnal.status_posting', 'Posted');
        $builderAwal->selectSum('debit'); $builderAwal->selectSum('kredit');
        $resAwal = $builderAwal->get()->getRow();
        $saldoAwal = ($resAwal->debit ?? 0) - ($resAwal->kredit ?? 0);

        $builder = $db->table('jurnal_detail');
        $builder->select('jurnal.tgl_jurnal, jurnal.no_bukti, jurnal.keterangan, jurnal_detail.debit, jurnal_detail.kredit, coa.nama_akun');
        $builder->join('jurnal', 'jurnal.id_jurnal = jurnal_detail.id_jurnal');
        $builder->join('coa', 'coa.kode_akun = jurnal_detail.kode_akun');
        $builder->whereIn('jurnal_detail.kode_akun', $kodeAkunKas);
        $builder->where('MONTH(jurnal.tgl_jurnal)', $bulan);
        $builder->where('YEAR(jurnal.tgl_jurnal)', $tahun);
        $builder->where('jurnal.status_posting', 'Posted');
        $builder->orderBy('jurnal.tgl_jurnal', 'ASC');
        $transaksi = $builder->get()->getResultArray();

        $arusMasuk = []; $arusKeluar = [];
        foreach ($transaksi as $t) {
            if ($t['debit'] > 0) $arusMasuk[] = $t;
            elseif ($t['kredit'] > 0) $arusKeluar[] = $t;
        }

        return [
            'title'       => 'Laporan Arus Kas', 'saldo_awal'  => $saldoAwal,
            'arus_masuk'  => $arusMasuk, 'arus_keluar' => $arusKeluar,
            'filter_bln'  => $bulan, 'filter_thn'  => $tahun,
            'perusahaan'  => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'      => $this->pengaturanModel->getNilai('alamat_perusahaan')
        ];
    }
    
    public function neracaLajur()
    {
        $data = $this->getNeracaLajurData();
        return view('laporan/neraca_lajur', $data);
    }

    public function cetakNeracaLajur()
    {
        $data = $this->getNeracaLajurData();
        return view('laporan/cetak_neraca_lajur', $data);
    }

    public function exportNeracaLajur()
    {
        $data = $this->getNeracaLajurData();
        $namaFile = "Neraca_Lajur_" . $data['filter_bln'] . "_" . $data['filter_thn'] . ".xls";
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$namaFile");
        return view('laporan/cetak_neraca_lajur', $data);
    }

    private function getNeracaLajurData()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');

        $raw_data = $this->jurnalModel->getNeracaLajur($bulan, $tahun);
        $worksheet = [];

        foreach ($raw_data as $row) {
            // 1. NERACA SALDO
            $ns_d = 0; $ns_k = 0;
            if ($row['posisi_saldo_normal'] == 'Debit') {
                $saldo = $row['ns_debit'] - $row['ns_kredit'];
                if($saldo >= 0) $ns_d = $saldo; else $ns_k = abs($saldo);
            } else {
                $saldo = $row['ns_kredit'] - $row['ns_debit'];
                if($saldo >= 0) $ns_k = $saldo; else $ns_d = abs($saldo);
            }

            // 2. PENYESUAIAN
            $ajp_d = $row['ajp_debit'];
            $ajp_k = $row['ajp_kredit'];

            // 3. NERACA SALDO DISESUAIKAN
            $nsd_d = 0; $nsd_k = 0;
            if ($row['posisi_saldo_normal'] == 'Debit') {
                $saldo_akhir = ($ns_d - $ns_k) + ($ajp_d - $ajp_k);
                if($saldo_akhir >= 0) $nsd_d = $saldo_akhir; else $nsd_k = abs($saldo_akhir);
            } else {
                $saldo_akhir = ($ns_k - $ns_d) + ($ajp_k - $ajp_d);
                if($saldo_akhir >= 0) $nsd_k = $saldo_akhir; else $nsd_d = abs($saldo_akhir);
            }

            // 4. PEMISAHAN LABA RUGI & NERACA
            $lr_d = 0; $lr_k = 0;
            $nr_d = 0; $nr_k = 0;

            // Pastikan Akun Penjualan & Pembelian masuk ke LABA RUGI
            $tipeLR = ['Pendapatan', 'Beban', 'Penjualan', 'Pembelian', 'Beban Lain-lain', 'Pendapatan Lain-lain'];
            
            if (in_array($row['tipe_akun'], $tipeLR)) {
                $lr_d = $nsd_d; $lr_k = $nsd_k;
            } else {
                $nr_d = $nsd_d; $nr_k = $nsd_k;
            }

            // Skip akun yang 0 semua
            if ($nsd_d == 0 && $nsd_k == 0 && $ajp_d == 0 && $ajp_k == 0 && $ns_d == 0 && $ns_k == 0) continue;

            $worksheet[] = [
                'kode'      => $row['kode_akun'],
                'nama'      => $row['nama_akun'],
                'kode_akun' => $row['kode_akun'],
                'nama_akun' => $row['nama_akun'],
                'tipe_akun' => $row['tipe_akun'],
                'posisi_saldo_normal' => $row['posisi_saldo_normal'],
                'ns_debit'  => $ns_d, 'ns_kredit' => $ns_k,
                'ajp_debit' => $ajp_d, 'ajp_kredit' => $ajp_k,
                'nsd_debit' => $nsd_d, 'nsd_kredit' => $nsd_k,
                'lr_debit'  => $lr_d,  'lr_kredit'  => $lr_k,
                'nr_debit'  => $nr_d,  'nr_kredit'  => $nr_k,
                'ns_d' => $ns_d, 'ns_k' => $ns_k,
                'ajp_d' => $ajp_d, 'ajp_k' => $ajp_k,
                'nsd_d' => $nsd_d, 'nsd_k' => $nsd_k,
                'lr_d' => $lr_d, 'lr_k' => $lr_k,
                'nr_d' => $nr_d, 'nr_k' => $nr_k,
            ];
        }

        return [
            'title'      => 'Neraca Lajur',
            'worksheet'  => $worksheet,
            'filter_bln' => $bulan, 'filter_thn' => $tahun,
            'bulan'      => $bulan, 'tahun'      => $tahun,
            'perusahaan' => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat'     => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'telepon'    => $this->pengaturanModel->getNilai('no_telp'),
            'email'      => $this->pengaturanModel->getNilai('email_perusahaan')
        ];
    }
}