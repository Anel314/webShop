-- MySQL dump 10.13  Distrib 8.0.43, for Linux (x86_64)
--
-- Host: localhost    Database: webShop
-- ------------------------------------------------------
-- Server version	8.0.43-0ubuntu0.24.04.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product_unique` (`user_id`,`product_id`),
  KEY `fk_cart_user` (`user_id`),
  KEY `fk_cart_product` (`product_id`),
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (2,1,3,1,'2025-10-25 22:02:32'),(3,2,4,1,'2025-10-25 22:02:32'),(4,2,5,3,'2025-10-25 22:02:32'),(5,3,7,1,'2025-10-25 22:02:32'),(6,3,8,2,'2025-10-25 22:02:32'),(7,1,9,1,'2025-10-25 22:02:32'),(8,2,2,1,'2025-10-25 22:02:32'),(9,3,6,1,'2025-10-25 22:02:32'),(10,1,10,1,'2025-10-25 22:02:32'),(13,1,1,3,'2025-10-29 14:41:56');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Electronics','Devices and gadgets','2025-10-25 22:01:38'),(2,'Books','Printed and digital books','2025-10-25 22:01:38'),(3,'Clothing','Men and women apparel','2025-10-25 22:01:38'),(4,'Home & Kitchen','Household and kitchen essentials','2025-10-25 22:01:38'),(5,'Sports','Sports and fitness items','2025-10-25 22:01:38');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_products`
--

DROP TABLE IF EXISTS `order_products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_op_order` (`order_id`),
  KEY `fk_op_product` (`product_id`),
  CONSTRAINT `fk_op_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_op_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_products`
--

