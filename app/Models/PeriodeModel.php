<?php

namespace App\Models;

use CodeIgniter\Model;

class PeriodeModel extends Model
{
    protected $table            = 'periode_akuntansi';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'tahun', 'bulan', 'nama_periode',
        'tgl_mulai', 'tgl_selesai', 'status'
    ];
    protected $useTimestamps = true;

    public function getPeriodeAktif()
    {
        return $this->where('status', 'OPEN')->first();
    }
}
