-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 15, 2021 at 08:56 AM
-- Server version: 5.7.34
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `chatbot_scenario`
--

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_analysis`
--

CREATE TABLE IF NOT EXISTS `tbl_analysis` (
  `visit` bigint(20) NOT NULL,
  `login` bigint(20) NOT NULL,
  `chat` bigint(20) NOT NULL,
  `scenario` bigint(20) NOT NULL,
  `faq` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_analysis`
--

INSERT INTO `tbl_analysis` (`visit`, `login`, `chat`, `scenario`, `faq`) VALUES
(347, 0, 30, 213, 133);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer`
--

CREATE TABLE IF NOT EXISTS `tbl_customer` (
  `customer_id` int(11) NOT NULL,
  `session` varchar(128) NOT NULL,
  `client_ip` varchar(50) NOT NULL,
  `agent` varchar(512) NOT NULL,
  `browser` varchar(1024) NOT NULL,
  `scenario` int(11) NOT NULL DEFAULT '0',
  `faq` int(11) NOT NULL DEFAULT '0',
  `chat` int(11) NOT NULL DEFAULT '0',
  `visit_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_customer`
--

INSERT INTO `tbl_customer` (`customer_id`, `session`, `client_ip`, `agent`, `browser`, `scenario`, `faq`, `chat`, `visit_time`) VALUES
(0, 'b47gf1jhkauee4qe5o6m8vu7drktld12', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-22 02:25:51'),
(0, '334nv4ekphefivkep6idneas49qedagt', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-22 02:27:23'),
(0, 'pi8uv2npvrv737knrj6nui3i1q358kp6', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-22 02:28:48'),
(0, 'jk1d6iop9p0c9ugd3osei9goqu0nmjau', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-22 23:40:23'),
(0, 'p87h7r8ecl8hcboo5lo938vsmpo3ov2d', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 11:01:28'),
(0, 'pd5j8l7osa3blcd02rkn9onu51hcj6ie', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 11:09:47'),
(0, '3a6du79kqu1a8lqamm7a5c5pj1uvgr3f', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-23 11:14:24'),
(0, 'a1os5mvvo165v8t46fh9uj7fqk05eqik', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 11:16:55'),
(0, 'ciciti9iqlahe5ipvmnm53o69nf3pq4o', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 11:27:33'),
(0, 'qm4rpluld5trs70dcg9q277gejsn4tl6', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 11:35:56'),
(0, '0ptf21t5mpogkugq5bk43hls2m82ajs0', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 11:45:31'),
(0, 'mvhjjfuivq4s558ilsi256a2o044o90d', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 13:42:14'),
(0, 'c4nc5kmeg85619iu3jjh6imn2prtk496', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 13:42:54'),
(0, 'spsm7fruetcijr15a4ct9gado2265ekl', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 14:06:36'),
(0, '715oagi65ak5mulli8lm228cr2tc0p3b', '126.157.250.222', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.78 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 1, 0, '2021-05-23 14:12:31'),
(0, '8pkami4fvflir8frdffj437247r7vuhq', '126.157.250.222', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.78 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 1, '2021-05-23 14:17:03'),
(0, 'heatim6hgahhk3ks9r5apkdcjfq4iph9', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 14:41:35'),
(0, 'k7lg000ohguf8tk107jphnacs0fcg27t', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-23 15:33:37'),
(0, 'i9o8fq0fmnr8lv7ggu0tk0fe9qo46c2g', '126.157.250.222', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.78 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 0, '2021-05-23 19:34:57'),
(0, '862lpo8u2n50urgt885brs5qeamfa7d2', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-24 09:33:55'),
(0, '96uhpehmh8tld23sc1c9kp8kamslf8al', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-24 09:34:02'),
(0, 'k7hmqkaek01mgf6bm4er3d3vu1k3jpb1', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36 Edg/90.0.818.66', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 09:52:10'),
(0, 'e5tvf8fkvmoikkbvq3qt9atu3h3qq70s', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 09:56:18'),
(0, '2eqefrhuq44cene0004lduvu9dk4nk59', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 09:59:23'),
(0, 'c69eq4ng6slsvnu1p3fluh6dsmqvnfjb', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 10:39:15'),
(0, 'f6a33stq7pt6bon996mg8lflmbml199q', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 10:43:44'),
(0, '4tr6pkg2v8ss9ou87kdakpc281eft65k', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 10:49:18'),
(0, 'fk7egoad9uoh9rgddvtiuj63ff9mvcf9', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-24 11:00:08'),
(0, '2f0pp6llsvipb5t7lgaa3l1nvf50k1f0', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 10:58:25'),
(0, 'qe458l45lpg4phsaihjs54tti9a8q82e', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 11:02:21'),
(0, 'pm55vdlov8q33hb6n1ei5si3mt10ha8p', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 15:51:10'),
(0, '4q1p44im250b07u79fr32ifjrlm3u4tj', '124.154.220.243', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 1, 0, '2021-05-24 15:55:46'),
(0, 'ng73eulq7uu43hio1pc7cs5h26t421m6', '124.154.220.243', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 1, 0, '2021-05-24 16:03:24'),
(0, '0fogkhj2otdsbgndb83eq8gcgfmo2hra', '49.97.106.10', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-24 16:02:11'),
(0, 'cf9hm0jt095apv53a98qbjhcc1n14od5', '124.154.220.243', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1 Mobile/15E148 Safari/604.1', 'Safari 604.1', 0, 1, 0, '2021-05-24 16:26:27'),
(0, 'ptce7iqou3jbtrk3rd3lrmc9ralquv9t', '124.154.220.243', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1 Mobile/15E148 Safari/604.1', 'Safari 604.1', 0, 1, 0, '2021-05-24 16:27:43'),
(0, 'hkhrc3ahspsq1jousj3m47gl8nautqki', '124.154.220.243', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.78 Mobile/15E148 Safari/604.1', 'Safari 604.1', 0, 1, 0, '2021-05-24 16:28:24'),
(0, 'v722e8389dvdmr1931ai9t80mkkcgm6r', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 16:31:40'),
(0, 'lj1qinvaquq4uvbnghh1j564mo3sl3b9', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 16:44:22'),
(0, 'll2ffaq2kei66ur9t8j0a45ce707egi0', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 1, '2021-05-24 16:50:07'),
(0, '43rbh082j6qm5shsm1huc1d2gn9q8909', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-24 17:32:46'),
(0, 'vqb8igkuth8n1hevgpu5v6c4q7pjeeat', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 1, '2021-05-24 17:56:42'),
(0, 'kd3as0mu92o9g71ash575qk7hbjhnmll', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-24 19:03:48'),
(0, '3nmfvue8g215863nthn5i8u0uk35r1v9', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-24 19:11:35'),
(0, 'jtfh3kd7os6kh3beu9r24cn5mi77nhun', '124.154.220.243', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.78 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 0, '2021-05-24 19:31:16'),
(0, '75ho1jgelp07tm2s7h3tvevan8tpubdl', '207.65.167.47', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari Line/11.8.2', 'Safari 11.8.2', 0, 1, 0, '2021-05-24 19:40:03'),
(0, '9cv0hag0jhuqphup0gskfmiec18m2q6f', '126.194.209.182', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari Line/11.8.2', 'Safari 11.8.2', 1, 0, 0, '2021-05-24 20:50:10'),
(0, 'orrk6ofbhta3euf732illf9tkmn12dq8', '126.51.81.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 1, '2021-05-24 22:49:17'),
(0, '6c64v1bgkektpe1pbtgce04aphc3bnv6', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 1, '2021-05-25 10:42:51'),
(0, 'mro1gsg5it88n4im0je3ai0n1qfff7qm', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-25 10:48:04'),
(0, 'ogjgnqa4v95bjsomtt9slgbsjgdtgmfg', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-25 11:43:49'),
(0, 'pcd7j05uhmdv63pfjstef1siuv92et2u', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-25 18:56:16'),
(0, '6g3khqfor9l4boa6uakbiqh29q4gf7lp', '124.154.220.243', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-25 19:07:37'),
(0, '3ti15hol23j6i5j55lompog0vs8e888c', '126.156.206.0', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.78 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 0, '2021-05-25 20:03:37'),
(0, '112h5p1dac5plsoabo0tve6f3rvelgkq', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-25 21:54:29'),
(0, 'mr6f7et4ctri6f04g91ifh1pgoe35ct1', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-25 22:10:09'),
(0, 'kdfknmhepp3re0c91umr26kame1v3tud', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 1, '2021-05-25 22:20:33'),
(0, 'otjetsm1t0f9t169jchjcepnm9ruq9ht', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-25 22:51:21'),
(0, '4ol887ivceqvou1ei83qrt06nn2psfpo', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-25 23:15:17'),
(0, 'dp98li7dgb0h7f2bbaspqmp92bda3nqj', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-25 23:25:05'),
(0, 'mphrgvnacj03qe902lcgu640obksecp8', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-26 09:11:59'),
(0, '956tocq7tfci5b725dmrvb23sb8i0e13', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-26 10:35:12'),
(0, 'flch5ovj3pe8ql7lq6t0ufc2mvf6qemh', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-26 10:51:03'),
(0, 'h9q37olckk5qi8rlqbq1cmahh33qq2dg', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-26 10:58:08'),
(0, 'qh2233meppu3ondsq8g2pp3d5f4f2jtf', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-26 11:06:37'),
(0, 'sj04vfiqo22lgvoc4qvu61s5o19uitll', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-27 10:29:47'),
(0, 'g9df6oif2v12mql5qk5978vqq31smfos', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-27 12:35:31'),
(0, 'nasll6p9uhbvj0t1lhan5jbqg0hekrl2', '126.236.37.149', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 LightSpeed [FBAN/MessengerLiteForiOS;FBAV/313.0.0.39.119;FBBV/292443650;FBDV/iPhone13,1;FBMD/iPhone;FBSN/iOS;FBSV/14.5;FBSS/3;FBCR/;FBID/phone;FBLC/ja;FBOP/0]', 'Mozilla 5.0', 1, 0, 0, '2021-05-27 12:44:00'),
(0, '6smma53lg10i1uq0mgsbe3g96kqg3r29', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 1, '2021-05-27 12:47:13'),
(0, '5fjt9e2ql4s20apoqmkvhgl41gvlqgv3', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-27 12:50:20'),
(0, 'b9abcv5fq416ketq4gg8lqo65ia2q7ga', '207.65.167.47', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 LightSpeed [FBAN/MessengerLiteForiOS;FBAV/313.0.0.39.119;FBBV/292443650;FBDV/iPhone13,1;FBMD/iPhone;FBSN/iOS;FBSV/14.5.1;FBSS/3;FBCR/;FBID/phone;FBLC/ja;FBOP/0]', 'Mozilla 5.0', 1, 1, 0, '2021-05-28 02:56:56'),
(0, 'jinpt7rdipsa4t4s87frl8ldin0um5n1', '126.157.99.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-28 07:11:14'),
(0, 'snh3uutvmmlo32lc7jmvlp2o3eu5nbpb', '126.157.99.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-28 07:11:37'),
(0, 'm507b7mgbn5laikndlmj8ovg29tog5ks', '126.157.99.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-28 07:42:17'),
(0, 'iguddn51fnsm07qt3d66qicck011pi9e', '126.157.99.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 1, '2021-05-28 07:47:37'),
(0, 'saa1i74gji5ocacjcah1k4cmoacp0vb3', '126.157.99.16', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 1, '2021-05-28 07:50:31'),
(0, 'ecvn21hro9m4mspj1189ah12v3tigqjn', '106.73.134.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-28 11:42:25'),
(0, 'mj2smb6lp5mi373gt3cdi08ln9mhp9p8', '106.73.134.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-28 12:47:23'),
(0, 'f4vks4bvn9nir7sjsp1pttqt7d4g0mqs', '106.73.134.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 1, 0, '2021-05-28 13:04:38'),
(0, '1qi8rn2k6lnlds2muujs6ko8u4brii0i', '106.73.134.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-28 13:35:09'),
(0, 'l92arnmiu24dn3br9j2bifi6mrcisbg2', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:13:56'),
(0, 'v1rlhvli0ur44f59nks6vt55coe33cni', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:26:43'),
(0, '3lafd2ucle2l7vrp9omgmplg2ptjtf11', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:31:14'),
(0, 'uc7rr75e5qbgkvdd8fglp83r1heldrkf', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:32:08'),
(0, 'f9544d00kejhapicfb6e8ie5pmk5ppoq', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:34:27'),
(0, 'aafvgagm9ufmbivtge1n9i05kmsm7ndi', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:38:50'),
(0, 'q9t61nf6vka08f91ebkdrmn1cbqm8ljf', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:39:22'),
(0, '7uau34aqrkka6cv2pi5somnj99lbcvvk', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:43:24'),
(0, '3vhg97voluvvitq8qb8jv135fiocmmie', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:45:52'),
(0, 'dah7onoufjse2r0avimu6c9febic963u', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 10:48:22'),
(0, '63h0i5cpk4fej9innq4ephmsr1s2br60', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:03:32'),
(0, 'bprvpv9libkpcpildphpp1htadpdvauu', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:05:14'),
(0, 'm7t2h5jpvuj1e0gd48kn9nul7iakqprs', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:08:21'),
(0, 'h7imh4osvu4p8t8b5gqr1qfu4ocfd8m6', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:10:38'),
(0, 'atfi7pdur79vvl91rf8krqpv08dvfng2', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:10:59'),
(0, 'ggi19fc1m5nt825meg79g5gstcvpoae5', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:12:08'),
(0, 'amqpc8nigb1srmluf6akr7hh55re3a0q', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:12:08'),
(0, 'ibsamq80lv6t5d4b49oqtp4q168vnsne', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 11:22:03'),
(0, 'i68vo9nel78pe98rqmivspcp26bbat4s', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 11:22:05'),
(0, 't0an61nf6qefishmqup85fslglsntkl2', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:25:31'),
(0, '8rsbg5nkuc3pn4gfebh2rr7771o97v0j', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:25:31'),
(0, 'r5e40sr5ontnts0dn8o5g01rdd9k88j8', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:25:35'),
(0, 'h1smjp9f6mri1cr6l9mt7a1hgmd1b87v', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:25:35'),
(0, '73mtmsrujmgq03407nbuh3ula96ds1m2', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 11:25:44'),
(0, 'gki267oh37eib8psftlh0dcbc80fpj02', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 11:25:46'),
(0, 'jk6i52j6cudgpaen0lvaq5t29gn3tm0f', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 11:28:08'),
(0, 'cn2nd39qop6p7bb8d5k7drtlikm1fke7', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 12:49:53'),
(0, 'f62vsuune8d3uhieferpabl7s42r964i', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 12:51:42'),
(0, '478sgqmo50636mpogsv1cs62seoaivek', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 12:51:42'),
(0, 'jvq5htfskmv1qfnnv0kfmh261j7cr3nd', '49.98.7.35', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_4_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari Line/11.0.0', 'Safari 11.0.0', 1, 0, 0, '2021-05-29 12:57:55'),
(0, 'e8bb0qug15tcjhc3a2ibq60e54oj7dm4', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:25'),
(0, 'sp6lj0ij3sucsotuelij6gnl9efj6vl0', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:25'),
(0, 'f7me4bn6mcmuldjclsdbsfdrq93712lp', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:28'),
(0, 'bmnjjgjtfo255kfa0njmjba10rt20son', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:28'),
(0, 'qk01iltpbgso1tt4qd3b6edq0c11n6lp', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:44'),
(0, 'hvhf4fm7u3tvofn2ui928qv90doc8qc1', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:44'),
(0, '4ja502jtiuniss1hqd6h51uqutqkqkq4', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:47'),
(0, 'l86k9v8hmka02tle8mdrj0og501t2uhi', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:47'),
(0, 'ah7p8uck24a06ol5eg1jvoh139h09rcj', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:53'),
(0, '6vq7ultj4l4cngf6m0s731t0aqgi3b57', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 13:46:53'),
(0, 'jhiv34g6mgqkgk47vi103hijuneuao36', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:14:27'),
(0, 'l0ntkdca8a1h6sou4ggqm2oag4rbpv9v', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:14:28'),
(0, 'bggfsbi9mtlq6n3ik281ahei0ooklaco', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:15:51'),
(0, 'h6g5ejaleov7c6c8etmoi0kt9ij29pas', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:22:12'),
(0, '0i78o3i6ipcgnqdvhg1b9gt1ilr02o3l', '188.43.136.32', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:22:14'),
(0, 'umuu5uh50ioapeesg58r6ti6rc2cf5ri', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:25:39'),
(0, 'uqin8e8agepulb1493snrkqmtqndqdlq', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:25:40'),
(0, 'o9bgkaqgvj4kcp16l5nfut7ic4bm6mss', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:25:59'),
(0, 't7vc0rfnebj7ui17aujfb14sq12528uq', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:26:01'),
(0, 'lg525b2qs273la575snumh4mpg15p053', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:26:57'),
(0, '7ph3pomndv87s57joc55198tm3604p4c', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:26:57'),
(0, 'k1ogv2ud4si4qdvuha2mm4rdq0n5mj7c', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:27:00'),
(0, 'g5m1lmvv8rcf8om5g1grv96hf8c8s0lg', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:27:00'),
(0, 'otfsl4e25hqps80qalimktssct4t5arf', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:27:03'),
(0, '52brnkp48r0pkcp94s6nlj2cbudmj0sk', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:27:03'),
(0, 'd9bren75tmbkibd38bv3kr5chpsvmagj', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:27:14'),
(0, 'snuc2g7pts69hughmhjdevuscgkhmuif', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 14:27:14'),
(0, 'n1tmqujros78otiedvh767o7mdlcocmb', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:27:38'),
(0, 'rk5dlu196afihvma87bhumqle773ttje', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-05-29 14:27:40'),
(0, 'sepbv1st30fvdgagd8fnrv5j0a5spd34', '106.73.134.193', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-29 16:28:14'),
(0, '4ankbv0gcdeehmv3phaf8p49ohiftf9k', '207.65.167.47', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari Line/11.8.2', 'Safari 11.8.2', 1, 0, 0, '2021-05-30 02:24:01'),
(0, '12apmi97s8stu5lj8t3hb1np4640oud5', '207.65.167.47', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 Safari Line/11.8.2', 'Safari 11.8.2', 1, 0, 0, '2021-05-30 09:16:57'),
(0, 'er32sdmoujkooq3f8qd88v7i4t7udcl5', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:17:02'),
(0, '0s23muru4j6hj2jutsf1cg00764dbe2r', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:17:02'),
(0, 'p0n6b7av4jfltke768kblqpe8deu13rp', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:17:06'),
(0, 'e092ridilblcv12ljbk2rs30nf49e9k4', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:17:06'),
(0, 'up2e0pk3ksfovib8slr56f41dog2rff6', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-05-31 22:17:15'),
(0, 'kg3kshbnhad9e7ueksvu4rv1n5ev4u3t', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-05-31 22:17:15'),
(0, 's8jl6fdg6s0jlk1s4g7vtq5rl8m3vr0u', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:18:13'),
(0, 'rb7afmbfr0ffupm402iqb7uuub09rll6', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:18:13'),
(0, '9us1839v2ec11549in7mpf3e6gbovj1v', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:18:15'),
(0, 'f0pc3l2lps8aplfobmu62t9p702fkoqk', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-05-31 22:18:15'),
(0, 'dp7lslqk91ll2ndcl3it1qck0oq3fqij', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-05-31 22:18:23'),
(0, 'euq887gn33f57a85efdgg6msg2sgnri2', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-05-31 22:18:23'),
(0, '7t2bpcc20qknptmlpt9qddjgqhstpbsn', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 16:25:16'),
(0, 'tj42712sn32r75njf2k3i5q1ut7d36as', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 16:25:16'),
(0, '0rgbvijt4ohgj1i43qpkdc4hbli90lkp', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:29:30'),
(0, 'c4nduhhb2u0ipjhpol6rtaji7aind8qd', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:29:30'),
(0, 'p4mmfp9mc3nlfl3faoir3jckscajn65m', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:29:32'),
(0, '4ndvc9koo5amvv6so8ifs66tmd407lhs', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:29:32'),
(0, '7k30nub5lh03sdcmmcgh0mtsv1v35gj8', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:31'),
(0, 'k4msmb08nqk78cjc3bikbn6mcbc31n72', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:31'),
(0, 'lgdacqgnqpbr94otdacvq118govis7qg', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:35'),
(0, 'ct2ncb9g762og4a248p21cc064trql8k', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:35'),
(0, 'uv2hfbvdhv6375q6pd0e43qjk6o8sh6i', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:38'),
(0, 'od1g4u0cs33fgjn0cjibml1c27qqkcg0', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:38'),
(0, '026479fah6gjb8ugll9juju7s20irbdt', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:41'),
(0, '4tuqv323fmuo7nubm02ek9amjqme2p7i', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-01 23:41:41'),
(0, 'qr66fnqth2ob1ibsg314m746tcr6adfj', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:33'),
(0, 'q6652i8t413hbn2p7dggd6qls300ob4b', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:34'),
(0, 'm3u18lrgds514eiil0tdifjtcu7mr15m', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:36'),
(0, 'c8cdui23pdha3icthtjlr1meamtov6i2', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:36'),
(0, 'tt597ag8b085e7n60h7rljoglgke32oi', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:40'),
(0, 'cpcd8vilmlja1q4nvhp5hesmbl8atl2q', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:40'),
(0, 'gn92o8v8e91vch44olu4ghkk695nv190', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:43'),
(0, 'kdapedlht42s8ouv897k4gr7mjmub0ic', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:28:43'),
(0, '1ou4nga6gfdla32bug76qj7l9ltpk64n', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:30:08'),
(0, 'ft3evs41leu44qubetb0oksr9qbbq9h9', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:30:08'),
(0, 'a1ogq1fgtm6hcim7bc39ao9hgjc7s8tv', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:30:11'),
(0, 'bqda3977s69670pf504shci01bpk7jsc', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:30:11'),
(0, 'suglft909k9pglt6ajaic4qcjil09lkj', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:38:24'),
(0, 'isqs9n9t7m977lilkjm2lcevsbq4l10g', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:38:25'),
(0, 'v7017cpssk8gjbucpi6l2h43btjblk64', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:44:55'),
(0, 'l0hisefllfa7lnnd7ca6e1t74rul1vuf', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:44:55'),
(0, 'ndrn2ni7gv6fg4j02s054sp8j7ok58uk', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:49:06'),
(0, 'u11eq3s3q78ltu85v4393sj88pb7pgvq', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-02 10:49:06'),
(0, 'c3ost0eup3vfho8mcs766prt4e4cnb5r', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:06:59'),
(0, '21ujb2thm657rjfantg6c1vi3m7ceeht', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:06:59'),
(0, 'tm4hg893biv2sgfudsoi19omogj8mmcc', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:07:13'),
(0, '68lmvltem2bj181qq7orp5kaknqrsucm', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:07:13'),
(0, 'mj5q2vjchq1kqh0mlc8qp6tgtae73s4i', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-02 14:07:35'),
(0, 'chiibjlnko1sl0d7i7cfkfj44155u6dm', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-02 14:07:37'),
(0, 'tkk549b54n2o7k2bnqiui5ngro93rfsn', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-02 14:07:42'),
(0, 'avqmehnhq63kj15m9dcvinbus66tcgfm', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-02 14:07:44'),
(0, 'v6mukjn3ic9s4391oecibopjsut263rb', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 0, 1, '2021-06-02 14:07:56'),
(0, 'ht3cuu6e710uu3hrb2ocbmcv9k1s3u1g', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 0, 1, '2021-06-02 14:07:56'),
(0, 'bobmlduqjstbhdne3u8ml22sibg6t19e', '3.91.132.15', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 0, 0, 1, '2021-06-02 14:09:30'),
(0, 'gtbuvnh3hm6jk10inb12abo57mpf5teo', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:10:15'),
(0, '1r7fg2ord8b3oklp492n6pfju851aljj', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:10:16'),
(0, 'urssqto17t3qmkosvf1geek56v3al7lk', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:10:22'),
(0, '5gqmtrok72jm6v7fnmvrl0lgim1bvp58', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-02 14:10:22'),
(0, 'quour47coqogurek7aksggqfoigtsuo6', '3.81.58.76', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 0, 1, 0, '2021-06-02 14:10:34'),
(0, 'teaoi6r1dr6q1gvc2ijaemli0bit8ckd', '52.90.211.63', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 1, 0, 0, '2021-06-02 14:10:36'),
(0, 'prfvtig932vcb9hk4j74a4fguav72ntj', '18.234.136.246', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 1, 0, 0, '2021-06-02 14:11:51'),
(0, '8hbapioa8iib9f1hi95oe39o0nigfrpp', '3.88.33.39', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 1, 0, 0, '2021-06-02 14:13:11'),
(0, 'bkasg8mtr8oc9rldv9dndl88gd5g597a', '52.90.211.63', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 1, 0, 0, '2021-06-02 14:14:19'),
(0, '6p4c1du7vlkrk5o26tkmb1dc94210o9o', '34.216.187.8', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 0, 1, 0, '2021-06-02 14:18:19'),
(0, 'f2to5k4c52up9ctganhk71sr3rms6b51', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:03'),
(0, 'a8pnn6fumvnin5s8igmatmukp1vh16re', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:03'),
(0, '93frt1mf5kadt17olq83cm1ls8cocmdi', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:06'),
(0, '4roqrdccilqfpu8csist08mlf8jdnfhq', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:06'),
(0, '5df49oqruusf8082tqotk2t8r5pf3teu', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:09'),
(0, 'rqe0q1ptrbl1lae169srv7u2dlbclng4', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:09');
INSERT INTO `tbl_customer` (`customer_id`, `session`, `client_ip`, `agent`, `browser`, `scenario`, `faq`, `chat`, `visit_time`) VALUES
(0, 'jj91hr316078ga7anmc8pof6a2m956e0', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:12'),
(0, 'j7jo65oio7fjgp186eh8qidqgfoikar5', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-03 16:42:12'),
(0, 'arpifb2ld5oj7uat37pse4n147mldktt', '126.194.215.74', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.216 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 0, '2021-06-04 20:26:28'),
(0, 'd4rvrffetid433k97b52n3kq4hmhpb3c', '126.194.215.74', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.216 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 0, '2021-06-04 20:26:28'),
(0, '6a5fl43b03t5lkfejs3pailqkbh32ngj', '126.194.215.74', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.216 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 0, '2021-06-04 20:26:33'),
(0, '4pc714j8bli94f4n42k9l6rso423sdmi', '126.194.215.74', 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/90.0.4430.216 Mobile/15E148 Safari/604.1', 'Safari 604.1', 1, 0, 0, '2021-06-04 20:26:33'),
(0, 'fnvm1ajbmvjde4l88dlfadllpmije1lc', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:13'),
(0, '6bnnue43rhk7mg1dntvqloqu7vojkn96', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:13'),
(0, 'ebeuo4l9bce1k5tqnvv5ju1t3o3806p2', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:15'),
(0, '88cd6uve8of1deicn4ec0kujtll0inds', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:15'),
(0, '9mniod39p56uangqvvuq5dnleqksj06g', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:18'),
(0, '25oadcbdaaho98o46n6294s7pmqquj8p', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:18'),
(0, '7p7mkninv78qlas40qhmq5e691pr18kf', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:21'),
(0, 'o4isebta8sj98rmhjd64a2pckct37idh', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-08 15:29:21'),
(0, '07csfl7g2v3slolki9vghp179ue9jmc0', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-06-08 15:29:30'),
(0, 'hsgm42pkvttamqe4lelb2bk3jhdeecko', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-06-08 15:29:30'),
(0, '56bauo546s5bc5jcdhpeh7tid7ls1ohp', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:17:59'),
(0, '0us3m1gadvgq7ufcu7g5jo4339051kgo', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:17:59'),
(0, 'j281josidnqo1dsur9ufqv4bsfob5p0d', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:18:02'),
(0, '9ig8i7pp2bm2sp2lsur36qhek59lus6j', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:18:02'),
(0, '0m1bgaov50jprhiodt8jgo676ggfgftl', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:18:05'),
(0, 'l05opf1nm8t960l53blbk07vfae5fh4p', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:18:05'),
(0, 't9nn40gbpftvakotsl1cq1epms5t4ars', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:18:58'),
(0, 'qurec84usc3q2tjlnvdukfj0n9jhi4hl', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:18:58'),
(0, 'q366lnks8fifjgd097853d5kviivcpvg', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:19:16'),
(0, '8eh2m3ud68850pflpc0rhosc01lramqq', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:19:18'),
(0, 'n4uqnr7n5gb7skq9pnl56kaa1ah40ig1', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:20:16'),
(0, 'botqrv4kj5scg22v4l02bopls0see03b', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:20:17'),
(0, 'sj1qargktu6ser8osp1osc105idahnv9', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:20:50'),
(0, 'e67u5s97l0ptlgte62rcu129ka5gj67r', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:20:51'),
(0, '0vo29i6as7b4kefn3npff0v534ffeo90', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-06-10 17:21:10'),
(0, '9pfooeslag716d36sda76ccgbg5qg224', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-06-10 17:21:10'),
(0, 'oc2uknpederunbo3mvkp5o11go38g4qu', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:23:09'),
(0, '7kppbb6fetoke46npvecei0b6le8dg6t', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:23:09'),
(0, '7gmjubda7molov9qsj9409k6r1oea21i', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-10 17:52:14'),
(0, 'r332m295s6j1q8tdt1ds76nfoqj71n5n', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-10 17:52:14'),
(0, 'p54t960e41r1e87h8ppp5i9h876tbidb', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:25'),
(0, 'fg4kcku3a6ojc723k3972jikqbnpd2gj', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:25'),
(0, '8di1jqh8prujc7n30ss8h9h41ie0g8q2', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-10 17:52:30'),
(0, 'k8sldku8lk5k2ecv4g8j5lv1he7h07sa', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-10 17:52:32'),
(0, 'r7f60im170jis4btv9kvb4d7c0bppivc', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:32'),
(0, 't59malin4av1feitil1sgdej4tbgdpjt', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:32'),
(0, '62g0eev62n4npmoc1q2nh6ucob8im9h6', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-10 17:52:45'),
(0, 'ebkdpopogier2ajdb813b1osuqcgcuuu', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-10 17:52:47'),
(0, 'jgccktver5ds2qb480hg0khm9apcmht7', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:51'),
(0, 'dle2i2otlc4feue0vu3piusbf5e1v5k6', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:52'),
(0, '2rrep4qb82g2ucrnucvjahdfmb8t0uq4', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:56'),
(0, 't94tr8mocpes2304bhpa16ttb1vhbset', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 1, 0, 0, '2021-06-10 17:52:56'),
(0, 'fnruop8mj8vbfrrsdsinor99u3qr7kju', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-10 17:53:09'),
(0, 'cv7bg5dfmn1k8qg0dumm50rcarl5od4m', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-10 17:53:09'),
(0, '8489ipc8t4jbrb3kfi8t6k02cbt37tq2', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-10 17:53:12'),
(0, 'og4ii4tb5ngp5pjq8fvd0t7ctjacuju1', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 1, 0, 0, '2021-06-10 17:53:12'),
(0, '4rrs0vu03r7db4ebeug49455mnl5in9g', '3.88.33.39', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 0, 1, 0, '2021-06-10 17:53:40'),
(0, 's48da2evn9r2j5vgf3cu14ct0p2pmnv9', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:53:44'),
(0, 'k3uj3ia3su3i6l892iir8r1vl48drgmr', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:53:45'),
(0, 'gpra68fpooquma6qfb0t48lh1uijd4nf', '3.82.47.248', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 0, 1, 0, '2021-06-10 17:54:28'),
(0, 'ud4khhtsdcamciusiecdt0gicdo0mbdj', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:55:52'),
(0, 'p5n6keq7bbmmj0j2pm43tisfselm1ffe', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 1, 0, '2021-06-10 17:55:54'),
(0, 'mmffn8n7uv8me11cppj8l83u8rme90cd', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-10 17:56:02'),
(0, 'ooohr06cgm2dnt4oghucmrkb5h25ca3v', '113.33.216.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.77 Safari/537.36', 'Chrome 91.0.4472.77', 0, 1, 0, '2021-06-10 17:56:03'),
(0, 'verqe1oht8ajerd7gjuj780b64mc3mfd', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-06-10 17:56:09'),
(0, 'fe5tvmvu22l6ik2cn76mipgorpe0dib5', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Chrome 90.0.4430.212', 0, 0, 1, '2021-06-10 17:56:09'),
(0, '10t6lbsisb52ccf8d13obhs7ik9q2e41', '3.83.212.207', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 1, 0, 0, '2021-06-10 17:59:26'),
(0, 'cfh5mh5nvcgdmotv492ng77ovcutjj3o', '54.188.57.66', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.109 Safari/537.36', 'Chrome 72.0.3626.109', 0, 1, 0, '2021-06-10 18:13:05'),
(0, '4tcl32bmntvphpdkhr951e03jkkh8euf', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:14:58'),
(0, '3fco1j6vptpa4ktuuc0gqc5fq9mcaaln', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:14:58'),
(0, 'sa9v7f5cd4kicu4fhj4kho5fuirrqsgb', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:42:02'),
(0, 'jdqi5l8o2kju9vjs9rm0naulfhgamd30', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:42:04'),
(0, 'o8rkilln303l3b02vuhp85taug6kev6o', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:42:44'),
(0, 'bj7b68oh28d07h35e57g2jlc20ffu2cj', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:42:44'),
(0, '8g61nge5n0d332d4ul31uct5n39v1pul', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:42:47'),
(0, 'qmn1qba69m66jgei488d3n4jcmlfi2pf', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:42:47'),
(0, 'c79e66ma28tiijdqg4urtvottgbq1qhi', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:43:16'),
(0, '4fafi0c35glpolp0tej4op3jdn5k70r4', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:43:18'),
(0, 'r8i865pk854bjvgcvonimk93dsslv0u8', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:43:40'),
(0, '63jv8noim8l7qs9sc1gs9or40dethj08', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:43:41'),
(0, 'vbbfps4oiaut37o17hjqmpfi2bdg3rp9', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:44:03'),
(0, 'gisrqss5psar88diqu8j8m0gik89kbok', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:44:04'),
(0, '8cfatgptuc7qgpveiki9go6497n9e9t6', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:44:37'),
(0, 'u3ss6ifnkc8t41v1788qa69id6sand2b', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:44:38'),
(0, 'aqv8569v5rpskvt5mghdk000l4jk0rl1', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:46:04'),
(0, 'fi0fporkdekksr4qdsua4pm3udj43bas', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:46:05'),
(0, 'evh161g4vhm9rr6ecci7e67pud5j7ajt', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:46:43'),
(0, 'n3ksklhh9lcieuv3p3i7ma23bu1k90op', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:46:44'),
(0, 'ia1qr48ujkjqci4ds101vmnvt3j3me9m', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:48:11'),
(0, 'b2svce6kbe8j50vdbt6q8b9i4r3au70s', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:48:12'),
(0, 'f4ms8kctetr04oi3rhmonr04gu154lg5', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:49:02'),
(0, 'cg5io807o6q3q3pkttgrvae3fvlhion3', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:49:04'),
(0, '6iefoalouk9vo4cb1eabrg64tehlrak7', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:49:30'),
(0, 'm6d69tblldfhnbdl1iv7hnspfi1jsrc3', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:49:32'),
(0, '866d72ojmjalt9ceohlnkngvl71jbkcb', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:51:06'),
(0, '726vel1qlv3le81nh99fmtus0g9mc79n', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 20:51:08'),
(0, 'erdfs3r2l2f7q7r7bns87dcurt1kfrvt', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:51:50'),
(0, 'km99nltn00tngupss53amdikesvenagp', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:51:51'),
(0, 'ou485ingdu7sb8n0c15ns6nme92mjm46', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:51:53'),
(0, 'h44pi5qegj3fc0c2079go02tbra597ei', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-14 20:51:53'),
(0, 'n3ge301ocu5cji2oud7fbhdrgmbje1gi', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 21:00:15'),
(0, '0cpqhluacqkbhvcg9m7lpvh9ci950mkh', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 21:00:16'),
(0, 'luf96f4bii5p2biec38btfjskuoarfra', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 21:02:44'),
(0, 'ddd5lk0nlrjvam8gsjuarp2sivjp92e9', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 21:02:45'),
(0, 'pb875023s1f1gr0khq7kf6j56i6ktvva', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 21:03:59'),
(0, 'kngfvlc95ngorvuo9qusvvdn0931huk0', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-14 21:04:01'),
(0, 'ci9g9nmoe1omdbmaeqmev94ilorluqot', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-14 21:04:16'),
(0, '8urfjotgpehopdjq94m9aef243h5s04u', '210.139.46.133', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-14 21:04:16'),
(0, '35jjeho7t5v9m4rv5mftq33l64g98rel', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:11:50'),
(0, 'f8snno21vkqrm796hfhdsm2unfjba726', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:11:52'),
(0, 'ruv5e0q3kau8p47h5bltprangvpctrns', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-15 09:12:07'),
(0, 'd3906tr07840jqsllsnv6kkmdi3c5ukh', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-15 09:12:07'),
(0, '7j8124o3jl22r5jghvact5tngoboekq8', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:14:26'),
(0, '00h0fkv3uoktc9mv36vcrnohjkb81o2t', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:14:28'),
(0, '455ibd0qjdku501ak14m8soqkt90oo7o', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:16:23'),
(0, 'q721b9ptucdrqcgmmc355jdl0lkg5que', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:16:24'),
(0, '779jcatbeergmjhl3alinf1sahjugdun', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:17:09'),
(0, '0hnvv1p90t9epesd6jg6i24eg1uvip0h', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 09:17:11'),
(0, 'rh8r3k3q6bbl37igu88k927b6bvmj3de', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-15 09:40:44'),
(0, 'ipunln6r9k4a3drjdp4flem50efsop1i', '207.65.167.47', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-15 09:40:44'),
(0, '622sua66bpdc6rru8inm5c6ckv7fvbhs', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:29:57'),
(0, 'aarrqc5e8djk8u306b6l176e4s0up5hj', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:29:59'),
(0, 'pdpjm2i69lj9h976es3kadleklo9bu9d', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:31:12'),
(0, '26g58kajp46jrel5okefmtssbmd4f2la', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:31:12'),
(0, 'rt16d9pdomv1sltgig4o2jpt0up3hvt7', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:31:15'),
(0, '6d61arcb3btc49ttari5l6fpgivfcjfl', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:31:15'),
(0, 'gk7h0n7o7601vq58ld8tjdd2hfjg2fji', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:31:19'),
(0, 'ba9sg7v9ns4ibtjpkdmpfi0q5ms5p677', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:31:19'),
(0, '50pmfj3f0n815v8lhhqgcgpj0prckfi3', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:32:21'),
(0, 'u291v0t3sfti27196fgnhjua16v7lnoj', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:32:23'),
(0, 'mcjfa7fkea61ca24ul9hdqu1ofu91a6l', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:34:05'),
(0, 'al5vtpl1q0oh3u09j5ieujr06eh240ur', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:34:07'),
(0, 'f44ge92gm33nfqalfmf1e60jrecj4lal', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:35:48'),
(0, '7aima8ogfgukqi76nl53l1om0lo44b0m', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 1, 0, 0, '2021-06-15 11:35:48'),
(0, 'c59n25fg2li5apjilpqitbrleslg51us', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:36:01'),
(0, 'hcmjc6m5jc5muk6l6nllr6l2i2jqtqhg', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 1, 0, '2021-06-15 11:36:03'),
(0, 'lrecv88nintomutra7cacc92snu28qm9', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-15 11:36:23'),
(0, 'thu6tb3pckhkfj3vbf25rv6sjd2pmho2', '126.255.96.221', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.101 Safari/537.36', 'Chrome 91.0.4472.101', 0, 0, 1, '2021-06-15 11:36:23');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_last_login`
--

