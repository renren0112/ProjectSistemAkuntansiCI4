<?php

namespace App\Controllers;

use App\Models\CoaModel;

class Coa extends BaseController
{
    protected $coaModel;

    public function __construct()
    {
        $this->coaModel = new CoaModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');

        if ($keyword) {
            $akun = $this->coaModel->like('kode_akun', $keyword)
                                   ->orLike('nama_akun', $keyword)
                                   ->orderBy('kode_akun', 'ASC')
                                   ->findAll();
        } else {
            $akun = $this->coaModel->orderBy('kode_akun', 'ASC')->findAll();
        }

        $data = [
            'title'   => 'Data Akun (Chart of Accounts)',
            'akun'    => $akun,
            'keyword' => $keyword
        ];
        return view('coa/index', $data);
    }

    public function create() {
        $data = ['title' => 'Tambah Akun Baru'];
        return view('coa/create', $data);
    }

    public function store() {
        if (!$this->validate([
            'kode_akun' => 'required|is_unique[coa.kode_akun]',
            'nama_akun' => 'required',
            'tipe_akun' => 'required',
            'posisi_saldo_normal' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Gagal validasi. Kode Akun harus unik.');
        }

        $this->coaModel->insert([
            'kode_akun' => $this->request->getPost('kode_akun'),
            'nama_akun' => $this->request->getPost('nama_akun'),
            'tipe_akun' => $this->request->getPost('tipe_akun'),
            'posisi_saldo_normal' => $this->request->getPost('posisi_saldo_normal'),
        ]);

        return redirect()->to('/akun')->with('success', 'Akun berhasil ditambahkan.');
    }

    public function edit($id) {
        $akun = $this->coaModel->find($id);
        if (!$akun) return redirect()->to('/akun')->with('error', 'Akun tidak ditemukan.');

        $data = [
            'title' => 'Edit Akun',
            'akun'  => $akun
        ];
        return view('coa/edit', $data);
    }

    // --- PERBAIKAN UTAMA DI SINI ---
    public function update($id) {
        // Ambil input
        $kodeBaru = $this->request->getPost('kode_akun'); // Pastikan form edit punya input name="kode_akun"
        $namaAkun = $this->request->getPost('nama_akun');
        $tipeAkun = $this->request->getPost('tipe_akun');
        $posisi   = $this->request->getPost('posisi_saldo_normal');

        // Cek apakah user mengganti Kode Akun?
        if ($kodeBaru != $id) {
            // Cek apakah kode baru sudah dipakai orang lain?
            $exist = $this->coaModel->where('kode_akun', $kodeBaru)->first();
            if ($exist) {
                return redirect()->back()->withInput()->with('error', "Kode Akun $kodeBaru sudah digunakan! Pilih kode lain.");
            }
        }

        // Proses Update
        // Karena kode_akun adalah Primary Key, update-nya agak tricky di CI4 Model standar.
        // Kita pakai Query Builder langsung agar aman.
        
        $db = \Config\Database::connect();
        
        // PENTING: Matikan FK Check dulu kalau update Primary Key yang berelasi
        $db->query('SET FOREIGN_KEY_CHECKS=0');
        
        $dataUpdate = [
            'kode_akun' => $kodeBaru, // Update kode akun juga
            'nama_akun' => $namaAkun,
            'tipe_akun' => $tipeAkun,
            'posisi_saldo_normal' => $posisi
        ];

        $builder = $db->table('coa');
        $builder->where('kode_akun', $id);
        $status = $builder->update($dataUpdate);
        
        // Update juga tabel jurnal_detail yang pakai kode lama (Manual Cascade)
        // Agar history transaksi tidak hilang/putus relasinya
        if ($kodeBaru != $id && $status) {
            $db->table('jurnal_detail')
               ->where('kode_akun', $id)
               ->update(['kode_akun' => $kodeBaru]);
        }

        // Nyalakan lagi FK Check
        $db->query('SET FOREIGN_KEY_CHECKS=1');

        if ($status) {
            return redirect()->to('/akun')->with('success', 'Data akun berhasil diperbarui.');
        } else {
            return redirect()->back()->withInput()->with('error', 'Gagal update database.');
        }
    }

    public function delete($id) {
        try {
            $this->coaModel->delete($id);
            return redirect()->to('/akun')->with('success', 'Akun berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->to('/akun')->with('error', 'Gagal hapus. Akun mungkin sudah digunakan di jurnal.');
        }
    }

    public function import()
    {
        $file = $this->request->getFile('file_excel');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $filepath = $file->getTempName();
            $handle = fopen($filepath, "r");
            $firstLine = fgets($handle);
            fclose($handle);

            $delimiter = (strpos($firstLine, ';') !== false) ? ';' : ',';

            $handle = fopen($filepath, "r");
            $count = 0; $success = 0; $updated = 0;
            
            $db = \Config\Database::connect();
            $db->transStart();

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                $count++;
                if ($count == 1) continue; 

                if (count($row) < 4) continue;

                $kode   = trim($row[0]);
                $nama   = trim($row[1]);
                $tipe   = trim($row[2]);
                $normal = trim($row[3]);

                if (!empty($kode) && !empty($nama)) {
                    $exists = $this->coaModel->find($kode);
                    $dataAkun = [
                        'kode_akun' => $kode,
                        'nama_akun' => $nama,
                        'tipe_akun' => $tipe,
                        'posisi_saldo_normal' => $normal
                    ];

                    if ($exists) {
                        $this->coaModel->update($kode, $dataAkun);
                        $updated++;
                    } else {
                        $this->coaModel->insert($dataAkun);
                        $success++;
                    }
                }
            }
            
            fclose($handle);
            $db->transComplete();

            return redirect()->to('/akun')->with('success', "Impor Selesai! $success baru, $updated diperbarui.");
        }
        return redirect()->to('/akun')->with('error', 'File tidak valid.');
    }

    public function export()
    {
        $akun = $this->coaModel->orderBy('kode_akun', 'ASC')->findAll();
        $filename = 'Data_Akun_'.date('Ymd').'.csv';
        
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; "); 
        
        $file = fopen('php://output', 'w');
        $header = ['Kode Akun', 'Nama Akun', 'Tipe Akun', 'Posisi Saldo Normal'];
        fputcsv($file, $header);
        
        foreach ($akun as $row){
            fputcsv($file, [$row['kode_akun'], $row['nama_akun'], $row['tipe_akun'], $row['posisi_saldo_normal']]);
        }
        fclose($file);
        exit;
    }
}