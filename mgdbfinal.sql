-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 27, 2025 at 01:53 PM
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
-- Database: `mgdbfinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `blocked_slots`
--

CREATE TABLE `blocked_slots` (
  `id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hour` time NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `name`) VALUES
(5, 'CPU'),
(1, 'Del Carmen'),
(2, 'Lapaz'),
(4, 'Lapuz'),
(3, 'Mandurriao');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `reservation_code` varchar(255) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `customer_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `branch` varchar(50) DEFAULT NULL,
  `reservation_date` date DEFAULT NULL,
  `reservation_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `reservation_code`, `rating`, `comment`, `created_at`, `customer_name`, `email`, `contact_number`, `branch`, `reservation_date`, `reservation_time`) VALUES
(8, 'RES689d4911abb52', 2, 'ads', '2025-08-14 02:25:33', NULL, 'deopetj@gmail.com', '09465170575', 'Mandurriao', '2025-08-22', '09:00:00'),
(9, 'RES68a2623195505', 5, 'asdad', '2025-08-17 23:33:42', NULL, 'deopetj@gmail.com', '09465170575', 'Lapaz', '2025-08-19', '09:00:00'),
(10, 'RES68a2b60358622', 1, ';kjjk', '2025-08-18 05:12:51', NULL, 'admin', '09217017064', 'Lapaz', '2025-08-18', '16:30:00'),
(11, 'RES68c2cd9772712', 2, 'oks lang', '2025-09-11 13:25:09', NULL, 'deopetj@gmail.com', '09465170575', 'Lapaz', '2025-09-24', '10:30:00'),
(12, 'RES68df1096d5132', 1, '', '2025-10-02 23:54:25', NULL, 'salaniogeran@gmail.com', '09300243114', 'CPU', '2025-10-06', '12:00:00'),
(13, 'RES68ef412ac3b25', 3, 'nice', '2025-10-15 06:38:36', NULL, 'deopett@gmaill.com', '09112345678', 'Del Carmen', '2025-10-17', '09:00:00'),
(14, 'RES68ef441f2e4de', 5, 'nice', '2025-10-15 06:50:29', NULL, 'deopetjoteaaa@gmail.com', '09122345671', 'Del Carmen', '2025-10-17', '09:00:00'),
(15, 'RES68fb824a15612', 1, 'very good\r\n', '2025-10-24 13:43:57', NULL, 'finalexam@gmail.com', '09639882679', 'Lapuz', '2025-10-24', '19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `table_number` int(11) NOT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `branch` varchar(50) NOT NULL,
  `status` enum('Pending','Approved','Declined','Cancelled') NOT NULL DEFAULT 'Pending',
  `reserv_date` date DEFAULT NULL,
  `reserv_time` time DEFAULT NULL,
  `amount` int(11) NOT NULL DEFAULT 100,
  `proof_of_payment` varchar(255) NOT NULL,
  `qr_code` varchar(255) DEFAULT NULL,
  `reservation_code` varchar(255) NOT NULL,
  `slot_end_time` datetime DEFAULT NULL,
  `release_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`user_id`, `email`, `contact_number`, `table_number`, `message`, `created_at`, `branch`, `status`, `reserv_date`, `reserv_time`, `amount`, `proof_of_payment`, `qr_code`, `reservation_code`, `slot_end_time`, `release_time`) VALUES
