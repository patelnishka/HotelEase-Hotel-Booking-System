-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2026 at 09:23 AM
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
-- Database: `hotel_ease_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `a_id` int(5) NOT NULL,
  `a_name` varchar(20) NOT NULL,
  `a_email` varchar(30) NOT NULL,
  `a_pwd` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`a_id`, `a_name`, `a_email`, `a_pwd`) VALUES
(1, 'admin', 'admin@hotelease.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `u_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `total_pay` decimal(10,2) NOT NULL,
  `advance` decimal(10,2) NOT NULL,
  `order_id` varchar(150) NOT NULL,
  `trans_id` varchar(150) DEFAULT NULL,
  `status` enum('pending','booked','cancelled') DEFAULT 'pending',
  `arrival` tinyint(1) NOT NULL DEFAULT 0,
  `room_no` varchar(50) DEFAULT NULL,
  `refund` tinyint(1) DEFAULT NULL,
  `datentime` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `u_id`, `room_id`, `check_in`, `check_out`, `total_pay`, `advance`, `order_id`, `trans_id`, `status`, `arrival`, `room_no`, `refund`, `datentime`) VALUES
(1, 1, 7, '2026-02-07', '2026-02-08', 1800.00, 180.00, 'ORD_133284', 'pay_SCjDanAdXrWEzU', 'booked', 1, '101', NULL, '2026-02-06 09:58:52'),
(2, 3, 7, '2026-02-10', '2026-02-11', 1800.00, 180.00, 'ORD_372474', 'pay_SCm7HyEUM1kYTe', 'booked', 2, '111', NULL, '2026-02-06 12:48:16'),
(3, 2, 5, '2026-02-27', '2026-02-28', 2500.00, 250.00, 'ORD_275769', 'pay_SKm376jc2KvtyI', 'cancelled', 0, NULL, NULL, '2026-02-26 17:57:05'),
(4, 2, 3, '2026-03-29', '2026-03-30', 3833.33, 383.33, 'ORD_219417', 'pay_SLukfN1QRnRUc2', 'booked', 0, NULL, NULL, '2026-03-01 15:06:54'),
(5, 3, 7, '2026-03-04', '2026-03-06', 3600.00, 360.00, 'ORD_324228', 'pay_SMiwZVRebMWNOo', 'booked', 0, NULL, NULL, '2026-03-03 16:10:36'),
(6, 1, 7, '2026-03-10', '2026-03-11', 1800.00, 180.00, 'ORD_198142', NULL, 'pending', 0, NULL, NULL, '2026-03-09 13:51:36');

-- --------------------------------------------------------

--
-- Table structure for table `contact_queries`
--

CREATE TABLE `contact_queries` (
  `id` int(5) NOT NULL,
  `name` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `date` datetime DEFAULT current_timestamp(),
  `admin_reply` text DEFAULT NULL,
  `status` int(11) DEFAULT 0 COMMENT '0: Pending, 1: Replied'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_queries`
--

INSERT INTO `contact_queries` (`id`, `name`, `email`, `subject`, `message`, `seen`, `date`, `admin_reply`, `status`) VALUES
(1, 'Nishka Patel', 'nishka@123gmail.com', 'Inquiry regarding Check-in Time', 'want to Inquiry regarding Check-in Time', 1, '2026-02-06 10:03:23', 'your check in time is 10:00 AM', 1),
(2, 'ttt', 'fgfgf@gmail.com', 'dfdf', 'fdfdfdf', 1, '2026-02-06 12:50:26', 'good', 1);

-- --------------------------------------------------------

--
-- Table structure for table `features`
--

CREATE TABLE `features` (
  `f_id` int(5) NOT NULL,
  `f_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `features`
--

INSERT INTO `features` (`f_id`, `f_name`) VALUES
(1, 'Free WiFi'),
(2, 'Air Conditioning'),
(3, 'Parking'),
(4, 'Swimming Pool'),
(5, 'Gym'),
(6, 'Room Service'),
(7, 'Breakfast Included'),
(8, 'TV'),
(10, '24x7 Support');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `f_id` int(5) NOT NULL,
  `u_id` int(5) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`f_id`, `u_id`, `rating`, `message`, `created_at`) VALUES
(1, 1, 5, 'I enjoy very much. ', '2026-02-06 06:41:56'),
(2, 2, 4, 'Very Good Hotel For Family and Friends. We enjoyed.', '2026-02-06 06:43:49');

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `g_id` int(5) NOT NULL,
  `g_desc` varchar(150) NOT NULL,
  `g_img` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`g_id`, `g_desc`, `g_img`) VALUES
