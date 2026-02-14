<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Reset extends Controller
{
    public function index()
    {
        // Peringatan Keamanan
        if (ENVIRONMENT !== 'development') {
            return "Fitur ini hanya boleh diakses di mode Development!";
        }

        $db = \Config\Database::connect();
        
        // Disable Foreign Key Checks
        $db->query('SET FOREIGN_KEY_CHECKS = 0');
        
        // Kosongkan Tabel
        $db->table('jurnal_detail')->truncate();
        $db->table('jurnal')->truncate();
        
        // Enable Foreign Key Checks
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        echo "<h1>Database Reset Berhasil!</h1>";
        echo "<p>Semua transaksi telah dihapus. No Bukti akan kembali ke 0001.</p>";
        echo "<a href='".base_url('/')."'>Kembali ke Dashboard</a>";
    }
}