-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 05, 2025 at 09:48 AM
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
-- Database: `gown&go`
--

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `Feedback_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Comments` text NOT NULL,
  `Rating` enum('1','2','3','4','5') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items (inventory)`
--

CREATE TABLE `items (inventory)` (
  `Item_ID` int(11) NOT NULL,
  `Item_Name` varchar(255) NOT NULL,
  `Item_Description` text NOT NULL,
  `Size` enum('XS','S','M','L','XL') NOT NULL,
  `Category` varchar(100) NOT NULL,
  `Rental_Price` decimal(10,2) NOT NULL,
  `Purchase_Price` decimal(10,2) NOT NULL,
  `Item_Status` enum('Available','Out of Stock','Rented') NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `Order_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Order_Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Order_Status` enum('Pending','Confirmed','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `Order_Type` enum('Rental','Purchase') NOT NULL,
  `Rental_Start_Date` datetime DEFAULT current_timestamp(),
  `Rental_End_Date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `Order_Item_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Item_ID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Rental_Period` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `Payment_ID` int(11) NOT NULL,
  `Order_ID` int(11) NOT NULL,
  `Payment_Method` varchar(255) NOT NULL,
  `Payment_Status` enum('Pending','Paid','Failed','Refunded') NOT NULL DEFAULT 'Pending',
  `Payment_Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales reports`
--

CREATE TABLE `sales reports` (
  `Report_ID` int(11) NOT NULL,
  `Generated_Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Total_Orders` int(11) NOT NULL,
  `Total_Revenue` decimal(12,2) NOT NULL,
  `Generated_By` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL,
  `User_Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Address` text NOT NULL,
  `Contact_No` varchar(255) NOT NULL,
  `Role` enum('Customer','Admin') NOT NULL,
  `Date_Registered` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`Feedback_ID`),
  ADD UNIQUE KEY `UserID` (`User_ID`,`Order_ID`);

--
-- Indexes for table `items (inventory)`
--
ALTER TABLE `items (inventory)`
  ADD PRIMARY KEY (`Item_ID`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`Order_ID`),
  ADD UNIQUE KEY `User_ID` (`User_ID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`Order_Item_ID`),
  ADD UNIQUE KEY `Order_Item_ID` (`Item_ID`,`Order_ID`) USING BTREE;

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD UNIQUE KEY `Order_ID` (`Order_ID`);

--
-- Indexes for table `sales reports`
--
ALTER TABLE `sales reports`
  ADD PRIMARY KEY (`Report_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `Feedback_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `Order_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `Order_Item_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales reports`
--
ALTER TABLE `sales reports`
  MODIFY `Report_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
