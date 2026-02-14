<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }
        return view('auth/login');
    }

    public function login()
    {
        $session = session();
        $model = new UserModel();
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $data = $model->where('username', $username)->first();

        if ($data) {
            $pass_valid = password_verify($password, $data['password']);
            
            if ($pass_valid) {
                $ses_data = [
                    'id_user'      => $data['id_user'],
                    'username'     => $data['username'],
                    'nama_lengkap' => $data['nama_lengkap'],
                    'role'         => $data['role'], // Masih disimpan tapi tidak krusial logic-nya
                    'logged_in'    => TRUE
                ];
                $session->set($ses_data);
                return redirect()->to('/');
            } else {
                return redirect()->back()->withInput()->with('error', 'Password salah.');
            }
        } else {
            return redirect()->back()->withInput()->with('error', 'Username tidak ditemukan.');
        }
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }
    
    // Fitur Bantuan: Reset User Admin Standar
    // Akses: http://localhost:8080/auth/setup
    public function setup()
    {
        $model = new UserModel();
        $user = $model->where('username', 'admin')->first();

        if ($user) {
            $model->update($user['id_user'], [
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin'
            ]);
            echo "User admin sudah ada. Password di-reset menjadi: <b>admin123</b>.";
        } else {
            $model->insert([
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'nama_lengkap' => 'Administrator',
                'role' => 'admin'
            ]);
            echo "User admin berhasil dibuat. User: <b>admin</b>, Pass: <b>admin123</b>.";
        }
        echo "<br><br><a href='".base_url('login')."'>Ke Halaman Login</a>";
    }
}