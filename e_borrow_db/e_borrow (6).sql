-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 02, 2026 at 11:24 AM
-- Server version: 10.11.13-MariaDB
-- PHP Version: 8.4.17

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `e_borrow`
--

-- --------------------------------------------------------

--
-- Table structure for table `borrow_categories`
--

CREATE TABLE `borrow_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `total_quantity` int(11) NOT NULL DEFAULT 0,
  `available_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_categories`
--

INSERT INTO `borrow_categories` (`id`, `name`, `description`, `image_url`, `total_quantity`, `available_quantity`) VALUES
(7, 'รถเข็นวีลแชร์ (Wheelchair)', NULL, 'uploads/equipment_images/equip-690e0c07554e63.22924925.jpg', 5, 3),
(8, 'หุ่น CPR', NULL, 'uploads/equipment_images/equip-690e0c109e0630.38319547.jpg', 3, 3),
(9, 'เครื่องวัดความดัน (BP Monitor)', NULL, 'uploads/equipment_images/equip-690e0c222b02a8.71977128.jpg', 3, 2),
(10, 'ไม้เท้า', NULL, 'uploads/equipment_images/equip-690e0c2e315ee6.66247111.jpg', 3, 2),
(12, 'ไม้เท้าสามขา (Tripod Cane)', NULL, 'uploads/equipment_images/equip-691360059c5022.72864918.jpg', 2, 2),
(13, 'Notebook', 'เครื่องคอมพิวเตอร์พกพา', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `borrow_fines`
--

CREATE TABLE `borrow_fines` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL COMMENT 'FK to med_transactions.id',
  `student_id` int(11) NOT NULL COMMENT 'FK to med_students.id',
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'จำนวนเงินค่าปรับ',
  `status` enum('pending','paid') NOT NULL DEFAULT 'pending' COMMENT 'สถานะค่าปรับ',
  `notes` text DEFAULT NULL COMMENT 'หมายเหตุจาก Admin',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by_staff_id` int(11) DEFAULT NULL COMMENT 'FK to med_users.id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_fines`
--

INSERT INTO `borrow_fines` (`id`, `transaction_id`, `student_id`, `amount`, `status`, `notes`, `created_at`, `created_by_staff_id`) VALUES
(4, 30, 20, 10.00, 'paid', 'เกินกำหนด 1 วัน', '2025-11-08 00:51:59', 9),
(5, 32, 20, 30.00, 'paid', 'เกินกำหนด 3 วัน', '2025-11-08 01:06:07', 9),
(6, 33, 20, 50.00, 'paid', 'เกินกำหนด 5 วัน', '2025-11-08 01:07:28', 9),
(7, 34, 20, 70.00, 'paid', 'เกินกำหนด 7 วัน', '2025-11-08 01:16:04', 9),
(8, 31, 20, 20.00, 'paid', 'เกินกำหนด 2 วัน', '2025-11-10 12:57:17', 2),
(9, 35, 20, 110.00, 'paid', 'เกินกำหนด 11 วัน', '2025-11-12 01:17:57', 9),
(10, 43, 22, 110.00, 'paid', 'เกินกำหนด 11 วัน', '2025-11-12 04:14:08', 9),
(11, 39, 22, 80.00, 'paid', 'เกินกำหนด 8 วัน', '2025-11-20 16:21:35', 9),
(12, 39, 22, 80.00, 'paid', 'เกินกำหนด 8 วัน', '2025-11-20 16:21:41', 9),
(13, 44, 22, 80.00, 'paid', 'เกินกำหนด 8 วัน', '2025-11-20 16:21:47', 9),
(14, 40, 22, 70.00, 'paid', 'เกินกำหนด 7 วัน', '2025-11-20 16:21:51', 9),
(15, 50, 22, 40.00, 'paid', 'เกินกำหนด 4 วัน', '2025-11-29 08:48:11', 3),
(16, 53, 22, 550.00, 'paid', 'เกินกำหนด 55 วัน', '2026-04-01 18:55:46', 9);

-- --------------------------------------------------------

--
-- Table structure for table `borrow_items`
--

