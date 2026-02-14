<?php

namespace App\Models;

use CodeIgniter\Model;

class JurnalModel extends Model
{
    protected $table            = 'jurnal';
    protected $primaryKey       = 'id_jurnal';
    
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at'; 

    protected $allowedFields    = ['no_bukti', 'jenis_transaksi', 'tgl_jurnal', 'keterangan', 'status_posting', 'deleted_at'];

    // 1. SIMPAN TRANSAKSI
    public function simpanJurnalLengkap($dataHeader, $dataDetail)
    {
        $db = \Config\Database::connect();
        $db->transBegin();
        try {
            if (!isset($dataHeader['status_posting'])) {
                $dataHeader['status_posting'] = 'Pending';
            }

            if (!$this->insert($dataHeader)) {
                $errors = $this->errors();
                throw new \Exception('Gagal simpan Header: ' . implode(', ', $errors));
            }
            $id_jurnal_baru = $this->getInsertID();

            $detailBuilder = $db->table('jurnal_detail');
            $dataSiapSimpan = [];
            foreach ($dataDetail as $row) {
                $dataSiapSimpan[] = [
                    'id_jurnal' => $id_jurnal_baru,
                    'kode_akun' => $row['kode_akun'],
                    'debit'     => $row['debit'],
                    'kredit'    => $row['kredit']
                ];
            }
            if (!empty($dataSiapSimpan)) {
                $detailBuilder->insertBatch($dataSiapSimpan);
            }

            if ($db->transStatus() === false) {
                $dbError = $db->error();
                throw new \Exception('Database Error: ' . $dbError['message']);
            }
            $db->transCommit();
            return true;
        } catch (\Exception $e) {
            $db->transRollback();
            return $e->getMessage();
        }
    }

    // 2. DATA DETAIL
    public function getJurnalByIdComplete($id_jurnal)
    {
        return $this->db->table('jurnal')
            ->select('jurnal.*, jurnal_detail.debit, jurnal_detail.kredit, coa.nama_akun, coa.kode_akun')
            ->join('jurnal_detail', 'jurnal_detail.id_jurnal = jurnal.id_jurnal')
            ->join('coa', 'coa.kode_akun = jurnal_detail.kode_akun')
            ->where('jurnal.id_jurnal', $id_jurnal)
            ->get()
            ->getResultArray();
    }
    
    public function getJurnalDetail($id) {
        return $this->getJurnalByIdComplete($id);
    }

    // 3. BUKU BESAR
    public function getBukuBesar($kode_akun, $bulan, $tahun)
    {
        return $this->db->table('jurnal_detail')
            ->select('jurnal.id_jurnal, jurnal.tgl_jurnal, jurnal.no_bukti, jurnal.keterangan, jurnal_detail.debit, jurnal_detail.kredit')
            ->join('jurnal', 'jurnal.id_jurnal = jurnal_detail.id_jurnal')
            ->where('jurnal_detail.kode_akun', $kode_akun)
            ->where('MONTH(jurnal.tgl_jurnal)', $bulan)
            ->where('YEAR(jurnal.tgl_jurnal)', $tahun)
            ->where('jurnal.status_posting', 'Posted')
            ->orderBy('jurnal.tgl_jurnal', 'ASC')
            ->get()
            ->getResultArray();
    }

