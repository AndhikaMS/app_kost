-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Jul 2025 pada 08.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app_kost`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_barang`
--

CREATE TABLE `tb_barang` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_barang`
--

INSERT INTO `tb_barang` (`id`, `nama`, `harga`) VALUES
(1, 'Kipas Angin', 10000),
(2, 'Lemari', 15000),
(3, 'Meja', 5000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_bayar`
--

CREATE TABLE `tb_bayar` (
  `id` int(11) NOT NULL,
  `id_tagihan` int(11) DEFAULT NULL,
  `jml_bayar` int(11) DEFAULT NULL,
  `tgl_bayar` date DEFAULT NULL,
  `status` enum('lunas','cicil') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_bayar`
--

INSERT INTO `tb_bayar` (`id`, `id_tagihan`, `jml_bayar`, `tgl_bayar`, `status`) VALUES
(1, 1, 30000, '2025-07-02', 'cicil'),
(2, 2, 75000, '2025-07-07', 'lunas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_brng_bawaan`
--

CREATE TABLE `tb_brng_bawaan` (
  `id` int(11) NOT NULL,
  `id_penghuni` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_brng_bawaan`
--

INSERT INTO `tb_brng_bawaan` (`id`, `id_penghuni`, `id_barang`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 2, 2),
(4, 3, 3);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kamar`
--

CREATE TABLE `tb_kamar` (
  `id` int(11) NOT NULL,
  `nomor` varchar(10) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_kamar`
--

INSERT INTO `tb_kamar` (`id`, `nomor`, `harga`) VALUES
(1, '1', 50000),
(2, '2', 60000),
(3, '3', 70000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_kmr_penghuni`
--

CREATE TABLE `tb_kmr_penghuni` (
  `id` int(11) NOT NULL,
  `id_kamar` int(11) DEFAULT NULL,
  `id_penghuni` int(11) DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_kmr_penghuni`
--

INSERT INTO `tb_kmr_penghuni` (`id`, `id_kamar`, `id_penghuni`, `tgl_masuk`, `tgl_keluar`) VALUES
(1, 1, 1, '2025-06-14', NULL),
(2, 2, 2, '2025-06-02', NULL),
(3, 3, 3, '2025-07-02', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_penghuni`
--

CREATE TABLE `tb_penghuni` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `no_ktp` varchar(30) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `tgl_keluar` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_penghuni`
--

INSERT INTO `tb_penghuni` (`id`, `nama`, `no_ktp`, `no_hp`, `tgl_masuk`, `tgl_keluar`) VALUES
(1, 'Andi', '1234567890', '081234567890', '2025-06-14', NULL),
(2, 'Budi', '2345678901', '082345678901', '2025-06-02', NULL),
(3, 'Cici', '3456789012', '083456789012', '2025-07-02', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `tb_tagihan`
--

CREATE TABLE `tb_tagihan` (
  `id` int(11) NOT NULL,
  `bulan` varchar(7) DEFAULT NULL,
  `id_kmr_penghuni` int(11) DEFAULT NULL,
  `jml_tagihan` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tb_tagihan`
--

INSERT INTO `tb_tagihan` (`id`, `bulan`, `id_kmr_penghuni`, `jml_tagihan`) VALUES
(1, '2025-07', 1, 75000),
(2, '2025-07', 2, 75000),
(3, '2025-07', 3, 75000);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `tb_barang`
--
ALTER TABLE `tb_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_bayar`
--
ALTER TABLE `tb_bayar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_tagihan` (`id_tagihan`);

--
-- Indeks untuk tabel `tb_brng_bawaan`
--
ALTER TABLE `tb_brng_bawaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_penghuni` (`id_penghuni`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indeks untuk tabel `tb_kamar`
--
ALTER TABLE `tb_kamar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_kmr_penghuni`
--
ALTER TABLE `tb_kmr_penghuni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kamar` (`id_kamar`),
  ADD KEY `id_penghuni` (`id_penghuni`);

--
-- Indeks untuk tabel `tb_penghuni`
--
ALTER TABLE `tb_penghuni`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `tb_tagihan`
--
ALTER TABLE `tb_tagihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_kmr_penghuni` (`id_kmr_penghuni`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `tb_barang`
--
ALTER TABLE `tb_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tb_bayar`
--
ALTER TABLE `tb_bayar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `tb_brng_bawaan`
--
ALTER TABLE `tb_brng_bawaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `tb_kamar`
--
ALTER TABLE `tb_kamar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tb_kmr_penghuni`
--
ALTER TABLE `tb_kmr_penghuni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tb_penghuni`
--
ALTER TABLE `tb_penghuni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `tb_tagihan`
--
ALTER TABLE `tb_tagihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `tb_bayar`
--
ALTER TABLE `tb_bayar`
  ADD CONSTRAINT `tb_bayar_ibfk_1` FOREIGN KEY (`id_tagihan`) REFERENCES `tb_tagihan` (`id`);

--
-- Ketidakleluasaan untuk tabel `tb_brng_bawaan`
--
ALTER TABLE `tb_brng_bawaan`
  ADD CONSTRAINT `tb_brng_bawaan_ibfk_1` FOREIGN KEY (`id_penghuni`) REFERENCES `tb_penghuni` (`id`),
  ADD CONSTRAINT `tb_brng_bawaan_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `tb_barang` (`id`);

--
-- Ketidakleluasaan untuk tabel `tb_kmr_penghuni`
--
ALTER TABLE `tb_kmr_penghuni`
  ADD CONSTRAINT `tb_kmr_penghuni_ibfk_1` FOREIGN KEY (`id_kamar`) REFERENCES `tb_kamar` (`id`),
  ADD CONSTRAINT `tb_kmr_penghuni_ibfk_2` FOREIGN KEY (`id_penghuni`) REFERENCES `tb_penghuni` (`id`);

--
-- Ketidakleluasaan untuk tabel `tb_tagihan`
--
ALTER TABLE `tb_tagihan`
  ADD CONSTRAINT `tb_tagihan_ibfk_1` FOREIGN KEY (`id_kmr_penghuni`) REFERENCES `tb_kmr_penghuni` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
