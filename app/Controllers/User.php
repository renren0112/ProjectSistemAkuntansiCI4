<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Menampilkan semua user tanpa filter role yang rumit
        $data = [
            'title' => 'Kelola Pengguna (User)',
            'users' => $this->userModel->findAll()
        ];
        return view('user/index', $data);
    }

    public function create()
    {
        $data = ['title' => 'Tambah User Baru'];
        return view('user/create', $data);
    }

    public function store()
    {
        // Validasi input
        if (!$this->validate([
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[4]',
            'nama_lengkap' => 'required'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Username mungkin sudah ada.');
        }

        // Simpan data
        $this->userModel->insert([
            'username'     => $this->request->getPost('username'),
            'password'     => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role'         => $this->request->getPost('role') ?? 'admin', // Default admin jika kosong
        ]);

        return redirect()->to('/user')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/user')->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit User',
            'user'  => $user
        ];
        return view('user/edit', $data);
    }

    public function update($id)
    {
        // Validasi sederhana
        $rules = [
            'nama_lengkap' => 'required'
        ];

        // Cek jika password diisi (artinya mau ganti password)
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[4]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $dataUpdate = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'role'         => $this->request->getPost('role'),
        ];

        // Update password hanya jika form diisi
        if (!empty($password)) {
            $dataUpdate['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $dataUpdate);

        return redirect()->to('/user')->with('success', 'Data user diperbarui.');
    }

    public function delete($id)
    {
        // Mencegah menghapus akun sendiri yang sedang login
        if (session()->get('id_user') == $id) {
             return redirect()->to('/user')->with('error', 'Tidak bisa menghapus akun sendiri.');
        }

        $this->userModel->delete($id);
        return redirect()->to('/user')->with('success', 'User dihapus.');
    }
}