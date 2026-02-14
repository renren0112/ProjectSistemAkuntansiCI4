<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CoaModel;
use App\Models\JurnalModel;

class SaldoAwal extends BaseController
{
    protected $coaModel;
    protected $jurnalModel;

    public function __construct()
    {
        $this->coaModel = new CoaModel();
        $this->jurnalModel = new JurnalModel();
    }

    public function index()
    {
        // Ambil Akun Neraca Saja
        $akun = $this->coaModel->whereIn('tipe_akun', ['Aset', 'Liabilitas', 'Ekuitas'])
                               ->orderBy('kode_akun', 'ASC')
                               ->findAll();

        $data = [
            'title' => 'Input Saldo Awal Periode',
            'akun'  => $akun
        ];

        return view('transaksi/saldo_awal', $data);
    }

    public function store()
    {
        $tgl_jurnal = $this->request->getPost('tgl_jurnal');
        $tahun = date('Y', strtotime($tgl_jurnal));
        $no_bukti = "SA-" . $tahun;
        $keterangan = "Saldo Awal Periode " . $tahun;

        // Cek duplikasi
        $exist = $this->jurnalModel->where('no_bukti', $no_bukti)->first();
        if ($exist) {
            return redirect()->back()->with('error', "Saldo Awal tahun $tahun sudah ada! Harap hapus transaksi SA-$tahun di Riwayat Jurnal terlebih dahulu.");
        }

        // AMBIL INPUT ARRAY
        // Struktur: [ '111' => ['debit' => '100.000', 'kredit' => '0'], ... ]
        $inputAkun = $this->request->getPost('akun'); 

        if (!$inputAkun || !is_array($inputAkun)) {
            return redirect()->back()->with('error', 'Tidak ada data yang dikirim.');
        }

        $totalDebit = 0;
        $totalKredit = 0;
        $dataDetail = [];

        foreach ($inputAkun as $kode => $nilai) {
            // Null Coalescing Operator untuk antisipasi field disabled
            $rawDebit  = $nilai['debit'] ?? 0;
            $rawKredit = $nilai['kredit'] ?? 0;

            // Bersihkan format (hapus titik)
            $d = (float) str_replace('.', '', (string)$rawDebit);
            $k = (float) str_replace('.', '', (string)$rawKredit);

            // Simpan hanya jika ada nilai > 0
            if ($d > 0 || $k > 0) {
                $dataDetail[] = [
                    'kode_akun' => $kode, // KODE AKUN DIJAMIN BENAR DARI KEY
                    'debit'     => $d,
                    'kredit'    => $k
                ];
                $totalDebit += $d;
                $totalKredit += $k;
            }
        }

        // Validasi Balance
        if (empty($dataDetail)) {
            return redirect()->back()->with('error', 'Belum ada nominal yang diisi.');
        }

        if (abs($totalDebit - $totalKredit) > 1) {
            return redirect()->back()->withInput()->with('error', 
                'Saldo Awal TIDAK BALANCE! Debit: '.number_format($totalDebit).' vs Kredit: '.number_format($totalKredit));
        }

        // Simpan Jurnal
        $dataHeader = [
            'no_bukti'        => $no_bukti,
            'tgl_jurnal'      => $tgl_jurnal,
            'keterangan'      => $keterangan,
            'jenis_transaksi' => 'Umum',
            'status_posting'  => 'Posted'
        ];

        try {
            $this->jurnalModel->simpanJurnalLengkap($dataHeader, $dataDetail);
            return redirect()->to('/transaksi')->with('success', 'Saldo Awal Berhasil Disimpan & Diposting!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}