    // 4. NERACA SALDO
    public function getNeracaSaldo($bulan, $tahun, $include_closing = true)
    {
        $builder = $this->db->table('coa');
        $builder->select('coa.kode_akun, coa.nama_akun, coa.posisi_saldo_normal, 
                          SUM(jurnal_detail.debit) as total_debit, 
                          SUM(jurnal_detail.kredit) as total_kredit');
        
        $builder->join('jurnal_detail', 'jurnal_detail.kode_akun = coa.kode_akun', 'left');
        $builder->join('jurnal', "jurnal.id_jurnal = jurnal_detail.id_jurnal AND jurnal.status_posting = 'Posted'", 'left');
        
        $whereClause = "(
            (YEAR(jurnal.tgl_jurnal) = " . $this->db->escape($tahun) . " AND MONTH(jurnal.tgl_jurnal) <= " . $this->db->escape($bulan) . ")
            OR (YEAR(jurnal.tgl_jurnal) < " . $this->db->escape($tahun) . ")
        )";

        if (!$include_closing) {
            $whereClause .= " AND jurnal.jenis_transaksi != 'Penutup'";
        }

        $builder->where("($whereClause OR jurnal.id_jurnal IS NULL)");
        $builder->groupBy('coa.kode_akun');
        $builder->orderBy('coa.kode_akun', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    // --- 5. PERBAIKAN PENTING: SUPPORT ARRAY TIPE AKUN ---
    public function getLaporanByTipe($tipe_akun, $bulan, $tahun)
    {
        // Konversi ke array jika input masih string tunggal
        if (!is_array($tipe_akun)) {
            $tipe_akun = [$tipe_akun];
        }

        // Cek apakah ini akun Neraca (Saldo berjalan/Kumulatif) atau Laba Rugi (Per Bulan)
        $isNeraca = false;
        foreach ($tipe_akun as $t) {
            if (in_array($t, ['Aset', 'Liabilitas', 'Ekuitas'])) {
                $isNeraca = true;
                break;
            }
        }
        
        $builder = $this->db->table('coa');
        $builder->select('coa.kode_akun, coa.nama_akun, coa.tipe_akun, coa.posisi_saldo_normal, 
                          SUM(jurnal_detail.debit) as total_debit, 
                          SUM(jurnal_detail.kredit) as total_kredit');
        
        $builder->join('jurnal_detail', 'jurnal_detail.kode_akun = coa.kode_akun', 'left');
        $builder->join('jurnal', "jurnal.id_jurnal = jurnal_detail.id_jurnal AND jurnal.status_posting = 'Posted'", 'left');
        
        // Filter Tipe Akun menggunakan WHERE IN (Support Array)
        $builder->whereIn('coa.tipe_akun', $tipe_akun);

        // Filter Waktu
        if ($isNeraca) {
            // Saldo Kumulatif (Sampai dengan bulan ini)
            $builder->where("(jurnal.tgl_jurnal IS NULL OR (YEAR(jurnal.tgl_jurnal) < $tahun) OR (YEAR(jurnal.tgl_jurnal) = $tahun AND MONTH(jurnal.tgl_jurnal) <= $bulan))");
        } else {
            // Saldo Periode Berjalan (Hanya Bulan & Tahun ini) -> PENTING UNTUK LABA RUGI BULANAN
            $builder->where("(jurnal.tgl_jurnal IS NULL OR (YEAR(jurnal.tgl_jurnal) = $tahun AND MONTH(jurnal.tgl_jurnal) = $bulan))");
        }

        // Jangan hitung jurnal penutup untuk laporan standar (agar tidak 0)
        $builder->where("(jurnal.jenis_transaksi != 'Penutup' OR jurnal.jenis_transaksi IS NULL)");
        
        $builder->groupBy('coa.kode_akun');
        $builder->orderBy('coa.kode_akun', 'ASC');
        
        return $builder->get()->getResultArray();
    }

    // 6. VALIDASI TUTUP BUKU
    public function cekStatusPenutupan($bulan, $tahun) {
        return $this->table('jurnal')
                    ->where('jenis_transaksi', 'Penutup')
                    ->where('MONTH(tgl_jurnal)', $bulan)
                    ->where('YEAR(tgl_jurnal)', $tahun)
                    ->where('status_posting', 'Posted') 
                    ->countAllResults() > 0;
    }

    // 7. NERACA LAJUR
    public function getNeracaLajur($bulan, $tahun)
    {
        $sql = "SELECT c.kode_akun, c.nama_akun, c.tipe_akun, c.posisi_saldo_normal,
                       SUM(CASE WHEN j.jenis_transaksi NOT IN ('Penyesuaian', 'Penutup') THEN jd.debit ELSE 0 END) as ns_debit,
                       SUM(CASE WHEN j.jenis_transaksi NOT IN ('Penyesuaian', 'Penutup') THEN jd.kredit ELSE 0 END) as ns_kredit,
                       
                       SUM(CASE WHEN j.jenis_transaksi = 'Penyesuaian' THEN jd.debit ELSE 0 END) as ajp_debit,
                       SUM(CASE WHEN j.jenis_transaksi = 'Penyesuaian' THEN jd.kredit ELSE 0 END) as ajp_kredit

                FROM coa c
                LEFT JOIN jurnal_detail jd ON c.kode_akun = jd.kode_akun
                LEFT JOIN jurnal j ON jd.id_jurnal = j.id_jurnal AND j.status_posting = 'Posted'
                
                WHERE (
                    (YEAR(j.tgl_jurnal) = " . $this->db->escape($tahun) . " AND MONTH(j.tgl_jurnal) <= " . $this->db->escape($bulan) . ")
                    OR (YEAR(j.tgl_jurnal) < " . $this->db->escape($tahun) . ")
                ) OR j.id_jurnal IS NULL
                
                GROUP BY c.kode_akun
                ORDER BY c.kode_akun ASC";

        return $this->db->query($sql)->getResultArray();
    }
}