CREATE TABLE IF NOT EXISTS `tbl_last_login` (
  `id` bigint(20) NOT NULL,
  `userId` bigint(20) NOT NULL,
  `sessionData` varchar(2048) NOT NULL,
  `machineIp` varchar(1024) NOT NULL,
  `userAgent` varchar(128) NOT NULL,
  `agentString` varchar(1024) NOT NULL,
  `platform` varchar(128) NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_last_login`
--

INSERT INTO `tbl_last_login` (`id`, `userId`, `sessionData`, `machineIp`, `userAgent`, `agentString`, `platform`, `createdDtm`) VALUES
(1, 1, '{"role":"1","roleText":"System Administrator","name":"System Administrator"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 10:52:03'),
(2, 2, '{"role":"2","roleText":"Manager","name":"Manager"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 10:52:40'),
(3, 1, '{"role":"1","roleText":"System Administrator","name":"System Administrator"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 10:57:03'),
(4, 1, '{"role":"1","roleText":"System Administrator","name":"System Administrator"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 14:55:14'),
(5, 3, '{"role":"2","roleText":"\\u30b9\\u30bf\\u30c3\\u30d5","name":"\\u00c2?\\u30bf\\u30c3\\u30d51"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 17:11:06'),
(6, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 17:20:10'),
(7, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 18:30:53'),
(8, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 20:38:50'),
(9, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-18 21:09:28'),
(10, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-19 09:42:42'),
(11, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-19 14:35:54'),
(12, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-20 01:31:36'),
(13, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-20 10:04:02'),
(14, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '::1', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-20 15:08:50'),
(15, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-20 17:47:04'),
(16, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-20 17:59:12'),
(17, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '126.158.224.96', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-20 20:37:44'),
(18, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '124.146.227.88', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-21 09:47:17'),
(19, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-21 11:07:28'),
(20, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-21 15:28:10'),
(21, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-21 16:17:04'),
(22, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '126.51.81.5', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-21 16:47:28'),
(23, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-22 02:21:58'),
(24, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '207.65.167.47', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-23 10:54:04'),
(25, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-23 15:29:49'),
(26, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '124.154.220.243', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-24 09:31:06'),
(27, 9, '{"role":"2","roleText":"\\u30b9\\u30bf\\u30c3\\u30d5","name":"\\u5c0f\\u5ddd"}', '124.154.220.243', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-24 09:32:49'),
(28, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '126.51.81.5', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-24 22:44:39'),
(29, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-25 11:41:56'),
(30, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '124.154.220.243', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-25 18:48:34'),
(31, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-25 21:29:36'),
(32, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '207.65.167.47', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-25 22:50:49'),
(33, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-26 02:02:46'),
(34, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-26 02:02:59'),
(35, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-26 09:55:58'),
(36, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-27 10:27:04'),
(37, 9, '{"role":"2","roleText":"\\u30b9\\u30bf\\u30c3\\u30d5","name":"\\u5c0f\\u5ddd"}', '106.73.134.193', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-27 10:40:45'),
(38, 9, '{"role":"2","roleText":"\\u30b9\\u30bf\\u30c3\\u30d5","name":"\\u5c0f\\u5ddd"}', '106.73.134.193', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-27 19:56:15'),
(39, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '126.157.99.16', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-28 07:46:49'),
(40, 9, '{"role":"2","roleText":"\\u30b9\\u30bf\\u30c3\\u30d5","name":"\\u5c0f\\u5ddd"}', '106.73.134.193', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-28 11:46:52'),
(41, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-29 10:13:21'),
(42, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-29 10:13:23'),
(43, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '188.43.136.32', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-29 10:25:14'),
(44, 1, '{"role":"1","roleText":"\\u7ba1\\u7406\\u8005","name":"\\u30b7\\u30b9\\u30c6\\u30e0\\u7ba1\\u7406\\u8005"}', '207.65.167.47', 'Chrome 90.0.4430.212', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.212 Safari/537.36', 'Windows 10', '2021-05-29 12:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_reset_password`
--

CREATE TABLE IF NOT EXISTS `tbl_reset_password` (
  `id` bigint(20) NOT NULL,
  `email` varchar(128) CHARACTER SET latin1 NOT NULL,
  `activation_id` varchar(32) CHARACTER SET latin1 NOT NULL,
  `agent` varchar(512) CHARACTER SET latin1 NOT NULL,
  `client_ip` varchar(32) CHARACTER SET latin1 NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  `createdBy` bigint(20) NOT NULL DEFAULT '1',
  `createdDtm` datetime NOT NULL,
  `updatedBy` bigint(20) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_roles`
--

CREATE TABLE IF NOT EXISTS `tbl_roles` (
  `roleId` tinyint(4) NOT NULL COMMENT 'role id',
  `role` varchar(50) NOT NULL COMMENT 'role text'
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_roles`
--

INSERT INTO `tbl_roles` (`roleId`, `role`) VALUES
(1, '管理者'),
(2, 'スタッフ');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_scenario`
--

CREATE TABLE IF NOT EXISTS `tbl_scenario` (
  `id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `title` varchar(1024) NOT NULL,
  `content` text NOT NULL,
  `tree_code` varchar(1024) NOT NULL,
  `group_order` int(11) NOT NULL DEFAULT '1',
  `level` int(11) NOT NULL DEFAULT '0',
  `select_cnt` bigint(20) NOT NULL DEFAULT '0' COMMENT 'シナリオ選択数',
  `child_flag` tinyint(1) NOT NULL DEFAULT '0',
  `view_flag` tinyint(2) NOT NULL DEFAULT '1',
  `create_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` datetime DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `tbl_scenario`
--

INSERT INTO `tbl_scenario` (`id`, `parent_id`, `title`, `content`, `tree_code`, `group_order`, `level`, `select_cnt`, `child_flag`, `view_flag`, `create_date`, `update_date`) VALUES
(1, 0, '製品の仕様・詳細について', '次から選択ください', '0001', 1, 1, 125, 1, 1, '2021-05-19 18:02:10', NULL),
(2, 0, '不具合・修理のご相談', '以下よりお選びください', '0002', 2, 1, 21, 1, 1, '2021-05-19 18:02:14', NULL),
(3, 0, '返品・交換について', '次からお選びください', '0003', 3, 1, 14, 1, 1, '2021-05-19 18:02:17', NULL),
(4, 0, 'ご注文・配送について', '次からお選びください', '0004', 4, 1, 14, 1, 1, '2021-05-19 18:02:19', NULL),
(5, 1, '特定の製品について', '詳細を知りたい製品を次から選択してください', '00010001', 1, 2, 51, 1, 1, '2021-05-19 18:02:34', NULL),
(6, 2, '充電できない', 'モバイルバッテリーが充電できない場合充電ケーブルの不良が原因でモバイルバッテリーの充電ができないことが考えられます。新しい充電ケーブルで充電が可能かご確認ください\r\n', '00020001', 1, 2, 5, 1, 1, '2021-05-19 18:51:31', NULL),
(23, 0, 'その他', '次からお選びください', '0005', 5, 1, 29, 1, 1, '2021-05-19 21:00:59', NULL),
(29, 23, '会員の特典は？', 'オンラインストアでは､会員登録をしていただいた方々へ､様々な特典サービスをご用意しております。詳細は会員特典についてのページをご確認ください｡', '00050001', 1, 2, 5, 0, 1, '2021-05-19 21:03:12', NULL),
(32, 5, 'Anker PowerCore 13000', 'Anker PowerCore 13000の何についてお調べになりたいですか？', '000100010002', 2, 3, 36, 1, 1, '2021-05-23 11:04:42', NULL),
(33, 32, '製品サイズについて', 'サイズは約97.5x 80 x 22mmです', '0001000100020001', 1, 4, 17, 0, 1, '2021-05-23 11:05:31', NULL),
(34, 32, '重さについて', '製品重量は約255gです', '0001000100020002', 2, 4, 2, 0, 1, '2021-05-23 11:05:50', NULL),
(35, 32, '出入力電力について', '入力は5V/2A、出力は5V/3Aです', '0001000100020003', 3, 4, 0, 0, 1, '2021-05-23 11:06:48', NULL),
(36, 32, 'カラーバリエーションについて', 'ブラック、ホワイト、レッド、ブルーの4色です', '0001000100020004', 4, 4, 4, 0, 1, '2021-05-23 11:07:39', NULL),
(37, 32, '付属品について', '・Anker PowerCore 13000\r\n・Micro USBケーブル\r\n・トラベルポーチ\r\n・18ヶ月保証 + 6ヶ月 (Anker会員登録後) ', '0001000100020005', 5, 4, 3, 0, 1, '2021-05-23 11:08:20', NULL),
(38, 32, '取扱説明書が見たい', '以下よりご覧いただけます\r\n\r\nhttps://www.ankerjapan.com/client_info/ANKERJAPAN/view/userweb/pdf/A1275Manual.pdf', '0001000100020006', 6, 4, 3, 0, 1, '2021-05-23 11:09:19', NULL),
(39, 32, 'その他', 'お手数ですがご質問内容を直接入力してください', '0001000100020007', 7, 4, 1, 0, 1, '2021-05-23 11:18:26', NULL),
(40, 5, 'Anker PowerCore 5000', 'Anker PowerCore 5000の何についてお調べになりたいですか？', '000100010003', 3, 3, 0, 1, 1, '2021-05-23 11:22:05', NULL),
(41, 40, '製品サイズについて', '製品サイズは約108 x 33 x 33mmです', '0001000100030001', 1, 4, 0, 0, 1, '2021-05-23 11:22:30', NULL),
(42, 40, '重さについて', '製品重量は約134gです', '0001000100030002', 2, 4, 0, 0, 1, '2021-05-23 11:23:08', NULL),
(43, 40, '出入力電力について', '入力は5V/2A、出力は5V/2Aです', '0001000100030003', 3, 4, 0, 0, 1, '2021-05-23 11:23:36', NULL),
(44, 40, 'カラーバリエーションについて', 'ブラック、ホワイト、ブルー、レッドの4色です', '0001000100030004', 4, 4, 0, 0, 1, '2021-05-23 11:24:01', NULL),
(45, 40, '付属品について', '・Anker PowerCore 5000 モバイルバッテリー\r\n・Micro USBケーブル\r\n・トラベルポーチ\r\n・取扱説明書\r\n・18ヶ月保証 + 6ヶ月 (Anker会員登録後) \r\n・カスタマーサポート', '0001000100030005', 5, 4, 0, 0, 1, '2021-05-23 11:24:35', NULL),
(46, 40, '取扱説明書が見たい', '以下をご覧ください\r\n\r\nhttps://www.ankerjapan.com/client_info/ANKERJAPAN/view/userweb/pdf/A1621Manual.pdf', '0001000100030006', 6, 4, 0, 0, 1, '2021-05-23 11:26:13', NULL),
(47, 40, 'その他', 'お手数ですがご質問内容を直接入力してください', '0001000100030007', 7, 4, 0, 0, 1, '2021-05-23 11:26:31', NULL),
(48, 1, '取扱説明書が見たい', '製品を次から選択ください', '00010002', 2, 2, 12, 1, 1, '2021-05-23 11:28:19', NULL),
(49, 48, 'Anker PowerCore 13000', '以下よりご覧ください\r\n\r\nhttps://www.ankerjapan.com/client_info/ANKERJAPAN/view/userweb/pdf/A1275Manual.pdf', '000100020001', 1, 3, 6, 0, 1, '2021-05-23 11:29:09', NULL),
(50, 48, 'Anker PowerCore 5000', '以下よりご覧ください\r\n\r\nhttps://www.ankerjapan.com/client_info/ANKERJAPAN/view/userweb/pdf/A1621Manual.pdf', '000100020002', 2, 3, 2, 0, 1, '2021-05-23 11:29:33', NULL),
(51, 1, 'フルスピード充電とは？', 'Anker独自技術PowerIQとVoltageBoostによる高速充電技術です', '00010003', 3, 2, 9, 0, 1, '2021-05-23 11:31:42', NULL),
(52, 1, 'PowerIQとは？', 'PowerIQは、Anker独自の急速充電テクノロジーで、AnkerのモバイルバッテリーやUSB急速充電器に搭載されています。このテクノロジーのスマートな点は、iPhoneやGalaxy、Kindleをはじめ、ほぼ全てのスマートフォンやタブレット等に対応できるように設計されているところ。PowerIQを搭載したモバイルバッテリーやUSB充電器にはスマート充電チップが組み込まれており、このチップが接続された機器を即座に認識し、その機器に適した最大のスピードでの急速充電を可能にしています。\r\n', '00010004', 4, 2, 3, 0, 1, '2021-05-23 11:35:40', NULL),
(53, 1, 'MultiProtectとは？', 'モバイルバッテリーはスマートフォンなどの機器へ給電を行うために、内部に電力を蓄えるバッテリーセルを搭載しています。言い換えれば、モバイルバッテリーを持ち歩くということは、大きなエネルギーを持ち運ぶということです。だからこそAnkerでは、お客様が安心かつ安全に充電を行えるよう、モバイルバッテリーやUSB急速充電器にMultiProtect (多重保護機能) と呼ばれるAnker独自の保護機能パッケージを採用しています。このMultiProtectは、サージプロテクターや温度管理、ショート防止をはじめとした11個の保護システムから成り立っており、モバイルバッテリーやUSB急速充電器を長時間使用しても高い安全性を維持できるよう徹底的に調査、開発がなされています。', '00010005', 5, 2, 1, 0, 1, '2021-05-23 11:37:15', NULL),
(54, 6, '充電ケーブルを変えても充電できない', 'モバイルバッテリーを長期間使っていると劣化してきます。モバイルバッテリーの充電回数は、約300〜500回ぐらいです。1日に1回、モバイルバッテリーで充電したとすると約1年〜1年半ぐらいが寿命となります。\r\n\r\n上記にも当てはまらない場合は「18ヶ月製品長期保証」の対象となる場合、同一新品製品との交換を行わせていただきます。\r\nお手数ですが以下よりお問合せをお願い致します\r\n\r\nhttps://sapporo.wixanswers.com/contact', '000200010001', 1, 3, 3, 0, 1, '2021-05-23 11:43:54', NULL),
(55, 6, '充電ケーブルを変えたら充電できた', '充電ケーブルに不具合が考えられます。', '000200010002', 2, 3, 2, 0, 1, '2021-05-23 11:45:28', NULL),
(56, 2, '充電がすぐ無くなる', 'モバイルバッテリーを長期間使っていると劣化してきます。モバイルバッテリーの充電回数は、約300〜500回ぐらいです。1日に1回、モバイルバッテリーで充電したとすると約1年〜1年半ぐらいが寿命となります。\r\n\r\n充電回数も少なく、モバイルバッテリーの故障が考えられる場合は弊社サポートセンターまでご相談をお願い致します\r\nhttps://sapporo.wixanswers.com/contact', '00020002', 2, 2, 4, 0, 1, '2021-05-23 11:47:44', NULL),
(57, 2, '不具合製品の修理をしてほしい', '弊社の全製品には18ヶ月間の製品保証がついておりますので、その期間内にお買い上げの製品に不具合があった場合、カスタマーサポートにお問い合わせください。弊社が不具合であると確認した後、迅速に同一新品製品との交換対応をお承り致します。\r\n\r\n【お問合せ先】\r\nhttps://sapporo.wixanswers.com/contact', '00020003', 3, 2, 3, 0, 1, '2021-05-23 11:49:15', NULL),
(58, 2, 'その他', 'お手数ですがご質問内容を直接入力してください', '00020004', 4, 2, 1, 0, 1, '2021-05-23 13:42:45', NULL),
(59, 3, '間違った製品を購入してしまった', 'ご注文日から30日以内に、メールやお電話にてカスタマーサポートにお問い合わせください。\r\n\r\nカスタマーサポートより、ご返品の方法についてご案内致します。\r\n製品返品のための返送送料をお客様にてご負担いただくことを条件に､製品代金の全額を返金致します。\r\n\r\nただし、お客様のご判断により着払い等でご返送いただいた場合、弊社では返品をお受けとり致しかねます。あらかじめご了承ください。\r\n\r\n【お問合せ】\r\nhttps://sapporo.wixanswers.com/contact', '00030001', 1, 2, 2, 0, 1, '2021-05-23 13:46:22', NULL),
(60, 3, '開封した製品の返品について', 'ご注文日から30日以内に、メールやお電話にてカスタマーサポートにお問い合わせください。\r\nカスタマーサポートより、ご返品の方法についてご案内致します。\r\n製品返品のための返送送料をお客様にてご負担いただくことを条件に､製品代金の全額を返金致します。\r\n\r\nただし、お客様のご判断により着払い等でご返送いただいた場合、弊社では返品をお受けとり致しかねます。あらかじめご了承ください。\r\n\r\n【お問合せ】\r\nhttps://sapporo.wixanswers.com/contact', '00030002', 2, 2, 2, 0, 1, '2021-05-23 13:47:43', NULL),
(61, 3, '返品の手続き方法について', '電子メールもしくはお電話でカスタマーサポートにお問い合わせください。\r\nカスタマーサポートより、ご返品の方法についてご案内致します。\r\n\r\n【お問合せ】\r\nhttps://sapporo.wixanswers.com/contact', '00030003', 3, 2, 0, 0, 1, '2021-05-23 13:49:05', NULL),
(62, 3, '外箱が破損していた', 'カスタマーサポートにお問い合わせください。お客様に到着した際に箱がつぶれていた場合、迅速に同一新品製品との交換対応をお承り致します。\r\n\r\n【お問合せ】\r\nhttps://sapporo.wixanswers.com/contact', '00030004', 4, 2, 3, 0, 1, '2021-05-23 13:50:00', NULL),
(63, 3, '届いた製品に傷がついていた', '全製品には18ヶ月間の製品保証がついておりますので、その期間内にお買い上げの製品に不具合があった場合、カスタマーサポートにお問い合わせください。弊社が不具合であると確認した後、迅速に同一新品製品との交換対応をお承り致します。\r\n\r\n【お問合せ】\r\nhttps://sapporo.wixanswers.com/contact', '00030005', 5, 2, 5, 0, 1, '2021-05-23 13:50:52', NULL),
(64, 3, '海外で購入した製品の交換、修理について', '海外で購入した製品を日本で交換/返品することはできません。ご購入元の店舗様宛にお問い合わせください。', '00030006', 6, 2, 1, 0, 1, '2021-05-23 13:51:26', NULL),
(65, 3, 'その他', 'お手数ですがご質問内容を直接入力してください', '00030007', 7, 2, 0, 0, 1, '2021-05-23 13:51:57', NULL),
(66, 4, '注文のキャンセルについて', '誠に申し訳ございませんが、原則としてご注文後発送手配が完了している場合､お客様のご都合によるご注文のキャンセルはお受け致しかねます｡', '00040001', 1, 2, 7, 0, 1, '2021-05-23 13:52:59', NULL),
(67, 4, '注文後の注文内容の変更', '誠に申し訳ございませんが、注文確定後のお客様のご都合によるご注文内容の変更・配送先のご変更は承っておりません｡', '00040002', 2, 2, 2, 0, 1, '2021-05-23 13:53:39', NULL),
(68, 4, '支払い方法の変更', '注文確定後のお支払いの変更はできません', '00040003', 3, 2, 0, 0, 1, '2021-05-23 13:54:08', NULL),
(69, 4, '配送先の変更', '誠に申し訳ございませんが、注文確定後のお客様のご都合による配送先のご変更はできません。ご了承ください。', '00040004', 4, 2, 2, 0, 1, '2021-05-23 13:54:29', NULL),
(70, 4, '支払い方法について', 'お支払方法は､以下のいずれかよりお選びいただけます｡\r\n・クレジットカード\r\n・Amazonペイメント\r\n・atone 翌月後払い (コンビニ)', '00040005', 5, 2, 0, 0, 1, '2021-05-23 13:56:34', NULL),
(71, 4, 'この中にない', '以下にお探しのご質問はございますか？', '00040006', 6, 2, 1, 1, 1, '2021-05-23 13:57:48', NULL),
(72, 71, '送料はかかりますか', 'ご購入金額が4000円未満の場合､送料540円(税込)が必要となります｡また､ご購入金額が4000円以上のお客様は､送料無料となります｡', '000400060001', 1, 3, 0, 0, 1, '2021-05-23 13:58:20', NULL),
(73, 71, '配送業者はどこですか？', '発送業務はアマゾン・ジャパン合同会社様へ外部委託を行っております。\r\n\r\n配送業者はデリバリープロバイダ､ヤマト運輸、日本郵便等（配送業者様は適宜異なる場合がございます。）\r\n\r\nアマゾン・ジャパン合同会社様のシステム仕様により、沖縄県の一部の住所には配送できない場合がございます。その場合につきましては、キャンセル対応とさせていただきます。 コンビニエンスストアまたは、配送業者局留めでのお受け取りはできません。お受け取りできるご自宅等のご住所にてご注文ください。', '000400060002', 2, 3, 0, 0, 1, '2021-05-23 13:59:03', NULL),
(74, 71, '配送日時の指定はできますか？', '誠に申し訳ございませんが、指定することはできません｡', '000400060003', 3, 3, 0, 0, 1, '2021-05-23 13:59:25', NULL),
(75, 71, '何日で届きますか？', '入金確認後1〜7営業日でお届け致します｡ただし､配送状況により上記日程を超えてのお届けになる場合もございます｡', '000400060004', 4, 3, 0, 0, 1, '2021-05-23 14:00:01', NULL),
(76, 71, 'ギフトラッピングはできますか？', '誠に申し訳ございませんが、ご指定は承っておりません｡', '000400060005', 5, 3, 0, 0, 1, '2021-05-23 14:00:44', NULL),
(77, 71, '複数製品の同梱指定は可能ですか？', '誠に申し訳ございませんが、ご指定は承っておりません｡', '000400060006', 6, 3, 0, 0, 1, '2021-05-23 14:01:03', NULL),
(78, 71, 'その他', 'お手数ですがご質問内容を直接入力してください', '000400060007', 7, 3, 1, 0, 1, '2021-05-23 14:01:29', NULL),
(79, 23, 'オペレーターと話したい', '回答はお役に立ちましたか？', '00050002', 2, 2, 7, 0, 1, '2021-05-23 14:03:31', NULL),
(80, 23, '問合せ先電話番号が知りたい', '03-4455-7823\r\n\r\n【受付時間】\r\n月～金 9:00～17:00 祝日・お盆・年末年始を除く', '00050003', 3, 2, 3, 0, 1, '2021-05-23 14:04:25', NULL),
(82, 23, '回答はお役に立ちましたか？', '回答はお役に立ちましたか？', '00050004', 4, 2, 1, 0, 1, '2021-05-25 22:51:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_search`
--

CREATE TABLE IF NOT EXISTS `tbl_search` (
  `search_text` varchar(128) NOT NULL,
  `search_cnt` int(11) NOT NULL DEFAULT '1',
  `search_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_search`
--

INSERT INTO `tbl_search` (`search_text`, `search_cnt`, `search_time`) VALUES
('anker', 4, '2021-05-24 10:58:04'),
('Power', 1, '2021-05-24 11:02:21'),
('電力', 1, '2021-05-24 15:49:31'),
('壊れていた', 1, '2021-05-24 15:49:58'),
('フルスピード充電', 1, '2021-05-24 15:50:17'),
('配送先を変えたい', 1, '2021-05-24 15:51:10'),
('修理したい', 2, '2021-05-24 15:52:43'),
('重量', 1, '2021-05-24 15:57:56'),
('配送先　住所　変更', 1, '2021-05-24 15:58:45'),
('オペレーターと話したい', 2, '2021-05-24 15:59:27'),
('傷ついていた　交換', 1, '2021-05-24 16:00:08'),
('おはよう', 10, '2021-05-24 16:01:58'),
('バッテリの充電', 1, '2021-05-24 16:02:11'),
('こんにちは', 7, '2021-05-24 16:20:35'),
('こんにちわ', 7, '2021-05-24 16:31:40'),
('hi', 8, '2021-05-24 16:44:22'),
('12345678', 1, '2021-05-24 16:49:58'),
('１２３４５６７８', 1, '2021-05-24 17:32:23'),
('修理をしたい', 1, '2021-05-24 17:55:31'),
('返品したい', 1, '2021-05-24 19:03:48'),
('壊れた', 1, '2021-05-24 19:36:03'),
('充電できない', 3, '2021-05-24 19:36:47'),
('充電が遅い', 1, '2021-05-24 19:37:40'),
('充電すえうのが遅くなった', 1, '2021-05-24 19:37:57'),
('1ヵ月しか使っていないのに、チャージが遅い', 1, '2021-05-24 19:38:36'),
('充電時間が遅い', 1, '2021-05-24 19:38:59'),
('商品が届かない', 5, '2021-05-24 19:39:18'),
('カスタマーサポートに電話したい', 1, '2021-05-24 19:40:03'),
('テスト送信', 1, '2021-05-24 22:47:10'),
('１２３', 3, '2021-05-24 22:47:20'),
('ｗｗｗｗｗｗｗｗ', 1, '2021-05-24 22:47:30'),
('ｗくぇｗ', 1, '2021-05-24 22:48:58'),
('123', 3, '2021-05-25 11:43:49'),
('はじめから', 1, '2021-05-25 18:54:52'),
('回答はお役に立ちましたか？', 2, '2021-05-25 18:56:16'),
('最初から', 1, '2021-05-25 18:59:29'),
('電話したい', 2, '2021-05-25 19:01:25'),
('人と話したい', 8, '2021-05-25 19:06:13'),
('１２', 2, '2021-05-25 22:10:09'),
('こんばんわ', 1, '2021-05-25 22:57:13'),
('こんばん', 1, '2021-05-25 22:59:39'),
('おは', 1, '2021-05-25 22:59:51'),
('hi, hello', 1, '2021-05-25 23:03:21'),
('電話番号', 1, '2021-05-25 23:04:50'),
('営業時間', 4, '2021-05-25 23:05:09'),
('おいっ', 1, '2021-05-25 23:12:34'),
('おい', 2, '2021-05-25 23:12:41'),
('アホ', 1, '2021-05-25 23:15:17'),
('oi', 1, '2021-05-25 23:25:05'),
('分割払いへの変更は可能？', 1, '2021-05-26 09:11:59'),
('ばか', 1, '2021-05-26 10:43:59'),
('おおい', 1, '2021-05-26 10:45:00'),
('充電器がばかになっている', 1, '2021-05-26 10:45:15'),
('おやすみ', 1, '2021-05-26 10:51:03'),
('こんばんは', 1, '2021-05-28 02:56:56'),
('あああ', 1, '2021-05-28 07:08:52'),
('パスワードを忘れてしまった', 1, '2021-05-28 07:10:54'),
('パスワード忘れた', 1, '2021-05-28 07:11:14'),
('パスワード忘れ', 2, '2021-05-28 07:11:37'),
('営業時間は', 1, '2021-05-28 11:41:36'),
('あいうえお', 1, '2021-05-28 12:04:54'),
('あああああああああああああああああああああああああ', 1, '2021-05-28 12:05:25'),
('おおおおおおおおおおおおおおおおおおおおおお', 1, '2021-05-28 12:05:34'),
('ははは', 1, '2021-05-28 12:05:39'),
('価格', 1, '2021-05-28 12:05:51'),
('操作方法が分からない', 1, '2021-05-28 12:39:49'),
('不具合で使えない', 1, '2021-05-28 12:40:36'),
('不具合', 1, '2021-05-28 12:47:23'),
('あ', 1, '2021-05-28 13:04:38'),
('qweqw', 2, '2021-05-29 11:22:03'),
('こんにちは今日は何のひ？', 1, '2021-05-29 12:49:53'),
('おはようおはよう', 2, '2021-05-29 14:25:39'),
('商品に傷がついてた', 2, '2021-05-29 14:25:59'),
('グレー色はないですか？', 2, '2021-05-29 14:27:38'),
('もう1点', 3, '2021-06-02 14:07:35'),
('聞きたいことがあります', 3, '2021-06-02 14:07:42'),
('寿命は？', 2, '2021-06-10 17:19:16'),
('製品に傷がついていた', 2, '2021-06-10 17:20:16'),
('箱が壊れていた', 2, '2021-06-10 17:20:50'),
('powerhouse', 3, '2021-06-10 17:52:30'),
('モバイルバッテリー', 3, '2021-06-10 17:52:45'),
('フルスピード充電とは？', 2, '2021-06-10 17:55:52'),
('今日も暑いですね', 3, '2021-06-10 17:56:02'),
('紛失', 2, '2021-06-14 20:42:02'),
('カードなくした', 2, '2021-06-14 20:43:16'),
('カード見つからない', 2, '2021-06-14 20:43:40'),
('カード　いつ届く？', 2, '2021-06-14 20:44:03'),
('オリコ　キャッシング　手数料', 2, '2021-06-14 20:44:37'),
('オペレーター', 2, '2021-06-14 20:46:04'),
('明細書　有料化　費用', 2, '2021-06-14 20:48:11'),
('ご利用代金明細書　有料化', 2, '2021-06-14 20:49:02'),
('ふぁｓｊｋｄｆ；あｓｌｋｄｊｆ；ぁｓｄｊｆ；ぁ', 2, '2021-06-14 20:49:30'),
('利用代金明細書　有料化', 2, '2021-06-14 20:51:06'),
('カード　紛失', 2, '2021-06-14 21:00:15'),
('キャッシング手数料', 2, '2021-06-14 21:02:44'),
('カードを無くした', 2, '2021-06-15 09:14:26'),
('カードを落としてしまった', 2, '2021-06-15 09:16:23'),
('カード　届くまで　時間', 2, '2021-06-15 09:17:09'),
('カードを紛失', 2, '2021-06-15 11:29:57'),
('ご利用代金明細書　費用', 4, '2021-06-15 11:32:21'),
('明細書', 2, '2021-06-15 11:36:01');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_setting`
--

CREATE TABLE IF NOT EXISTS `tbl_setting` (
  `id` int(11) NOT NULL,
  `key_name` varchar(255) NOT NULL,
  `key_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbl_setting`
--

INSERT INTO `tbl_setting` (`id`, `key_name`, `key_value`) VALUES
(1, 'work_start', '7'),
(2, 'work_end', '24');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE IF NOT EXISTS `tbl_users` (
  `userId` int(11) NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'login email',
  `password` varchar(128) CHARACTER SET utf8 NOT NULL COMMENT 'hashed login password',
  `name` varchar(1024) CHARACTER SET utf8 DEFAULT NULL COMMENT 'full name of user',
  `mobile` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `roleId` tinyint(4) NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  `createdBy` int(11) NOT NULL,
  `createdDtm` datetime NOT NULL,
  `updatedBy` int(11) DEFAULT NULL,
  `updatedDtm` datetime DEFAULT NULL
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`userId`, `email`, `password`, `name`, `mobile`, `roleId`, `isDeleted`, `createdBy`, `createdDtm`, `updatedBy`, `updatedDtm`) VALUES
(1, 'admin@example.com', '$2y$10$z7aiNveI4ebBr/st27oSuemwKs9pNoyyLkb/Os7/yKjrmZSKgY7d2', 'システム管理者', '0000000000', 1, 0, 0, '2015-07-01 18:56:49', 1, '2021-05-20 08:09:39'),
(2, 'user1@example.com', '$2y$10$FLHUy.v2QKHRV3aG7OWx9eqiXagKUjNOp/KxQL1Sgd7ATU9ulFWGS', 'ユーザー1', '9890098900', 2, 0, 1, '2016-12-09 17:49:56', 1, '2021-05-20 03:36:38'),
(3, 'staff1@example.com', '$2y$10$rN5humYKvUMGmlkWOwrcguSkXjnP2X5SV0XOpkPLiR9bNYwqZpKva', 'スタッフ１', '9890098900', 2, 0, 1, '2016-12-09 17:50:22', 1, '2021-05-20 17:50:55'),
(9, 'mogawa@proz.jp', '$2y$10$V2XQX1ut4PKrUwvhJsM0/O4QF1lbtdkESfCFOOTYYzWIGBi/67Lg.', '小川', '0357844515', 2, 0, 1, '2021-05-24 09:31:38', NULL, NULL),
(10, 'na@proz.jp', '$2y$10$XGM0mKe17STXPDoefmmrfuUr86TolP84W52SA6XkTmu.GDXHz9gia', 'na', '7015221100', 2, 0, 1, '2021-05-29 12:47:27', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `last_activity_idx` (`last_activity`);

--
-- Indexes for table `tbl_last_login`
--
ALTER TABLE `tbl_last_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_reset_password`
--
ALTER TABLE `tbl_reset_password`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  ADD PRIMARY KEY (`roleId`);

--
-- Indexes for table `tbl_scenario`
--
ALTER TABLE `tbl_scenario`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_last_login`
--
ALTER TABLE `tbl_last_login`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=45;
--
-- AUTO_INCREMENT for table `tbl_reset_password`
--
ALTER TABLE `tbl_reset_password`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_roles`
--
ALTER TABLE `tbl_roles`
  MODIFY `roleId` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT 'role id',AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `tbl_scenario`
--
ALTER TABLE `tbl_scenario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=83;
--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `userId` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
