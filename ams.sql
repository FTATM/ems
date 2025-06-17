-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 06:05 AM
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
-- Database: `ams`
--

-- --------------------------------------------------------

--
-- Table structure for table `bill`
--

CREATE TABLE `bill` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `startbill_date` date DEFAULT NULL,
  `endbill_date` date DEFAULT NULL,
  `unit_start` decimal(10,2) NOT NULL,
  `unit_end` decimal(10,2) NOT NULL,
  `type` enum('Water','Electricity','Rent') DEFAULT NULL,
  `usageamount` decimal(10,2) DEFAULT NULL,
  `unitprice` decimal(10,4) DEFAULT NULL,
  `totalamount` decimal(10,2) DEFAULT NULL,
  `paymentstatus` enum('Paid','Unpaid','Partially Paid') DEFAULT NULL,
  `PaymentDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

CREATE TABLE `buildings` (
  `id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`id`, `location_id`, `name`) VALUES
(1, 1, 'A1'),
(2, 1, 'A2'),
(3, 1, 'B1'),
(4, 2, 'A1'),
(5, 2, 'A2'),
(6, 2, 'A3');

-- --------------------------------------------------------

--
-- Table structure for table `contract`
--

CREATE TABLE `contract` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `movein_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `device_realtime`
--

CREATE TABLE `device_realtime` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `create_date` date NOT NULL,
  `Kw` decimal(10,2) NOT NULL,
  `KwHr` decimal(10,2) NOT NULL,
  `kVA` decimal(10,2) NOT NULL,
  `kVAHr` decimal(10,2) NOT NULL,
  `kVAR` decimal(10,2) NOT NULL,
  `kVARHr` decimal(10,2) NOT NULL,
  `Vch-P1` decimal(10,2) NOT NULL,
  `Vch-P2` decimal(10,2) NOT NULL,
  `Vch-P3` decimal(10,2) NOT NULL,
  `Amp-L1` decimal(10,2) NOT NULL,
  `Amp-L2` decimal(10,2) NOT NULL,
  `Amp-L3` decimal(10,2) NOT NULL,
  `Amp-N` decimal(10,2) NOT NULL,
  `Pf` decimal(10,2) NOT NULL,
  `Frequency` decimal(10,2) NOT NULL,
  `is_deleted` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device_realtime`
--

