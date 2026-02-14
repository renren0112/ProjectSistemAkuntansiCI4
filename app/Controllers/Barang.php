<?php

namespace App\Controllers;

use App\Models\BarangModel;

class Barang extends BaseController
{
    protected $barangModel;

    public function __construct()
    {
        $this->barangModel = new BarangModel();
    }

    public function index()
    {
        $keyword = $this->request->getGet('keyword');
        $builder = $this->barangModel->orderBy('kode_barang', 'ASC');

        if ($keyword) {
            $builder->groupStart()
                    ->like('nama_barang', $keyword)
                    ->orLike('kode_barang', $keyword)
                    ->orLike('kategori', $keyword)
                    ->groupEnd();
        }

        $data = [
            'title' => 'Data Barang (Inventory)',
            'barang' => $builder->findAll(),
            'keyword' => $keyword
        ];
        return view('barang/index', $data);
    }

    public function create()
    {
        $data = ['title' => 'Tambah Barang Baru'];
        return view('barang/create', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'kode_barang' => 'required|is_unique[barang.kode_barang]',
            'nama_barang' => 'required',
            'kategori'    => 'required|in_list[Laptop,Komponen PC,Aksesoris]', // Validasi ENUM
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan data benar.');
        }

        $this->barangModel->save([
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'kategori'    => $this->request->getPost('kategori'),
            'stok'        => $this->request->getPost('stok'),
            'harga_beli'  => $this->request->getPost('harga_beli'),
            'harga_jual'  => $this->request->getPost('harga_jual'),
        ]);

        return redirect()->to('/barang')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Barang',
            'barang' => $this->barangModel->find($id)
        ];
        return view('barang/edit', $data);
    }

    public function update($id)
    {
        $lama = $this->barangModel->find($id);
        $baru = $this->request->getPost('kode_barang');
        
        $rule_kode = 'required';
        if($baru != $lama['kode_barang']){
            $rule_kode = 'required|is_unique[barang.kode_barang]';
        }

        if (!$this->validate([
            'kode_barang' => $rule_kode,
            'nama_barang' => 'required',
            'kategori'    => 'required|in_list[Laptop,Komponen PC,Aksesoris]',
            'harga_beli'  => 'required|numeric',
            'harga_jual'  => 'required|numeric'
        ])) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->barangModel->update($id, [
            'kode_barang' => $this->request->getPost('kode_barang'),
            'nama_barang' => $this->request->getPost('nama_barang'),
            'kategori'    => $this->request->getPost('kategori'),
            'stok'        => $this->request->getPost('stok'), 
            'harga_beli'  => $this->request->getPost('harga_beli'),
            'harga_jual'  => $this->request->getPost('harga_jual'),
        ]);

        return redirect()->to('/barang')->with('success', 'Data barang diperbarui.');
    }

    public function delete($id)
    {
        $this->barangModel->delete($id);
        return redirect()->to('/barang')->with('success', 'Barang dihapus.');
    }
}