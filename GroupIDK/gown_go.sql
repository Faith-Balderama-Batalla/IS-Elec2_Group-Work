-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 08, 2025 at 04:29 PM
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
-- Database: `gown_and_go`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `comments` text DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED DEFAULT NULL CHECK (`rating` between 1 and 5),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `order_id`, `comments`, `rating`, `created_at`) VALUES
(1, 2, 10, 'Cool', 5, '2025-11-30 12:37:31'),
(2, 2, 13, 'Typing a very long feedback comment to see how it looks like on the page XD', 1, '2025-11-30 16:24:29'),
(3, 2, 11, 'Emperador Light Premium Brandy Liqueur\r\nMeticulously blended to attain an extra smooth character, full body and notably distinctive aroma.', 5, '2025-12-01 13:14:00'),
(4, 2, 12, 'QUALITY', 2, '2025-12-02 12:09:20'),
(5, 2, 9, 'Hi', 5, '2025-12-08 23:06:25');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `rental_price` decimal(10,2) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `status` enum('Available','Out of Stock','Rented') NOT NULL DEFAULT 'Available',
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`item_id`, `name`, `description`, `rental_price`, `purchase_price`, `status`, `stock`, `image`, `created_at`) VALUES
(1, 'Rose Gold Sequin Evening Dress', 'Formal garment, floor-length, rose gold-colored sequins', 2500.00, 3800.00, '', 3, '1764346935_img1_RoseGoldSequinDress.jpg', '2025-11-23 17:23:32'),
(3, 'Midnight Blue Ball Gown', 'A formal dress with a fitted bodice, floor-length skirt, and a rich deep blue hue.', 3000.00, 5000.00, '', 3, '1764346984_img3_MidnightBlueBallGown.jpg', '2025-11-23 17:42:48'),
(4, 'Classic Red Dress', 'An elegant and sophisticated timeless symbol of power, confidence, and romance.', 1800.00, 2500.00, '', 4, '1764346966_img2_ClassicRedDress.jpg', '2025-11-23 17:42:48'),
(5, 'Mindy Gown', 'Dreamy tule dress with bow details.', 3000.00, 6980.00, 'Available', 3, '1764347051_img4_MindyGown.jpg', '2025-11-28 21:59:38'),
(6, 'Suzy Gown', 'Classic square neck with chiffon sleeves.', 2500.00, 5980.00, '', 3, '1764347074_img5_SuzyyGown.jpg', '2025-11-28 21:59:38'),
(7, 'Elizabeth Gown', 'Dreamy tiered tulle gown.', 3500.00, 7980.00, 'Available', 4, '1764347091_img6_ElizabethGown.jpg', '2025-11-28 21:59:38'),
(8, 'Matilda Gown', 'Classic off shoulder tulle gown.', 2500.00, 5980.00, '', 3, '1764347109_img7_MatildaGown.jpg', '2025-11-28 21:59:38'),
(9, 'Margaret Gown', 'Off shoulder gazar gown with separate sash.', 2500.00, 5980.00, '', 3, '1764347123_img8_MargaretGown.jpg', '2025-11-28 21:59:38'),
(10, 'Sadie Gown', 'Puff sleeves gown with asymmetrical skirt.', 2300.00, 4480.00, '', 1, '1764347040_img9_SadieGown.jpg', '2025-11-28 21:59:38');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `order_status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `order_type` enum('Rental','Purchase') NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `delivery_address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `order_status`, `order_type`, `total_amount`, `delivery_address`) VALUES
(1, 2, '2025-11-25 00:14:33', 'Completed', 'Purchase', 2500.00, 'Oas, Albay'),
(2, 2, '2025-11-25 01:34:14', 'Completed', 'Purchase', 8800.00, 'Oas, Albay'),
(3, 2, '2025-11-25 02:02:52', 'Completed', 'Purchase', 5000.00, 'Oas, Albay'),
(4, 2, '2025-11-26 12:32:51', 'Completed', 'Purchase', 3800.00, 'Oas, Albay'),
(5, 2, '2025-11-26 13:08:46', 'Completed', 'Purchase', 6300.00, 'Oas, Albay'),
(8, 3, '2025-11-29 01:04:05', 'Completed', '', 8980.00, 'Ligao'),
(9, 2, '2025-11-30 06:50:07', 'Completed', '', 7980.00, 'Ligao City'),
(10, 2, '2025-11-30 06:50:39', 'Completed', '', 2500.00, 'Oas Albay'),
(11, 2, '2025-11-30 12:37:09', 'Completed', '', 2500.00, 'Ligao City'),
(12, 2, '2025-11-30 13:42:22', 'Completed', '', 10480.00, 'Oas Albay'),
(13, 2, '2025-11-30 14:06:51', 'Completed', '', 6980.00, 'Ligao City'),
(14, 2, '2025-12-01 13:11:03', 'Completed', '', 10980.00, 'Ligao City'),
(15, 2, '2025-12-01 13:42:41', 'Completed', '', 10460.00, 'Daraga Albay'),
(16, 3, '2025-12-01 13:46:03', 'Completed', '', 5000.00, 'Polangui'),
(17, 2, '2025-12-02 12:08:42', 'Completed', '', 3000.00, 'OAS, ALBAY'),
(18, 5, '2025-12-02 12:19:00', 'Completed', '', 4480.00, 'Oas Albay'),
(19, 2, '2025-12-02 13:56:16', 'Completed', '', 7780.00, 'Ligao City'),
(20, 2, '2025-12-08 23:06:10', 'Completed', '', 9480.00, 'Daraga Albay'),
(21, 2, '2025-12-08 23:15:39', 'Completed', '', 2500.00, 'Florida, Philippines');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_detail_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `order_type` enum('Rental','Purchase') NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `rental_period_days` int(11) DEFAULT NULL,
  `return_status` enum('Not Returned','Returned') NOT NULL DEFAULT 'Not Returned',
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_detail_id`, `order_id`, `item_id`, `order_type`, `quantity`, `rental_period_days`, `return_status`, `unit_price`, `subtotal`) VALUES
(1, 1, 4, 'Purchase', 1, NULL, 'Not Returned', 2500.00, 2500.00),
(2, 2, 3, 'Purchase', 1, NULL, 'Not Returned', 5000.00, 5000.00),
(3, 2, 1, 'Purchase', 1, NULL, 'Not Returned', 3800.00, 3800.00),
(4, 3, 3, 'Purchase', 1, NULL, 'Not Returned', 5000.00, 5000.00),
(5, 4, 1, 'Purchase', 1, NULL, 'Not Returned', 3800.00, 3800.00),
(6, 5, 1, 'Purchase', 1, NULL, 'Not Returned', 3800.00, 3800.00),
(7, 5, 4, 'Purchase', 1, NULL, 'Not Returned', 2500.00, 2500.00),
(8, 8, 5, 'Rental', 1, 5, 'Returned', 3000.00, 3000.00),
(9, 8, 6, 'Purchase', 1, 0, 'Not Returned', 5980.00, 5980.00),
(10, 9, 7, 'Purchase', 1, 0, 'Not Returned', 7980.00, 7980.00),
(11, 10, 6, 'Rental', 1, 5, 'Returned', 2500.00, 2500.00),
(12, 11, 6, 'Rental', 1, 5, 'Returned', 2500.00, 2500.00),
(13, 12, 6, 'Rental', 1, 5, 'Returned', 2500.00, 2500.00),
(14, 12, 7, 'Purchase', 1, 0, 'Not Returned', 7980.00, 7980.00),
(15, 13, 5, 'Purchase', 1, 0, 'Not Returned', 6980.00, 6980.00),
(16, 14, 1, 'Rental', 2, 5, 'Returned', 2500.00, 5000.00),
(17, 14, 8, 'Purchase', 1, 0, 'Not Returned', 5980.00, 5980.00),
(18, 15, 8, 'Purchase', 1, 0, 'Not Returned', 5980.00, 5980.00),
(19, 15, 10, 'Purchase', 1, 0, 'Not Returned', 4480.00, 4480.00),
(20, 16, 3, 'Purchase', 1, 0, 'Not Returned', 5000.00, 5000.00),
(21, 17, 3, 'Rental', 1, 5, 'Returned', 3000.00, 3000.00),
(22, 18, 10, 'Purchase', 1, 0, 'Not Returned', 4480.00, 4480.00),
(23, 19, 8, 'Purchase', 1, 0, 'Returned', 5980.00, 5980.00),
(24, 19, 4, 'Rental', 1, 5, 'Returned', 1800.00, 1800.00),
(25, 20, 6, 'Purchase', 1, 0, 'Not Returned', 5980.00, 5980.00),
(26, 20, 7, 'Rental', 1, 5, 'Returned', 3500.00, 3500.00),
(27, 21, 6, 'Rental', 1, 5, 'Returned', 2500.00, 2500.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `payment_status` enum('Pending','Paid','Failed','Refunded') NOT NULL DEFAULT 'Pending',
  `payment_date` datetime DEFAULT current_timestamp(),
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `payment_status`, `payment_date`, `amount`) VALUES
(1, 1, 'Cash on Delivery', 'Pending', '2025-11-25 00:14:33', 2500.00),
(2, 2, 'Cash on Delivery', 'Pending', '2025-11-25 01:34:15', 8800.00),
(3, 3, 'Cash on Delivery', 'Pending', '2025-11-25 02:02:52', 5000.00),
(4, 4, 'Cash on Delivery', 'Pending', '2025-11-26 12:32:51', 3800.00),
(5, 5, 'Cash on Delivery', 'Pending', '2025-11-26 13:08:46', 6300.00),
(6, 8, 'Cash on Delivery', 'Pending', '2025-11-29 01:04:05', 8980.00),
(7, 9, 'Cash on Delivery', 'Pending', '2025-11-30 06:50:07', 7980.00),
(8, 10, 'Cash on Delivery', 'Pending', '2025-11-30 06:50:39', 2500.00),
(9, 10, NULL, 'Paid', '2025-11-30 11:59:42', 2500.00),
(10, 9, NULL, 'Paid', '2025-11-30 12:08:40', 7980.00),
(11, 1, NULL, 'Paid', '2025-11-30 12:08:57', 2500.00),
(12, 2, NULL, 'Paid', '2025-11-30 12:34:28', 8800.00),
(13, 11, 'Cash on Delivery', 'Pending', '2025-11-30 12:37:09', 2500.00),
(14, 11, NULL, 'Paid', '2025-11-30 12:38:32', 2500.00),
(15, 8, NULL, 'Paid', '2025-11-30 12:40:36', 8980.00),
(16, 5, NULL, 'Paid', '2025-11-30 12:41:13', 6300.00),
(17, 12, 'Cash on Delivery', 'Pending', '2025-11-30 13:42:22', 10480.00),
(18, 12, NULL, 'Paid', '2025-11-30 13:43:36', 10480.00),
(19, 3, NULL, 'Paid', '2025-11-30 14:06:09', 5000.00),
(20, 13, 'Cash on Delivery', 'Pending', '2025-11-30 14:06:51', 6980.00),
(21, 13, NULL, 'Paid', '2025-11-30 14:07:08', 6980.00),
(22, 14, 'Cash on Delivery', 'Pending', '2025-12-01 13:11:03', 10980.00),
(23, 14, NULL, 'Paid', '2025-12-01 13:15:53', 10980.00),
(24, 15, 'Cash on Delivery', 'Pending', '2025-12-01 13:42:41', 10460.00),
(25, 15, NULL, 'Paid', '2025-12-01 13:44:27', 10460.00),
(26, 16, 'Cash on Delivery', 'Pending', '2025-12-01 13:46:03', 5000.00),
(27, 17, 'Cash on Delivery', 'Pending', '2025-12-02 12:08:42', 3000.00),
(28, 18, 'Cash on Delivery', 'Pending', '2025-12-02 12:19:00', 4480.00),
(29, 19, 'Cash on Delivery', 'Pending', '2025-12-02 13:56:16', 7780.00),
(30, 16, NULL, 'Paid', '2025-12-02 14:05:50', 5000.00),
(31, 19, NULL, 'Paid', '2025-12-08 22:56:53', 7780.00),
(32, 20, 'Cash on Delivery', 'Pending', '2025-12-08 23:06:10', 9480.00),
(33, 21, 'Cash on Delivery', 'Pending', '2025-12-08 23:15:39', 2500.00),
(34, 21, NULL, 'Paid', '2025-12-08 23:26:10', 2500.00),
(35, 20, NULL, 'Paid', '2025-12-08 23:26:11', 9480.00),
(36, 18, NULL, 'Paid', '2025-12-08 23:26:12', 4480.00),
(37, 17, NULL, 'Paid', '2025-12-08 23:26:13', 3000.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales_reports`
--

