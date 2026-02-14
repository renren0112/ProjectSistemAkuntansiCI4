<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PeriodeModel;
use App\Models\JurnalModel; // Butuh model jurnal untuk lihat transaksi

class PeriodeController extends BaseController
{
    protected $periodeModel;
    protected $jurnalModel;

    public function __construct()
    {
        $this->periodeModel = new PeriodeModel();
        $this->jurnalModel = new JurnalModel();
    }

    public function index()
    {
        $data['title'] = 'Kelola Periode Akuntansi';
        $data['periode'] = $this->periodeModel
            ->orderBy('tahun', 'DESC')
            ->orderBy('bulan', 'ASC')
            ->findAll();

        return view('periode/index', $data);
    }

    public function open($id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        // Tutup semua periode
        $db->table('periode_akuntansi')->update(['status' => 'CLOSED']);

        // Buka periode terpilih
        $db->table('periode_akuntansi')
            ->where('id', $id)
            ->update(['status' => 'OPEN']);

        $db->transComplete();

        return redirect()->to('/periode')->with('success', 'Periode berhasil diaktifkan.');
    }

    public function create()
    {
        $data['title'] = 'Buat Periode Baru';
        return view('periode/create', $data);
    }

    public function store()
    {
        // Validasi Duplikasi
        $bulan = $this->request->getPost('bulan');
        $tahun = $this->request->getPost('tahun');
        
        $exist = $this->periodeModel->where('bulan', $bulan)->where('tahun', $tahun)->first();
        if($exist) {
            return redirect()->back()->with('error', "Periode $bulan-$tahun sudah ada.");
        }

        $data = [
            'tahun'        => $tahun,
            'bulan'        => $bulan,
            'nama_periode' => $this->request->getPost('nama_periode'),
            'tgl_mulai'    => $this->request->getPost('tgl_mulai'),
            'tgl_selesai'  => $this->request->getPost('tgl_selesai'),
            'status'       => 'CLOSED' // Default tertutup
        ];

        $this->periodeModel->insert($data);
        return redirect()->to('/periode')->with('success', 'Periode baru berhasil dibuat.');
    }

    // --- FITUR BARU: LIHAT DETAIL PERIODE (READ ONLY) ---
    public function detail($id)
    {
        $periode = $this->periodeModel->find($id);
        if (!$periode) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Periode tidak ditemukan');
        }

        // Ambil ringkasan jurnal periode tersebut
        $jurnal = $this->jurnalModel
            ->where('MONTH(tgl_jurnal)', $periode['bulan'])
            ->where('YEAR(tgl_jurnal)', $periode['tahun'])
            ->orderBy('tgl_jurnal', 'ASC')
            ->findAll();

        // Hitung total transaksi
        $totalNilai = 0;
        // Note: Karena jurnal dan detail terpisah, idealnya join. 
        // Tapi untuk preview cepat, kita hitung jumlah record saja atau query khusus.
        // Agar simpel, kita tampilkan list jurnalnya saja.

        $data = [
            'title'   => 'Detail Periode: ' . $periode['nama_periode'],
            'periode' => $periode,
            'jurnal'  => $jurnal
        ];

        return view('periode/detail', $data);
    }
}