<?php

namespace App\Models;

use CodeIgniter\Model;

class BarangModel extends Model
{
    protected $table = 'barang';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    
    // PERBAIKAN: Menambahkan 'kategori'
    protected $allowedFields = ['kode_barang', 'nama_barang', 'kategori', 'stok', 'harga_beli', 'harga_jual'];
}