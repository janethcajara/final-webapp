-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 12:34 PM
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
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders_tbl`
--

CREATE TABLE `orders_tbl` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled','completed') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items_tbl`
--

CREATE TABLE `order_items_tbl` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products_tbl`
--

CREATE TABLE `products_tbl` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `size` varchar(20) DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products_tbl`
--

INSERT INTO `products_tbl` (`product_id`, `product_name`, `description`, `category`, `size`, `color`, `price`, `stock_quantity`, `image_url`, `created_at`) VALUES
(1, 'Nike Air Zoom Pegasus', 'Responsive running shoes with Zoom Air technology for maximum cushioning', 'Running', '9', 'Black/White', 129.99, 50, 'images/nike-pegasus.jpg', '2025-10-29 10:31:40'),
(2, 'Adidas Ultraboost 22', 'Energy-returning running shoes with Boost technology', 'Running', '10', 'Core Black', 189.99, 35, 'images/adidas-ultraboost.jpg', '2025-10-29 10:31:40'),
(3, 'Saucony Ride 15', 'Cushioned running shoes with PWRRUN foam for all-day comfort', 'Running', '8', 'Blue/Silver', 139.99, 40, 'images/saucony-ride.jpg', '2025-10-29 10:31:40'),
(4, 'ASICS Gel-Kayano 28', 'Stability running shoes with GEL technology for support', 'Running', '11', 'Midnight/Midnight', 159.99, 30, 'images/asics-kayano.jpg', '2025-10-29 10:31:40'),
(5, 'Puma Deviate Nitro 2', 'Lightweight running shoes with energy-returning foam', 'Running', '9.5', 'Puma Black', 149.99, 45, 'images/puma-deviate.jpg', '2025-10-29 10:31:40'),
(6, 'Converse Chuck Taylor All Star', 'Classic canvas sneakers for everyday casual wear', 'Casual', '8', 'Navy', 59.99, 100, 'images/converse-chuck.jpg', '2025-10-29 10:31:40'),
(7, 'Vans Old Skool', 'Iconic skate shoes with canvas upper and rubber sole', 'Casual', '9', 'Black/White', 69.99, 80, 'images/vans-oldskool.jpg', '2025-10-29 10:31:40'),
(8, 'Adidas Stan Smith', 'Timeless tennis-inspired sneakers with leather upper', 'Casual', '10', 'White/Green', 89.99, 60, 'images/adidas-stan.jpg', '2025-10-29 10:31:40'),
(9, 'New Balance 574', 'Retro running-inspired casual shoes with suede accents', 'Casual', '8.5', 'Navy', 79.99, 70, 'images/nb-574.jpg', '2025-10-29 10:31:40'),
(10, 'Crocs Classic Clog', 'Comfortable foam clogs with iconic Croslite material', 'Casual', '9', 'Navy', 39.99, 120, 'images/crocs-classic.jpg', '2025-10-29 10:31:40'),
(11, 'Nike Air Force 1 \'07', 'Basketball-inspired shoes with durable leather upper', 'Sports', '10', 'White', 109.99, 55, 'images/nike-af1.jpg', '2025-10-29 10:31:40'),
(12, 'Under Armour Curry 9', 'Basketball shoes with responsive cushioning and ankle support', 'Sports', '11', 'Black/Red', 149.99, 25, 'images/ua-curry.jpg', '2025-10-29 10:31:40'),
(13, 'Puma Suede Classic', 'Basketball shoes with suede upper and rubber outsole', 'Sports', '9', 'Red/Black', 79.99, 65, 'images/puma-suede.jpg', '2025-10-29 10:31:40'),
(14, 'Adidas Harden Vol. 6', 'Basketball shoes with Lightstrike foam for quick movements', 'Sports', '10.5', 'Core Black', 139.99, 30, 'images/adidas-harden.jpg', '2025-10-29 10:31:40'),
(15, 'Nike LeBron 19', 'High-performance basketball shoes with Zoom Air units', 'Sports', '12', 'White/University Red', 199.99, 20, 'images/nike-lebron.jpg', '2025-10-29 10:31:40'),
(16, 'nike', '12332', 'Shoes', '37', 'blue', 2000.00, 4, NULL, '2025-10-30 03:23:46');

-- --------------------------------------------------------

--
-- Table structure for table `users_tbl`
--

CREATE TABLE `users_tbl` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `barangay` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `province` varchar(50) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users_tbl`
--

INSERT INTO `users_tbl` (`user_id`, `username`, `password`, `role`, `first_name`, `last_name`, `email`, `phone`, `street`, `barangay`, `city`, `province`, `zipcode`, `created_at`) VALUES
(1, 'testuser', '$2y$10$mRKaybru2CXevxQ3Rvyv2eqUwGHm2cy3XQQ1tp4uq/gbbWhe/S8/2', 'customer', 'Test', 'User', 'test@example.com', NULL, NULL, NULL, NULL, NULL, NULL, '2025-10-29 12:43:41'),
(3, 'jen', '$2y$10$JlUyIVbg7c44lEvoiHPJEej4rXJwXM7IrLMlp8A2Xy5fz0AXjzOSm', 'customer', 'Janeth1', 'cajara1', 'aaa@gmail.com', '09641614359', '166', 'Libertad', 'calapan city', 'Oriental Mindoro', '5200', '2025-10-29 13:47:09'),
(4, 'janethcajara', '$2y$10$zlccbeEkFK7TCCSHxwY9yeyKCKdMhfKptKF8okaZRu9zKC/sSWM5W', 'admin', 'Janeth', 'Cajara', 'admin@example.com', '09276305886', '166', 'lumangbayan', 'Calapan City', 'Oriental Mindoro', '5200', '2025-10-29 13:54:49'),
(5, 'jane1', '$2y$10$nNEYEFUXZ8qjsB8lLZOdxuqG.M4GAABgz267ZtgqlHVe0HGEMFZKO', 'customer', 'Jen', 'cajara', 'cajara@gmailcom', '09276305886', '166', 'lumangbayan', 'Calapan City', 'Oriental Mindoro', '5200', '2025-10-30 03:20:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders_tbl`
--
ALTER TABLE `orders_tbl`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items_tbl`
--
ALTER TABLE `order_items_tbl`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products_tbl`
--
ALTER TABLE `products_tbl`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users_tbl`
--
ALTER TABLE `users_tbl`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders_tbl`
--
ALTER TABLE `orders_tbl`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items_tbl`
--
ALTER TABLE `order_items_tbl`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products_tbl`
--
ALTER TABLE `products_tbl`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `users_tbl`
--
ALTER TABLE `users_tbl`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders_tbl`
--
ALTER TABLE `orders_tbl`
  ADD CONSTRAINT `orders_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items_tbl`
--
ALTER TABLE `order_items_tbl`
  ADD CONSTRAINT `order_items_tbl_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders_tbl` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_tbl_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products_tbl` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
