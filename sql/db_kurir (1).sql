-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Jul 2020 pada 04.16
-- Versi server: 10.4.11-MariaDB
-- Versi PHP: 7.2.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kurir`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `jenis_patokan`
--

CREATE TABLE `jenis_patokan` (
  `id_jenis_patokan` int(11) NOT NULL,
  `jenis_patokan` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `jenis_patokan`
--

INSERT INTO `jenis_patokan` (`id_jenis_patokan`, `jenis_patokan`) VALUES
(1, 'Kantor'),
(2, 'Rumah'),
(3, 'Sekolah'),
(4, 'Kampus'),
(5, 'Restaurant'),
(6, 'Minimarket');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir`
--

CREATE TABLE `kurir` (
  `id_kurir` int(11) NOT NULL,
  `nama_kurir` varchar(100) NOT NULL,
  `alamat_kurir` varchar(100) NOT NULL,
  `no_hp_kurir` varchar(20) NOT NULL,
  `foto_kurir` varchar(100) NOT NULL,
  `plat_nomor` varchar(20) NOT NULL,
  `mode` enum('aktif','nonaktif') NOT NULL DEFAULT 'nonaktif',
  `nomor_ktp` varchar(100) NOT NULL,
  `id_user` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `nama_kurir`, `alamat_kurir`, `no_hp_kurir`, `foto_kurir`, `plat_nomor`, `mode`, `nomor_ktp`, `id_user`, `created_date`, `modified_date`) VALUES
(1, 'M.Hakim Amransyah', 'Jln. Sukabangun 2, Komp.SukbangunCindo, NO.A9,Palembang', '081271286874', '167809019291201290.jpg', 'BG 7373 XX', 'aktif', '167809019291201290', 1, '2020-06-14 09:41:03', '2020-07-05 07:40:24'),
(2, 'Bambang Harajuku S.P', 'Jln KentenPermai 2 Karya Sepakat, RT : 01, RW : 21', '0823456777', '167839393909093.jpg', 'BG 5971 RV', 'aktif', '167839393909093', 5, '2020-06-14 11:53:03', '2020-07-01 22:46:48');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir_geotracking`
--

CREATE TABLE `kurir_geotracking` (
  `id_kurir_geotracking` int(11) NOT NULL,
  `id_kurir` int(11) NOT NULL,
  `kordinat_terkini` varchar(200) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kurir_geotracking`
--

INSERT INTO `kurir_geotracking` (`id_kurir_geotracking`, `id_kurir`, `kordinat_terkini`, `created_date`, `modified_date`) VALUES
(1, 1, '-2.981864, 104.758334', '2020-07-04 20:20:04', '2020-07-04 14:01:38'),
(2, 2, '-2.975325, 104.760155', '2020-07-04 20:20:39', '2020-07-04 14:07:36');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir_kordinat`
--

CREATE TABLE `kurir_kordinat` (
  `id_kurir_kordinat` int(11) NOT NULL,
  `kordinat` varchar(100) NOT NULL,
  `alamat` varchar(1000) NOT NULL,
  `id_kurir` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kurir_kordinat`
--