INSERT INTO `device_realtime` (`id`, `name`, `create_date`, `Kw`, `KwHr`, `kVA`, `kVAHr`, `kVAR`, `kVARHr`, `Vch-P1`, `Vch-P2`, `Vch-P3`, `Amp-L1`, `Amp-L2`, `Amp-L3`, `Amp-N`, `Pf`, `Frequency`, `is_deleted`) VALUES
(5303, 'Device_67', '2025-06-17', 86.68, 296.66, 88.28, 523.86, 97.10, 283.37, 210.08, 213.39, 216.70, 16.62, 32.65, 13.40, 13.80, 0.05, 49.35, 0),
(5304, 'Device_82', '2025-06-18', 61.07, 572.77, 3.17, 440.11, 10.55, 207.27, 214.40, 219.54, 214.52, 69.88, 31.61, 48.42, 9.45, 0.91, 49.27, 0),
(5305, 'Device_77', '2025-06-19', 11.74, 275.78, 2.68, 306.62, 45.27, 343.72, 207.21, 215.42, 215.49, 55.86, 46.99, 67.35, 19.16, 0.77, 50.95, 0),
(5306, 'Device_8', '2025-06-20', 30.27, 269.23, 43.80, 382.20, 59.71, 838.73, 208.05, 209.92, 205.46, 87.59, 56.11, 17.79, 4.12, 0.50, 50.73, 0),
(5307, 'Device_57', '2025-06-21', 99.53, 282.35, 42.58, 281.94, 13.23, 815.79, 213.64, 219.25, 215.33, 94.49, 42.52, 29.11, 3.60, 0.03, 50.19, 0),
(5308, 'Device_36', '2025-06-22', 93.86, 585.19, 11.01, 794.76, 64.36, 833.78, 204.76, 213.78, 214.61, 58.53, 73.54, 92.11, 7.99, 0.23, 50.94, 0),
(5309, 'Device_7', '2025-06-23', 25.40, 40.48, 44.03, 79.99, 7.91, 155.56, 210.81, 204.71, 211.14, 7.89, 72.28, 37.72, 14.36, 0.46, 49.27, 0),
(5310, 'Device_6', '2025-06-24', 69.12, 272.68, 28.97, 630.53, 28.35, 525.88, 215.58, 206.34, 204.97, 29.15, 71.17, 68.42, 5.72, 0.38, 49.05, 0),
(5311, 'Device_43', '2025-06-25', 94.23, 394.31, 14.47, 540.52, 26.85, 721.00, 215.99, 216.69, 215.47, 36.52, 50.50, 42.93, 12.63, 0.87, 49.91, 0),
(5312, 'Device_97', '2025-06-26', 26.39, 397.49, 19.58, 786.53, 34.53, 366.74, 215.96, 217.78, 201.05, 59.46, 81.58, 29.51, 0.56, 0.25, 49.38, 0),
(5313, 'Device_29', '2025-06-27', 37.11, 975.89, 76.63, 903.78, 22.00, 388.80, 205.68, 205.06, 208.28, 31.07, 31.13, 62.46, 3.78, 0.07, 50.58, 0),
(5314, 'Device_28', '2025-06-28', 57.15, 10.22, 33.65, 651.65, 24.89, 289.43, 214.01, 212.69, 201.40, 44.68, 2.44, 78.13, 16.67, 0.82, 50.23, 0),
(5315, 'Device_22', '2025-06-29', 61.61, 400.44, 15.38, 567.69, 37.70, 182.17, 215.59, 207.04, 208.41, 4.76, 97.61, 73.75, 15.18, 0.58, 50.28, 0),
(5316, 'Device_10', '2025-06-30', 15.11, 447.67, 78.50, 581.89, 55.45, 26.82, 209.41, 205.45, 219.03, 93.88, 84.01, 38.40, 7.99, 0.85, 49.06, 0),
(5317, 'Device_11', '2025-07-01', 37.11, 540.27, 58.79, 318.84, 83.04, 195.55, 209.73, 216.92, 215.40, 31.26, 25.27, 32.59, 17.42, 0.38, 49.56, 0),
(5318, 'Device_30', '2025-07-02', 20.26, 134.09, 6.26, 910.75, 36.59, 97.34, 207.78, 213.05, 201.93, 52.48, 33.42, 9.68, 9.62, 0.12, 49.27, 0),
(5319, 'Device_91', '2025-07-03', 1.67, 348.21, 69.09, 409.85, 97.66, 653.34, 206.74, 214.50, 212.26, 89.02, 61.25, 39.16, 2.41, 0.43, 50.56, 0),
(5320, 'Device_55', '2025-07-04', 56.06, 151.90, 7.78, 933.07, 43.21, 361.25, 210.20, 209.32, 216.01, 60.34, 61.61, 27.05, 10.08, 0.71, 49.06, 0),
(5321, 'Device_72', '2025-07-05', 87.83, 212.14, 42.58, 492.70, 18.60, 452.03, 214.04, 203.09, 213.30, 86.33, 32.07, 1.38, 2.13, 0.49, 49.28, 0),
(5322, 'Device_85', '2025-07-06', 91.72, 12.46, 31.07, 516.16, 64.87, 694.90, 210.57, 211.15, 204.06, 34.25, 10.33, 48.87, 2.68, 0.20, 50.23, 0),
(5323, 'Device_38', '2025-07-07', 35.09, 608.31, 98.89, 119.53, 63.10, 796.29, 201.77, 201.07, 200.05, 85.28, 25.57, 72.01, 16.67, 0.01, 50.08, 0),
(5324, 'Device_56', '2025-07-08', 68.40, 738.53, 64.08, 988.18, 1.87, 128.74, 211.75, 211.05, 219.97, 33.56, 68.26, 40.59, 19.64, 0.69, 50.03, 0),
(5325, 'Device_4', '2025-07-09', 62.40, 981.24, 3.43, 227.93, 3.66, 499.44, 207.74, 208.76, 200.56, 82.55, 4.40, 74.35, 11.71, 0.70, 50.45, 0),
(5326, 'Device_48', '2025-07-10', 98.10, 762.13, 86.76, 51.64, 65.54, 122.01, 212.88, 217.07, 206.71, 11.68, 57.78, 53.87, 19.20, 0.18, 49.08, 0);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`) VALUES
(1, 'Bangkok'),
(2, 'Chiangmai');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `bill_id` int(11) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount_paid` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `building_id` int(11) DEFAULT NULL,
  `name` varchar(10) DEFAULT NULL,
  `size_sqm` float DEFAULT NULL,
  `price_per_month` decimal(10,2) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `id_card` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `full_name`, `phone`, `email`, `password`, `id_card`, `address`) VALUES
(0, '', 'Admin RMS', '0981237654', 'admin@rms.com', '', '1309496012533', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `buildings`
--
ALTER TABLE `buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

--
-- Indexes for table `contract`
--
ALTER TABLE `contract`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `device_realtime`
--
ALTER TABLE `device_realtime`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bill_id` (`bill_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `building_id` (`building_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buildings`
--
ALTER TABLE `buildings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `device_realtime`
--
ALTER TABLE `device_realtime`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5327;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `bill_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `buildings`
--
ALTER TABLE `buildings`
  ADD CONSTRAINT `buildings_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `contract`
--
ALTER TABLE `contract`
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`bill_id`) REFERENCES `bill` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
