-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2025 at 02:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skygoal_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 1,
  `title` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `ad_mode` enum('image','script') DEFAULT 'image',
  `image_url` varchar(255) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `script_code` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `clicks` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`id`, `user_id`, `title`, `type`, `ad_mode`, `image_url`, `target_url`, `script_code`, `is_active`, `start_date`, `end_date`, `clicks`, `created_at`) VALUES
(11, 3, 'Promo Jersey Original', 'banner_top', 'image', 'https://images.unsplash.com/photo-1521412644187-c49fa049e84d?q=80&w=800&auto=format&fit=crop', 'https://shopee.co.id', NULL, 1, NULL, NULL, 0, '2025-11-26 12:52:34'),
(12, 3, 'Diskon Sepatu Futsal', 'sidebar_square', 'image', 'https://images.unsplash.com/photo-1511886929837-354d827aae26?q=80&w=600&auto=format&fit=crop', 'https://tokopedia.com', NULL, 1, NULL, NULL, 0, '2025-11-26 12:52:34'),
(13, 3, 'Iklan Sidebar Google', 'sidebar_square', 'script', '', '', '<div style=\"width:100%; height:250px; background:#f8f9fa; border:2px dashed #ccc; display:flex; align-items:center; justify-content:center; color:#666; font-weight:bold; font-size:12px;\">(Simulasi)<br>Iklan Google 300x250</div>', 1, NULL, NULL, 0, '2025-11-26 12:52:34'),
(14, 3, 'Iklan Horizontal Tengah', 'horizontal_banner', 'script', '', '', '<div style=\"width:100%; height:90px; background:#e2e8f0; border:1px solid #94a3b8; display:flex; align-items:center; justify-content:center; color:#475569; font-weight:bold;\">:: Google Adsense Leaderboard (728x90) ::</div>', 1, NULL, NULL, 0, '2025-11-26 12:52:34'),
(15, 3, 'Join Grup WhatsApp Bola', 'popup', 'image', 'https://images.unsplash.com/photo-1505250469679-203ad9ced0cb?q=80&w=800&auto=format&fit=crop', 'https://whatsapp.com', NULL, 1, NULL, NULL, 0, '2025-11-26 12:52:34'),
(16, 3, 'Iklan Native Text', 'native_feed', 'script', '', '', '<div style=\"background:#fff7ed; border-left:4px solid #f97316; padding:15px; margin:10px 0; font-size:14px;\"><strong>Sponsor:</strong> Ingin menang terus saat main FPL? <a href=\"#\" style=\"color:#f97316; text-decoration:underline;\">Cek Tips Jitunya di Sini!</a></div>', 1, NULL, NULL, 0, '2025-11-26 12:52:34'),
(17, 3, 'Tiket Timnas Lawan Jepang', 'article_end', 'image', 'https://images.unsplash.com/photo-1487466365202-1afdb86c764e?q=80&w=800&auto=format&fit=crop', 'https://kitagaruda.id', NULL, 1, NULL, NULL, 0, '2025-11-26 12:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `excerpt` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `user_id`, `category_id`, `title`, `slug`, `excerpt`, `content`, `tags`, `image_url`, `status`, `views`, `created_at`, `updated_at`) VALUES
(73, 3, 4, 'Timnas Indonesia Siap Tempur: Shin Tae-yong Panggil 3 Pemain Baru', 'timnas-indonesia-siap-tempur-sty', NULL, '<p>Kabar gembira bagi pecinta sepak bola tanah air. Skuad Garuda mendapatkan suntikan tenaga baru jelang laga krusial kualifikasi Piala Dunia. Shin Tae-yong optimis dengan kedalaman skuad saat ini.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'timnas, sty, piala dunia, garuda', 'https://images.unsplash.com/photo-1543326727-25e55080986c?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(74, 3, 1, 'Manchester United Krisis Bek: Ten Hag Pusing Tujuh Keliling', 'manchester-united-krisis-bek-ten-hag', NULL, '<p>Badai cedera kembali menghantam Old Trafford. Kali ini lini pertahanan menjadi korban utama. Erik ten Hag harus memutar otak untuk meracik formasi darurat jelang big match akhir pekan ini.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'mu, liga inggris, cedera, ten hag', 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?q=80&w=800&auto=format&fit=crop', 'published', 2, '2025-11-26 12:52:34', '2025-11-26 13:13:18'),
(75, 3, 2, 'Real Madrid vs Barcelona: El Clasico Penentu Juara La Liga', 'real-madrid-vs-barcelona-el-clasico', NULL, '<p>Duel klasik terbesar di muka bumi akan tersaji akhir pekan ini. Bukan sekadar gengsi, tapi perebutan tahta klasemen Liga Spanyol. Vinicius Jr dan Lamine Yamal siap beradu skill di lapangan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'el clasico, madrid, barca, la liga', 'https://images.unsplash.com/photo-1517466787929-bc90951d0974?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(76, 3, 5, 'Bursa Transfer: Liverpool Siapkan 1 Triliun untuk Bintang Bundesliga', 'bursa-transfer-liverpool-siapkan-triliun', NULL, '<p>The Reds mulai bergerak senyap di pasar transfer. Target utama mereka adalah gelandang muda yang sedang bersinar di Jerman. Manajemen siap menggelontorkan dana besar demi regenerasi lini tengah.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'transfer, liverpool, bundesliga, rumor', 'https://images.unsplash.com/photo-1522778119026-d647f0565c71?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(77, 3, 3, 'Hasil Drawing Liga Champions: Grup Neraka Tercipta!', 'hasil-drawing-liga-champions-grup-neraka', NULL, '<p>Hasil undian fase grup UCL musim ini mengejutkan banyak pihak. Tiga raksasa Eropa harus saling bunuh di grup yang sama. Analisis peluang lolos menjadi topik hangat di berbagai media olahraga.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'ucl, drawing, champions league, uefa', 'https://images.unsplash.com/photo-1624880357913-a8539238245b?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(78, 3, 1, 'Arsenal Kembali ke Puncak: Odegaard Jadi Pahlawan', 'arsenal-kembali-ke-puncak-odegaard', NULL, '<p>The Gunners berhasil mengamankan poin penuh dalam laga tandang yang sulit. Sang kapten, Martin Odegaard, mencetak gol indah di menit akhir. Mikel Arteta memuji mentalitas baja anak asuhnya.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'arsenal, liga inggris, odegaard, arteta', 'https://images.unsplash.com/photo-1489944440615-453fc2b6a9a9?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(79, 3, 4, 'PSSI Targetkan Emas SEA Games: Skuad Muda Dijanjikan Bonus', 'pssi-targetkan-emas-sea-games-bonus', NULL, '<p>Federasi sepak bola Indonesia memasang target tinggi untuk timnas kelompok umur. Ketua umum PSSI menjanjikan bonus fantastis jika berhasil membawa pulang medali emas. Persiapan intensif mulai dilakukan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'pssi, sea games, timnas, bonus', 'https://images.unsplash.com/photo-1600679472829-3044539ce8ed?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(80, 3, 2, 'Atletico Madrid Tampil Garang, Simeone: Kami Belum Habis', 'atletico-madrid-tampil-garang-simeone', NULL, '<p>Los Rojiblancos membungkam kritik dengan kemenangan telak 4-0. Gaya main pragmatis Simeone terbukti masih ampuh meredam tim-tim ofensif. Griezmann tampil sebagai man of the match.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'atletico, simeone, la liga, griezmann', 'https://images.unsplash.com/photo-1510563800743-aed236490d08?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(81, 3, 3, 'Prediksi Final UCL: Manchester City vs Bayern Munchen?', 'prediksi-final-ucl-city-vs-bayern', NULL, '<p>Berdasarkan performa terkini, dua tim ini digadang-gadang akan bertemu di partai puncak. Superkomputer memprediksi peluang City sebesar 60%. Namun, DNA Eropa Bayern tidak bisa diremehkan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'city, bayern, ucl, prediksi', 'https://images.unsplash.com/photo-1459865264687-595d652de67e?q=80&w=800&auto=format&fit=crop', 'published', 0, '2025-11-26 12:52:34', '2025-11-26 13:04:24'),
(82, 3, 5, 'Drama Mbappe Berlanjut: Ibunda Sang Pemain Angkat Bicara', 'drama-mbappe-berlanjut-ibunda-bicara', NULL, '<p>Saga transfer Kylian Mbappe memasuki babak baru. Sang agen sekaligus ibunda pemain memberikan kode keras mengenai masa depan anaknya. Fans Real Madrid dan PSG dibuat harap-harap cemas.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p><p>\r\nPertandingan berjalan dengan intensitas tinggi sejak menit awal. Kedua tim saling jual beli serangan. Pelatih menerapkan strategi pressing ketat yang membuat lawan kesulitan mengembangkan permainan. Statistik penguasaan bola menunjukkan dominasi tim tuan rumah, namun efektivitas serangan balik tim tamu patut diwaspadai.\r\n\r\nDi babak kedua, drama terjadi. Keputusan wasit menuai kontroversi setelah melihat VAR. Penonton di stadion bergemuruh memberikan dukungan. Pemain kunci mulai kelelahan dan pergantian pemain dilakukan untuk menyegarkan lini tengah. Gol tercipta di menit-menit akhir yang mengubah jalannya pertandingan secara drastis.\r\n\r\nPasca laga, analisis menunjukkan bahwa faktor mental menjadi penentu. Kemenangan ini sangat krusial untuk memperbaiki posisi di klasemen sementara. Pelatih berharap tren positif ini dapat berlanjut di pekan depan.</p>', 'mbappe, psg, madrid, transfer', 'https://images.unsplash.com/photo-1551958219-acbc608c6377?q=80&w=800&auto=format&fit=crop', 'published', 2, '2025-11-26 12:52:34', '2025-11-26 13:12:48');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `slug` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Match Reports', 'matches', '2025-11-23 23:14:57'),
(2, 'Transfers', 'transfers', '2025-11-23 23:14:57'),
(3, 'Premier League', 'premier-league', '2025-11-23 23:14:57'),
(4, 'Champions League', 'champions-league', '2025-11-23 23:14:57'),
(5, 'Barcelona', '-arcelona-1763949691', '2025-11-24 02:01:31'),
(6, 'La Liga', 'la-liga', '2025-11-24 02:10:55'),
(11, 'Juve', '-uve-1763950418', '2025-11-24 02:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `security_answer` varchar(100) DEFAULT NULL,
  `role` enum('admin','editor') DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `security_answer`, `role`, `created_at`) VALUES
(3, 'admin', 'admin@gmail.com', '$2y$10$M/F8210f.ZXPbLMQiLucXuUKnJBdT6ppu2TxRiui.MTr/gix0Kd0.', 'barca', 'admin', '2025-11-24 03:04:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articles`
--
ALTER TABLE `articles`
  ADD CONSTRAINT `articles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `articles_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