CREATE TABLE `borrow_items` (
  `id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL COMMENT 'ชื่ออุปกรณ์ เช่น รถเข็นวีลแชร์, ไม้เท้า',
  `description` text DEFAULT NULL COMMENT 'รายละเอียด หรือ หมายเหตุ',
  `image_url` varchar(255) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL COMMENT 'เลขซีเรียล (ถ้ามี)',
  `status` enum('available','borrowed','maintenance') NOT NULL DEFAULT 'available' COMMENT 'สถานะ: ว่าง, ถูกยืม, ซ่อมบำรุง'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_items`
--

INSERT INTO `borrow_items` (`id`, `type_id`, `name`, `description`, `image_url`, `serial_number`, `status`) VALUES
(40, 7, 'รถเข็นวีลแชร์ (Wheelchair)', '', NULL, 'WC-001', 'borrowed'),
(41, 7, 'รถเข็นวีลแชร์ (Wheelchair)', NULL, NULL, 'WC-002', 'available'),
(42, 7, 'รถเข็นวีลแชร์ (Wheelchair)', NULL, NULL, 'WC-003', 'borrowed'),
(44, 8, 'หุ่น CPR', NULL, NULL, 'CPR-01', 'available'),
(45, 8, 'หุ่น CPR', NULL, NULL, 'CPR-02', 'available'),
(46, 8, 'หุ่น CPR', NULL, NULL, 'CPR-03', 'available'),
(47, 9, 'เครื่องวัดความดัน (BP Monitor)', NULL, NULL, 'BP-001', 'borrowed'),
(48, 9, 'เครื่องวัดความดัน (BP Monitor)', NULL, NULL, 'BP-002', 'available'),
(49, 9, 'เครื่องวัดความดัน (BP Monitor)', NULL, NULL, 'BP-003', 'available'),
(50, 10, 'ไม้เท้า', NULL, NULL, 'MT-001', 'borrowed'),
(51, 10, 'ไม้เท้า', NULL, NULL, 'MT-002', 'available'),
(52, 10, 'ไม้เท้า', NULL, NULL, 'MT-003', 'available'),
(53, 7, 'รถเข็นวีลแชร์ (Wheelchair)', NULL, NULL, 'WC-004', 'borrowed'),
(54, 7, 'รถเข็นวีลแชร์ (Wheelchair)', NULL, NULL, 'WC-005', 'available'),
(55, 12, 'ไม้เท้าสามขา (Tripod Cane)', NULL, NULL, 'MM01', 'available'),
(56, 12, 'ไม้เท้าสามขา (Tripod Cane)', NULL, NULL, 'MM02', 'available'),
(57, 13, 'Asus', NULL, NULL, '2901A', 'borrowed');

-- --------------------------------------------------------

--
-- Table structure for table `borrow_payments`
--

CREATE TABLE `borrow_payments` (
  `id` int(11) NOT NULL,
  `fine_id` int(11) NOT NULL COMMENT 'FK to med_fines.id',
  `amount_paid` decimal(10,2) NOT NULL COMMENT 'จำนวนเงินที่จ่าย',
  `payment_method` enum('cash','bank_transfer') NOT NULL DEFAULT 'cash' COMMENT 'วิธีการชำระเงิน',
  `payment_slip_url` varchar(255) DEFAULT NULL COMMENT 'URL สลิปการโอน',
  `payment_date` datetime NOT NULL DEFAULT current_timestamp(),
  `received_by_staff_id` int(11) NOT NULL COMMENT 'FK to med_users.id',
  `receipt_number` varchar(100) DEFAULT NULL COMMENT 'เลขที่ใบเสร็จ (ถ้ามี)',
  `payment_notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_payments`
--

INSERT INTO `borrow_payments` (`id`, `fine_id`, `amount_paid`, `payment_method`, `payment_slip_url`, `payment_date`, `received_by_staff_id`, `receipt_number`, `payment_notes`) VALUES
(3, 4, 10.00, 'bank_transfer', '../uploads/slips/slip-30-690e31bf6762f.jpg', '2025-11-08 00:51:59', 9, NULL, NULL),
(4, 5, 30.00, 'bank_transfer', 'uploads/slips/slip-32-690e350f745fb.jpg', '2025-11-08 01:06:07', 9, NULL, NULL),
(5, 6, 50.00, 'bank_transfer', 'uploads/slips/slip-33-690e356051bbb.jpg', '2025-11-08 01:07:28', 9, NULL, NULL),
(6, 7, 70.00, 'bank_transfer', 'uploads/slips/slip-34-690e3764a836a.jpg', '2025-11-08 01:16:04', 9, NULL, NULL),
(7, 8, 20.00, 'cash', NULL, '2025-11-10 12:57:17', 2, NULL, NULL),
(8, 9, 110.00, 'cash', NULL, '2025-11-12 01:17:57', 9, NULL, NULL),
(9, 10, 110.00, 'bank_transfer', 'uploads/slips/slip-43-6913a720a4370.png', '2025-11-12 04:14:08', 9, NULL, NULL),
(10, 11, 80.00, 'cash', NULL, '2025-11-20 16:21:35', 9, NULL, NULL),
(11, 12, 80.00, 'cash', NULL, '2025-11-20 16:21:41', 9, NULL, NULL),
(12, 13, 80.00, 'cash', NULL, '2025-11-20 16:21:47', 9, NULL, NULL),
(13, 14, 70.00, 'cash', NULL, '2025-11-20 16:21:51', 9, NULL, NULL),
(14, 15, 40.00, 'cash', NULL, '2025-11-29 08:48:11', 3, NULL, NULL),
(15, 16, 550.00, 'cash', NULL, '2026-04-01 18:55:46', 9, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `borrow_records`
--

CREATE TABLE `borrow_records` (
  `id` int(11) NOT NULL,
  `type_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `equipment_id` int(11) NOT NULL COMMENT 'ID ของอุปกรณ์ที่ถูกยืม (จากตาราง med_equipment)',
  `equipment_type_id` int(11) DEFAULT NULL,
  `borrower_student_id` int(11) DEFAULT NULL COMMENT 'ID ของผู้ยืม (FK to med_students.id)',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'จำนวนที่ขอยืม',
  `reason_for_borrowing` text DEFAULT NULL COMMENT 'เหตุผลการขอยืม',
  `lending_staff_id` int(11) DEFAULT NULL COMMENT 'ID เจ้าหน้าที่ที่ นศ. ระบุ (FK to med_users)',
  `approver_id` int(11) DEFAULT NULL COMMENT 'ID เจ้าหน้าที่ผู้อนุมัติ (FK to med_users.id)',
  `borrow_date` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่ยืม',
  `due_date` date DEFAULT NULL COMMENT 'วันที่กำหนดคืน',
  `return_date` datetime DEFAULT NULL COMMENT 'วันที่คืนจริง (จะเป็น NULL ถ้ายังไม่คืน)',
  `return_staff_id` int(11) DEFAULT NULL,
  `status` enum('borrowed','returned') NOT NULL DEFAULT 'borrowed' COMMENT 'สถานะการยืมปัจจุบัน',
  `approval_status` enum('pending','approved','rejected','staff_added') NOT NULL DEFAULT 'staff_added' COMMENT 'สถานะคำขอยืม: pending=รออนุมัติ, approved=อนุมัติแล้ว, rejected=ปฏิเสธ, staff_added=เจ้าหน้าที่เพิ่มเอง',
  `attachment_url` varchar(255) DEFAULT NULL COMMENT 'ลิงก์ไปยังไฟล์แนบคำร้อง',
  `fine_status` enum('none','pending','paid') NOT NULL DEFAULT 'none' COMMENT 'สถานะค่าปรับ: none=ยังไม่ปรับ, pending=มีค่าปรับค้าง, paid=จ่ายแล้ว'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrow_records`
--

INSERT INTO `borrow_records` (`id`, `type_id`, `item_id`, `equipment_id`, `equipment_type_id`, `borrower_student_id`, `quantity`, `reason_for_borrowing`, `lending_staff_id`, `approver_id`, `borrow_date`, `due_date`, `return_date`, `return_staff_id`, `status`, `approval_status`, `attachment_url`, `fine_status`) VALUES
(27, 7, 41, 41, NULL, NULL, 1, 'ทดสอบ', 9, NULL, '2025-11-07 23:54:23', '2025-11-07', NULL, NULL, 'returned', 'rejected', NULL, 'none'),
(28, 7, 42, 42, NULL, NULL, 1, 'ทดสอบ', 9, NULL, '2025-11-08 00:00:05', '2025-11-08', NULL, NULL, 'returned', 'rejected', NULL, 'none'),
(29, 7, 41, 41, NULL, NULL, 1, 'ทดสอบ', 9, NULL, '2025-11-08 00:19:33', '2025-11-08', '2025-11-08 00:00:00', 9, 'returned', 'approved', NULL, 'none'),
(30, 7, 42, 42, NULL, 20, 1, 'ทดสอบ', 9, NULL, '2025-11-08 00:24:30', '2025-11-07', '2025-11-10 00:00:00', 2, 'returned', 'approved', NULL, 'paid'),
(31, 8, 44, 44, NULL, 20, 1, 'ทดสอบ', 9, NULL, '2025-11-08 00:25:06', '2025-11-08', '2025-11-10 00:00:00', 2, 'returned', 'approved', NULL, 'paid'),
(32, 7, 41, 41, NULL, 20, 1, 'Test', 9, NULL, '2025-11-08 01:05:50', '2025-11-05', '2025-11-10 00:00:00', 2, 'returned', 'approved', NULL, 'paid'),
(33, 8, 45, 45, NULL, 20, 1, 'ทดสอบ', 9, NULL, '2025-11-08 01:07:17', '2025-11-03', '2025-11-10 00:00:00', 2, 'returned', 'approved', NULL, 'paid'),
(34, 9, 47, 47, NULL, 20, 1, 'ทดสอบ', 9, NULL, '2025-11-08 01:15:53', '2025-11-01', '2025-11-10 00:00:00', 2, 'returned', 'approved', NULL, 'paid'),
(35, 7, 41, 41, NULL, 20, 1, 'ทดสอบ', 9, NULL, '2025-11-10 13:19:53', '2025-11-01', '2025-11-12 00:00:00', 9, 'returned', 'approved', NULL, 'paid'),
(36, 7, 42, 42, NULL, 20, 1, 'ทดสอบ', 9, NULL, '2025-11-12 01:01:36', '2025-11-12', '2025-11-12 00:00:00', 9, 'returned', 'approved', NULL, 'none'),
(37, 8, 44, 44, NULL, 20, 1, 'ทดสอบ', 9, NULL, '2025-11-12 01:01:40', '2025-11-12', '2025-11-12 00:00:00', 9, 'returned', 'approved', NULL, 'none'),
(38, 7, 41, 41, NULL, 21, 1, 'ทดสอบ', 9, NULL, '2025-11-12 01:36:41', '2025-11-12', '2025-11-12 00:00:00', 9, 'returned', 'approved', NULL, 'none'),
(39, 7, 42, 42, NULL, 22, 1, 'test', 9, NULL, '2025-11-12 01:58:11', '2025-11-12', '2025-11-20 00:00:00', 9, 'returned', 'approved', NULL, 'paid'),
(40, 8, 44, 44, NULL, 22, 1, 'ทดสอบ', 9, NULL, '2025-11-12 02:09:46', '2025-11-13', '2025-11-20 00:00:00', 9, 'returned', 'approved', NULL, 'paid'),
(41, 9, 47, 47, NULL, 22, 1, 'ทดสอบ\n\n(ยกเลิกโดยผู้ใช้)', 9, NULL, '2025-11-12 02:35:25', '2025-11-13', NULL, NULL, 'returned', 'rejected', NULL, 'none'),
(42, 7, 41, 41, NULL, 22, 1, 'ทดสอบ', 9, NULL, '2025-11-12 03:06:57', '2025-11-12', NULL, NULL, 'returned', 'rejected', NULL, 'none'),
(43, 9, 47, 47, NULL, 22, 1, 'ทดสอบ', 9, NULL, '2025-11-12 03:10:01', '2025-11-01', '2025-11-12 00:00:00', 9, 'returned', 'approved', NULL, 'paid'),
(44, 7, 53, 53, NULL, 22, 1, 'ทดสอบ', 9, NULL, '2025-11-12 03:27:58', '2025-11-12', '2025-11-20 00:00:00', 9, 'returned', 'approved', 'uploads/attachments/req-69139c3ee5a79-Screenshot_20251111_085032_Instagram.jpg', 'paid'),
(45, 8, 45, 45, NULL, 22, 1, NULL, 9, NULL, '2025-11-20 03:51:57', '2025-11-26', '2025-11-20 00:00:00', 9, 'returned', 'approved', NULL, 'none'),
(46, 7, 42, 42, NULL, 22, 1, 'ทดสอบเอกสารแนบ', 9, NULL, '2025-11-24 14:56:13', '2025-11-24', NULL, NULL, 'returned', 'rejected', 'uploads/attachments/req-69139c3ee5a79-Screenshot_20251111_085032_Instagram.jpg', 'none'),
(47, 7, 53, 53, NULL, 22, 1, 'ทดสอบแนบบเอกสารครั้งที่ 2', 9, NULL, '2025-11-24 16:18:10', '2568-11-24', NULL, NULL, 'returned', 'rejected', NULL, 'none'),
(48, 8, 44, 44, NULL, 22, 1, 'ทดสอบแบบเอกสารครั้งที่ 3', 9, NULL, '2025-11-24 16:51:17', '2568-11-24', '2025-11-29 00:00:00', 6, 'returned', 'approved', 'uploads/attachments/req-692428cd4e92d.pdf', 'none'),
(49, 7, 53, 53, NULL, 22, 1, 'ทดสอบยืมของ', 9, NULL, '2025-11-24 17:00:28', '2568-11-24', NULL, NULL, 'returned', 'rejected', 'uploads/attachments/req-69242cbcd9c8a.pdf', 'none'),
(50, 12, 55, 55, NULL, 22, 1, 'ทดสอบ', 9, 9, '2025-11-24 22:25:05', '2025-11-25', '2025-11-29 00:00:00', 6, 'returned', 'approved', NULL, 'paid'),
(51, 7, 40, 40, NULL, 23, 1, 'เพื่อใช้งาน', 9, 6, '2025-11-29 09:25:55', '2025-11-29', '2025-11-29 00:00:00', 6, 'returned', 'approved', NULL, 'none'),
(52, 7, 40, 40, NULL, 23, 1, 'เพื่อใช้งาน', 3, 6, '2025-11-29 10:01:22', '2025-12-02', NULL, NULL, 'borrowed', 'approved', NULL, 'none'),
(53, 7, 41, 41, NULL, 22, 1, NULL, 9, NULL, '2026-01-29 17:42:44', '2026-02-05', '2026-04-01 18:55:50', 9, 'returned', 'staff_added', NULL, 'paid'),
(54, 10, 50, 50, NULL, 27, 1, 'Accident', 14, 3, '2026-02-10 12:02:57', '2026-03-10', '2026-02-20 00:00:00', 3, 'returned', 'approved', NULL, 'none'),
(55, 10, 51, 51, NULL, 28, 1, 'เป็นเเผลที่ขาขวาเดินไม่ได้\n\n(ยกเลิกโดยผู้ใช้)', 11, NULL, '2026-02-10 15:52:08', '2026-02-27', NULL, NULL, 'returned', 'rejected', NULL, 'none'),
(56, 10, 50, 50, NULL, 31, 1, 'ข้อเท้าพลิก', 16, 3, '2026-03-08 08:58:54', '2026-03-31', NULL, NULL, 'borrowed', 'approved', NULL, 'none'),
(57, 9, 47, 47, NULL, 23, 1, 'อ สื่อจิต ขอยืมใช้ส่วนตัว', 3, 6, '2026-03-18 16:27:45', '2027-04-18', NULL, NULL, 'borrowed', 'approved', NULL, 'none'),
(58, 13, 57, 57, NULL, 23, 1, 'ใช้ชั่วคราว', 3, 6, '2026-03-20 08:18:11', '2026-03-23', NULL, NULL, 'borrowed', 'approved', NULL, 'none'),
(59, 7, 42, 42, NULL, 23, 1, '6402711\r\nชื่อฐนพล ตนดี\r\n0632165451\r\nคณะวิศวกรรม', 3, 6, '2026-03-22 11:25:40', '2026-03-26', NULL, NULL, 'borrowed', 'approved', NULL, 'none'),
(61, 7, 53, 53, NULL, 22, 1, 'Test', 9, 9, '2026-04-01 01:29:23', '2026-04-02', NULL, NULL, 'borrowed', 'approved', NULL, 'none');

-- --------------------------------------------------------

--
-- Table structure for table `camp_bookings`
--

CREATE TABLE `camp_bookings` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `status` enum('booked','confirmed','cancelled','cancelled_by_admin') DEFAULT 'booked',
  `attended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `camp_list`
--

CREATE TABLE `camp_list` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` enum('vaccine','training','health_check','other') DEFAULT 'other',
  `description` mediumtext DEFAULT NULL,
  `total_capacity` int(11) NOT NULL DEFAULT 0,
  `available_until` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `is_auto_approve` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `camp_list`
--

INSERT INTO `camp_list` (`id`, `title`, `type`, `description`, `total_capacity`, `available_until`, `status`, `is_auto_approve`, `created_at`) VALUES
(2, 'ฉีดวัคซีนป้องกันโรคไข้หวัดใหญ่ตามฤดูกาล ชนิด 3 สายพันธุ์ ฟรี! ไม่มีค่าใช้จ่าย', 'vaccine', '🗓 เริ่มให้บริการตั้งแต่วันที่ 1 พฤษภาคม 2569 เป็นต้นไป\r\n⏰ วันจันทร์ – ศุกร์ เวลา 08.00 – 16.00 น.\r\n⏰ วันเสาร์ – อาทิตย์ เวลา 08.00 – 12.00 น.\r\n📍 สถานที่ให้บริการ\r\nคลินิกเวชกรรม มหาวิทยาลัยรังสิต\r\n📌 ก่อนเข้ารับบริการ กรุณาเตรียมเอกสารดังนี้\r\n1️⃣ บัตรประชาชนตัวจริง\r\n2️⃣ แบบคัดกรองและแบบแสดงความยินยอม\r\n⚠️ กรณีผู้รับบริการอายุต่ำกว่า 18 ปี\r\nกรุณาแนบ สำเนาบัตรประชาชนผู้ปกครอง\r\n✨ ป้องกันก่อน ปลอดภัยกว่า\r\nวัคซีนไข้หวัดใหญ่ช่วยลดความเสี่ยงของการเจ็บป่วยและภาวะแทรกซ้อน โดยเฉพาะในช่วงฤดูกาลระบาด', 1500, NULL, 'active', 1, '2026-03-20 09:58:27'),
(3, 'CPR รุ่น 1', 'training', '', 20, NULL, 'inactive', 1, '2026-03-20 12:26:48'),
(4, 'วัคซีน HPV', 'vaccine', '', 100, NULL, 'inactive', 0, '2026-03-22 03:22:20'),
(5, 'ฉีดวัคซีนป้องกันโรคไข้หวัดใหญ่ตามฤดูกาล ชนิด 3 สายพันธุ์ ฟรี!', 'vaccine', '🗓 เริ่มให้บริการตั้งแต่วันที่ 1 พฤษภาคม 2569 เป็นต้นไป\r\n⏰ วันจันทร์ – ศุกร์ เวลา 08.00 – 16.00 น.\r\n⏰ วันเสาร์ – อาทิตย์ เวลา 08.00 – 12.00 น.\r\n📍 สถานที่ให้บริการ\r\nคลินิกเวชกรรม มหาวิทยาลัยรังสิต\r\n📌 ก่อนเข้ารับบริการ กรุณาเตรียมเอกสารดังนี้\r\n1️⃣ บัตรประชาชนตัวจริง\r\n2️⃣ แบบคัดกรองและแบบแสดงความยินยอม\r\n⚠️ กรณีผู้รับบริการอายุต่ำกว่า 18 ปี\r\nกรุณาแนบ สำเนาบัตรประชาชนผู้ปกครอง\r\n✨ ป้องกันก่อน ปลอดภัยกว่า\r\nวัคซีนไข้หวัดใหญ่ช่วยลดความเสี่ยงของการเจ็บป่วยและภาวะแทรกซ้อน โดยเฉพาะในช่วงฤดูกาลระบาด', 1500, NULL, 'inactive', 0, '2026-03-22 03:38:03'),
(6, 'นักจิต', 'other', '', 200, NULL, 'inactive', 0, '2026-03-22 06:15:11'),
(7, 'นัดหมายคลินิก กระดูกและข้อ', 'health_check', '', 6, NULL, 'active', 0, '2026-03-23 03:39:45'),
(13, 'Test หมดเขตรับสมัคร', 'other', '', 10, '2026-03-29', 'active', 0, '2026-03-28 18:40:42');

-- --------------------------------------------------------

--
-- Table structure for table `camp_slots`
--

CREATE TABLE `camp_slots` (
  `id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `slot_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `max_capacity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `camp_slots`
--

INSERT INTO `camp_slots` (`id`, `campaign_id`, `slot_date`, `start_time`, `end_time`, `max_capacity`) VALUES
(1, 2, '2026-03-21', '08:00:00', '10:00:00', 50),
(2, 2, '2026-03-20', '08:00:00', '11:00:00', 20),
(4, 2, '2026-03-22', '08:00:00', '11:00:00', 20),
(5, 3, '2026-03-21', '08:00:00', '11:00:00', 50),
(14, 2, '2026-04-01', '08:30:00', '16:00:00', 50),
(15, 2, '2026-04-02', '08:30:00', '16:00:00', 50),
(16, 2, '2026-04-03', '08:30:00', '16:00:00', 50),
(17, 2, '2026-04-04', '08:30:00', '16:00:00', 50),
(18, 2, '2026-04-05', '08:30:00', '16:00:00', 50),
(19, 2, '2026-04-06', '08:30:00', '16:00:00', 50),
(20, 2, '2026-04-07', '08:30:00', '16:00:00', 50),
(21, 2, '2026-04-08', '08:30:00', '16:00:00', 50),
(22, 2, '2026-04-09', '08:30:00', '16:00:00', 50),
(23, 2, '2026-04-10', '08:30:00', '16:00:00', 50),
(24, 2, '2026-04-11', '08:30:00', '16:00:00', 50),
(25, 2, '2026-04-12', '08:30:00', '16:00:00', 50),
(26, 2, '2026-04-13', '08:30:00', '16:00:00', 50),
(27, 2, '2026-04-14', '08:30:00', '16:00:00', 50),
(28, 2, '2026-04-15', '08:30:00', '16:00:00', 50),
(29, 2, '2026-04-16', '08:30:00', '16:00:00', 50),
(30, 2, '2026-04-17', '08:30:00', '16:00:00', 50),
(31, 2, '2026-04-18', '08:30:00', '16:00:00', 50),
(32, 2, '2026-04-19', '08:30:00', '16:00:00', 50),
(33, 2, '2026-04-20', '08:30:00', '16:00:00', 50),
(34, 2, '2026-04-21', '08:30:00', '16:00:00', 50),
(35, 2, '2026-04-22', '08:30:00', '16:00:00', 50),
(36, 2, '2026-04-23', '08:30:00', '16:00:00', 50),
(37, 2, '2026-04-24', '08:30:00', '16:00:00', 50),
(38, 2, '2026-04-25', '08:30:00', '16:00:00', 50),
(39, 2, '2026-04-26', '08:30:00', '16:00:00', 50),
(40, 2, '2026-04-27', '08:30:00', '16:00:00', 50),
(41, 2, '2026-04-28', '08:30:00', '16:00:00', 50),
(44, 5, '2026-05-02', '08:30:00', '16:00:00', 50),
(45, 5, '2026-05-03', '08:30:00', '16:00:00', 50),
(46, 5, '2026-05-04', '08:30:00', '16:00:00', 50),
(47, 5, '2026-05-05', '08:30:00', '16:00:00', 50),
(48, 5, '2026-05-06', '08:30:00', '16:00:00', 50),
(49, 5, '2026-05-07', '08:30:00', '16:00:00', 50),
(50, 5, '2026-05-08', '08:30:00', '16:00:00', 50),
(51, 5, '2026-05-09', '08:30:00', '16:00:00', 50),
(52, 5, '2026-05-10', '08:30:00', '16:00:00', 50),
(53, 5, '2026-05-11', '08:30:00', '16:00:00', 50),
(54, 5, '2026-05-12', '08:30:00', '16:00:00', 50),
(55, 5, '2026-05-13', '08:30:00', '16:00:00', 50),
(56, 5, '2026-05-14', '08:30:00', '16:00:00', 50),
(57, 5, '2026-05-15', '08:30:00', '16:00:00', 50),
(58, 5, '2026-05-16', '08:30:00', '16:00:00', 50),
(59, 5, '2026-05-17', '08:30:00', '16:00:00', 50),
(60, 5, '2026-05-18', '08:30:00', '16:00:00', 50),
(61, 5, '2026-05-19', '08:30:00', '16:00:00', 50),
(62, 5, '2026-05-20', '08:30:00', '16:00:00', 50),
(63, 5, '2026-05-21', '08:30:00', '16:00:00', 50),
(64, 5, '2026-05-22', '08:30:00', '16:00:00', 50),
(65, 5, '2026-05-23', '08:30:00', '16:00:00', 50),
(66, 5, '2026-05-24', '08:30:00', '16:00:00', 50),
(67, 5, '2026-05-25', '08:30:00', '16:00:00', 50),
(68, 5, '2026-05-26', '08:30:00', '16:00:00', 50),
(69, 5, '2026-05-27', '08:30:00', '16:00:00', 50),
(70, 5, '2026-05-28', '08:30:00', '16:00:00', 50),
(71, 5, '2026-05-29', '08:30:00', '16:00:00', 50),
(72, 5, '2026-05-30', '08:30:00', '16:00:00', 50),
(73, 5, '2026-05-31', '08:30:00', '16:00:00', 50),
(74, 6, '2026-03-23', '08:00:00', '10:00:00', 10),
(75, 6, '2026-03-24', '08:00:00', '10:00:00', 10),
(79, 6, '2026-03-30', '08:00:00', '10:00:00', 10),
(80, 6, '2026-03-31', '08:00:00', '10:00:00', 10),
(81, 5, '2026-03-23', '08:00:00', '10:00:00', 50),
(82, 5, '2026-03-24', '08:00:00', '10:00:00', 50),
(83, 5, '2026-03-25', '08:00:00', '10:00:00', 50),
(84, 5, '2026-03-26', '08:00:00', '10:00:00', 50),
(85, 5, '2026-03-27', '08:00:00', '10:00:00', 50),
(86, 5, '2026-03-30', '08:00:00', '10:00:00', 50),
(87, 5, '2026-03-31', '08:00:00', '10:00:00', 50),
(88, 5, '2026-04-01', '08:00:00', '10:00:00', 50),
(89, 6, '2026-03-22', '13:00:00', '16:00:00', 6),
(90, 6, '2026-03-22', '16:00:00', '18:00:00', 5),
(91, 6, '2026-03-23', '13:00:00', '16:00:00', 6),
(92, 6, '2026-03-23', '16:00:00', '18:00:00', 5),
(93, 6, '2026-03-24', '13:00:00', '16:00:00', 6),
(94, 6, '2026-03-24', '16:00:00', '18:00:00', 5),
(95, 6, '2026-03-25', '13:00:00', '16:00:00', 6),
(96, 6, '2026-03-25', '16:00:00', '18:00:00', 5),
(97, 6, '2026-03-26', '13:00:00', '16:00:00', 6),
(98, 6, '2026-03-26', '16:00:00', '18:00:00', 5),
(101, 6, '2026-03-19', '11:00:00', '12:00:00', 17),
(102, 6, '2026-03-19', '13:00:00', '14:00:00', 17),
(103, 6, '2026-03-19', '14:00:00', '15:00:00', 16),
(104, 6, '2026-03-19', '15:00:00', '16:00:00', 16),
(105, 6, '2026-04-05', '09:00:00', '10:00:00', 17),
(106, 6, '2026-04-05', '10:00:00', '11:00:00', 17),
(107, 6, '2026-04-05', '11:00:00', '12:00:00', 17),
(108, 6, '2026-04-05', '13:00:00', '14:00:00', 17),
(109, 6, '2026-04-05', '14:00:00', '15:00:00', 16),
(110, 6, '2026-04-05', '15:00:00', '16:00:00', 16),
(111, 7, '2026-03-27', '13:00:00', '15:30:00', 4),
(130, 13, '2026-03-30', '09:00:00', '10:00:00', 9),
(131, 13, '2026-03-30', '10:00:00', '11:00:00', 9),
(132, 13, '2026-03-30', '11:00:00', '12:00:00', 8),
(133, 13, '2026-03-30', '13:00:00', '14:00:00', 8),
(134, 13, '2026-03-30', '14:00:00', '15:00:00', 8),
(135, 13, '2026-03-30', '15:00:00', '16:00:00', 8),
(136, 7, '2026-04-29', '09:00:00', '10:00:00', 9),
(137, 7, '2026-04-29', '10:00:00', '11:00:00', 9),
(138, 7, '2026-04-29', '11:00:00', '12:00:00', 8),
(139, 7, '2026-04-29', '13:00:00', '14:00:00', 8),
(140, 7, '2026-04-29', '14:00:00', '15:00:00', 8),
(141, 7, '2026-04-29', '15:00:00', '16:00:00', 8);

-- --------------------------------------------------------

--
-- Table structure for table `sys_activity_logs`
--

CREATE TABLE `sys_activity_logs` (
  `id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL COMMENT 'Admin/Staff ID ที่กระทำ (จาก med_users.id)',
  `action` varchar(100) NOT NULL COMMENT 'ประเภทการกระทำ (เช่น create_user, delete_equipment)',
  `description` text NOT NULL COMMENT 'รายละเอียด (เช่น Admin A ลบ User B)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sys_activity_logs`
--

INSERT INTO `sys_activity_logs` (`id`, `timestamp`, `user_id`, `action`, `description`) VALUES
(223, '2026-01-28 16:14:42', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(224, '2026-01-28 17:24:01', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(225, '2026-01-28 17:25:51', 2, 'login_password', 'พนักงาน \'เจ้าหน้าที่ทั่วไป\' (Username: user) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(226, '2026-01-28 17:26:08', 2, 'login_password', 'พนักงาน \'เจ้าหน้าที่ทั่วไป\' (Username: user) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(227, '2026-01-28 17:50:02', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(228, '2026-01-28 17:50:23', 2, 'login_password', 'พนักงาน \'เจ้าหน้าที่ทั่วไป\' (Username: user) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(229, '2026-01-28 18:23:03', 2, 'login_password', 'พนักงาน \'เจ้าหน้าที่ทั่วไป\' (Username: user) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(230, '2026-01-28 18:42:53', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(231, '2026-01-28 18:44:08', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(232, '2026-01-28 19:00:13', 18, 'login_password', 'พนักงาน \'test\' (Username: folk1947) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(233, '2026-01-28 19:00:33', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(234, '2026-01-29 08:51:58', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(235, '2026-01-29 09:23:40', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(236, '2026-01-29 10:58:09', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(237, '2026-01-29 11:00:44', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(238, '2026-01-29 11:26:14', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(239, '2026-01-29 12:20:25', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(240, '2026-01-29 12:39:25', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(241, '2026-01-29 13:00:20', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(242, '2026-01-29 13:52:04', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(243, '2026-01-29 13:56:40', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(244, '2026-01-29 15:18:15', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(245, '2026-02-20 17:14:03', 3, 'login_password', 'พนักงาน \'ผู้ดูแลระบบ\' (Username: admin) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(246, '2026-02-20 17:17:36', 3, 'approve_request', 'Admin \'ผู้ดูแลระบบ\' (ID: 3) ได้อนุมัติคำขอ (TID: 54) สำหรับอุปกรณ์ (Item ID: 50)'),
(247, '2026-02-20 17:17:52', 3, 'return_equipment', 'Admin \'ผู้ดูแลระบบ\' (ID: 3) \r\n                     ได้บันทึกการคืนอุปกรณ์ (ItemID: 50, TID: 54)'),
(248, '2026-03-18 16:28:24', 6, 'login_password', 'พนักงาน \'นายนพดล เหล่าเขตกิจ\' (Username: 6290123) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(249, '2026-03-18 16:28:45', 6, 'approve_request', 'Admin \'นายนพดล เหล่าเขตกิจ\' (ID: 6) ได้อนุมัติคำขอ (TID: 57) สำหรับอุปกรณ์ (Item ID: 47)'),
(250, '2026-03-20 08:14:05', 6, 'login_password', 'พนักงาน \'นายนพดล เหล่าเขตกิจ\' (Username: 6290123) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(251, '2026-03-20 08:15:24', 6, 'add_type', 'Admin \'นายนพดล เหล่าเขตกิจ\' (ID: 6) \r\n                     ได้เพิ่มประเภทอุปกรณ์ใหม่ (Type ID: 13, Name: Notebook)'),
(252, '2026-03-20 08:17:21', 6, 'add_item', 'Admin \'นายนพดล เหล่าเขตกิจ\' (ID: 6) \r\n                     ได้เพิ่มอุปกรณ์ชิ้นใหม่ (ItemID: 57, Name: Asus) \r\n                     ลงในประเภท (TypeID: 13)'),
(253, '2026-03-20 08:18:26', 6, 'login_password', 'พนักงาน \'นายนพดล เหล่าเขตกิจ\' (Username: 6290123) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(254, '2026-03-20 08:18:41', 6, 'approve_request', 'Admin \'นายนพดล เหล่าเขตกิจ\' (ID: 6) ได้อนุมัติคำขอ (TID: 58) สำหรับอุปกรณ์ (Item ID: 57)'),
(255, '2026-03-22 11:16:21', 6, 'login_password', 'พนักงาน \'นายนพดล เหล่าเขตกิจ\' (Username: 6290123) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(256, '2026-03-22 11:23:44', 6, 'login_password', 'พนักงาน \'นายนพดล เหล่าเขตกิจ\' (Username: 6290123) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(257, '2026-03-22 11:26:04', 6, 'login_password', 'พนักงาน \'นายนพดล เหล่าเขตกิจ\' (Username: 6290123) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(258, '2026-03-22 11:26:14', 6, 'approve_request', 'Admin \'นายนพดล เหล่าเขตกิจ\' (ID: 6) ได้อนุมัติคำขอ (TID: 59) สำหรับอุปกรณ์ (Item ID: 42)'),
(259, '2026-03-23 02:34:09', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(260, '2026-03-23 03:26:54', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(261, '2026-03-23 16:13:25', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(262, '2026-03-25 05:38:10', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(263, '2026-03-25 09:06:04', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(264, '2026-03-26 08:06:45', 1, 'Updated Admin', 'แก้ไขข้อมูลเจ้าหน้าที่: test02 (test)'),
(265, '2026-03-26 08:12:53', 1, 'Deleted Admin', 'ลบเจ้าหน้าที่ ID: 4 เรียบร้อยแล้ว'),
(266, '2026-04-01 00:53:52', 1, 'add_type', 'Admin \'Admin_Napat\' (ID: 1) \r\n                     ได้เพิ่มประเภทอุปกรณ์ใหม่ (Type ID: 14, Name: ทดสอบ)'),
(267, '2026-04-01 01:24:15', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(268, '2026-04-01 01:24:34', 9, 'add_item', 'Admin \'Admin_Napat\' (ID: 9) \r\n                     ได้เพิ่มอุปกรณ์ชิ้นใหม่ (ItemID: 58, Name: ทดสอบ) \r\n                     ลงในประเภท (TypeID: 14)'),
(269, '2026-04-01 01:28:24', 9, 'approve_request', 'Admin \'Admin_Napat\' (ID: 9) ได้อนุมัติคำขอ (TID: 60) สำหรับอุปกรณ์ (Item ID: 58)'),
(270, '2026-04-01 01:37:48', 9, 'approve_request', 'Admin \'Admin_Napat\' (ID: 9) ได้อนุมัติคำขอ (TID: 61) สำหรับอุปกรณ์ (Item ID: 53)'),
(271, '2026-04-01 01:42:49', 9, 'return_equipment', 'Admin \'Admin_Napat\' (ID: 9) \r\n                     ได้บันทึกการคืนอุปกรณ์ (ItemID: 58, TID: 60)'),
(272, '2026-04-01 08:51:15', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(273, '2026-04-01 08:51:34', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(274, '2026-04-01 09:38:40', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(275, '2026-04-01 09:39:39', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(276, '2026-04-01 09:40:48', 9, 'login_password', 'พนักงาน \'Admin_Napat\' (Username: 6300710) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(277, '2026-04-01 09:41:10', 6, 'login_password', 'พนักงาน \'นายนพดล เหล่าเขตกิจ\' (Username: 6290123) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(278, '2026-04-01 09:50:38', 9, 'edit_staff', 'Admin \'Admin_Napat\' (ID: 9) ได้แก้ไขข้อมูลบัญชีพนักงาน (UID: 3, Username: admin) (มีการ Reset รหัสผ่าน)'),
(279, '2026-04-01 10:46:10', 3, 'login_password', 'พนักงาน \'ผู้ดูแลระบบ\' (Username: admin) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(280, '2026-04-01 11:10:29', 9, 'delete_equipment_item', 'Admin \'Admin_Napat\' ได้ลบอุปกรณ์ (ItemID: 58) ออกจากประเภท (TypeID: 14) (พร้อมประวัติการยืมทั้งหมด)'),
(281, '2026-04-01 11:10:39', 9, 'delete_type', 'Admin \'Admin_Napat\' (ID: 9) \r\n                         ได้ลบประเภทอุปกรณ์ (Type ID: 14, Name: ทดสอบ)'),
(282, '2026-04-01 18:33:00', 3, 'login_password', 'พนักงาน \'ผู้ดูแลระบบ\' (Username: admin) ได้เข้าสู่ระบบ (ผ่าน Password)'),
(283, '2026-04-01 18:33:29', 3, 'approve_request', 'Admin \'ผู้ดูแลระบบ\' (ID: 3) ได้อนุมัติคำขอ (TID: 56) สำหรับอุปกรณ์ (Item ID: 50)'),
(284, '2026-04-01 18:55:46', 9, 'direct_payment', 'Admin \'Admin_Napat\' รับชำระเงิน (Direct, cash) ยอด 550 บาท (TID: 53)'),
(285, '2026-04-01 18:55:50', 9, 'return_equipment', 'Admin \'Admin_Napat\' (ID: 9) \r\n                     ได้บันทึกการคืนอุปกรณ์ (ItemID: 41, TID: 53)');

-- --------------------------------------------------------

--
-- Table structure for table `sys_admins`
--

CREATE TABLE `sys_admins` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','editor') DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `sys_admins`
--

INSERT INTO `sys_admins` (`id`, `full_name`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin_Napat', '6300710', 'napat.trimongkol@gmail.com', '$2y$10$0uwNwE1nomNwu3HoJbOlyO8eprzOVJSU0i9uLBkHOj8p3uP0yvT32', 'admin', '2026-03-25 00:37:48'),
(2, 'Administrator', 'admin-campaign', 'admin@example.com', '$2y$10$.wfQDTNBH5N.9Y3LddXb6e2bjkCQ/41qoxztPxCBbyJThchrE4IEC', 'admin', '2026-03-25 00:57:20'),
(3, 'Admin_Nopadol', 'Nopadol', 'Nopadol.l@rsu.ac.th', '$2y$10$6QQvMd5PJv6hQV1mNt4U4et35YEH/4JKDFN868X/bT2mRPTiwROs2', 'admin', '2026-03-25 01:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `sys_error_logs`
--

CREATE TABLE `sys_error_logs` (
  `id` int(10) UNSIGNED NOT NULL,
  `level` enum('error','warning','info') NOT NULL DEFAULT 'error',
  `source` varchar(300) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `context` text NOT NULL DEFAULT '',
  `ip_address` varchar(45) NOT NULL DEFAULT '',
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sys_error_logs`
--

INSERT INTO `sys_error_logs` (`id`, `level`, `source`, `message`, `context`, `ip_address`, `user_id`, `created_at`) VALUES
(1, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:5', '175.176.222.7', 1, '2026-04-01 11:54:29'),
(2, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:6', '175.176.222.7', 1, '2026-04-01 11:54:29'),
(3, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:7', '175.176.222.7', 1, '2026-04-01 11:54:29'),
(4, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:8', '175.176.222.7', 1, '2026-04-01 11:54:29'),
(5, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:5', '175.176.222.7', 1, '2026-04-01 11:56:21'),
(6, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:6', '175.176.222.7', 1, '2026-04-01 11:56:21'),
(7, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:7', '175.176.222.7', 1, '2026-04-01 11:56:21'),
(8, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:8', '175.176.222.7', 1, '2026-04-01 11:56:21'),
(9, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:5', '175.176.222.7', 1, '2026-04-01 12:01:37'),
(10, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:6', '175.176.222.7', 1, '2026-04-01 12:01:37'),
(11, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:7', '175.176.222.7', 1, '2026-04-01 12:01:37'),
(12, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:8', '175.176.222.7', 1, '2026-04-01 12:01:37'),
(13, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:5', '10.210.6.24', 9, '2026-04-01 13:13:26'),
(14, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:6', '10.210.6.24', 9, '2026-04-01 13:13:26'),
(15, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:7', '10.210.6.24', 9, '2026-04-01 13:13:26'),
(16, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:8', '10.210.6.24', 9, '2026-04-01 13:13:26'),
(17, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:5', '10.210.6.24', 1, '2026-04-01 13:14:11'),
(18, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:6', '10.210.6.24', 1, '2026-04-01 13:14:11'),
(19, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:7', '10.210.6.24', 1, '2026-04-01 13:14:11'),
(20, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:8', '10.210.6.24', 1, '2026-04-01 13:14:11'),
(21, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:5', '10.210.6.24', 1, '2026-04-01 13:15:13'),
(22, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:6', '10.210.6.24', 1, '2026-04-01 13:15:13'),
(23, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:7', '10.210.6.24', 1, '2026-04-01 13:15:13'),
(24, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:8', '10.210.6.24', 1, '2026-04-01 13:15:13'),
(25, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:5', '10.210.6.24', 1, '2026-04-01 13:17:19'),
(26, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:6', '10.210.6.24', 1, '2026-04-01 13:17:19'),
(27, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:7', '10.210.6.24', 1, '2026-04-01 13:17:19'),
(28, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\portal\\includes\\auth.php:8', '10.210.6.24', 1, '2026-04-01 13:17:19'),
(29, 'warning', 'auth.php:5', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:5', '10.210.6.24', 1, '2026-04-01 13:17:21'),
(30, 'warning', 'auth.php:6', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:6', '10.210.6.24', 1, '2026-04-01 13:17:21'),
(31, 'warning', 'auth.php:7', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:7', '10.210.6.24', 1, '2026-04-01 13:17:21'),
(32, 'warning', 'auth.php:8', 'ini_set(): Session ini settings cannot be changed when a session is active', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\admin\\includes\\auth.php:8', '10.210.6.24', 1, '2026-04-01 13:17:21'),
(33, 'warning', 'mail_helper.php:58', 'stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages:\nerror:0A00010B:SSL routines::wrong version number', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\includes\\mail_helper.php:58', '49.49.220.42', 22, '2026-04-01 14:13:56'),
(34, 'warning', 'mail_helper.php:58', 'stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages:\nerror:0A00010B:SSL routines::wrong version number', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\includes\\mail_helper.php:58', '10.210.6.24', 1, '2026-04-01 14:14:27'),
(35, 'warning', 'mail_helper.php:58', 'stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages:\nerror:0A00010B:SSL routines::wrong version number', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\includes\\mail_helper.php:58', '10.210.6.24', 1, '2026-04-01 14:16:05'),
(36, 'warning', 'mail_helper.php:58', 'stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages:\nerror:0A00010B:SSL routines::wrong version number', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\includes\\mail_helper.php:58', '49.49.220.42', 22, '2026-04-01 14:16:32'),
(37, 'warning', 'mail_helper.php:58', 'stream_socket_enable_crypto(): SSL operation failed with code 1. OpenSSL Error messages:\nerror:0A00010B:SSL routines::wrong version number', 'C:\\inetpub\\vhosts\\healthycampus.rsu.ac.th\\httpdocs\\e-campaignv2\\includes\\mail_helper.php:58', '10.210.6.24', 1, '2026-04-01 14:16:39');

-- --------------------------------------------------------

--
-- Table structure for table `sys_staff`
--

CREATE TABLE `sys_staff` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL COMMENT 'ชื่อผู้ใช้ (สำหรับ Log in)',
  `password_hash` varchar(255) NOT NULL COMMENT 'รหัสผ่าน (ที่เข้ารหัสแล้ว)',
  `full_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อ-สกุล จริง',
  `role` enum('admin','employee','editor') DEFAULT NULL,
  `account_status` enum('active','disabled') NOT NULL DEFAULT 'active',
  `linked_line_user_id` varchar(255) DEFAULT NULL COMMENT 'LINE User ID ที่เชื่อมโยงกับบัญชีเจ้าหน้าที่นี้'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sys_staff`
--

INSERT INTO `sys_staff` (`id`, `username`, `password_hash`, `full_name`, `role`, `account_status`, `linked_line_user_id`) VALUES
(3, 'admin', '$2y$10$MC.eXvc5FJZvct7QJ9pBje9yFxWEGV12h/SIpjYHqWn8qcSOUrbpe', 'ผู้ดูแลระบบ', 'admin', 'active', NULL),
(5, '4703121', '$2y$10$qf6cTUyxgpvLpKVoQQbxIuIEO4tkahO7qKosD41JcIFWUTyEGh0CO', 'ผศ.ดร.มนพร ชาติชำนิ', 'employee', 'active', NULL),
(6, '6290123', '$2y$10$yLIMB/UPaSmRLmm5IQxCjOdRbBq6iVlmb7D2vcFXUM9cQQF/2qHH2', 'นายนพดล เหล่าเขตกิจ', 'editor', 'active', NULL),
(9, '6300710', '$2y$10$sDW/kzM8iL7./j4aDVOsUuPrB5sDe0yctE3IjzSmXNlZEHSQWJol2', 'Admin_Napat', 'admin', 'active', NULL),
(11, '6507760', '$2y$10$LKrM3QZq0ygi6UQwTxcJN.OCvjVak5T3jS3WGip7X5c/TCOzPtf2W', 'น.ส.อธิชา  เจริญใจธนะกุล', 'employee', 'active', NULL),
(12, '6507869', '$2y$10$CROnIQklFHtIcU19de5R3.0S.HbRm.9IMZtTEc93CWy9u0DR2LDTi', 'น.ส.กรวรรณ  เทวินโท', 'employee', 'active', NULL),
(13, '6790015', '$2y$10$cm/dA1XHwgtzBPzh6U1srOswUmSmGlYLEkj19cI0cBNrJ.FY/dHty', 'น.ส.อุมา คุ้มวงศ์', 'employee', 'active', NULL),
(14, '6790057', '$2y$10$H6yRJbrqXYYnar.rtksZMurqhMTM.EXbkAP2fEBYnwey8b5MOu8jy', 'น.ส.ภัทราพร จันทรังษี', 'employee', 'active', NULL),
(15, '6890072', '$2y$10$mkQL.Ay1Ijo7bLm990Vko.1EaItHyfvGvNwvP7sUNJysVHN5FS2wu', 'น.ส.กัลย์สุดา แสงรัศมีธนสิน', 'employee', 'active', NULL),
(16, '6790162', '$2y$10$O.PZ2kuxM8rai89ocQlqhe3k/JTMQ2gZUTZQeKE.Fg/7h53cCRXNC', 'น.ส.ศุภิสชา เจริญวงศ์', 'employee', 'active', NULL),
(17, '6790155', '$2y$10$NxJ2nl/.wtQUS0ozTNhoPOiXOGd1DQX02zs0TTr74FrBXm7liriw.', 'น.ส.วาสนา เฉยพินิจ', 'employee', 'active', NULL),
(18, 'folk1947', '$2y$10$krdm6k6K1ZcikDSW9C7tgewY396XQibhP9DZEi5fW06WXVJ.F1zy6', 'test', 'employee', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sys_users`
--

CREATE TABLE `sys_users` (
  `id` int(11) NOT NULL,
  `line_user_id` varchar(255) DEFAULT NULL COMMENT 'User ID ที่ได้จาก LINE (NULLable for staff-added)',
  `full_name` varchar(255) NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `department` varchar(255) DEFAULT NULL COMMENT 'คณะ/หน่วยงาน/สถาบัน',
  `status` enum('student','teacher','staff','other') NOT NULL COMMENT 'สถานภาพ',
  `status_other` varchar(255) DEFAULT NULL COMMENT 'ระบุสถานภาพอื่นๆ',
  `student_personnel_id` varchar(100) DEFAULT NULL COMMENT 'รหัสนักศึกษา/บุคลากร',
  `citizen_id` varchar(13) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL COMMENT 'เบอร์โทรศัพท์',
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='โปรไฟล์ผู้ใช้ที่ Login ผ่าน LINE';

--
-- Dumping data for table `sys_users`
--

INSERT INTO `sys_users` (`id`, `line_user_id`, `full_name`, `department`, `status`, `status_other`, `student_personnel_id`, `citizen_id`, `phone_number`, `email`, `created_at`, `updated_at`) VALUES
(44, 'U7fbb98dfabcb9241e3a3e32f298aa7e0', 'Nopadol', NULL, 'staff', NULL, '6290123', '1620400190039', '0801457167', 'nopadol.l@rsu.ac.th', '2026-04-02 04:20:40', '2026-04-02 04:21:36');

-- --------------------------------------------------------

--
-- Table structure for table `vac_appointments`
--

CREATE TABLE `vac_appointments` (
  `id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `vaccine_id` int(11) DEFAULT NULL,
  `status` enum('pending','confirmed','cancelled') NOT NULL DEFAULT 'confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vac_appointments`
--

INSERT INTO `vac_appointments` (`id`, `slot_id`, `student_id`, `vaccine_id`, `status`, `created_at`) VALUES
(1, 3, 33, NULL, 'confirmed', '2026-03-19 08:37:19'),
(2, 1, 33, NULL, 'confirmed', '2026-03-19 08:38:46'),
(3, 3, 33, NULL, 'confirmed', '2026-03-19 08:48:37'),
(4, 2, 22, NULL, 'cancelled', '2026-03-19 09:45:23'),
(5, 1, 22, NULL, 'cancelled', '2026-03-19 10:08:51'),
(6, 2, 22, NULL, 'cancelled', '2026-03-19 10:29:22'),
(7, 1, 22, NULL, 'cancelled', '2026-03-19 10:53:20'),
(8, 1, 22, NULL, 'cancelled', '2026-03-19 11:15:02'),
(9, 2, 22, NULL, 'cancelled', '2026-03-19 11:19:04'),
(10, 1, 22, NULL, 'cancelled', '2026-03-19 11:24:49'),
(11, 1, 35, NULL, 'confirmed', '2026-03-19 11:52:14'),
(12, 3, 36, NULL, 'confirmed', '2026-03-19 13:19:03'),
(13, 1, 38, NULL, 'cancelled', '2026-03-19 13:56:13'),
(14, 1, 22, NULL, 'cancelled', '2026-03-19 14:18:39'),
(15, 3, 22, NULL, 'cancelled', '2026-03-19 14:19:58'),
(16, 3, 38, NULL, 'confirmed', '2026-03-19 15:24:07'),
(17, 6, 22, NULL, 'cancelled', '2026-03-20 03:01:01');

-- --------------------------------------------------------

--
-- Table structure for table `vac_list`
--

CREATE TABLE `vac_list` (
  `id` int(11) NOT NULL,
  `vaccine_name` varchar(255) NOT NULL,
  `total_stock` int(11) NOT NULL DEFAULT 0,
  `available_until` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- Dumping data for table `vac_list`
--

INSERT INTO `vac_list` (`id`, `vaccine_name`, `total_stock`, `available_until`, `status`, `created_at`) VALUES
(1, 'ไข้หวัดใหญ่ 3 สายพันธุ์', 500, '2026-08-20', 'active', '2026-03-20 04:28:00');

-- --------------------------------------------------------

--
-- Table structure for table `vac_time_slots`
--

CREATE TABLE `vac_time_slots` (
  `id` int(11) NOT NULL,
  `slot_date` date NOT NULL COMMENT 'วันที่เปิดรับจอง',
  `start_time` time NOT NULL COMMENT 'เวลาเริ่ม',
  `end_time` time NOT NULL COMMENT 'เวลาสิ้นสุด',
  `max_capacity` int(11) NOT NULL DEFAULT 50 COMMENT 'จำนวนโควต้า'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vac_time_slots`
--

INSERT INTO `vac_time_slots` (`id`, `slot_date`, `start_time`, `end_time`, `max_capacity`) VALUES
(7, '2026-03-21', '08:00:00', '10:00:00', 30);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `borrow_categories`
--
ALTER TABLE `borrow_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `borrow_fines`
--
ALTER TABLE `borrow_fines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `borrow_items`
--
ALTER TABLE `borrow_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `borrow_payments`
--
ALTER TABLE `borrow_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fine_id` (`fine_id`);

--
-- Indexes for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipment_id` (`equipment_id`),
  ADD KEY `fk_lending_staff` (`lending_staff_id`),
  ADD KEY `equipment_type_id` (`equipment_type_id`);

--
-- Indexes for table `camp_bookings`
--
ALTER TABLE `camp_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `campaign_id` (`campaign_id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `camp_list`
--
ALTER TABLE `camp_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `camp_slots`
--
ALTER TABLE `camp_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaign_id` (`campaign_id`);

--
-- Indexes for table `sys_activity_logs`
--
ALTER TABLE `sys_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `sys_admins`
--
ALTER TABLE `sys_admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `sys_error_logs`
--
ALTER TABLE `sys_error_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_level` (`level`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `sys_staff`
--
ALTER TABLE `sys_staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `linked_line_user_id` (`linked_line_user_id`);

--
-- Indexes for table `sys_users`
--
ALTER TABLE `sys_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `line_user_id` (`line_user_id`),
  ADD UNIQUE KEY `line_user_id_2` (`line_user_id`);

--
-- Indexes for table `vac_appointments`
--
ALTER TABLE `vac_appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vac_list`
--
ALTER TABLE `vac_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vac_time_slots`
--
ALTER TABLE `vac_time_slots`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `borrow_categories`
--
ALTER TABLE `borrow_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `borrow_fines`
--
ALTER TABLE `borrow_fines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `borrow_items`
--
ALTER TABLE `borrow_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `borrow_payments`
--
ALTER TABLE `borrow_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `borrow_records`
--
ALTER TABLE `borrow_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `camp_bookings`
--
ALTER TABLE `camp_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `camp_list`
--
ALTER TABLE `camp_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `camp_slots`
--
ALTER TABLE `camp_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `sys_activity_logs`
--
ALTER TABLE `sys_activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=286;

--
-- AUTO_INCREMENT for table `sys_admins`
--
ALTER TABLE `sys_admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sys_error_logs`
--
ALTER TABLE `sys_error_logs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `sys_staff`
--
ALTER TABLE `sys_staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `sys_users`
--
ALTER TABLE `sys_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `vac_appointments`
--
ALTER TABLE `vac_appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `vac_list`
--
ALTER TABLE `vac_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vac_time_slots`
--
ALTER TABLE `vac_time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `borrow_records`
--
ALTER TABLE `borrow_records`
  ADD CONSTRAINT `borrow_records_ibfk_1` FOREIGN KEY (`equipment_id`) REFERENCES `borrow_items` (`id`),
  ADD CONSTRAINT `fk_lending_staff` FOREIGN KEY (`lending_staff_id`) REFERENCES `sys_staff` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `camp_bookings`
--
ALTER TABLE `camp_bookings`
  ADD CONSTRAINT `camp_bookings_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `sys_users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `camp_bookings_ibfk_2` FOREIGN KEY (`campaign_id`) REFERENCES `camp_list` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `camp_bookings_ibfk_3` FOREIGN KEY (`slot_id`) REFERENCES `camp_slots` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `camp_slots`
--
ALTER TABLE `camp_slots`
  ADD CONSTRAINT `camp_slots_ibfk_1` FOREIGN KEY (`campaign_id`) REFERENCES `camp_list` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
