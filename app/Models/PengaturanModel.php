<?php

namespace App\Models;

use CodeIgniter\Model;

class PengaturanModel extends Model
{
    protected $table            = 'pengaturan';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['kunci', 'nilai'];

    // Helper: Ambil satu nilai berdasarkan kunci
    public function getNilai($kunci)
    {
        $row = $this->where('kunci', $kunci)->first();
        return $row ? $row['nilai'] : '';
    }

    // Helper: Update atau Insert jika belum ada
    public function updateNilai($kunci, $nilai)
    {
        $exist = $this->where('kunci', $kunci)->first();
        
        if ($exist) {
            // Update jika ada
            return $this->where('kunci', $kunci)->set(['nilai' => $nilai])->update();
        } else {
            // Insert baru jika tidak ada
            return $this->insert(['kunci' => $kunci, 'nilai' => $nilai]);
        }
    }
}