-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 17, 2025 at 10:40 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `crafts_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `announcement_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `content`, `announcement_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Set Up Shipping Rates', 'Learn how to set up flexible shipping rates for different regions and customer types to ensure accurate delivery costs.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(2, 'Managing Taxes for E-commerce', 'Understand how to configure taxes for your products and manage different tax rates based on location and tax laws.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(3, 'Payment Gateway Integration', 'Easily integrate popular payment gateways like PayPal, Stripe, and others to accept payments securely.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(4, 'Shipping Rate Zones Setup', 'Configure shipping rate zones to apply different shipping charges for various delivery regions.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(5, 'Configuring Shipping Methods', 'Set up multiple shipping methods, including standard, express, and international shipping options for customers.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(6, 'Tax Exemptions for Products', 'Configure tax exemptions for specific products or customer groups who are eligible for tax-free purchases.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(7, 'Setting Up Multiple Payment Gateways', 'Integrate multiple payment gateways and enable customers to choose their preferred payment method during checkout.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(8, 'Dynamic Shipping Pricing Based on Weight', 'Set up dynamic shipping pricing that adjusts based on the weight or dimensions of the products being ordered.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(9, 'Custom Shipping Rules', 'Create custom shipping rules for special promotions, such as free shipping or discounted rates for specific products or orders.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11'),
(10, 'Fraud Prevention in Payment Gateways', 'Implement fraud prevention measures and security checks to safeguard payment transactions on your website.', '2025-04-17 08:30:11', 'active', '2025-04-17 08:30:11', '2025-04-17 08:30:11');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(3, 'Men'),
(4, 'Women'),
(5, 'Kids'),
(6, 'Home'),
(7, 'Electronics'),
(8, 'Pets'),
(9, 'Handbag'),
(10, 'Shoes'),
(11, 'Jewelry & Accessories'),
(12, 'Makeup'),
(14, 'Cycle');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `ComplaintID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `ComplaintReason` varchar(255) NOT NULL,
  `Text` text NOT NULL,
  `SubmissionDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `custom_requests`
--

CREATE TABLE `custom_requests` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `details` text NOT NULL,
  `estimated_price` decimal(10,2) NOT NULL,
  `status` enum('pending','accepted','declined','in progress','completed') DEFAULT 'pending',
  `seller_response_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `custom_requests`
--

INSERT INTO `custom_requests` (`id`, `customer_id`, `seller_id`, `title`, `details`, `estimated_price`, `status`, `seller_response_message`, `created_at`) VALUES
(1, 12, 11, 'FDe', 'fdes', 34.00, 'pending', NULL, '2025-04-17 06:49:23'),
(2, 12, 11, 'ds', 'ds', 1000.00, 'accepted', 'Yes we will make it', '2025-04-17 06:53:09');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TotalPrice` decimal(10,2) NOT NULL,
  `DeliveryAddress` varchar(255) NOT NULL,
  `OrderStatus` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `PaymentMethod` varchar(50) NOT NULL DEFAULT 'Cash on Delivery',
  `PaymentStatus` enum('unpaid','paid') NOT NULL DEFAULT 'unpaid',
  `OrderTime` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`OrderID`, `UserID`, `TotalPrice`, `DeliveryAddress`, `OrderStatus`, `PaymentMethod`, `PaymentStatus`, `OrderTime`) VALUES
(1, 12, 1353.75, 'jampur', 'Pending', 'Cash on Delivery', 'unpaid', '2025-04-17 12:07:01'),
(2, 12, 76712.50, 'jampur', 'Delivered', 'Cash on Delivery', 'unpaid', '2025-04-17 12:07:01'),
(3, 12, 270.75, 'jampur', 'Pending', 'Cash on Delivery', 'unpaid', '2025-04-17 12:07:01'),
(4, 12, 3790.50, 'jampur', 'Pending', 'Cash on Delivery', 'unpaid', '2025-04-17 12:07:01'),
(8, 12, 285.00, 'Multan Line two', 'Pending', 'Cash on Delivery', 'unpaid', '2025-04-17 12:07:01'),
(9, 12, 285.00, 'Multan Line two', 'Pending', 'Cash on Delivery', 'unpaid', '2025-04-17 12:07:01'),
(10, 12, 285.00, 'erfg', 'Pending', 'Cash on Delivery', 'unpaid', '2025-04-17 12:07:01'),
(11, 12, 190000.00, 'Multan Line two', 'Pending', 'credit_card', 'paid', '2025-04-17 09:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `OrderItemID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `ProductID` int(11) NOT NULL,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`OrderItemID`, `OrderID`, `ProductID`, `Quantity`) VALUES
(1, 1, 1, 5),
(2, 2, 2, 17),
(3, 3, 1, 1),
(4, 4, 1, 14),
(5, 8, 1, 1),
(6, 10, 1, 1),
(7, 11, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `ProductID` int(11) NOT NULL,
  `ProductName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `StockQuantity` int(11) NOT NULL,
  `CategoryID` int(11) DEFAULT NULL,
  `SellerID` int(11) DEFAULT NULL,
  `ImageURL` varchar(255) DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'approved'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`ProductID`, `ProductName`, `Description`, `Price`, `StockQuantity`, `CategoryID`, `SellerID`, `ImageURL`, `Timestamp`, `status`) VALUES
(1, 'product 1', 'daette', 300.00, 11, 11, 11, '6800bdac017d6.jpg', '2024-04-13 10:34:57', 'pending'),
(2, 'product 2', 'afeeg', 5000.00, 3, 3, 11, '6800bdb5748a4.jpg', '2024-04-13 10:34:57', 'approved'),
(3, 'abb', 'asr', 5000.00, 2345, 3, 13, '6800be3eb86ab.jpg', '2024-04-13 08:33:15', 'approved'),
(4, 'grass', 'dfgf', 4000.00, 2345, 3, 13, '6800be46462c5.jpg', '2024-04-13 08:33:38', 'approved'),
(5, 'New Test Product', 'New Test Product', 200000.00, 19, 3, 11, '6800bdbdcb327.jpg', '2025-04-17 03:07:02', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `ReviewID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Comment` text NOT NULL,
  `Rating` int(11) NOT NULL,
  `Image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`ReviewID`, `OrderID`, `UserID`, `Comment`, `Rating`, `Image`) VALUES
