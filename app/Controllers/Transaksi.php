<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JurnalModel;
use App\Models\CoaModel;

class Transaksi extends BaseController
{
    protected $jurnalModel;
    protected $coaModel;

    public function __construct()
    {
        $this->jurnalModel = new JurnalModel();
        $this->coaModel = new CoaModel();
    }

    private function generateNoBukti($prefix)
    {
        $lastTransaction = $this->jurnalModel->like('no_bukti', $prefix . '-', 'after')
                                             ->orderBy('id_jurnal', 'DESC')
                                             ->first();
        if ($lastTransaction) {
            $parts = explode('-', $lastTransaction['no_bukti']);
            $lastNumber = end($parts);
            $nextNumber = is_numeric($lastNumber) ? intval($lastNumber) + 1 : 1;
        } else {
            $nextNumber = 1;
        }
        return $prefix . '-' . sprintf('%04d', $nextNumber);
    }

    public function index()
    {
        $keyword   = $this->request->getGet('keyword');
        $startDate = $this->request->getGet('start'); 
        $endDate   = $this->request->getGet('end');     
        
        // REVISI 1: Default Sort dari ASC (Terlama dulu)
        $sortOrder = $this->request->getGet('sort') ?? 'ASC';
        
        $viewMode  = $this->request->getGet('mode') ?? 'ringkas';
        $viewJenis = $this->request->getGet('jenis') ?? ''; 
        $statusPosting = $this->request->getGet('status') ?? '';

        $db = \Config\Database::connect();
        
        if ($viewMode == 'detail') {
            $builder = $db->table('jurnal_detail');
            $builder->select('jurnal.*, jurnal_detail.*, coa.nama_akun, coa.kode_akun');
            $builder->join('jurnal', 'jurnal.id_jurnal = jurnal_detail.id_jurnal');
            $builder->join('coa', 'coa.kode_akun = jurnal_detail.kode_akun');
        } else {
            $builder = $db->table('jurnal');
            $builder->select('jurnal.*');
        }

        if ($keyword) {
            $builder->groupStart()
                ->like('jurnal.no_bukti', $keyword)
                ->orLike('jurnal.keterangan', $keyword);
            if ($viewMode == 'detail') $builder->orLike('coa.nama_akun', $keyword);
            $builder->groupEnd();
        }
        if ($startDate && $endDate) {
            $builder->where("jurnal.tgl_jurnal BETWEEN '$startDate' AND '$endDate'");
        }
        if ($viewJenis) {
            $builder->where('jurnal.jenis_transaksi', $viewJenis);
        }
        
        if ($statusPosting == 'Trash') {
            $builder->where('jurnal.deleted_at IS NOT NULL');
        } else {
            if ($statusPosting) $builder->where('jurnal.status_posting', $statusPosting);
            $builder->where('jurnal.deleted_at', null);
        }

        $builder->orderBy('jurnal.tgl_jurnal', $sortOrder);
        $builder->orderBy('jurnal.created_at', $sortOrder);

        $perPage = ($viewMode == 'detail') ? 20 : 10;
        $page = $this->request->getVar('page_transaksi') ? $this->request->getVar('page_transaksi') : 1;
        $offset = ($page - 1) * $perPage;
        
        $countBuilder = clone $builder;
        $totalRows = $countBuilder->countAllResults();
        
        $result = $builder->limit($perPage, $offset)->get()->getResultArray();

        if ($viewMode == 'ringkas' && !empty($result)) {
            $jurnalIds = array_column($result, 'id_jurnal');
            if (!empty($jurnalIds)) {
                $details = $db->table('jurnal_detail')
                    ->select('jurnal_detail.*, coa.nama_akun')
                    ->join('coa', 'coa.kode_akun = jurnal_detail.kode_akun')
                    ->whereIn('id_jurnal', $jurnalIds)
                    ->get()->getResultArray();

                foreach ($result as &$j) {
                    $j['details'] = [];
                    $totalDebit  = 0;
                    $totalKredit = 0;

                    foreach ($details as $d) {
                        if ($d['id_jurnal'] == $j['id_jurnal']) {
                            $j['details'][] = $d;
                            $totalDebit  += (float) $d['debit'];
                            $totalKredit += (float) $d['kredit'];
                        }
                    }
                    $j['total_nilai'] = max($totalDebit, $totalKredit);
                }
            }
        }

        $pager = \Config\Services::pager();
        $pager_links = $pager->makeLinks($page, $perPage, $totalRows, 'default_full', 0, 'transaksi');

        $data = [
            'title'     => 'Riwayat Transaksi Jurnal',
            'jurnal'    => $result,
            'pager_links' => $pager_links,
            'filter'    => [
                'keyword' => $keyword,
                'start'   => $startDate,
                'end'     => $endDate,
                'sort'    => $sortOrder,
                'mode'    => $viewMode,
                'jenis'   => $viewJenis,
                'status'  => $statusPosting
            ]
        ];

        return view('transaksi/index', $data);
    }

