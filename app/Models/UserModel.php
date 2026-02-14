<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id_user';
    protected $useAutoIncrement = true;
    
    // Kolom yang boleh diisi secara massal (melalui insert/update)
    protected $allowedFields    = ['username', 'password', 'nama_lengkap', 'role'];

    // Pengaturan Tanggal Otomatis (sesuai tabel database)
    protected $useTimestamps = false; 
    // Jika di tabel users Anda ada kolom 'created_at' dan ingin diisi otomatis oleh CI4, set true.
    // Tapi karena di SQL sebelumnya 'created_at' pakai DEFAULT CURRENT_TIMESTAMP dari MySQL, 
    // set false juga tidak masalah, database yang akan mengisinya.
}