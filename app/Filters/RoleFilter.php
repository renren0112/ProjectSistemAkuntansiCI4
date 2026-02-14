<?php 

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. Cek Login (Backup jika Auth Filter lolos)
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        // 2. Ambil Role User dari Session
        $userRole = session()->get('role');

        // 3. Cek apakah Role user ada di daftar yang diizinkan
        // $arguments dikirim dari Routes, misal: ['admin', 'accounting']
        if (empty($arguments) || !in_array($userRole, $arguments)) {
            // Jika role tidak cocok, tendang ke dashboard dengan pesan error
            return redirect()->to('/')->with('error', 'Akses Ditolak! Anda tidak memiliki izin untuk mengakses halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing here
    }
}