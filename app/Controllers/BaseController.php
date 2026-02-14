<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\PengaturanModel; // 1. Panggil Model Pengaturan

/**
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 *
 * Extend this class in any new controllers:
 * ```
 * class Home extends BaseController
 * ```
 *
 * For security, be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        // $this->session = service('session');

        // --- 2. LOGIC GLOBAL PROFIL PERUSAHAAN ---
        // Kode ini akan jalan otomatis di setiap halaman
        $pengaturanModel = new PengaturanModel();
        
        // Ambil nama perusahaan dari database
        $namaPerusahaan = $pengaturanModel->getNilai('nama_perusahaan');
        
        // Jika kosong, pakai default
        if (empty($namaPerusahaan)) {
            $namaPerusahaan = 'SIA AKUNTANSI';
        }

        // 3. Share Data ke Semua View (Sidebar, Layout, dll)
        // Jadi di sidebar bisa panggil $company['nama_perusahaan']
        \Config\Services::renderer()->setData([
            'company' => [
                'nama_perusahaan' => $namaPerusahaan
            ]
        ]);
    }
}