(2, 2, 12, 'New Review', 5, '6800ab04455f84.59052599.png');

-- --------------------------------------------------------

--
-- Table structure for table `site_content`
--

CREATE TABLE `site_content` (
  `id` int(11) NOT NULL,
  `content_type` varchar(50) DEFAULT 'main',
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_content`
--

INSERT INTO `site_content` (`id`, `content_type`, `title`, `content`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'main', 'HandCraft Haven', 'Discover Unique Handmade Treasures from Talented Artisans', 'images/hotel.jpg', '2025-04-17 13:24:18', '2025-04-17 13:27:07');

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `StoreID` int(11) NOT NULL,
  `StoreName` varchar(255) NOT NULL,
  `Description` text NOT NULL,
  `Location` varchar(255) NOT NULL,
  `SellerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`StoreID`, `StoreName`, `Description`, `Location`, `SellerID`) VALUES
(2, 'Nasir\'s Store', 'fgh', 'lahore', 11),
(3, 'New Store', 'fd', 'model town lahore', 13);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `OrderID` int(11) NOT NULL,
  `PaymentImage` varchar(255) DEFAULT NULL,
  `PaymentDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `OrderID`, `PaymentImage`, `PaymentDate`) VALUES
(1, 11, 'transactions/1744873940_cs201.png', '2025-04-17 07:12:20');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `usertype` enum('buyer','seller') NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `status` varchar(10) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `usertype`, `email`, `phone`, `status`) VALUES
(11, 'seller', '$2y$10$cAEbPVUDgoAzhUTIED5syO/vSHhFlBjOMBV2u1HRL6jUkWbYY76re', 'seller', 'seller@gmail.com', '031887895222', 'approved'),
(12, 'Buyer', '$2y$10$K17n1/b0aH78M7O1gBc.qeepCZ62NUjM3UGlW6RRyKVyCs4ItbtQ2', 'buyer', 'saifx280@gmail.com', '3176526827', 'approved'),
(13, 'seller2', '$2y$10$rOGbCo70yS2G4IhIQCnzUuSdDGGMZTLyFo3r/ip03NZRnAlMuYuMy', 'seller', 'seller2@gmail.com', '03176526835', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`ComplaintID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `custom_requests`
--
ALTER TABLE `custom_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `seller_id` (`seller_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ProductID` (`ProductID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`ProductID`),
  ADD KEY `CategoryID` (`CategoryID`),
  ADD KEY `SellerID` (`SellerID`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`ReviewID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `site_content`
--
ALTER TABLE `site_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`StoreID`),
  ADD KEY `fk_seller_id` (`SellerID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `ComplaintID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `custom_requests`
--
ALTER TABLE `custom_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `OrderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `OrderItemID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `ProductID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `ReviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `StoreID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Constraints for table `custom_requests`
--
ALTER TABLE `custom_requests`
  ADD CONSTRAINT `custom_requests_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `custom_requests_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `stores` (`SellerID`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`ProductID`) REFERENCES `products` (`ProductID`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`CategoryID`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`SellerID`) REFERENCES `users` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Constraints for table `stores`
--
ALTER TABLE `stores`
  ADD CONSTRAINT `fk_seller_id` FOREIGN KEY (`SellerID`) REFERENCES `users` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`OrderID`) REFERENCES `orders` (`OrderID`);

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`ProductID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
