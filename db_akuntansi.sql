-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Feb 2026 pada 04.21
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_akuntansi`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(20) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `kategori` enum('Laptop','Komponen PC','Aksesoris') DEFAULT 'Aksesoris',
  `stok` int(11) DEFAULT 0,
  `harga_beli` decimal(15,2) DEFAULT 0.00,
  `harga_jual` decimal(15,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `kategori`, `stok`, `harga_beli`, `harga_jual`, `created_at`, `updated_at`) VALUES
(1, 'LPT-ASUS-01', 'Laptop Asus Vivobook 14 (i5/8GB/512GB)', 'Laptop', 10, 8500000.00, 9200000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(2, 'LPT-LNV-02', 'Lenovo Ideapad Slim 3 (Ryzen 3/8GB/256GB)', 'Laptop', 15, 6200000.00, 6800000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(3, 'LPT-HP-03', 'HP Pavilion Gaming 15 (Ryzen 5/16GB/RTX3050)', 'Laptop', 5, 12500000.00, 13750000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(4, 'LPT-MAC-04', 'MacBook Air M1 2020 (8GB/256GB)', 'Laptop', 3, 11000000.00, 12200000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(5, 'LPT-ACR-05', 'Acer Aspire 5 Slim (i3/4GB/512GB)', 'Laptop', 8, 5800000.00, 6400000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(6, 'CMP-SSD-01', 'SSD Samsung 870 EVO 500GB SATA', 'Komponen PC', 20, 850000.00, 990000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(7, 'CMP-SSD-02', 'SSD NVMe Kingston 250GB', 'Komponen PC', 25, 450000.00, 575000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(8, 'CMP-RAM-01', 'RAM SODIMM DDR4 8GB Kingston 3200MHz', 'Komponen PC', 30, 380000.00, 475000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(9, 'CMP-RAM-02', 'RAM PC Corsair Vengeance RGB 16GB (2x8GB)', 'Komponen PC', 10, 1100000.00, 1350000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(10, 'CMP-HDD-01', 'Harddisk Seagate Barracuda 1TB', 'Komponen PC', 15, 650000.00, 780000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(11, 'CMP-VGA-01', 'VGA NVIDIA GTX 1650 4GB', 'Komponen PC', 4, 2500000.00, 2900000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(12, 'ACC-MSE-01', 'Mouse Logitech B100 Optical', 'Aksesoris', 50, 45000.00, 65000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(13, 'ACC-MSE-02', 'Mouse Wireless Logitech M220 Silent', 'Aksesoris', 30, 150000.00, 195000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(14, 'ACC-KBD-01', 'Keyboard Logitech K120 USB', 'Aksesoris', 25, 110000.00, 145000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(15, 'ACC-KBD-02', 'Keyboard Mechanical VortexSeries GT-65', 'Aksesoris', 8, 850000.00, 1050000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(16, 'ACC-MNT-01', 'Monitor LG 24 Inch IPS 75Hz', 'Aksesoris', 12, 1600000.00, 1850000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(17, 'ACC-FD-01', 'Flashdisk Sandisk Cruzer Blade 32GB', 'Aksesoris', 100, 55000.00, 75000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35'),
(18, 'ACC-PRT-01', 'Printer Epson L3210 EcoTank', 'Aksesoris', 6, 2100000.00, 2450000.00, '2026-01-12 18:47:35', '2026-01-12 18:47:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `coa`
--

CREATE TABLE `coa` (
  `kode_akun` varchar(10) NOT NULL,
  `nama_akun` varchar(100) NOT NULL,
  `tipe_akun` enum('Aset','Liabilitas','Ekuitas','Pendapatan','Beban','Penjualan','Pembelian') NOT NULL,
  `posisi_saldo_normal` enum('D','K') NOT NULL COMMENT 'D=Debit, K=Kredit'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `coa`
--

INSERT INTO `coa` (`kode_akun`, `nama_akun`, `tipe_akun`, `posisi_saldo_normal`) VALUES
('111', 'Kas', 'Aset', 'D'),
('112', 'Bank BCA', 'Aset', 'D'),
('113', 'Piutang Dagang', 'Aset', 'D'),
('114', 'Perlengkapan Toko', 'Aset', 'D'),
('115', 'Persediaan Barang Dagang', 'Aset', 'D'),
('121', 'Peralatan Toko', 'Aset', 'D'),
('122', 'Akumulasi Depresiasi Peralatan Toko', 'Aset', 'K'),
('211', 'Utang Usaha', 'Liabilitas', 'K'),
('212', 'Utang Gaji', 'Liabilitas', 'K'),
('226', 'Utang Bunga', 'Liabilitas', 'K'),
('311', 'Modal', 'Ekuitas', 'K'),
('312', 'Prive Pemilik', 'Ekuitas', 'D'),
('313', 'Ikhtisar Laba-Rugi', 'Ekuitas', 'K'),
('411', 'Penjualan', 'Penjualan', 'K'),
('412', 'Retur Penjualan', 'Penjualan', 'D'),
('511', 'Pembelian', 'Pembelian', 'D'),
('512', 'Retur Pembelian', 'Pembelian', 'D'),
('611', 'Beban Gaji', 'Beban', 'D'),
('612', 'Beban Listrik & Air', 'Beban', 'D'),
('613', 'Beban Sewa', 'Beban', 'D'),
('614', 'Beban Iklan', 'Beban', 'D'),
('615', 'Beban Depresiasi Peralatan Toko', 'Beban', 'D'),
('711', 'Pendapatan Lain-lain', 'Pendapatan', 'K');

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal`
--

