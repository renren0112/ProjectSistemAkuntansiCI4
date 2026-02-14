<?php

namespace App\Models;

use CodeIgniter\Model;

class CoaModel extends Model
{
    protected $table            = 'coa';
    protected $primaryKey       = 'kode_akun';
    protected $useAutoIncrement = false; // Karena kode akun kita input manual (String)
    protected $allowedFields    = ['kode_akun', 'nama_akun', 'tipe_akun', 'posisi_saldo_normal'];
}