    // ... (CREATE METHODS SAMA SEPERTI SEBELUMNYA) ...
    public function create() {
        $data = ['title' => 'Input Jurnal Umum', 'akun' => $this->coaModel->orderBy('kode_akun', 'ASC')->findAll(), 'no_bukti_auto' => $this->generateNoBukti('JU')];
        return view('transaksi/create', $data);
    }
    public function create_penjualan() {
        $all = $this->coaModel->orderBy('kode_akun', 'ASC')->findAll();
        $aset = array_filter($all, function($a) { return $a['tipe_akun'] == 'Aset'; });
        $data = ['title' => 'Input Penjualan', 'akun_aset' => $aset, 'akun_pendapatan' => array_filter($all, function($a) { return in_array($a['tipe_akun'], ['Penjualan', 'Pendapatan']); }), 'no_bukti_auto' => $this->generateNoBukti('JPJ')];
        return view('transaksi/create_penjualan', $data);
    }
    public function create_pembelian() {
        $all = $this->coaModel->orderBy('kode_akun', 'ASC')->findAll();
        $bayar = array_filter($all, function($a) { return in_array($a['tipe_akun'], ['Aset', 'Liabilitas']); });
        $data = ['title' => 'Input Pembelian', 'akun_beban' => array_filter($all, function($a) { return in_array($a['tipe_akun'], ['Pembelian', 'Beban', 'Aset']); }), 'akun_bayar' => $bayar, 'no_bukti_auto' => $this->generateNoBukti('JPB')];
        return view('transaksi/create_pembelian', $data);
    }
    public function create_penerimaan() {
        $all = $this->coaModel->orderBy('kode_akun', 'ASC')->findAll();
        $data = ['title' => 'Input Penerimaan Kas', 'akun_kas' => array_filter($all, function($a) { return $a['tipe_akun'] == 'Aset'; }), 'all_akun' => $all, 'no_bukti_auto' => $this->generateNoBukti('BKM')];
        return view('transaksi/create_penerimaan', $data);
    }
    public function create_pengeluaran() {
        $all = $this->coaModel->orderBy('kode_akun', 'ASC')->findAll();
        $data = ['title' => 'Input Pengeluaran Kas', 'akun_kas' => array_filter($all, function($a) { return $a['tipe_akun'] == 'Aset'; }), 'all_akun' => $all, 'no_bukti_auto' => $this->generateNoBukti('BKK')];
        return view('transaksi/create_pengeluaran', $data);
    }
    public function penyesuaian() {
        $data = ['title' => 'Input Jurnal Penyesuaian', 'akun' => $this->coaModel->orderBy('kode_akun', 'ASC')->findAll(), 'no_bukti_auto' => $this->generateNoBukti('AJP')];
        return view('transaksi/penyesuaian', $data);
    }
    public function create_koreksi() {
        $data = ['title' => 'Input Jurnal Koreksi', 'akun' => $this->coaModel->orderBy('kode_akun', 'ASC')->findAll(), 'no_bukti_auto' => $this->generateNoBukti('JK')];
        return view('transaksi/create_koreksi', $data);
    }

