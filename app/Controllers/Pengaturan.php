<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PengaturanModel;

class Pengaturan extends BaseController
{
    protected $pengaturanModel;

    public function __construct()
    {
        $this->pengaturanModel = new PengaturanModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Pengaturan Perusahaan',
            'nama_perusahaan'   => $this->pengaturanModel->getNilai('nama_perusahaan'),
            'alamat_perusahaan' => $this->pengaturanModel->getNilai('alamat_perusahaan'),
            'no_telp'           => $this->pengaturanModel->getNilai('no_telp'),
            'email_perusahaan'  => $this->pengaturanModel->getNilai('email_perusahaan'),
            'logo'              => $this->pengaturanModel->getNilai('logo'), // Tambahan
        ];

        return view('pengaturan/index', $data);
    }

    public function update()
    {
        // 1. Simpan Teks Biasa
        $this->pengaturanModel->updateNilai('nama_perusahaan', $this->request->getPost('nama_perusahaan'));
        $this->pengaturanModel->updateNilai('alamat_perusahaan', $this->request->getPost('alamat_perusahaan'));
        $this->pengaturanModel->updateNilai('no_telp', $this->request->getPost('no_telp'));
        $this->pengaturanModel->updateNilai('email_perusahaan', $this->request->getPost('email_perusahaan'));

        // 2. Proses Upload Logo (Jika ada file yang dipilih)
        $fileLogo = $this->request->getFile('logo');

        if ($fileLogo && $fileLogo->isValid() && !$fileLogo->hasMoved()) {
            // Generate nama file random agar unik
            $namaFile = $fileLogo->getRandomName();
            
            // Pindahkan ke folder public/uploads
            $fileLogo->move('uploads', $namaFile);
            
            // Simpan nama file ke database
            $this->pengaturanModel->updateNilai('logo', $namaFile);
        }

        return redirect()->to('/pengaturan')->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}