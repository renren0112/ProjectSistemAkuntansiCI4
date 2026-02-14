<?php

namespace App\Models;

use CodeIgniter\Model;

class LogModel extends Model
{
    protected $table = 'log_aktivitas';
    protected $primaryKey = 'id_log';
    protected $allowedFields = ['tgl_log', 'username', 'role', 'aksi', 'keterangan'];
    protected $useTimestamps = false; // Kita pakai timestamp database saja

    // Fungsi Praktis untuk mencatat log
    public function catat($aksi, $keterangan)
    {
        $session = session();
        // Hanya catat jika user sedang login
        if ($session->get('logged_in')) {
            $this->insert([
                'username'   => $session->get('username') ?? 'System',
                'role'       => $session->get('role') ?? 'System',
                'aksi'       => $aksi,
                'keterangan' => $keterangan
            ]);
        }
    }
}