(1, 'Hotel Ease', 'IMG_54438.jpg'),
(2, 'Hotel Ease', 'IMG_30686.jpg'),
(3, 'Hotel Ease', 'IMG_93208.jpg'),
(4, 'Hotel Ease', 'IMG_48291.jpg'),
(6, 'Hotel Ease', 'IMG_15637.jpg'),
(7, 'Hotel Ease', 'IMG_24828.jpg'),
(8, 'Hotel Ease', 'IMG_49106.jfif'),
(9, 'Hotel Ease', 'IMG_77086.jpg'),
(10, 'Hotel Ease', 'IMG_58734.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `p_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `trans_id` varchar(200) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` varchar(50) DEFAULT 'Online',
  `status` varchar(20) NOT NULL DEFAULT 'success',
  `payment_type` enum('advance','full','remaining') DEFAULT 'advance',
  `pay_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`p_id`, `booking_id`, `trans_id`, `amount`, `method`, `status`, `payment_type`, `pay_date`) VALUES
(1, 1, 'pay_SCjDanAdXrWEzU', 180.00, 'Online', 'success', 'advance', '2026-02-06 09:59:25'),
(2, 2, 'pay_SCm7HyEUM1kYTe', 180.00, 'Online', 'success', 'advance', '2026-02-06 12:49:33'),
(3, 3, 'pay_SKm376jc2KvtyI', 250.00, 'Online', 'success', 'advance', '2026-02-26 17:57:35'),
(4, 4, 'pay_SLukfN1QRnRUc2', 383.33, 'Online', 'success', 'advance', '2026-03-01 15:07:24'),
(5, 5, 'pay_SMiwZVRebMWNOo', 360.00, 'Online', 'success', 'advance', '2026-03-03 16:13:19');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `area` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `adult` int(11) NOT NULL,
  `children` int(11) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `area`, `price`, `quantity`, `adult`, `children`, `description`, `status`) VALUES
(1, 'Standard Room', 220, 2000.00, 3, 2, 1, 'Comfortable room suitable for solo travelers or couples.', 1),
(2, 'Deluxe Room', 300, 3000.00, 5, 2, 2, 'Spacious room with modern interiors and extra comfort.', 1),
(3, 'Executive Room', 320, 4000.00, 6, 2, 1, 'Ideal for business travelers with work-friendly facilities.', 1),
(4, 'Suite Room', 500, 6000.00, 3, 3, 2, 'Luxury room with separate living area for premium stay.', 1),
(5, 'Guest Room', 250, 2500.00, 2, 2, 1, 'Comfortable and well-furnished room ideal for short or long stays.', 1),
(6, 'Family Room', 450, 5000.00, 6, 4, 3, 'Large room designed for families with extra beds.', 1),
(7, 'Single Room', 180, 1800.00, 5, 1, 0, 'Perfect for solo travelers with essential comfort.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `room_features`
--

CREATE TABLE `room_features` (
  `rf_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `f_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_features`
--

INSERT INTO `room_features` (`rf_id`, `room_id`, `f_id`) VALUES
(1, 1, 10),
(2, 1, 2),
(3, 1, 1),
(4, 1, 8),
(5, 2, 2),
(6, 2, 1),
(7, 2, 6),
(8, 2, 8),
(9, 3, 2),
(10, 3, 7),
(11, 3, 1),
(12, 3, 8),
(13, 4, 10),
(14, 4, 2),
(15, 4, 7),
(16, 4, 1),
(17, 4, 5),
(18, 4, 4),
(19, 4, 8),
(20, 5, 10),
(21, 5, 2),
(22, 5, 1),
(23, 5, 6),
(24, 5, 8),
(25, 6, 10),
(26, 6, 2),
(27, 6, 1),
(28, 6, 3),
(29, 6, 6),
(30, 6, 8),
(31, 7, 2),
(32, 7, 1),
(33, 7, 6),
(34, 7, 8);

-- --------------------------------------------------------

--
-- Table structure for table `room_images`
--

CREATE TABLE `room_images` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `thumb` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_images`
--

INSERT INTO `room_images` (`id`, `room_id`, `image`, `thumb`) VALUES
(1, 1, 'room_1_1770297686_1.jpg', 1),
(2, 1, 'room_1_1770297686_2.jpg', 0),
(3, 1, 'room_1_1770297686_3.jpg', 0),
(4, 2, 'room_2_1770297865_1.jpg', 1),
(5, 2, 'room_2_1770297865_2.jpg', 0),
(6, 2, 'room_2_1770297865_3.jpg', 0),
(7, 3, 'room_3_1770297973_1.jpg', 1),
(8, 3, 'room_3_1770297973_2.jpg', 0),
(9, 3, 'room_3_1770297973_3.jpg', 0),
(10, 4, 'room_4_1770298175_1.jpg', 1),
(11, 4, 'room_4_1770298175_2.jpg', 0),
(12, 4, 'room_4_1770298175_3.jpg', 0),
(13, 5, 'room_5_1770298275_1.jpg', 1),
(14, 5, 'room_5_1770298275_2.jpg', 0),
(15, 5, 'room_5_1770298275_3.jpg', 0),
(16, 6, 'room_6_1770298384_1.jpg', 1),
(17, 6, 'room_6_1770298384_2.jpg', 0),
(18, 6, 'room_6_1770298384_3.jpg', 0),
(19, 7, 'room_7_1770298480_1.jpg', 1),
(20, 7, 'room_7_1770298480_2.jpg', 0),
(21, 7, 'room_7_1770298480_3.jpg', 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_numbers`
--

CREATE TABLE `room_numbers` (
  `id` int(5) NOT NULL,
  `room_id` int(11) NOT NULL,
  `room_no` varchar(50) NOT NULL,
  `status` enum('available','occupied','maintenance') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_numbers`
--

INSERT INTO `room_numbers` (`id`, `room_id`, `room_no`, `status`) VALUES
(1, 7, '101', 'occupied'),
(2, 7, '111', 'available'),
(3, 1, '202', 'available'),
(4, 3, '201', 'available');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `s_id` int(5) NOT NULL,
  `s_name` varchar(20) NOT NULL,
  `s_desc` varchar(50) NOT NULL,
  `s_icon` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`s_id`, `s_name`, `s_desc`, `s_icon`) VALUES
(1, 'Housekeeping', 'Daily cleaning services to keep rooms neat and hyg', 'fas fa-broom'),
(2, 'Swimming Pool', 'Clean and well-maintained pool for leisure and rel', 'fas fa-swimmer'),
(3, 'Parking Facility', 'Safe and secure on-site parking available for gues', 'fas fa-parking'),
(4, 'Laundry Services', 'Quick and reliable laundry and dry-cleaning servic', 'fas fa-tshirt'),
(5, 'Air Conditioning', 'Fully air-conditioned rooms with temperature contr', 'fas fa-snowflake'),
(6, 'Room Services', '24/7 in-room dining and assistance to ensure comfo', 'fas fa-concierge-bell'),
(7, 'Restaurant & Dining', 'Multi-cuisine restaurant offering breakfast, lunch', 'fas fa-utensils'),
(8, 'Wellness & Spa', 'Relax and rejuvenate with our spa and wellness ser', 'fas fa-spa');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `u_id` int(5) NOT NULL,
  `u_name` varchar(50) NOT NULL,
  `u_address` varchar(255) NOT NULL,
  `u_pincode` varchar(6) DEFAULT NULL,
  `u_gender` varchar(10) NOT NULL,
  `u_email` varchar(50) NOT NULL,
  `u_phone` varchar(15) NOT NULL,
  `u_pwd` varchar(255) NOT NULL,
  `profile_pic` varchar(100) DEFAULT 'default_user.jpg',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`u_id`, `u_name`, `u_address`, `u_pincode`, `u_gender`, `u_email`, `u_phone`, `u_pwd`, `profile_pic`, `created_at`, `status`) VALUES
(1, 'Rhea Kukadia', 'Ranip, Ahmedabad', '380015', 'Female', 'riya@kukadia123gmail.com', '9898804567', '$2y$10$jcqwZNOSWZyWTd5CIX27KuzgliikLQH.A2Y8tumBHDNxHYIBPEihS', 'default_user.jpg', '2026-02-05 13:52:28', 1),
(2, 'Archana Suthar', 'Ghatlodiya, Ahmedabad', '380015', 'Female', 'arch@123gmail.com', '9313278077', '$2y$10$mMiftAfW1UaoozsU1YpbA.1TSU7YYTb6SdohvIFQ8qTmTdVYSbRH2', 'default_user.jpg', '2026-02-06 06:43:10', 1),
(3, 'Nishka Patel', 'Satellite, Ahmedabad', '380015', 'Female', 'nishkapatel2003@gmail.com', '9313278066', '$2y$10$ZZjedXnlkTU0zsVyfWohlOEaBYRsUrWJS7mDFYUixDSvQ3bjxMYD.', 'USER_5745.jpg', '2026-02-06 07:17:30', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`a_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `u_id` (`u_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `contact_queries`
--
ALTER TABLE `contact_queries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `features`
--
ALTER TABLE `features`
  ADD PRIMARY KEY (`f_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`f_id`),
  ADD KEY `u_id` (`u_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`g_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`p_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_features`
--
ALTER TABLE `room_features`
  ADD PRIMARY KEY (`rf_id`),
  ADD KEY `room_id` (`room_id`),
  ADD KEY `f_id` (`f_id`);

--
-- Indexes for table `room_images`
--
ALTER TABLE `room_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_no` (`room_no`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`s_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`u_id`),
  ADD UNIQUE KEY `u_email` (`u_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `a_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `contact_queries`
--
ALTER TABLE `contact_queries`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `features`
--
ALTER TABLE `features`
  MODIFY `f_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `f_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `g_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `room_features`
--
ALTER TABLE `room_features`
  MODIFY `rf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `room_images`
--
ALTER TABLE `room_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `room_numbers`
--
ALTER TABLE `room_numbers`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `s_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `u_id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`u_id`) REFERENCES `users` (`u_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_features`
--
ALTER TABLE `room_features`
  ADD CONSTRAINT `room_features_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `room_features_ibfk_2` FOREIGN KEY (`f_id`) REFERENCES `features` (`f_id`) ON DELETE CASCADE;

--
-- Constraints for table `room_images`
--
ALTER TABLE `room_images`
  ADD CONSTRAINT `room_images_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `room_numbers`
--
ALTER TABLE `room_numbers`
  ADD CONSTRAINT `room_numbers_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
