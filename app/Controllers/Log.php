<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\LogModel; // Pastikan Model Log diload

class Log extends BaseController
{
    protected $logModel;

    public function __construct()
    {
        $this->logModel = new LogModel();
    }

    public function index()
    {
        // Fitur ini biasanya hanya untuk Admin
        // Tapi filter sudah ditangani di Routes, jadi aman.
        
        $data = [
            'title' => 'Audit Trail (Log Aktivitas)',
            // Ambil 200 aktivitas terakhir, urutkan dari yang terbaru
            'logs'  => $this->logModel->orderBy('tgl_log', 'DESC')->findAll(200)
        ];

        return view('pengaturan/log_aktivitas', $data);
    }
}