<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $tahunIni = date('Y');
        $bulanIni = date('m');
        
        // 1. STATISTIK UMUM
        $totalJurnal = $db->table('jurnal')->countAll();
        $totalAkun   = $db->table('coa')->countAll();

        // 2. HITUNG KEUANGAN BULAN INI (REAL CALCULATION)
        
        // A. Hitung Pendapatan (Pendapatan + Penjualan)
        $queryPendapatan = $db->table('jurnal_detail')
            ->join('jurnal', 'jurnal.id_jurnal = jurnal_detail.id_jurnal')
            ->join('coa', 'coa.kode_akun = jurnal_detail.kode_akun')
            ->whereIn('coa.tipe_akun', ['Pendapatan', 'Penjualan']) 
            ->where('MONTH(jurnal.tgl_jurnal)', $bulanIni, false) 
            ->where('YEAR(jurnal.tgl_jurnal)', $tahunIni, false)
            ->where('jurnal.status_posting', 'Posted') // Pastikan hanya yg Posted
            ->selectSum('jurnal_detail.kredit', 'total')
            ->get()->getRow();
        $pendapatan = $queryPendapatan->total ?? 0;

        // B. Hitung Pembelian (KHUSUS Tipe Pembelian) - BARU DIPISAH
        $queryPembelian = $db->table('jurnal_detail')
            ->join('jurnal', 'jurnal.id_jurnal = jurnal_detail.id_jurnal')
            ->join('coa', 'coa.kode_akun = jurnal_detail.kode_akun')
            ->whereIn('coa.tipe_akun', ['Pembelian']) // Hanya tipe Pembelian
            ->where('MONTH(jurnal.tgl_jurnal)', $bulanIni, false)
            ->where('YEAR(jurnal.tgl_jurnal)', $tahunIni, false)
            ->where('jurnal.status_posting', 'Posted')
            ->selectSum('jurnal_detail.debit', 'total')
            ->get()->getRow();
        $pembelian = $queryPembelian->total ?? 0;

        // C. Hitung Beban (HANYA Biaya Operasional) - DIPISAH DARI PEMBELIAN
        $queryBeban = $db->table('jurnal_detail')
            ->join('jurnal', 'jurnal.id_jurnal = jurnal_detail.id_jurnal')
            ->join('coa', 'coa.kode_akun = jurnal_detail.kode_akun')
            ->whereIn('coa.tipe_akun', ['Beban']) // Hanya tipe Beban
            ->where('MONTH(jurnal.tgl_jurnal)', $bulanIni, false)
            ->where('YEAR(jurnal.tgl_jurnal)', $tahunIni, false)
            ->where('jurnal.status_posting', 'Posted')
            ->selectSum('jurnal_detail.debit', 'total')
            ->get()->getRow();
        $beban = $queryBeban->total ?? 0;

        // D. Hitung Laba/Rugi (Pendapatan - Pembelian - Beban)
        $labaRugi = $pendapatan - ($pembelian + $beban);

        // 3. DATA GRAFIK (Tetap digabung atau dipisah terserah, default ini saya gabung bebannya utk grafik agar simpel)
        // Grafik Pendapatan
        $grafikPendapatan = array_fill(0, 12, 0);
        $sqlP = "SELECT MONTH(j.tgl_jurnal) as bulan, SUM(jd.kredit) as total 
                 FROM jurnal_detail jd
                 JOIN jurnal j ON j.id_jurnal = jd.id_jurnal
                 JOIN coa c ON c.kode_akun = jd.kode_akun
                 WHERE c.tipe_akun IN ('Pendapatan', 'Penjualan') 
                 AND YEAR(j.tgl_jurnal) = ? AND j.status_posting = 'Posted'
                 GROUP BY MONTH(j.tgl_jurnal)";
        $resP = $db->query($sqlP, [$tahunIni])->getResultArray();
        foreach($resP as $row) { $grafikPendapatan[$row['bulan'] - 1] = (int)$row['total']; }

        // Grafik Pengeluaran (Beban + Pembelian)
        $grafikBeban = array_fill(0, 12, 0);
        $sqlB = "SELECT MONTH(j.tgl_jurnal) as bulan, SUM(jd.debit) as total 
                 FROM jurnal_detail jd
                 JOIN jurnal j ON j.id_jurnal = jd.id_jurnal
                 JOIN coa c ON c.kode_akun = jd.kode_akun
                 WHERE c.tipe_akun IN ('Beban', 'Pembelian') 
                 AND YEAR(j.tgl_jurnal) = ? AND j.status_posting = 'Posted'
                 GROUP BY MONTH(j.tgl_jurnal)";
        $resB = $db->query($sqlB, [$tahunIni])->getResultArray();
        foreach($resB as $row) { $grafikBeban[$row['bulan'] - 1] = (int)$row['total']; }

        // 4. TRANSAKSI TERAKHIR
        $transaksiTerakhir = $db->table('jurnal')
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $data = [
            'title' => 'Dashboard Keuangan',
            'stats' => [
                'jurnal'     => $totalJurnal,
                'akun'       => $totalAkun,
                'pendapatan' => $pendapatan,
                'pembelian'  => $pembelian, // Data Baru
                'beban'      => $beban,     // Data Baru (Terpisah)
                'laba_rugi'  => $labaRugi
            ],
            'chart' => [
                'pendapatan' => $grafikPendapatan,
                'beban'      => $grafikBeban
            ],
            'recent_jurnal' => $transaksiTerakhir
        ];

        return view('dashboard/index', $data);
    }
}