    public function store()
    {
        // 1. VALIDASI DATA HEADER
        if (!$this->validate([
            'no_bukti'   => [
                'rules'  => 'required|is_unique[jurnal.no_bukti]',
                'errors' => [
                    'required'  => 'Nomor Bukti wajib diisi.',
                    'is_unique' => 'Nomor Bukti sudah terdaftar di database.'
                ]
            ],
            'tgl_jurnal' => [
                'rules'  => 'required|valid_date',
                'errors' => [
                    'required'   => 'Tanggal transaksi wajib diisi.',
                    'valid_date' => 'Format tanggal tidak valid.'
                ]
            ],
            'keterangan' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        // 2. PERSIAPAN DATA DETAIL
        $kode_akun = $this->request->getPost('kode_akun');
        $debit     = $this->request->getPost('debit');
        $kredit    = $this->request->getPost('kredit');

        $dataDetail = [];
        $totalDebit = 0;
        $totalKredit = 0;
        $barisTerisi = 0;

        // Cek apakah ada input akun
        if (empty($kode_akun)) {
            return redirect()->back()->withInput()->with('error', 'Harap isi minimal satu baris akun.');
        }

        // 3. LOOPING & SANITASI DATA (PEMBERSIHAN)
        foreach ($kode_akun as $key => $val) {
            // Abaikan baris jika Kode Akun tidak dipilih
            if (empty($val)) continue;

            // Bersihkan format Rupiah (Hapus titik, ganti koma jadi titik desimal)
            // Contoh: "1.000.000" -> 1000000
            $rawDebit  = $debit[$key] ?? 0;
            $rawKredit = $kredit[$key] ?? 0;

            $d = (float) str_replace(['.', ','], ['', '.'], (string)$rawDebit);
            $k = (float) str_replace(['.', ','], ['', '.'], (string)$rawKredit);

            // Validasi: Tidak boleh negatif
            if ($d < 0 || $k < 0) {
                return redirect()->back()->withInput()->with('error', 'Nominal tidak boleh negatif.');
            }

            // Validasi: Baris dianggap valid hanya jika ada nilai > 0
            if ($d > 0 || $k > 0) {
                // Validasi: Cek apakah akun benar-benar ada di database (Mencegah manipulasi Inspect Element)
                if (!$this->coaModel->find($val)) {
                    return redirect()->back()->withInput()->with('error', "Kode Akun $val tidak ditemukan di sistem.");
                }

                $dataDetail[] = [
                    'kode_akun' => $val,
                    'debit'     => $d,
                    'kredit'    => $k
                ];
                $totalDebit += $d;
                $totalKredit += $k;
                $barisTerisi++;
            }
        }

        // 4. VALIDASI LOGIKA AKUNTANSI

        // a. Cek apakah ada baris yang terisi?
        if ($barisTerisi == 0) {
            return redirect()->back()->withInput()->with('error', 'Nominal Debit atau Kredit tidak boleh kosong semua (0).');
        }

        // b. Cek Balance (Seimbang)
        // Gunakan margin error kecil (0.01) untuk mengatasi masalah floating point komputer
        if (abs($totalDebit - $totalKredit) > 1) { 
            return redirect()->back()->withInput()->with('error', 
                'Jurnal TIDAK BALANCE! <br>Total Debit: Rp ' . number_format($totalDebit, 0, ',', '.') . 
                ' <br>Total Kredit: Rp ' . number_format($totalKredit, 0, ',', '.') .
                ' <br>Selisih: Rp ' . number_format(abs($totalDebit - $totalKredit), 0, ',', '.')
            );
        }

        // 5. PROSES SIMPAN KE DATABASE
        $dataHeader = [
            'no_bukti'        => $this->request->getPost('no_bukti'),
            'tgl_jurnal'      => $this->request->getPost('tgl_jurnal'),
            'keterangan'      => $this->request->getPost('keterangan'),
            'jenis_transaksi' => $this->request->getPost('jenis_transaksi') ?? 'Umum',
            'status_posting'  => 'Pending'
        ];

        try {
            $this->jurnalModel->simpanJurnalLengkap($dataHeader, $dataDetail);
            
            // Redirect sesuai jenis transaksi agar UX lebih baik
            $jenis = $dataHeader['jenis_transaksi'];
            $routes = [
                'PenerimaanKas'  => 'transaksi/create-penerimaan',
                'PengeluaranKas' => 'transaksi/create-pengeluaran',
                'Penjualan'      => 'transaksi/create-penjualan',
                'Pembelian'      => 'transaksi/create-pembelian',
                'Penyesuaian'    => 'transaksi/penyesuaian',
                'Umum'           => 'transaksi/create'
            ];
            
            $urlRedirect = $routes[$jenis] ?? '/transaksi';
            
            return redirect()->to($urlRedirect)->with('success', 'Transaksi Berhasil Disimpan! (No Bukti: '.$dataHeader['no_bukti'].')');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        $jurnal = $this->jurnalModel->find($id);
        if ($jurnal && $jurnal['status_posting'] == 'Posted') return redirect()->to('/transaksi')->with('error', 'Jurnal Posted tidak boleh dihapus.');
        $this->jurnalModel->delete($id);
        return redirect()->to('/transaksi')->with('success', 'Transaksi dihapus.');
    }
    public function restore($id) {
        $this->jurnalModel->withDeleted()->update($id, ['deleted_at' => null]);
        return redirect()->to('/transaksi')->with('success', 'Transaksi dipulihkan.');
    }
    public function purge($id) {
        $this->jurnalModel->delete($id, true);
        return redirect()->to('/transaksi')->with('success', 'Transaksi dihapus permanen.');
    }
    public function detail($id) {
        $jurnal = $this->jurnalModel->getJurnalDetail($id);
        if (empty($jurnal)) throw new \CodeIgniter\Exceptions\PageNotFoundException('Jurnal tidak ditemukan');
        return view('transaksi/detail', ['title' => 'Detail ' . $jurnal[0]['no_bukti'], 'jurnal' => $jurnal]);
    }
    public function cetak($id) {
        $jurnal = $this->jurnalModel->getJurnalDetail($id);
        if (empty($jurnal)) return redirect()->to('/transaksi');
        return view('transaksi/cetak', ['jurnal' => $jurnal]);
    }
    public function export($id) {
        $jurnal = $this->jurnalModel->getJurnalDetail($id);
        if (empty($jurnal)) return redirect()->to('/transaksi');
        $filename = 'Bukti_' . $jurnal[0]['no_bukti'] . '.xls';
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=$filename");
        return view('transaksi/cetak', ['jurnal' => $jurnal, 'is_excel' => true]);
    }
    public function posting($id) {
        $this->jurnalModel->update($id, ['status_posting' => 'Posted']);
        return redirect()->back()->with('success', 'Diposting.');
    }

    // --- REVISI 2: FIX POSTING OTOMATIS ---
    public function posting_otomatis()
    {
        // Hitung dulu
        $jumlahPending = $this->jurnalModel->where('status_posting', 'Pending')->countAllResults();

        // REVISI: Ubah syarat minimal dari > 10 menjadi > 0 agar berapapun bisa diposting
        if ($jumlahPending > 0) {
            $this->jurnalModel->where('status_posting', 'Pending')
                              ->set(['status_posting' => 'Posted'])
                              ->update();
            return redirect()->to('/transaksi')->with('success', "$jumlahPending Jurnal berhasil diposting otomatis!");
        } else {
            return redirect()->to('/transaksi')->with('info', "Tidak ada jurnal pending untuk diposting.");
        }
    }
}