LOCK TABLES `order_products` WRITE;
/*!40000 ALTER TABLE `order_products` DISABLE KEYS */;
INSERT INTO `order_products` VALUES (31,21,1,2,25.99),(32,21,3,1,15.99),(33,22,4,1,59.99),(34,23,7,1,699.00),(35,23,8,2,25.00),(36,24,5,1,19.99),(37,25,9,1,89.00),(38,26,8,1,25.00),(39,27,6,1,49.99),(40,28,7,1,699.00);
/*!40000 ALTER TABLE `order_products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_user` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (21,1,'2025-10-25 22:08:25','processing',120.98,'123 Main St'),(22,2,'2025-10-25 22:08:25','delivered',59.99,'456 Oak Ave'),(23,3,'2025-10-25 22:08:25','shipped',788.00,'789 Pine Rd'),(24,1,'2025-10-25 22:08:25','cancelled',19.99,'123 Main St'),(25,2,'2025-10-25 22:08:25','processing',89.00,'456 Oak Ave'),(26,3,'2025-10-25 22:08:25','delivered',25.00,'789 Pine Rd'),(27,1,'2025-10-25 22:08:25','pending',49.99,'123 Main St'),(28,2,'2025-10-25 22:08:25','processing',699.00,'456 Oak Ave'),(29,3,'2025-10-25 22:08:25','shipped',112.50,'789 Pine Rd'),(30,1,'2025-10-25 22:08:25','delivered',59.99,'123 Main St');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int unsigned NOT NULL DEFAULT '0',
  `category_id` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_product_category` (`category_id`),
  KEY `fk_products_user` (`user_id`),
  CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_products_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Wireless Mouse','Ergonomic wireless mouse',25.99,111,1,'2025-10-25 22:02:06','2025-10-29 14:41:56',1),(2,'Mechanical Keyboard','RGB backlit mechanical keyboard',89.50,78,1,'2025-10-25 22:02:06','2025-10-29 14:46:50',1),(3,'Cookbook','Healthy recipes for everyday cooking',15.99,200,2,'2025-10-25 22:02:06','2025-10-25 22:02:06',2),(4,'Running Shoes','Lightweight and comfortable',59.99,150,5,'2025-10-25 22:02:06','2025-10-25 22:02:06',2),(5,'T-shirt','Cotton T-shirt with logo',19.99,300,3,'2025-10-25 22:02:06','2025-10-25 22:02:06',1),(6,'Blender','High-speed kitchen blender',49.99,80,4,'2025-10-25 22:02:06','2025-10-25 22:02:06',3),(7,'Smartphone','Latest model smartphone',699.00,40,1,'2025-10-25 22:02:06','2025-10-25 22:02:06',3),(8,'Yoga Mat','Non-slip yoga mat',25.00,90,5,'2025-10-25 22:02:06','2025-10-25 22:02:06',2),(9,'Coffee Maker','Automatic drip coffee machine',89.00,60,4,'2025-10-25 22:02:06','2025-10-25 22:02:06',1),(10,'Novel','Best-selling mystery novel',12.50,250,2,'2025-10-25 22:02:06','2025-10-25 22:02:06',3);
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `address` text,
  `profile_image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'jdoe','jdoe@example.com','hash123abc','John','Doe','123 Maple St, Springfield','https://example.com/images/jdoe.jpg','2023-01-12 08:21:33','2023-04-15 14:18:09'),(2,'asmith','asmith@example.com','hash456def','Alice','Smith','456 Oak St, Shelbyville','https://example.com/images/asmith.jpg','2023-02-22 13:05:11','2023-06-01 08:12:33'),(3,'bwayne','bwayne@example.com','hash789ghi','Bruce','Wayne','1007 Mountain Dr, Gotham','https://example.com/images/bwayne.jpg','2023-03-01 16:22:44','2023-08-03 09:44:55'),(4,'ckent','ckent@example.com','hash987xyz','Clark','Kent','344 Clinton St, Metropolis','https://example.com/images/ckent.jpg','2023-04-12 05:19:32','2023-05-10 07:10:23'),(5,'pparker','pparker@example.com','hash159abc','Peter','Parker','20 Ingram St, Queens','https://example.com/images/pparker.jpg','2023-05-23 18:22:11','2023-06-20 16:21:09'),(6,'dprince','dprince@example.com','hash753bcd','Diana','Prince','10 Amazon Way, Themyscira','https://example.com/images/dprince.jpg','2023-06-10 11:11:42','2023-07-14 17:31:50'),(7,'tstark','tstark@example.com','hash852klm','Tony','Stark','10880 Malibu Point, Malibu','https://example.com/images/tstark.jpg','2023-07-18 14:54:33','2023-09-01 06:15:23'),(8,'ssummers','ssummers@example.com','hash369pqr','Scott','Summers','1407 Graymalkin Ln, Salem Center','https://example.com/images/ssummers.jpg','2023-08-03 04:18:44','2023-09-15 09:00:00'),(9,'jlane','jlane@example.com','hash963abc','Lois','Lane','555 Daily Planet Ave, Metropolis','https://example.com/images/jlane.jpg','2023-09-22 10:45:11','2023-10-01 12:30:32'),(10,'nromanoff','nromanoff@example.com','hash123xyz','Natasha','Romanoff','321 Red Room Rd, Russia','https://example.com/images/nromanoff.jpg','2023-10-01 06:32:00','2023-10-20 07:15:10'),(11,'srogers','srogers@example.com','hash321qwe','Steve','Rogers','890 Brooklyn St, New York','https://example.com/images/srogers.jpg','2023-01-01 09:00:00','2023-01-05 11:00:00'),(12,'bwilson','bwilson@example.com','hash654wer','Barbara','Wilson','202 Gotham Blvd, Gotham','https://example.com/images/bwilson.jpg','2023-02-14 13:44:44','2023-03-01 14:55:55'),(13,'hsimpson','hsimpson@example.com','hash741asd','Homer','Simpson','742 Evergreen Terrace, Springfield','https://example.com/images/hsimpson.jpg','2023-03-03 16:17:17','2023-06-06 16:18:18'),(14,'mlane','mlane@example.com','hash987lkj','Mary','Lane','1234 Pine St, Smallville','https://example.com/images/mlane.jpg','2023-04-10 09:22:33','2023-05-15 12:44:55'),(15,'rhudson','rhudson@example.com','hash258mnb','Rachel','Hudson','222 Cedar Rd, Star City','https://example.com/images/rhudson.jpg','2023-05-12 08:10:10','2023-05-20 10:12:12'),(16,'mmurdock','mmurdock@example.com','hash456cvb','Matt','Murdock','45 Hell’s Kitchen St, NYC','https://example.com/images/mmurdock.jpg','2023-06-25 11:35:35','2023-07-30 12:45:45'),(17,'blane','blane@example.com','hash951dfg','Brittany','Lane','67 Palm Ave, Central City','https://example.com/images/blane.jpg','2023-07-04 07:15:20','2023-08-08 08:20:30'),(18,'hpotter','hpotter@example.com','hash753vbn','Harry','Potter','4 Privet Drive, Little Whinging','https://example.com/images/hpotter.jpg','2023-08-19 04:30:00','2023-09-01 05:45:00'),(19,'rlupin','rlupin@example.com','hash159ghj','Remus','Lupin','12 Grimmauld Place, London','https://example.com/images/rlupin.jpg','2023-09-09 16:00:00','2023-10-10 17:00:00'),(20,'swilson','swilson@example.com','hash852zxc','Sam','Wilson','210 Falcon Rd, Washington DC','https://example.com/images/swilson.jpg','2023-10-10 18:20:20','2023-10-21 19:21:21');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-29 18:10:16
