-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2025 at 10:37 AM
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
-- Table structure for table `data_type`
--

CREATE TABLE `data_type` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `create_date` date NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `data_type`
--

INSERT INTO `data_type` (`id`, `name`, `create_date`, `is_deleted`) VALUES
(1, 'kW', '2025-07-10', 0),
(2, 'kWh', '2025-07-10', 0),
(3, 'kVA', '2025-07-10', 0),
(4, 'kVAh', '2025-07-10', 0),
(5, 'kVAR', '2025-07-10', 0),
(6, 'kVARh', '2025-07-10', 0),
(7, 'Vch_P1', '2025-07-10', 0),
(8, 'Vch_P2', '2025-07-10', 0),
(9, 'Vch_P3', '2025-07-10', 0),
(10, 'Amp_L1', '2025-07-10', 0),
(11, 'Amp_L2', '2025-07-10', 0),
(12, 'Amp_L3', '2025-07-10', 0),
(13, 'Amp_N', '2025-07-10', 0),
(14, 'Voltage A_B', '2025-07-10', 0),
(15, 'Voltage B_C', '2025-07-10', 0),
(16, 'Voltage C_A', '2025-07-10', 0),
(17, 'Pf', '2025-07-10', 0),
(18, 'Frequency', '2025-07-10', 0);

-- --------------------------------------------------------

--
-- Table structure for table `electricity_rates`
--

CREATE TABLE `electricity_rates` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL,
  `min_unit` int(11) NOT NULL,
  `max_unit` int(11) DEFAULT NULL,
  `unit_price` decimal(10,4) NOT NULL,
  `is_fixed_rate` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `electricity_readings`
--

CREATE TABLE `electricity_readings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reading_date` date NOT NULL,
  `units_used` decimal(10,2) NOT NULL,
  `calculated_amount` decimal(10,2) DEFAULT NULL,
  `vat_amount` decimal(10,2) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `ft_id` int(11) DEFAULT NULL,
  `type_id` int(11) DEFAULT NULL,
  `calculated_at` datetime DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `electricity_types`
--

CREATE TABLE `electricity_types` (
  `id` int(11) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `service_charge` decimal(10,4) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ft_rates`
--

CREATE TABLE `ft_rates` (
  `id` int(11) NOT NULL,
  `ft_value` decimal(10,4) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
-- Table structure for table `meter`
--

CREATE TABLE `meter` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `room_id` int(11) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `meter`
--

INSERT INTO `meter` (`id`, `name`, `room_id`, `is_deleted`) VALUES
(1, 'Meter Room A1 01', 1, 0),
(2, 'Meter Room A1 02', 2, 0),
(3, 'Meter Room A2 01', 3, 0),
(4, 'Meter Room A2 02', 4, 0),
(5, 'Meter Room A1 01', 5, 0),
(6, 'Meter Room A1 02', 6, 0),
(7, 'Meter Room A2 01', 7, 0),
(8, 'Meter Room A2 02', 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `meter_data`
--

CREATE TABLE `meter_data` (
  `id` int(11) NOT NULL,
  `meter_id` int(11) NOT NULL,
  `create_date` date NOT NULL DEFAULT current_timestamp(),
  `type_value_id` int(11) NOT NULL,
  `value` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `building_id`, `name`, `size_sqm`, `price_per_month`, `status`, `type`, `note`) VALUES
(1, 1, 'Room A1 01', 28, 3800.00, 'empty', 'studio', NULL),
(2, 1, 'Room A1 02', 28, 3800.00, 'empty', 'studio', NULL),
(3, 2, 'Room A2 01', 28, 3800.00, 'empty', 'studio', NULL),
(4, 2, 'Room A2 02', 28, 3800.00, 'empty', 'studio', NULL),
(5, 4, 'Room A1 01', 28, 3600.00, 'empty', 'studio', NULL),
(6, 4, 'Room A1 02', 28, 3600.00, 'empty', 'studio', NULL),
(7, 5, 'Room A2 01', 28, 3600.00, 'empty', 'studio', NULL),
(8, 5, 'Room A2 02', 28, 3600.00, 'empty', 'studio', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `full_name`, `phone`, `email`, `password`, `id_card`, `address`) VALUES
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
-- Indexes for table `data_type`
--
ALTER TABLE `data_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `electricity_rates`
--
ALTER TABLE `electricity_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `electricity_readings`
--
ALTER TABLE `electricity_readings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `type_id` (`type_id`),
  ADD KEY `ft_id` (`ft_id`);

--
-- Indexes for table `electricity_types`
--
ALTER TABLE `electricity_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ft_rates`
--
ALTER TABLE `ft_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meter`
--
ALTER TABLE `meter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `meter_data`
--
ALTER TABLE `meter_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_value_id` (`type_value_id`),
  ADD KEY `meter_id` (`meter_id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
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
-- AUTO_INCREMENT for table `data_type`
--
ALTER TABLE `data_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `electricity_rates`
--
ALTER TABLE `electricity_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `electricity_readings`
--
ALTER TABLE `electricity_readings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `electricity_types`
--
ALTER TABLE `electricity_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ft_rates`
--
ALTER TABLE `ft_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meter`
--
ALTER TABLE `meter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `meter_data`
--
ALTER TABLE `meter_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bill`
--
ALTER TABLE `bill`
  ADD CONSTRAINT `bill_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
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
  ADD CONSTRAINT `contract_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `contract_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `electricity_rates`
--
ALTER TABLE `electricity_rates`
  ADD CONSTRAINT `electricity_rates_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `electricity_types` (`id`);

--
-- Constraints for table `electricity_readings`
--
ALTER TABLE `electricity_readings`
  ADD CONSTRAINT `electricity_readings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `electricity_readings_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `electricity_types` (`id`),
  ADD CONSTRAINT `electricity_readings_ibfk_3` FOREIGN KEY (`ft_id`) REFERENCES `ft_rates` (`id`);

--
-- Constraints for table `meter`
--
ALTER TABLE `meter`
  ADD CONSTRAINT `meter_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Constraints for table `meter_data`
--
ALTER TABLE `meter_data`
  ADD CONSTRAINT `meter_data_ibfk_1` FOREIGN KEY (`type_value_id`) REFERENCES `data_type` (`id`),
  ADD CONSTRAINT `meter_data_ibfk_2` FOREIGN KEY (`meter_id`) REFERENCES `meter` (`id`);

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