(71, 'deopetj@gmail.com', '9465170575', 2, '', '2025-04-28 11:00:54', 'Lapaz', 'Cancelled', '2025-05-01', '09:00:00', 100, 'proofs/Screenshot 2024-11-01 143053.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES680f5fe68fffb.svg', 'RES680f5fe68fffb', NULL, NULL),
(72, 'deopetj@gmail.com', '09465170575', 3, '', '2025-04-28 11:04:08', 'CPU', 'Approved', '2025-10-29', '13:00:00', 100, 'proofs/Screenshot 2024-08-23 193807.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES680f60a86a797.svg', 'RES680f60a86a797', NULL, NULL),
(74, 'deopetj@gmail.com', '09465170575', 5, '', '2025-04-28 23:49:25', 'Mandurriao', 'Approved', '2025-04-30', '19:00:00', 100, 'proofs/Screenshot 2025-01-06 091730.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68101405a45d5.svg', 'RES68101405a45d5', NULL, NULL),
(75, 'deopetj@gmail.com', '09465170575', 4, '', '2025-04-29 12:01:18', 'Mandurriao', 'Approved', '2025-05-03', '09:00:00', 100, 'proofs/Screenshot 2024-06-25 152545.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES6810bf8e62851.svg', 'RES6810bf8e62851', NULL, NULL),
(77, 'deopetj@gmail.com', '09465170575', 2, '', '2025-05-01 12:29:04', 'Lapaz', 'Approved', '2025-05-02', '09:00:00', 100, 'proofs/Screenshot 2024-09-17 151036.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68136910747b0.svg', 'RES68136910747b0', NULL, NULL),
(78, 'deopetj@gmail.com', '09465170575', 1, '', '2025-05-02 13:03:22', 'Mandurriao', 'Approved', '2025-05-03', '09:00:00', 100, 'proofs/Screenshot 2024-07-13 220329.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES6814c29a9b9bd.svg', 'RES6814c29a9b9bd', NULL, NULL),
(79, 'deopetj@gmail.com', '09465170575', 3, '', '2025-05-02 13:07:18', 'Del Carmen', 'Pending', '2025-05-03', '18:00:00', 100, 'proofs/Screenshot 2024-07-28 222728.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES6814c3861d34b.svg', 'RES6814c3861d34b', NULL, NULL),
(81, 'deopetj@gmail.com', '09465170575', 4, '', '2025-05-02 13:41:45', 'Lapuz', 'Pending', '2025-05-06', '10:00:00', 100, 'proofs/Screenshot 2024-08-23 193807.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES6814cb99c2bdb.svg', 'RES6814cb99c2bdb', NULL, NULL),
(82, 'deopetj@gmail.com', '09465170575', 4, '', '2025-05-03 13:12:37', 'Lapaz', 'Pending', '2025-05-15', '09:00:00', 100, 'proofs/Screenshot 2024-09-17 155007.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES6816164495fa2.svg', 'RES6816164495fa2', NULL, NULL),
(83, 'deopetj@gmail.com', '09465170575', 1, '', '2025-05-03 13:19:31', 'Mandurriao', 'Pending', '2025-05-03', '16:00:00', 100, 'proofs/IMG_8769.jpeg', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES681617e3742c8.svg', 'RES681617e3742c8', NULL, NULL),
(84, 'deopetj@gmail.com', '09465170575', 4, '', '2025-05-03 13:27:54', 'Mandurriao', 'Cancelled', '2025-05-30', '09:00:00', 100, 'proofs/Screenshot 2024-07-23 101911.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES681619dad2035.svg', 'RES681619dad2035', NULL, NULL),
(85, 'deopetj@gmail.com', '09465170575', 1, '', '2025-05-03 13:51:26', 'Lapuz', 'Approved', '2025-05-09', '09:00:00', 100, 'proofs/Screenshot 2024-12-15 214722.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68161f5dee541.svg', 'RES68161f5dee541', NULL, NULL),
(86, 'deopetj@gmail.com', '09465170575', 1, '', '2025-05-03 13:52:29', 'Mandurriao', 'Approved', '2025-05-24', '12:00:00', 100, 'proofs/Screenshot 2024-07-13 221147.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68161f9d30f58.svg', 'RES68161f9d30f58', NULL, NULL),
(87, 'deopetj@gmail.com', '09465170575', 2, '', '2025-05-03 13:53:01', 'CPU', 'Approved', '2025-05-10', '09:00:00', 100, 'proofs/Screenshot 2024-06-11 074149.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68161fbcf1ff1.svg', 'RES68161fbcf1ff1', NULL, NULL),
(88, 'deopetj@gmail.com', '09465170575', 2, '', '2025-05-03 13:53:16', 'Mandurriao', 'Approved', '2025-05-03', '09:00:00', 100, 'proofs/Screenshot 2024-07-22 220041.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68161fccbec15.svg', 'RES68161fccbec15', NULL, NULL),
(89, 'deopetj@gmail.com', '09465170575', 3, '', '2025-05-04 13:07:33', 'Mandurriao', 'Approved', '2025-05-08', '09:00:00', 100, 'proofs/Screenshot 2024-07-28 222938.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES681766945e9f9.svg', 'RES681766945e9f9', NULL, NULL),
(90, 'deopetj@gmail.com', '09465170575', 1, '', '2025-05-05 00:34:52', 'Del Carmen', 'Cancelled', '2025-05-10', '10:30:00', 100, 'proofs/Screenshot 2024-08-27 211054.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES681807ac0984f.svg', 'RES681807ac0984f', NULL, NULL),
(91, 'deopetj@gmail.com', '09465170575', 4, '', '2025-08-13 13:16:43', 'Mandurriao', 'Approved', '2025-08-14', '09:00:00', 100, 'proofs/banner2.jpg', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES689c9039dbb35.svg', 'RES689c9039dbb35', NULL, NULL),
(92, 'deopetj@gmail.com', '09465170575', 5, '', '2025-08-14 02:25:23', 'Mandurriao', 'Pending', '2025-08-22', '09:00:00', 100, 'proofs/WIN_20250217_12_23_27_Pro.jpg', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES689d4911abb52.svg', 'RES689d4911abb52', NULL, NULL),
(93, 'deopetj@gmail.com', '09465170575', 1, '', '2025-08-14 12:59:16', 'Lapuz', 'Approved', '2025-08-15', '10:30:00', 100, 'proofs/WIN_20250217_12_23_47_Pro.jpg', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES689ddda4b86b6.svg', 'RES689ddda4b86b6', NULL, NULL),
(94, 'deopetj@gmail.com', '09465170575', 1, '', '2025-08-15 15:40:32', 'Del Carmen', 'Approved', '2025-08-15', '09:00:00', 100, 'proofs/Screenshot 2024-07-13 221147.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES689f54eebccb8.svg', 'RES689f54eebccb8', NULL, NULL),
(95, 'deopetj@gmail.com', '09465170575', 1, 'asdadada', '2025-08-15 15:44:49', 'Del Carmen', 'Approved', '2025-08-15', '10:30:00', 100, 'proofs/Screenshot 2024-07-22 220041.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES689f55f16d1d5.svg', 'RES689f55f16d1d5', NULL, NULL),
(96, 'deopetj@gmail.com', '09465170575', 1, '', '2025-08-17 23:13:54', 'Lapaz', 'Pending', '2025-08-19', '09:00:00', 100, 'proofs/banner2.jpg', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68a2623195505.svg', 'RES68a2623195505', NULL, NULL),
(97, 'admin', '09217017064', 1, 'for vip', '2025-08-18 05:11:32', 'Lapaz', 'Pending', '2025-08-18', '16:30:00', 100, 'proofs/Screenshot 2025-08-12 213947.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68a2b60358622.svg', 'RES68a2b60358622', NULL, NULL),
(98, 'deopetj@gmail.com', '09465170575', 2, '', '2025-08-18 05:36:56', 'Del Carmen', 'Approved', '2025-08-21', '12:00:00', 100, 'proofs/Screenshot 2024-06-25 152545.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68a2bbf8b30b9.svg', 'RES68a2bbf8b30b9', NULL, NULL),
(99, 'deopetj@gmail.com', '09465170575', 2, '', '2025-08-18 09:06:20', 'Del Carmen', 'Approved', '2025-08-19', '09:00:00', 100, 'proofs/Screenshot 2024-07-13 221147.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68a2ed0c02a38.svg', 'RES68a2ed0c02a38', NULL, NULL),
(100, 'deopetj@gmail.com', '09465170575', 2, '', '2025-08-25 13:51:04', 'Lapaz', 'Pending', '2025-08-27', '09:10:00', 100, 'proofs/Screenshot 2024-07-13 220329.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68ac6a46e3a81.svg', 'RES68ac6a46e3a81', NULL, NULL),
(103, 'deopetj@gmail.com', '09465170575', 1, '', '2025-09-05 03:26:59', 'Lapaz', 'Approved', '2025-09-06', '09:00:00', 100, 'proofs/Screenshot 2024-07-22 214931.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68ba5882cd8c4.svg', 'RES68ba5882cd8c4', NULL, NULL),
(105, 'deopetj@gmail.coms', '09465170543', 3, '', '2025-09-07 14:29:50', 'Del Carmen', 'Approved', '2025-09-09', '12:00:00', 100, '', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68bd96dcdc178.svg', 'RES68bd96dcdc178', NULL, NULL),
(106, 'deopetj@gmail.com', '09465170575', 1, '', '2025-09-10 14:16:32', 'Lapaz', 'Pending', '2025-09-13', '13:30:00', 100, 'proofs/Screenshot 2024-09-17 144906.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68c1883f8bdd0.svg', 'RES68c1883f8bdd0', NULL, NULL),
(107, 'deopetj@gmail.com', '09465170575', 1, '', '2025-09-11 13:24:40', 'Lapaz', 'Pending', '2025-09-24', '10:30:00', 100, 'proofs/IMG_2746.jpeg', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68c2cd9772712.svg', 'RES68c2cd9772712', NULL, NULL),
(108, 'deopetj@gmail.com', '09465170575', 1, '', '2025-09-14 14:04:07', 'Del Carmen', 'Pending', '2025-09-23', '19:30:00', 100, '', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68c6cb55750ac.svg', 'RES68c6cb55750ac', NULL, NULL),
(109, 'jotea@gmail.com', '09123456789', 3, '', '2025-09-14 14:06:56', 'Mandurriao', 'Declined', '2025-09-30', '09:00:00', 100, 'proofs/IMG_2869.png', 'C:\\xampp\\htdocs\\mariagraciasfinal1/qr_codes/RES68c6cc00ad787.svg', 'RES68c6cc00ad787', NULL, NULL),
(110, 'deopetj@gmail.com', '09465170575', 2, '', '2025-09-22 14:44:42', 'Del Carmen', 'Approved', '2025-09-24', '10:30:00', 100, 'proofs/IMG_2723.JPG', NULL, 'RES68d160dadaaba', NULL, NULL),
(111, 'salaniogeran@gmail.com', '09300243114', 2, '', '2025-10-02 23:53:58', 'CPU', 'Approved', '2025-10-06', '12:00:00', 100, 'proofs/att.up5CefuPmTNCpQPqpWh5kPGtNmp0obNiL7Wn150XgIg.jpeg', NULL, 'RES68df1096d5132', NULL, NULL),
(112, 'deopetj@gmail.com', '09465170575', 4, '', '2025-10-04 14:00:12', 'Del Carmen', 'Declined', '2025-10-08', '15:00:00', 100, 'proofs/pngtree-avatar-icon-profile-icon-member-login-vector-isolated-png-image_5247852.jpg', NULL, 'RES68e1286c4da42', NULL, NULL),
(113, 'deopett@gmaill.com', '09112345678', 4, '', '2025-10-15 06:37:30', 'Del Carmen', 'Approved', '2025-10-17', '09:00:00', 100, 'proofs/IMG_3765.png', NULL, 'RES68ef412ac3b25', NULL, NULL),
(114, 'deopetjoteaaa@gmail.com', '09122345671', 1, '', '2025-10-15 06:50:07', 'Del Carmen', 'Approved', '2025-10-17', '09:00:00', 100, 'proofs/IMG_3769.png', NULL, 'RES68ef441f2e4de', NULL, NULL),
(115, 'deopetj@gmail.com', '09465170575', 2, '', '2025-10-20 13:56:27', 'Lapaz', 'Approved', '2025-10-25', '09:00:00', 100, 'proofs/deepseek_mermaid_20251011_b62112.png', NULL, 'RES68f63f8b2d35a', NULL, NULL),
(116, 'deopetj@gmail.com', '09465170575', 2, '', '2025-10-23 15:09:57', 'Lapaz', 'Pending', '2025-10-29', '15:00:00', 100, 'proofs/deepseek_mermaid_20251010_32928f.png', NULL, 'RES68fa4545b7b66', NULL, NULL),
(117, 'finalexam@gmail.com', '09639882679', 4, '', '2025-10-24 13:42:34', 'Lapuz', 'Pending', '2025-10-24', '19:00:00', 100, 'proofs/banner2.jpg', NULL, 'RES68fb824a15612', NULL, NULL),
(118, 'deopetj@.comgmails', '09465111575', 1, '', '2025-10-24 18:16:14', 'Del Carmen', 'Pending', '2025-10-26', '09:00:00', 100, '', NULL, 'RES68fbc26e8a9f8', NULL, NULL),
(119, 'deopetj@.comgmails', '09465111575', 1, '', '2025-10-24 18:16:14', 'Del Carmen', 'Approved', '2025-10-26', '09:00:00', 100, 'proofs/Screenshot 2024-09-13 204508.png', NULL, 'RES68fbc26ea9944', NULL, NULL),
(121, '', '', 1, NULL, '2025-10-25 13:15:55', 'Del Carmen', 'Pending', '2025-10-25', '09:00:00', 100, '', NULL, '', NULL, NULL),
(122, '', '', 1, NULL, '2025-10-25 13:27:11', 'Del Carmen', 'Pending', '2025-10-25', '10:00:00', 100, '', NULL, '', NULL, NULL),
(123, '', '', 3, NULL, '2025-10-25 13:52:14', 'CPU', 'Pending', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(124, '', '', 1, NULL, '2025-10-25 13:52:25', 'Del Carmen', 'Pending', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(125, '', '', 1, NULL, '2025-10-25 13:53:38', 'Lapaz', 'Pending', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(126, '', '', 1, NULL, '2025-10-25 13:54:57', 'Lapuz', 'Pending', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(127, '', '', 1, NULL, '2025-10-25 13:55:36', 'Lapuz', 'Pending', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(128, '', '', 1, NULL, '2025-10-25 14:00:02', 'Lapaz', 'Approved', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(129, '', '', 1, NULL, '2025-10-25 14:00:14', 'Lapaz', 'Approved', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(130, '', '', 1, NULL, '2025-10-25 14:00:25', 'Lapaz', 'Approved', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(131, '', '', 1, NULL, '2025-10-25 14:03:50', 'Lapaz', 'Approved', '2025-10-25', '00:00:00', 100, '', NULL, '', NULL, NULL),
(132, '', '', 2, NULL, '2025-10-25 14:07:39', 'Lapuz', 'Approved', '2025-10-25', '14:00:00', 100, '', NULL, '', NULL, NULL),
(133, 'deopetj@gmail.com', '09465170575', 1, '', '2025-10-27 07:28:31', 'Mandurriao', 'Approved', '2025-10-28', '09:00:00', 100, 'proofs/Screenshot 2025-01-20 220345.png', NULL, 'RES68ff1f1fbd813', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reservation_times`
--

CREATE TABLE `reservation_times` (
  `id` int(11) NOT NULL,
  `reserv_time` time NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `table_id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `capacity` varchar(255) NOT NULL,
  `disabled_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`table_id`, `branch_id`, `table_number`, `is_active`, `capacity`, `disabled_reason`) VALUES
(1, 1, 1, 1, '6', NULL),
(2, 1, 2, 1, '4', NULL),
(3, 1, 3, 1, '4', NULL),
(4, 1, 4, 1, '4', NULL),
(5, 1, 5, 1, '6', NULL),
(6, 1, 6, 1, '8', NULL),
(7, 2, 1, 1, '4', NULL),
(8, 2, 2, 1, '4', NULL),
(9, 2, 3, 1, '4', NULL),
(10, 2, 4, 1, '6', NULL),
(11, 2, 5, 1, '8', NULL),
(12, 3, 1, 1, '4', NULL),
(13, 3, 2, 1, '4', NULL),
(14, 3, 3, 1, '4', NULL),
(15, 3, 4, 1, '4', NULL),
(16, 3, 5, 1, '6', NULL),
(17, 3, 6, 1, '6', NULL),
(18, 3, 7, 1, '8', NULL),
(19, 4, 1, 1, '4', NULL),
(20, 4, 2, 1, '4', NULL),
(21, 4, 3, 1, '4', NULL),
(22, 4, 4, 1, '6', NULL),
(23, 4, 5, 1, '6', NULL),
(24, 4, 6, 1, '8', NULL),
(25, 4, 7, 1, '8', NULL),
(26, 4, 8, 1, '', NULL),
(27, 5, 1, 0, '', 'fadfa'),
(28, 5, 2, 0, '', 'maintenance'),
(29, 5, 3, 1, '', NULL),
(30, 5, 4, 1, '', NULL),
(31, 5, 5, 1, '', NULL),
(32, 5, 6, 1, '', NULL),
(33, 5, 7, 1, '', NULL),
(34, 5, 8, 1, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `branch` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `total_sales` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `branch`, `date`, `total_sales`) VALUES
(1, 'Lapaz', '2025-08-11', 12312.00),
(2, 'Lapaz', '2025-08-11', 12312.00),
(3, 'Lapaz', '2025-08-18', 3343.00),
(4, 'Lapaz', '2025-08-18', 3343.00),
(5, 'Lapaz', '2025-08-18', 3343.00),
(6, 'Lapaz', '2025-08-18', 3343.00),
(7, 'Lapaz', '2025-08-18', 3343.00),
(8, 'Lapaz', '2025-08-18', 3343.00),
(9, 'Lapaz', '2025-08-18', 0.04),
(10, 'Lapaz', '2025-08-18', 0.04),
(11, 'Del Carmen', '2025-08-18', 350000.00),
(12, 'Lapuz', '2025-08-18', 670000.00),
(13, 'Mandurriao', '2025-08-18', 100000.00),
(14, 'Lapaz', '2024-01-20', 23232.00),
(15, 'Mandurriao', '2024-01-20', 24343.00),
(16, 'Mandurriao', '2021-01-02', 333.00),
(17, 'Mandurriao', '2021-01-02', 333.00),
(18, 'Mandurriao', '2021-01-02', 333.00),
(19, 'Lapaz', '2025-08-20', 343433.00),
(20, 'CPU', '2025-08-20', 9999999999.99),
(21, 'CPU', '2025-08-20', 9999999999.99),
(22, 'Mandurriao', '2025-09-05', 1234.00),
(23, 'Lapaz', '2025-09-05', 222231.00),
(24, 'Mandurriao', '2025-09-05', 22233.00),
(25, 'CPU', '2025-09-05', 23334.00),
(26, 'CPU', '2025-09-05', 23332.00),
(27, 'Del Carmen', '2025-09-05', 233332.00),
(28, 'Lapaz', '2025-10-15', 300000.00);

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` int(11) NOT NULL,
  `time_slot` time NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `max_capacity` int(11) DEFAULT 1,
  `current_bookings` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `time_slot`, `is_active`, `max_capacity`, `current_bookings`, `created_at`, `updated_at`) VALUES
(1, '09:00:00', 0, 1, 0, '2025-10-23 15:28:12', '2025-10-24 11:12:05'),
(2, '09:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(3, '10:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(4, '10:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(5, '11:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(6, '11:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(7, '12:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(8, '12:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(9, '13:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(10, '13:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(11, '14:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(12, '14:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(13, '15:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(14, '15:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(15, '16:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(16, '16:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(17, '17:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(18, '17:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(19, '18:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(20, '18:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(21, '19:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(22, '19:30:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12'),
(23, '20:00:00', 1, 1, 0, '2025-10-23 15:28:12', '2025-10-23 15:28:12');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cnum` varchar(20) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `email` varchar(255) NOT NULL,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `cnum`, `username`, `password`, `role`, `email`, `status`) VALUES
(9999, 'Admin', '09217017064', 'admin', '$2a$12$yDxAL7KiwaOPuiINbAecueBaCB67W2E2HmqadMrIs7YvUzWBHc3jO', 'admin', 'admin', 'active'),
(10021, '', '09465170522', '', '$2y$10$Zcus2FzvGXP/0m8dCbAage8IyQxe/vIcOBNAxwOjvue16nd0TOD7C', 'user', 'deopetj@gmail.com22', 'active'),
(10025, '', '09465170575', '', '$2y$10$I3dOt2HRAUCiqzKUC6u4eeaNNMqpml5MyN.dYdeGGIXspXI5RxwGS', 'user', 'deopetj@gmail.com', 'active'),
(10026, '', '123456777', '', '$2y$10$rV/g8IpZD5nlkvj9Pd.dQOIb5YvLjBN2tPCknIcft5WaWnugSuVGS', 'admin', 'maam@gmail.com', 'pending'),
(10028, '', '12345678', '', '$2y$10$32t1ds9Nn4Si8nDa13cRgOY8NjsMP3DbsnBlrMYK70ceb7S0.qmpK', 'admin', 'maam@gmail.comm', 'active'),
(10031, '', '09876543221', '', '$2y$10$PlKJ/WFEgn0/zRA8mUkWweJbbr5Hn/T4Zb5ukZGMZARUQ/3XAhG7a', 'user', 'joshua@gmail.com', 'active'),
(10032, '', '12231231231', '', '$2y$10$qv7n8fatzwQspW9rwtqHoeO9NNd32ZVkEC2pQKzeLCYkIJODyNGW.', 'user', 'geran@gmail.com\r\n', 'pending'),
(10033, '', '1231231231', '', '$2y$10$6fNnPnqR8Q1qlf5TtuJnj.wrbISrNMZXRB8UM6sghUJ92qysEBUau', 'admin', 'itot@gmail.comm', 'pending'),
(10034, '', '09465170543', '', '$2y$10$NAyCdsNSaKzv7hiobBYY9.7eu9gImxw.0FbiPY2aXZk84zWpHoRB2', 'user', 'deopetj@gmail.coms', 'active'),
(10035, '', '09123456789', '', '$2y$10$EB7Rgn.BR8CX95xhzsJpNOxQ6GagPs3EDeq4eV4kOD7cXruBD39Sq', 'user', 'jotea@gmail.com', 'active'),
(10036, '', '09465170511', '', '$2y$10$Rf6rvXMM3Pmvstm3bg.pLu9mk2pRc9gUcl1VNiI/5JMlqk5PcG8UW', 'user', 'deopetj@gmail.comssss', 'active'),
(10037, '', '09465170111', '', '$2y$10$.67EXrta8thmqEaHkRc5.ek8MvElV6Ge0.20TU8axzsQP1ynlzjGK', 'user', 'deopetj@gmail.com1111', 'active'),
(10038, '', '09876543211', '', '$2y$10$mApqc/xg6UoHfvuskQczPOk9BTWoZkeH8GUMVkFwOufu1p2FWTYYO', 'user', 'deopetj@gmail.comzzz', 'active'),
(10039, '', '09465170577', '', '$2y$10$8alzB9ikfpvHqkT1.2QXpenf/7T3oWU6ZSdytpo51qMyVZ85Pt4qy', 'user', 'deopetj@gmail.comzs', 'active'),
(10040, '', '09465170333', '', '$2y$10$eQF.juWgn6kKL2bts4ObAO1c8OPjnhifDd0OjopodNg.91JAMhyU2', 'user', 'deo@gmail.com1', 'active'),
(10041, '', '09300243114', '', '$2y$10$zqA4bbVDilwAhy4JNJCohOFfMgix4mSZXaD3BSRybCMG1.iBJHeIm', 'user', 'salaniogeran@gmail.com', 'active'),
(10042, '', '09112345678', '', '$2y$10$r31tZIxon937wZGXcIU17unsIIqrPFK7kqwT.6JQA5M9e5V1lL7he', 'user', 'deopett@gmaill.com', 'active'),
(10043, '', '09122345671', '', '$2y$10$CEDNMYSBleyPWyirBP306O6/lHfn9ut3O/IR8cXQ8pYYdl/icvRkW', 'user', 'deopetjoteaaa@gmail.com', 'active'),
(10045, '', '09465170474', '', '$2y$10$1v5V6qzpW3a.uLRvJUCV1OpVwgjfWWDD7ybQw8kKxTVpxW9h5w9Be', 'admin', 'deopetj@gmail.commmm', 'active'),
(10046, '', '09876232221', '', '$2y$10$IyWDbf9Vv80AyCXrbLxaQ.2vnbNwFV/J.80mmgQPQEKk6u45rjwq.', 'user', 'deopetj@gmail.comzzs', 'active'),
(10047, '', '09639882679', '', '$2y$10$MaXIMBKyoWn8UDLAXvVIJuUB.5b5rgk7ugUtpvvsPw0N3So8216PK', 'user', 'finalexam@gmail.com', 'active'),
(10048, '', '09465111575', '', '$2y$10$KVZ7oWaP99MxXXxr8NSvEudz4GtXpTHXjmKmJmg/u4XJEC4JE.tEi', 'user', 'deopetj@.comgmails', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `reservation_times`
--
ALTER TABLE `reservation_times`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `branch_id` (`branch_id`,`table_number`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `time_slot` (`time_slot`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `reservation_times`
--
ALTER TABLE `reservation_times`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10049;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blocked_slots`
--
ALTER TABLE `blocked_slots`
  ADD CONSTRAINT `blocked_slots_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`table_id`),
  ADD CONSTRAINT `blocked_slots_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD CONSTRAINT `restaurant_tables_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