CREATE TABLE `sales_reports` (
  `report_id` int(11) NOT NULL,
  `generated_at` datetime DEFAULT current_timestamp(),
  `total_orders` int(11) DEFAULT NULL,
  `total_revenue` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_reports`
--

INSERT INTO `sales_reports` (`report_id`, `generated_at`, `total_orders`, `total_revenue`) VALUES
(1, '2025-11-30 14:06:09', 10, 55040.00),
(2, '2025-11-30 14:07:08', 11, 62020.00),
(3, '2025-12-01 13:15:53', 12, 73000.00),
(4, '2025-12-01 13:44:27', 13, 83460.00),
(5, '2025-12-02 14:05:50', 17, 88460.00),
(6, '2025-12-08 22:56:53', 17, 96240.00),
(7, '2025-12-08 23:26:10', 19, 98740.00),
(8, '2025-12-08 23:26:11', 19, 108220.00),
(9, '2025-12-08 23:26:12', 19, 112700.00),
(10, '2025-12-08 23:26:13', 19, 115700.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `address`, `contact_no`, `role`, `created_at`) VALUES
(1, 'Admin', 'Admin@gmail.com', '$2y$10$Kk189iCXbDPEHXZwKcP0guEjlwYi/QCgtZM7SvlWwnONM5wqlvMmG', 'Ligao City', '09850843823', 'admin', '2025-11-23 15:30:10'),
(2, 'FADE', 'Fade@gmail.com', '$2y$10$FfoqyUU4WI/SyAu349CdhO4mxOHu6y75C.VtwrhaNR0zP.Tg.NQ0y', 'Oas, Albay', '09123456789', 'customer', '2025-11-23 15:57:06'),
(3, 'Bem', 'Bem@gmail.com', '$2y$10$e4cb2zTUY3LOVqs0LfzbkuezNnzZhmZCabontcr0v9aSPx/uf3Hdm', 'Paulog, Ligao City', '09123456789', 'customer', '2025-11-24 21:52:04'),
(4, 'Bonnie', 'Bonnie@gmail.com', '$2y$10$2ME4qz4.c11w9sM/TNQE5OpKnL9xUjdbJN0mcqbNAYBwWhNZ75iTa', 'Paulog Ligao', '09123456789', 'customer', '2025-11-30 19:40:57'),
(5, 'Annaliza', 'annaliza@gmail.com', '$2y$10$HUuJqj.sLN2qSOXTPeiHR.nHH4SS.zclGSiLPbWxYaA/WztmPiH1S', 'Oas Albay', '0987654347', 'customer', '2025-12-02 12:17:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_detail_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `sales_reports`
--
ALTER TABLE `sales_reports`
  ADD PRIMARY KEY (`report_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `sales_reports`
--
ALTER TABLE `sales_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`item_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
