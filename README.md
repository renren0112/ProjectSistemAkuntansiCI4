Sistem Informasi Akuntansi (SIA) Perusahaan Dagang

Aplikasi akuntansi berbasis web yang dibangun menggunakan CodeIgniter 4 untuk menangani siklus akuntansi perusahaan dagang (Metode Periodik).

🚀 Fitur Utama

Master Data: Kelola Akun (COA), Periode Akuntansi, User (Role: Admin, Accounting, Kasir).

Transaksi Jurnal:

Jurnal Umum, Pembelian, Penjualan.

Penerimaan & Pengeluaran Kas.

Jurnal Penyesuaian (AJP).

Laporan Keuangan (Real-time):

Jurnal Umum & Buku Besar.

Neraca Saldo & Neraca Lajur.

Laporan Laba Rugi, Perubahan Modal, Neraca, Arus Kas.

Fitur Khusus:

Tutup Buku (Closing) otomatis.

Audit Trail (Log Aktivitas User).

Backup & Restore Database.

Saldo Awal setup.

🛠️ Teknologi

Backend: PHP 8.2 (CodeIgniter 4 Framework)

Frontend: Bootstrap 4 (SB Admin 2 Template)

Database: MySQL

📦 Cara Instalasi

Clone Repository

git clone [https://github.com/username/sia-dagang-ci4.git](https://github.com/username/sia-dagang-ci4.git)


Install Dependencies
Masuk ke folder project dan jalankan:

composer install


Setup Database

Buat database baru di phpMyAdmin (misal: db_akuntansi).

Import file database_akuntansi.sql yang ada di root folder.

Konfigurasi Environment

Rename file env menjadi .env.

Buka .env dan sesuaikan konfigurasi database:

CI_ENVIRONMENT = development
app.baseURL = 'http://localhost:8080/'

database.default.hostname = localhost
database.default.database = db_akuntansi
database.default.username = root
database.default.password = 


Jalankan Server

php spark serve


Buka browser di http://localhost:8080.

👤 Akun Demo

Admin: admin / 123456 (Sesuaikan dengan data Anda)