INSERT INTO `kurir_kordinat` (`id_kurir_kordinat`, `kordinat`, `alamat`, `id_kurir`, `created_date`, `modified_date`) VALUES
(1, '-2.913480, 104.619738', 'Jl. Palembang - Jambi\r\nAir Batu\r\nKec. Talang Klp.\r\nKabupaten Banyu Asin', 2, '2020-06-14 15:55:09', '2020-06-28 08:29:27'),
(2, '-2.922898, 104.711045', 'Kebun Bunga\r\nKec. Sukarami\r\nKota Palembang', 2, '2020-06-14 15:55:56', '2020-06-28 08:34:05'),
(3, '-2.909248, 104.618191', 'Jl. Komunikasi\r\nAir Batu\r\nKec. Talang Klp.\r\nKabupaten Banyu Asin', 1, '2020-06-14 15:57:32', '2020-06-28 08:31:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order`
--

CREATE TABLE `order` (
  `id_order` int(11) NOT NULL,
  `kordinat_order` varchar(100) NOT NULL,
  `alamat_order` varchar(1000) NOT NULL,
  `status` enum('kurir_setuju','pelanggan_batal','pelanggan_menunggu_konfirmasi_charge','pelanggan_setuju','pelanggan_baru','kurir_tidak_tersedia','kurir_batal','selesai') DEFAULT NULL,
  `destination_failed` int(11) DEFAULT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_order_jenis` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order`
--

INSERT INTO `order` (`id_order`, `kordinat_order`, `alamat_order`, `status`, `destination_failed`, `id_pelanggan`, `id_order_jenis`, `rating`, `created_date`, `modified_date`) VALUES
(1, '-2.9360616,104.6817803', 'Jl. Perumnas Talang Klp., Talang Klp., Kec. Alang-Alang Lebar, Kota Palembang, Sumatera Selatan 3096Jl. Perumnas Talang Klp., Talang Klp., Kec. Alang-Alang Lebar, Kota Palembang, Sumatera Selatan 30961', 'kurir_setuju', NULL, 2, 1, NULL, '2020-06-14 11:25:27', '2020-06-14 12:07:09'),
(2, '-2.9324443,104.7349938', 'Jl. Sukabangun 2, Ruko Sukabangun Cindo No 2 Rt 002 Rw 001 kel, Sukajaya, Kec. Sukarami, Kota Palembang, Sumatera Selatan 30151', 'kurir_setuju', 0, 1, 2, NULL, '2020-06-14 12:06:48', '2020-07-05 11:41:07'),
(3, '-2.9360616,104.6817803', 'Jl. Perumnas Talang Klp., Talang Klp., Kec. Alang-Alang Lebar, Kota Palembang, Sumatera Selatan 30961', 'pelanggan_setuju', NULL, 1, 2, NULL, '2020-06-14 12:30:46', '2020-06-14 12:46:32'),
(4, '-2.9360616,104.6817803', 'Jln di rumah wahid', 'pelanggan_batal', NULL, 2, 1, NULL, '2020-06-14 12:44:07', '2020-06-14 12:46:42'),
(9, '-2.938236, 104.768176', 'Jl. Karya Sepakat, Kenten Permai 2', 'pelanggan_baru', 0, 12, 1, NULL, '2020-06-29 14:14:34', '2020-06-30 21:46:47'),
(10, '-2.938236, 104.768176', 'Jl. Karya Sepakat, Kenten Permai 2', 'selesai', 0, 2, 1, 4, '2020-07-01 15:03:04', '2020-07-05 07:38:57'),
(12, '-2.938236, 104.768176', 'Jl. Karya Sepakat, Kenten Permai 2', 'pelanggan_menunggu_konfirmasi_charge', 1, 2, 1, NULL, '2020-07-01 15:52:46', '2020-07-03 04:40:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_barang`
--

CREATE TABLE `order_barang` (
  `id_order_barang` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah_paket` varchar(100) NOT NULL,
  `foto_barang` varchar(100) NOT NULL,
  `catatan_kurir` varchar(200) DEFAULT NULL,
  `estimasi_berat` varchar(20) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order_barang`
--

INSERT INTO `order_barang` (`id_order_barang`, `nama_barang`, `jumlah_paket`, `foto_barang`, `catatan_kurir`, `estimasi_berat`, `created_date`, `modified_date`) VALUES
(1, 'Pempek', '2 bungkus', 'M._Wahid_Alqorni-order-barang11:06:07.jpg', 'pempek nya ada bungkus cuka dan gunting. tolong jangan ada bantingan keras keras takutnya kantong cuka-nya bocor.', '100 gram', '2020-06-14 11:06:07', '2020-06-14 14:06:42'),
(2, 'Pakaian', '3 bungkus', 'Wendy_Saputra-order-barang11:54:13.jpg', 'tolong cepat yah ditunggu jam 2', '1 kg', '2020-06-14 11:55:23', '2020-06-14 14:06:15'),
(3, 'Minuman', '2 kardus, dan 1 set chese fanta botol', 'Wendy Saputra-order-barang12:23:15.jpg', 'tolong express', '3 kg', '2020-06-14 12:24:29', '2020-06-14 14:07:21'),
(4, 'Ujiacoba', '3 paket', 'M._Wahid_Alqorni-order-barang12:40:07.jpg', 'uji coba order kurir', '0', '2020-06-14 12:41:13', '2020-06-14 14:06:51'),
(5, 'Keripik Singkong', '2 bungkus', 'M._Agus_Kuncoro_Susilo_Hastono-order-barang14:07:03.jpg', 'Keripik mudah hancur', '50 gram', '2020-06-14 14:08:10', NULL),
(6, 'Keripik Ubi', '1 bungkus', 'M._Agus_Kuncoro_Susilo_Hastono-order-barang14:23:03.jpg', 'Keripik mudah hancur', '10 gram', '2020-06-14 14:23:37', NULL),
(27, 'pisang goreng', '1 kotak', '9-5EWN9yIwG8FJne5Z7KMH.jpg', 'hati hati nanti toping buyar', '5 gram', '2020-06-30 14:32:26', '2020-07-01 02:03:42'),
(28, 'pisang goreng', '1 kotak', '9-QyNq5R0eAwCNjbqYwMjI.jpg', 'hati hati nanti toping buyar', '5 gram', '2020-06-30 14:33:23', '2020-07-01 01:53:10'),
(30, 'pisang goreng', '1 kotak', '9-p8ujAJbIZV3kfbMAW5yH.jpg', 'hati hati nanti toping buyar', '5 gram', '2020-06-30 14:35:12', '2020-07-01 02:14:54'),
(33, 'Karpet', '1 gulung', '10-JOPsPqE1b1fifQlziPUG.jpg', 'kapetnya agak tebal', '3 kg', '2020-07-01 15:10:37', '2020-07-01 15:10:37'),
(34, 'Karpet', '1 gulung', '10-SGtJNIKJcCoZwhMoyLyA.jpg', 'kapetnya agak tebal', '3 kg', '2020-07-01 15:12:59', '2020-07-01 15:12:59'),
(35, 'Karpet', '1 gulung', '10-5tfioAzwIBYMxFGwvAyA.jpg', 'kapetnya agak tebal', '3 kg', '2020-07-01 15:15:06', '2020-07-01 15:15:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_destinasi`
--

CREATE TABLE `order_destinasi` (
  `id_order_destinasi` int(11) NOT NULL,
  `detail_destinasi` varchar(100) DEFAULT NULL,
  `kordinat_destinasi` varchar(100) DEFAULT NULL,
  `alamat_destinasi` varchar(100) DEFAULT NULL,
  `nama_penerima` varchar(30) DEFAULT NULL,
  `no_hp_penerima` varchar(20) DEFAULT NULL,
  `kode_patokan` varchar(6) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order_destinasi`
--

INSERT INTO `order_destinasi` (`id_order_destinasi`, `detail_destinasi`, `kordinat_destinasi`, `alamat_destinasi`, `nama_penerima`, `no_hp_penerima`, `kode_patokan`, `created_date`, `modified_date`) VALUES
(1, 'di belakang ruko rumah semangka', '-2.9324443,104.7349938', 'Jln. Sukabangun 2, Komplek SukabangunCindo, NO.A9, Palembang', 'Hakim', '081271286874', NULL, '2020-06-14 11:11:50', NULL),
(2, NULL, NULL, NULL, NULL, NULL, '28KO17', '2020-06-14 12:02:26', NULL),
(3, NULL, NULL, NULL, NULL, NULL, 'Z1KOXQ', '2020-06-14 12:28:11', NULL),
(4, NULL, NULL, NULL, NULL, NULL, 'QWEE7U', '2020-06-14 12:42:04', NULL),
(5, NULL, NULL, NULL, NULL, NULL, '28KO17', '2020-06-14 14:09:32', NULL),
(6, NULL, NULL, NULL, NULL, NULL, 'QWEE7U', '2020-06-14 14:24:19', NULL),
(27, 'Di samping indomaret', '-2.928097, 104.787574', 'Jl. Siaran, Sako, Kec. Sako, Kota Palembang, Sumatera Selatan 30961', 'Ujang Spektra Uhuy', '0878120102020', NULL, '2020-06-30 14:32:26', '2020-07-01 02:03:42'),
(28, 'Pagar warna kuning dan hijau', '-3.197565, 104.821349\n', 'Padang Bulan, Jejawi, Kabupaten Ogan Komering Ilir, Sumatera Selatan 30652', 'Ayman Laporte Zon', '0878120102020', NULL, '2020-06-30 14:33:23', '2020-07-01 01:53:10'),
(30, 'Di seberang taman lawang', '-3.200677, 104.826882', 'Padang Bulan Jejawi Kabupaten Ogan Komering Ilir\nSumatera Selatan 30652', 'Santoso Saphire', '0812726882829', NULL, '2020-06-30 14:35:12', '2020-07-01 02:14:54'),
(33, NULL, NULL, NULL, NULL, NULL, '28KO17', '2020-07-01 15:10:37', '2020-07-05 10:42:18'),
(34, 'Di seberang taman hiburan OVI', '-3.052504, 104.790290', 'Jln. OVI mall, Jakabaring', 'Asep Sunandar Karisma', '089812912812', NULL, '2020-07-01 15:12:59', '2020-07-01 15:12:59'),
(35, 'Di seberang rumah alquran dekta dinas perikanan', '-3.011239, 104.704111', 'Jln. gandus jaya', 'Ujang Kaliho Hiya Hiya', '089812912812', NULL, '2020-07-01 15:15:06', '2020-07-01 15:15:06');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_detail`
--

CREATE TABLE `order_detail` (
  `id_order_detail` int(11) NOT NULL,
  `jarak` int(11) NOT NULL,
  `tarif_charge_jarak` int(11) DEFAULT NULL,
  `tarif_charge_beban` int(11) DEFAULT NULL,
  `id_order_destinasi` int(11) NOT NULL,
  `id_order_barang` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `foto_selesai` varchar(100) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order_detail`
--

INSERT INTO `order_detail` (`id_order_detail`, `jarak`, `tarif_charge_jarak`, `tarif_charge_beban`, `id_order_destinasi`, `id_order_barang`, `id_order`, `foto_selesai`, `created_date`, `modified_date`) VALUES
(1, 13, NULL, NULL, 1, 1, 1, 'hakim-selesai11:50:11.jpg', '2020-06-14 11:30:59', '2020-06-14 15:11:30'),
(2, 10, NULL, NULL, 2, 2, 2, NULL, '2020-06-14 12:09:31', NULL),
(3, 19, NULL, 5000, 3, 3, 3, 'hakim-selesai12:37:11.jpg', '2020-06-14 12:33:41', '2020-06-14 15:12:24'),
(4, 23, 5000, NULL, 4, 4, 4, NULL, '2020-06-14 12:44:49', '2020-06-14 12:45:39'),
(22, 2, 0, NULL, 27, 27, 9, NULL, '2020-06-30 14:32:26', '2020-07-01 02:03:42'),
(23, 30, 15000, NULL, 28, 28, 9, NULL, '2020-06-30 14:33:24', '2020-07-01 02:03:42'),
(24, 1, 0, NULL, 30, 30, 9, NULL, '2020-06-30 14:35:12', '2020-07-01 02:14:54'),
(27, 4, 0, NULL, 33, 33, 10, '10-lmaC2AFPEgLhImGfn5O5.jpg', '2020-07-01 15:10:37', '2020-07-05 07:17:27'),
(28, 12, 0, NULL, 34, 34, 10, '10-6Q9CRLTDOxnKTKgG79zo.jpg', '2020-07-01 15:12:59', '2020-07-05 07:13:31'),
(29, 11, 0, NULL, 35, 35, 10, '10-k2DV20gWprposoBgwsx8.JPG', '2020-07-01 15:15:06', '2020-07-05 07:18:14');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_jenis`
--

CREATE TABLE `order_jenis` (
  `id_order_jenis` int(11) NOT NULL,
  `jenis` varchar(100) NOT NULL,
  `tarif` varchar(100) NOT NULL,
  `deskripsi_jenis_order` text NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order_jenis`
--

INSERT INTO `order_jenis` (`id_order_jenis`, `jenis`, `tarif`, `deskripsi_jenis_order`, `created_date`, `modified_date`) VALUES
(1, 'Reguler', '10000', 'Kurir memilik waktu tunggu maksimal 2 jam setelah kesepakatan terjadi', '2020-06-14 10:53:28', NULL),
(2, 'Express', '13000', 'Kurir langsung menuju lokasi tanpa ada waktu tunggu', '2020-06-14 10:53:57', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_kurir`
--

CREATE TABLE `order_kurir` (
  `id_order_kurir` int(11) NOT NULL,
  `aksi` enum('Setuju','Tolak','Charge','Baru') NOT NULL,
  `id_kurir` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order_kurir`
--

INSERT INTO `order_kurir` (`id_order_kurir`, `aksi`, `id_kurir`, `id_order`, `created_date`, `modified_date`) VALUES
(2, 'Setuju', 1, 1, '2020-06-14 11:45:26', '2020-06-28 07:48:01'),
(3, 'Tolak', 1, 2, '2020-06-14 12:11:50', '2020-06-28 08:23:13'),
(4, 'Setuju', 2, 2, '2020-06-14 12:12:22', NULL),
(5, 'Charge', 1, 3, '2020-06-14 12:32:07', '2020-06-14 12:32:25'),
(10, 'Baru', 2, 9, '2020-06-30 13:14:08', '2020-07-03 14:00:10'),
(11, 'Setuju', 1, 10, '2020-07-01 15:03:04', '2020-07-04 14:01:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_selesai`
--

CREATE TABLE `order_selesai` (
  `id_order_selesai` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `id_kurir` int(11) NOT NULL,
  `id_order` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `order_selesai`
--

INSERT INTO `order_selesai` (`id_order_selesai`, `rating`, `id_kurir`, `id_order`, `created_date`, `modified_date`) VALUES
(1, 4, 1, 1, '2020-06-14 11:50:21', NULL),
(2, 5, 1, 3, '2020-06-14 12:37:13', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(100) NOT NULL,
  `nomor_hp_pelanggan` varchar(20) DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama_pelanggan`, `nomor_hp_pelanggan`, `id_user`, `created_date`, `modified_date`) VALUES
(1, 'Wendy Saputra', NULL, 2, '2020-06-14 09:46:14', NULL),
(2, 'M. Wahid Alqorni', NULL, 4, '2020-06-14 09:46:39', NULL),
(12, 'Cheppy Alejandro', '08127128685', 45, '2020-06-22 15:09:38', '2020-06-24 13:35:43'),
(22, 'Jefferson Moreno Supratno', '08134566579', 47, '2020-06-24 13:24:17', '2020-06-24 13:24:17'),
(23, 'M.Hakim Amransyah', '082377025932', 58, '2020-07-11 13:24:37', '2020-07-11 13:24:37'),
(24, 'M.Hakim Amransyah', '082377025932', 59, '2020-07-11 13:37:00', '2020-07-11 13:39:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan_patokkan`
--

CREATE TABLE `pelanggan_patokkan` (
  `id_pelanggan_patokkan` int(11) NOT NULL,
  `kode_patokan` varchar(6) DEFAULT NULL,
  `nama_penerima_patokan` varchar(100) NOT NULL,
  `no_hp_penerima_patokan` varchar(100) NOT NULL,
  `alamat_patokan` varchar(1000) NOT NULL,
  `kordinat_patokan` varchar(100) NOT NULL,
  `foto_patokan` varchar(100) NOT NULL,
  `detail_patokan` varchar(100) NOT NULL,
  `id_jenis_patokan` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pelanggan_patokkan`
--

INSERT INTO `pelanggan_patokkan` (`id_pelanggan_patokkan`, `kode_patokan`, `nama_penerima_patokan`, `no_hp_penerima_patokan`, `alamat_patokan`, `kordinat_patokan`, `foto_patokan`, `detail_patokan`, `id_jenis_patokan`, `id_pelanggan`, `created_date`, `modified_date`) VALUES
(1, 'QWEE7U', '', '', 'Jl. Perumnas Talang Klp., Talang Klp., Kec. Alang-Alang Lebar, Kota Palembang, Sumatera Selatan 3096', '-2.9360616,104.6817803', 'Wendy_Saputra-foto-patokan09:54:54.jpg', 'Pagar warna hitam , di depan lapangan, ada spanduk.', 2, 1, '2020-06-14 09:55:47', '2020-06-14 14:11:14'),
(2, '28KO17', 'Wahid', '088919912012', 'Jln. Jalan Ke Tanah Abang, RT : 02, RW : 03, Palembang, NO.8', '-2.9898149,104.7443325', 'M._Wahid_Alqorni-foto-patokan09:57:45.jpg', 'di belakang ruko indomaret, pagar warna kuning, ada mobil warna putih', 2, 2, '2020-06-14 09:58:30', '2020-07-05 12:14:29'),
(3, 'YE9OLU', '', '', 'Ilir, Bukit Kecil, Jl. Nyoman Ratu No.1271, Sungai Pangeran, Palembang, Kota Palembang, Sumatera Selatan', '-2.9780596,104.7461233', 'M._Wahid_Alqorni-foto-patokan10:02:11.jpg', 'kantor diskominfo, dibelakang gedung telekom, didepan kantor disnaker palembang', 1, 2, '2020-06-14 10:03:26', '2020-06-14 14:10:38'),
(4, 'Z1KOXQ', '', '', 'Pahlawan, Kec. Kemuning, Kota Palembang, Sumatera Selatan', '-2.96221,104.7498608', 'M._Wahid_Alqorni-foto-patokan10:06:30.jpg', 'Univ UIIN Raden Fatah, Fakultas dakwah dan komunikasi', 4, 2, '2020-06-14 10:06:58', '2020-06-14 14:10:47'),
(6, 'KYX01K', '', '', 'Jl. Simpang 4 No. 1 - 3, Hook, Jl. Soekarno Hatta, Karya Baru, Kec. Alang-Alang Lebar, Kota Palembang, Sumatera Selatan 30121', '-2.9233129,104.7085701', 'Wendy_Saputra-foto-patokan10:40:12.jpg', 'KFC Simpang Bandara', 5, 1, '2020-06-14 10:40:32', '2020-06-14 14:11:22'),
(7, 'ZG97MO', '', '', 'Bukit Lama, Kec. Ilir Bar. I, Kota Palembang, Sumatera Selatan 30128', '-2.9842555,104.7306114', 'Wendy_Saputra-foto-patokan10:50:15.jpg', 'Kampus unsri bukit, fakultas ilmu komputer', 4, 1, '2020-06-14 10:50:35', '2020-06-14 14:11:31'),
(13, '9WW34I', '', '', 'Jalan Yos Sudarsono, No 5A', '-2920.203902,23892379.90', '12-W8Oo3brnlfQiibxl6whC.jpg', 'Pagar warna emas dan ada pohon jambu ', 2, 12, '2020-06-25 07:32:34', '2020-06-25 07:33:14');

--
-- Trigger `pelanggan_patokkan`
--
DELIMITER $$
CREATE TRIGGER `KODE_PATOKAN` BEFORE INSERT ON `pelanggan_patokkan` FOR EACH ROW BEGIN
    declare ready int default 0;
    declare rnd_str text;
    if new.kode_patokan is null then
        while not ready do
            set rnd_str := lpad(conv(floor(rand()*pow(36,6)), 10, 36), 6, 0);
            if not exists (select * from pelanggan_patokkan where kode_patokan = rnd_str) then
                set new.kode_patokan = rnd_str;
                set ready := 1;
            end if;
        end while;
    end if;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `id_role` int(11) NOT NULL,
  `jenis` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `role`
--

INSERT INTO `role` (`id_role`, `jenis`) VALUES
(1, 'admin'),
(2, 'pelanggan'),
(3, 'kurir');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sistem_destinasi`
--

CREATE TABLE `sistem_destinasi` (
  `id_sistem_destinasi` int(11) NOT NULL,
  `alamat` varchar(500) NOT NULL,
  `kordinat` varchar(500) NOT NULL,
  `id_user` int(11) NOT NULL,
  `verified` enum('ya','tidak') NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `sistem_destinasi`
--

INSERT INTO `sistem_destinasi` (`id_sistem_destinasi`, `alamat`, `kordinat`, `id_user`, `verified`, `created_date`, `modified_date`) VALUES
(1, 'Gedung Magister Manajemen Universitas Sriwijaya, Bukit Lama, Kec. Ilir Bar. I, Kota Palembang, Sumatera Selatan 30128', '-2.984584, 104.732575', 3, 'ya', '2020-07-10 06:56:29', '2020-07-10 07:18:29'),
(2, 'Yayasan IBA, 9 Ilir, Kec. Ilir Tim. II, Kota Palembang, Sumatera Selatan 30114', '-2.965078, 104.763572', 3, 'ya', '2020-07-10 07:21:31', NULL),
(3, 'Bandara Internasional Sultan Mahmud Badaruddin II, Jl. Bandara Sultan Mahmud Badaruddin II, Talang Betutu, Kec. Sukarami, Kota Palembang, Sumatera Selatan 30761', '-2.896300, 104.699110', 1, 'tidak', '2020-07-10 07:23:43', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `sistem_image_information`
--

CREATE TABLE `sistem_image_information` (
  `id_sistem_image_information` int(11) NOT NULL,
  `foto_banner` varchar(100) NOT NULL,
  `label` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `sistem_image_information`
--

INSERT INTO `sistem_image_information` (`id_sistem_image_information`, `foto_banner`, `label`, `deskripsi`) VALUES
(1, 'banner10:54:13.jpg', 'selamat datang', '<p>Selamat datang di aplikasi kurir</p>'),
(2, 'banner10:56:33.jpg', 'promo hari jumat', '<h1> Yuk Kenalan untuk promo hari jumat</h1>\r\n<p> Hari jumat seluruh jenis order menurun dan tidak ada charge beban</p>'),
(3, 'banner10:58:15.jpg', 'ketentuan tarif', '<h1>Yuk kenalan dengan ketentuan tarif</h1>');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sistem_text_information`
--

CREATE TABLE `sistem_text_information` (
  `id_sistem_text_information` int(11) NOT NULL,
  `deskripsi_text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `sistem_text_information`
--

INSERT INTO `sistem_text_information` (`id_sistem_text_information`, `deskripsi_text`) VALUES
(1, '<h1>FAQ</h1>'),
(2, '<h1>Ketentuan Tarif</h1>');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token_fcm` varchar(1000) DEFAULT NULL,
  `jenis_registrasi` enum('Manual','Gmail') NOT NULL,
  `password` varchar(200) DEFAULT NULL,
  `api_token` varchar(100) DEFAULT NULL,
  `is_activate` enum('yes','no') NOT NULL,
  `token_aktivasi` varchar(100) DEFAULT NULL,
  `id_role` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_date` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `email`, `token_fcm`, `jenis_registrasi`, `password`, `api_token`, `is_activate`, `token_aktivasi`, `id_role`, `created_date`, `modified_date`) VALUES
(1, 'm.hakim@gmail.com', '0980d99s8jds89d8d9s89sjs0jd0s9d0jnds0d0jdsijdiosjdloaiipw', 'Manual', '$2y$10$l22MymMqvtz4kMvgDEYXDeJBJd0XnYFNAyq3ums1psr62YsctA.xS', 'Kv8GKE6r9umeilVKuFMRgOcuI1NNxyuTg2AQMXcv7MvtSZWA2k', 'yes', NULL, 3, '2020-06-13 20:41:04', '2020-06-24 11:10:38'),
(2, 'wendy_s@gmail.com', '1iuidu027302j2jp92uqpwoqwl1282039023jjldssldk090s8d02032k2', 'Gmail', NULL, NULL, 'yes', NULL, 2, '2020-06-14 09:35:15', NULL),
(3, 'admin@gmail.com', '1iuidu027302j2jp92uqpwoqwl1282039023jjldssldk090s8d02032k2', 'Manual', '8cb2237d0679ca88db6464eac60da96345513964', NULL, 'yes', NULL, 1, '2020-06-14 09:36:45', NULL),
(4, 'wahid@gmail.com', '1iuidu027302j2jp92uqpwoqwl1282039023jjldssldk090s8d02032k2', 'Manual', '8cb2237d0679ca88db6464eac60da96345513964', NULL, 'yes', NULL, 2, '2020-06-14 09:37:56', NULL),
(5, 'bambangharajuku@gmail.com', 'djhsod98d0s9d9sd9dsldjls9sd8s0djsld9', 'Manual', 'shsidosdosiud0s9d80sdsojdlsjds9d8', NULL, 'yes', NULL, 3, '2020-06-14 11:51:30', NULL),
(32, 'agusmistiawan@yahoo.co.id', NULL, 'Manual', '2y10PUMHwppMUsWg7tqZbMsmVOZunnpmv93yzhEq0JbfQkoX8F49oTgBW', NULL, 'yes', '', 2, '2020-06-21 13:39:54', '2020-06-21 13:47:31'),
(34, 'sintongpanjaitan@outlook.co.id', NULL, 'Manual', '2y10ABeBcHPI1FtseUKXdeRQeui3UZ0bvqm4X4kemrHH9tXjVtwgwlLYe', NULL, 'yes', '', 2, '2020-06-21 14:01:01', '2020-06-21 14:01:19'),
(45, 'cheppy@gmail.com', 'Fgcush8sd9sd9sdnsiu87980xmsiUnlkl', 'Gmail', NULL, '9vTWljudmmQ8pFJej4sgxLb93J2qpA2GFk5zEejX9NQF8bx3de', 'yes', NULL, 2, '2020-06-22 15:09:38', '2020-06-22 15:25:10'),
(47, 'jefferson@gmail.com', '0980d99s8jds89d8d9s89sjs0jd0s9d0jnds0d0jdsijdiosjdloaiipw', 'Manual', '$2y$10$DduQkgrdy8KaeI4AqbbLA.6FOBsNEYxj3.3dSsovL65thbma1JOSa', 'eB4aWJd1oGeRIzRy4U1PvQQCX9vBUeFcjCu4apRq5Vj5oIaIzm', 'yes', '', 2, '2020-06-22 16:02:23', '2020-06-24 11:11:49'),
(48, 'ryan.yan@gmail.com', NULL, 'Manual', '$2y$10$l22MymMqvtz4kMvgDEYXDeJBJd0XnYFNAyq3ums1psr62YsctA.xS', NULL, 'yes', '', 3, '2020-06-24 10:32:43', '2020-06-24 10:37:25'),
(49, 'AgusCek@yahoo.com', '0980d99s8jds89d8d9s89sjs0jd0s9d0jnds0d0jdsijdiosjdloaiipw', 'Manual', '$2y$10$FmYCwICpilv1y74hIroboO4JRqZvjKRfEnSPF/4J64OwGW1EH3hda', 'HxAW6TD0uE3Uck3Kmk5t8cbFrJy3H4hb2toH9mXwSMTQsuUZcI', 'yes', '', 2, '2020-07-11 04:24:14', '2020-07-11 04:27:31'),
(58, 'm.hakim.amransyah.hakim@gmail.com', '0980d99s8jds89d8d9s89sjs0jd0s9d0jnds0d0jdsijdiosjdloaiipw', 'Manual', '$2y$10$r6xXtLAfcC4yDG8.NqUByuU6ZHBCHjDMRdR4WSWGWNxjglZI8BWD.', 'DGtJzmIUE4S5QiIP0oTrHOtM5HHqtrfJxqRmlCKnkU5dvKaO9p', 'yes', '', 2, '2020-07-11 13:09:02', '2020-07-11 15:17:23'),
(59, '96mhakim@gmail.com', 'Fgcush8sd9sd9sdnsiu87980xmsiUnlkl', 'Gmail', NULL, 'PyPmIL7erTLOp4Eil73x3s4rQfX7ypNPOYTvDf9uEnddNFyAMT', 'yes', NULL, 2, '2020-07-11 13:37:00', '2020-07-11 15:15:11');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `jenis_patokan`
--
ALTER TABLE `jenis_patokan`
  ADD PRIMARY KEY (`id_jenis_patokan`);

--
-- Indeks untuk tabel `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`),
  ADD KEY `RELASI_USER_KURIR` (`id_user`);

--
-- Indeks untuk tabel `kurir_geotracking`
--
ALTER TABLE `kurir_geotracking`
  ADD PRIMARY KEY (`id_kurir_geotracking`),
  ADD KEY `RELASI_KURIRGEOTRACKING_KURIR` (`id_kurir`);

--
-- Indeks untuk tabel `kurir_kordinat`
--
ALTER TABLE `kurir_kordinat`
  ADD PRIMARY KEY (`id_kurir_kordinat`),
  ADD KEY `RELASI_KURIR_KURIRKORDINAT` (`id_kurir`);

--
-- Indeks untuk tabel `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id_order`),
  ADD KEY `RELASI_ORDER_PELANGGAN` (`id_pelanggan`),
  ADD KEY `RELASI_ORDER_ORDERJENIS` (`id_order_jenis`);

--
-- Indeks untuk tabel `order_barang`
--
ALTER TABLE `order_barang`
  ADD PRIMARY KEY (`id_order_barang`);

--
-- Indeks untuk tabel `order_destinasi`
--
ALTER TABLE `order_destinasi`
  ADD PRIMARY KEY (`id_order_destinasi`),
  ADD KEY `RELASI_DESTINASI_KODEPATOKAN` (`kode_patokan`);

--
-- Indeks untuk tabel `order_detail`
--
ALTER TABLE `order_detail`
  ADD PRIMARY KEY (`id_order_detail`),
  ADD KEY `RELASI_DESTINASI_DETAIL` (`id_order_destinasi`),
  ADD KEY `RELASI_BARANG_DETAIL` (`id_order_barang`),
  ADD KEY `RELASI_DESTINASI_ORDER` (`id_order`);

--
-- Indeks untuk tabel `order_jenis`
--
ALTER TABLE `order_jenis`
  ADD PRIMARY KEY (`id_order_jenis`);

--
-- Indeks untuk tabel `order_kurir`
--
ALTER TABLE `order_kurir`
  ADD PRIMARY KEY (`id_order_kurir`),
  ADD KEY `RELASI_ORDERKURIR_KURIR` (`id_kurir`),
  ADD KEY `RELASI_ORDERKURIR_ORDER` (`id_order`);

--
-- Indeks untuk tabel `order_selesai`
--
ALTER TABLE `order_selesai`
  ADD PRIMARY KEY (`id_order_selesai`),
  ADD KEY `RELASI_ORDER_SELESAI` (`id_order`),
  ADD KEY `RELASI_ORDER_KURIR` (`id_kurir`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `pelanggan_patokkan`
--
ALTER TABLE `pelanggan_patokkan`
  ADD PRIMARY KEY (`id_pelanggan_patokkan`),
  ADD UNIQUE KEY `kode_patokan` (`kode_patokan`),
  ADD KEY `RELASI_PELANGGAN_PATOKAN` (`id_pelanggan`),
  ADD KEY `RELASI_PELANGGANPATOKAN_JENISPATOKAN` (`id_jenis_patokan`);

--
-- Indeks untuk tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id_role`);

--
-- Indeks untuk tabel `sistem_destinasi`
--
ALTER TABLE `sistem_destinasi`
  ADD PRIMARY KEY (`id_sistem_destinasi`),
  ADD KEY `RELASI_USER_SISTEMDESTINASI` (`id_user`);

--
-- Indeks untuk tabel `sistem_image_information`
--
ALTER TABLE `sistem_image_information`
  ADD PRIMARY KEY (`id_sistem_image_information`);

--
-- Indeks untuk tabel `sistem_text_information`
--
ALTER TABLE `sistem_text_information`
  ADD PRIMARY KEY (`id_sistem_text_information`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD KEY `RELASI_USER_ROLE` (`id_role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `jenis_patokan`
--
ALTER TABLE `jenis_patokan`
  MODIFY `id_jenis_patokan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kurir`
--
ALTER TABLE `kurir`
  MODIFY `id_kurir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kurir_geotracking`
--
ALTER TABLE `kurir_geotracking`
  MODIFY `id_kurir_geotracking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `kurir_kordinat`
--
ALTER TABLE `kurir_kordinat`
  MODIFY `id_kurir_kordinat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `order`
--
ALTER TABLE `order`
  MODIFY `id_order` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `order_barang`
--
ALTER TABLE `order_barang`
  MODIFY `id_order_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `order_destinasi`
--
ALTER TABLE `order_destinasi`
  MODIFY `id_order_destinasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `order_detail`
--
ALTER TABLE `order_detail`
  MODIFY `id_order_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `order_jenis`
--
ALTER TABLE `order_jenis`
  MODIFY `id_order_jenis` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `order_kurir`
--
ALTER TABLE `order_kurir`
  MODIFY `id_order_kurir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `order_selesai`
--
ALTER TABLE `order_selesai`
  MODIFY `id_order_selesai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `pelanggan_patokkan`
--
ALTER TABLE `pelanggan_patokkan`
  MODIFY `id_pelanggan_patokkan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `role`
--
ALTER TABLE `role`
  MODIFY `id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sistem_destinasi`
--
ALTER TABLE `sistem_destinasi`
  MODIFY `id_sistem_destinasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sistem_image_information`
--
ALTER TABLE `sistem_image_information`
  MODIFY `id_sistem_image_information` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `sistem_text_information`
--
ALTER TABLE `sistem_text_information`
  MODIFY `id_sistem_text_information` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `kurir`
--
ALTER TABLE `kurir`
  ADD CONSTRAINT `RELASI_USER_KURIR` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kurir_geotracking`
--
ALTER TABLE `kurir_geotracking`
  ADD CONSTRAINT `RELASI_KURIRGEOTRACKING_KURIR` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kurir_kordinat`
--
ALTER TABLE `kurir_kordinat`
  ADD CONSTRAINT `RELASI_KURIR_KURIRKORDINAT` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `RELASI_ORDER_ORDERJENIS` FOREIGN KEY (`id_order_jenis`) REFERENCES `order_jenis` (`id_order_jenis`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RELASI_ORDER_PELANGGAN` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_destinasi`
--
ALTER TABLE `order_destinasi`
  ADD CONSTRAINT `RELASI_DESTINASI_KODEPATOKAN` FOREIGN KEY (`kode_patokan`) REFERENCES `pelanggan_patokkan` (`kode_patokan`);

--
-- Ketidakleluasaan untuk tabel `order_detail`
--
ALTER TABLE `order_detail`
  ADD CONSTRAINT `RELASI_BARANG_DETAIL` FOREIGN KEY (`id_order_barang`) REFERENCES `order_barang` (`id_order_barang`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RELASI_DESTINASI_DETAIL` FOREIGN KEY (`id_order_destinasi`) REFERENCES `order_destinasi` (`id_order_destinasi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RELASI_DESTINASI_ORDER` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_kurir`
--
ALTER TABLE `order_kurir`
  ADD CONSTRAINT `RELASI_ORDERKURIR_KURIR` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RELASI_ORDERKURIR_ORDER` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_selesai`
--
ALTER TABLE `order_selesai`
  ADD CONSTRAINT `RELASI_ORDER_KURIR` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `RELASI_ORDER_SELESAI` FOREIGN KEY (`id_order`) REFERENCES `order` (`id_order`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `RELASI_PELANGGAN_USER` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pelanggan_patokkan`
--
ALTER TABLE `pelanggan_patokkan`
  ADD CONSTRAINT `RELASI_PELANGGANPATOKAN_JENISPATOKAN` FOREIGN KEY (`id_jenis_patokan`) REFERENCES `jenis_patokan` (`id_jenis_patokan`) ON UPDATE CASCADE,
  ADD CONSTRAINT `RELASI_PELANGGAN_PATOKAN` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sistem_destinasi`
--
ALTER TABLE `sistem_destinasi`
  ADD CONSTRAINT `RELASI_USER_SISTEMDESTINASI` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `RELASI_USER_ROLE` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