CREATE TABLE `jurnal` (
  `id_jurnal` int(11) NOT NULL,
  `no_bukti` varchar(50) DEFAULT NULL,
  `jenis_transaksi` enum('Umum','Penjualan','Pembelian','Penyesuaian','Penutup','Koreksi','PenerimaanKas','PengeluaranKas') DEFAULT 'Umum',
  `tgl_jurnal` date NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status_posting` enum('Pending','Posted') DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jurnal_detail`
--

CREATE TABLE `jurnal_detail` (
  `id_detail` int(11) NOT NULL,
  `id_jurnal` int(11) DEFAULT NULL,
  `kode_akun` varchar(10) DEFAULT NULL,
  `debit` decimal(15,2) DEFAULT 0.00,
  `kredit` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id_log` int(11) NOT NULL,
  `tgl_log` datetime DEFAULT current_timestamp(),
  `username` varchar(50) NOT NULL,
  `role` varchar(20) NOT NULL,
  `aksi` varchar(50) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id` int(11) NOT NULL,
  `kode_pelanggan` varchar(20) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaturan`
--

CREATE TABLE `pengaturan` (
  `id` int(11) NOT NULL,
  `kunci` varchar(50) NOT NULL,
  `nilai` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaturan`
--

INSERT INTO `pengaturan` (`id`, `kunci`, `nilai`) VALUES
(1, 'nama_perusahaan', 'PRIMA COMPUTER '),
(2, 'alamat_perusahaan', 'Jl. Jenderal Ahmad Yani No. 45, Kendal'),
(3, 'no_telp', '(024) 777-1234'),
(4, 'email_perusahaan', 'admin@primacomputer.com');

-- --------------------------------------------------------

--
-- Struktur dari tabel `periode_akuntansi`
--

CREATE TABLE `periode_akuntansi` (
  `id` int(11) NOT NULL,
  `tahun` int(11) NOT NULL,
  `bulan` int(11) NOT NULL,
  `nama_periode` varchar(30) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `status` enum('OPEN','CLOSED') DEFAULT 'CLOSED',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `periode_akuntansi`
--

INSERT INTO `periode_akuntansi` (`id`, `tahun`, `bulan`, `nama_periode`, `tgl_mulai`, `tgl_selesai`, `status`, `created_at`, `updated_at`) VALUES
(1, 2026, 1, 'Januari 2026', '2026-01-01', '2026-01-31', 'CLOSED', '2026-01-13 14:32:16', '2026-01-24 14:56:51'),
(2, 2026, 2, 'Februari 2026', '2026-02-01', '2026-02-28', 'OPEN', '2026-01-13 14:32:16', '2026-02-02 22:44:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `supplier`
--

CREATE TABLE `supplier` (
  `id` int(11) NOT NULL,
  `kode_supplier` varchar(20) NOT NULL,
  `nama_supplier` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `role` enum('admin','accounting','kasir') DEFAULT 'accounting',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `nama_lengkap`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$F8zaqEcfv2HWXCWmfXyrqeCquVKus3xYFZuT8rgk2Pwut.5mqQjrG', 'Administrator', 'admin', '2026-01-08 21:44:11'),
(2, 'mahardhika', '$2y$10$Srho7cKv7RSfikM6auqkc.XItTt2Cz9koOVM8i72FgXJd9pqhFUhy', 'Mahardhika', 'admin', '2026-01-08 22:28:34'),
(4, 'kasir01', '$2y$10$kkqo0jMZ7/cQxktYGM819.ckaS1OMHaLbHEJfO65ZXGxFf8.LmoQi', 'kasir', 'kasir', '2026-01-20 11:31:04'),
(5, 'accounting01', '$2y$10$dIkDBvaBJHkHlSozKnjKWO59mmcQn/ED7VAxDUUniBE7nezVBYMKi', 'accounting01', 'accounting', '2026-01-20 11:31:40');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`);

--
-- Indeks untuk tabel `coa`
--
ALTER TABLE `coa`
  ADD PRIMARY KEY (`kode_akun`);

--
-- Indeks untuk tabel `jurnal`
--
ALTER TABLE `jurnal`
  ADD PRIMARY KEY (`id_jurnal`);

--
-- Indeks untuk tabel `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_jurnal` (`id_jurnal`),
  ADD KEY `kode_akun` (`kode_akun`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id_log`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pelanggan` (`kode_pelanggan`);

--
-- Indeks untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `periode_akuntansi`
--
ALTER TABLE `periode_akuntansi`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_supplier` (`kode_supplier`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `jurnal`
--
ALTER TABLE `jurnal`
  MODIFY `id_jurnal` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pengaturan`
--
ALTER TABLE `pengaturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `periode_akuntansi`
--
ALTER TABLE `periode_akuntansi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `supplier`
--
ALTER TABLE `supplier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `jurnal_detail`
--
ALTER TABLE `jurnal_detail`
  ADD CONSTRAINT `jurnal_detail_ibfk_1` FOREIGN KEY (`id_jurnal`) REFERENCES `jurnal` (`id_jurnal`) ON DELETE CASCADE,
  ADD CONSTRAINT `jurnal_detail_ibfk_2` FOREIGN KEY (`kode_akun`) REFERENCES `coa` (`kode_akun`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
