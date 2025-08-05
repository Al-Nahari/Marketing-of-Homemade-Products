-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: productive_families_system
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_log`
--

LOCK TABLES `activity_log` WRITE;
/*!40000 ALTER TABLE `activity_log` DISABLE KEYS */;
INSERT INTO `activity_log` VALUES (1,3,'قام بتسجيل الدخول إلى النظام','2025-08-05 11:58:24'),(2,4,'أضاف منتج جديد: تمر السكري','2025-08-05 11:58:24'),(3,5,'قام بتحديث معلومات المنتج: معمول بالتمر','2025-08-05 11:58:24'),(4,6,'قام بتغيير حالة الطلب رقم #4 إلى قيد الشحن','2025-08-05 11:58:24'),(5,7,'قام بتسجيل الخروج من النظام','2025-08-05 11:58:24'),(6,8,'قام بإنشاء طلب جديد رقم #7','2025-08-05 11:58:24'),(7,9,'قام بتقييم منتج: سجادة يدوية','2025-08-05 11:58:24'),(8,14,'تسجيل دخول ناجح','2025-08-05 13:02:00'),(9,14,'تسجيل دخول ناجح','2025-08-05 13:04:07'),(10,14,'تسجيل دخول ناجح','2025-08-05 13:04:16'),(11,14,'تسجيل دخول ناجح','2025-08-05 13:04:58'),(12,14,'تسجيل خروج','2025-08-05 13:10:00'),(13,14,'تسجيل دخول ناجح','2025-08-05 13:12:31'),(14,14,'تسجيل خروج','2025-08-05 13:12:51'),(15,14,'تسجيل دخول ناجح','2025-08-05 13:13:43'),(16,14,'تغيير حالة العائلة #15 إلى active','2025-08-05 13:27:09');
/*!40000 ALTER TABLE `activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'الأغذية','منتجات غذائية منزلية وصحية'),(2,'المشروبات','مشروبات طبيعية وعصائر منزلية'),(3,'الحلويات','حلويات تقليدية سعودية'),(4,'المصنوعات اليدوية','منتجات يدوية وحرفية'),(5,'العناية الشخصية','منتجات عناية شخصية طبيعية');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaints`
--

DROP TABLE IF EXISTS `complaints`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `complaints` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `message` text,
  `status` enum('pending','resolved') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaints`
--

LOCK TABLES `complaints` WRITE;
/*!40000 ALTER TABLE `complaints` DISABLE KEYS */;
INSERT INTO `complaints` VALUES (1,10,12,'المنتج الذي وصلني لا يشبه المواصفات المذكورة','pending','2025-08-05 11:58:36'),(2,7,9,'تأخر في موعد التسليم المتفق عليه','resolved','2025-08-05 11:58:36'),(3,4,11,'وجدت عيب في المنتج المستلم','pending','2025-08-05 11:58:36');
/*!40000 ALTER TABLE `complaints` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `message` text,
  `is_read` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,3,'لديك طلب جديد على منتج مربى تمر العنبرة',0,'2025-08-05 11:58:12'),(2,4,'تم تأكيد طلبك على تمر السكري',0,'2025-08-05 11:58:12'),(3,5,'قام خالد بتقييم منتجك كليجة سعودية',0,'2025-08-05 11:58:12'),(4,6,'تم شحن طلبك رقم #4',0,'2025-08-05 11:58:12'),(5,7,'تم توصيل طلبك رقم #5',0,'2025-08-05 11:58:12'),(6,8,'شكراً لشرائك من متجرنا، نتمنى لك تجربة سعيدة',0,'2025-08-05 11:58:12'),(7,9,'طلبك قيد التحضير وسيتم شحنه قريباً',0,'2025-08-05 11:58:12'),(8,2,'طلب تسجيل جديد لعائلة منتجة: M_Ak Nahari',0,'2025-08-05 12:59:24'),(9,2,'طلب تسجيل جديد لعائلة منتجة: M_Ak hari',0,'2025-08-05 13:13:23');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `buyer_id` int DEFAULT NULL,
  `seller_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `delivery_method` varchar(50) DEFAULT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `seller_id` (`seller_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`),
  CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,8,3,1,2,70.00,'credit_card','delivery','delivered','2025-08-05 11:57:50'),(2,9,4,4,3,150.00,'apple_pay','pickup','processing','2025-08-05 11:57:50'),(3,10,5,7,1,30.00,'credit_card','delivery','shipped','2025-08-05 11:57:50'),(4,11,6,10,1,350.00,'bank_transfer','delivery','pending','2025-08-05 11:57:50'),(5,12,7,13,2,130.00,'credit_card','pickup','delivered','2025-08-05 11:57:50'),(6,8,5,8,1,55.00,'credit_card','delivery','processing','2025-08-05 11:57:50'),(7,9,6,11,1,120.00,'apple_pay','delivery','shipped','2025-08-05 11:57:50'),(8,10,7,14,3,270.00,'bank_transfer','pickup','pending','2025-08-05 11:57:50'),(9,11,3,2,5,75.00,'credit_card','delivery','delivered','2025-08-05 11:57:50'),(10,12,4,5,2,90.00,'apple_pay','pickup','cancelled','2025-08-05 11:57:50');
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_types`
--

DROP TABLE IF EXISTS `product_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_types`
--

LOCK TABLES `product_types` WRITE;
/*!40000 ALTER TABLE `product_types` DISABLE KEYS */;
INSERT INTO `product_types` VALUES (1,'مربى وعسل'),(2,'مخبوزات'),(3,'تمور'),(4,'أعشاب وتوابل'),(5,'أعمال يدوية'),(6,'عطور طبيعية'),(7,'منتجات حليب');
/*!40000 ALTER TABLE `product_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int DEFAULT '1',
  `category_id` int DEFAULT NULL,
  `type_id` int DEFAULT NULL,
  `family_id` int DEFAULT NULL,
  `status` enum('available','out_of_stock','archived') DEFAULT 'available',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `type_id` (`type_id`),
  KEY `family_id` (`family_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `product_types` (`id`),
  CONSTRAINT `products_ibfk_3` FOREIGN KEY (`family_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'مربى تمر العنبرة','مربى طبيعي من تمر العنبرة السعودي بدون إضافات','/images/murabba.jpg',35.00,20,1,1,3,'available','2025-08-05 11:57:25'),(2,'خبز صاج','خبز صاج تقليدي مطبوخ على الحطب','/images/saj.jpg',15.00,30,1,2,3,'available','2025-08-05 11:57:25'),(3,'قهوة سعودية','خلطة قهوة سعودية أصيلة','/images/coffee.jpg',40.00,15,2,4,3,'available','2025-08-05 11:57:25'),(4,'تمر السكري','تمر سكري من أفضل مزارع المدينة المنورة','/images/tamr.jpg',50.00,40,1,3,4,'available','2025-08-05 11:57:25'),(5,'حلى أم علي','حلى تقليدي سعودي بطعم مميز','/images/umali.jpg',45.00,10,3,2,4,'available','2025-08-05 11:57:25'),(6,'مخلل ليمون','مخلل ليمون بلدي بنكهة خاصة','/images/pickle.jpg',25.00,18,1,4,4,'available','2025-08-05 11:57:25'),(7,'كليجة سعودية','كليجة تقليدية محشوة بالتمر','/images/kleja.jpg',30.00,25,3,2,5,'available','2025-08-05 11:57:25'),(8,'معمول بالتمر','معمول منزلي بجودة عالية','/images/maamoul.jpg',55.00,12,3,2,5,'available','2025-08-05 11:57:25'),(9,'لقيمات','لقيمات مقرمشة بطعم الفستق الحلبي','/images/luqaimat.jpg',20.00,30,3,2,5,'available','2025-08-05 11:57:25'),(10,'سجادة يدوية','سجادة صوفية مصنوعة يدوياً','/images/carpet.jpg',350.00,5,4,5,6,'available','2025-08-05 11:57:25'),(11,'مشغولات جلدية','محفظة جلدية سعودية تقليدية','/images/leather.jpg',120.00,8,4,5,6,'available','2025-08-05 11:57:25'),(12,'عطر دهن العود','عطر طبيعي من دهن العود الأصلي','/images/oud.jpg',280.00,6,5,6,6,'available','2025-08-05 11:57:25'),(13,'تمر الخلاص','تمر خلاص من الأحساء بجودة ممتازة','/images/khalas.jpg',65.00,35,1,3,7,'available','2025-08-05 11:57:25'),(14,'زبدة سمن بلدي','سمن بقري طبيعي 100%','/images/samn.jpg',90.00,15,1,7,7,'available','2025-08-05 11:57:25'),(15,'لبن خاثر','لبن خاثر منزلي طبيعي','/images/laban.jpg',25.00,20,1,7,7,'available','2025-08-05 11:57:25');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `product_id` int DEFAULT NULL,
  `rating` int DEFAULT NULL,
  `comment` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `ratings_chk_1` CHECK ((`rating` between 1 and 5))
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (1,8,1,5,'المربى رائع جداً ونكهته طبيعية','2025-08-05 11:58:02'),(2,9,4,4,'التمر جيد لكن السعر مرتفع قليلاً','2025-08-05 11:58:02'),(3,10,7,5,'الكليجة لذيذة وتستحق التجربة','2025-08-05 11:58:02'),(4,11,10,3,'السجادة جميلة لكن اللون يختلف عن الصورة','2025-08-05 11:58:02'),(5,12,13,5,'التمر ممتاز وشكله طازج','2025-08-05 11:58:02'),(6,8,8,4,'المعمول لذيذ لكن يحتاج أكثر من حبة','2025-08-05 11:58:02'),(7,9,11,5,'المحفظة جلد طبيعي ومتينة','2025-08-05 11:58:02'),(8,10,14,2,'السمن طعمه مقبول لكن الكمية قليلة','2025-08-05 11:58:02');
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('user','family','admin') NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `status` enum('active','pending','blocked') DEFAULT 'pending',
  `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Mohammed Al-Nahari','nahari@gmail.com','$2y$10$V7./Y1gdhu/AoIRfnGBeGeW08fW/MCo.zXGEbZnlLyYSknO1NJqDm','user',NULL,NULL,'active','pending','2025-08-05 10:20:13'),(2,'أحمد العلي','admin1@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','الرياض','0512345678','active','approved','2025-08-05 11:56:49'),(3,'فاطمة محمد','admin2@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','جدة','0512345679','active','approved','2025-08-05 11:56:49'),(4,'عائلة السليم','alsalim@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','family','الرياض','0511111111','active','approved','2025-08-05 11:56:49'),(5,'منتجات العتيبي','alotaibi@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','family','مكة','0522222222','active','approved','2025-08-05 11:56:49'),(6,'حلويات القحطاني','algahatani@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','family','الدمام','0533333333','active','approved','2025-08-05 11:56:49'),(7,'مشغولات الغامدي','alghamdi@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','family','جدة','0544444444','active','approved','2025-08-05 11:56:49'),(8,'تمور الزهراني','alzahrani@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','family','الأحساء','0555555555','active','approved','2025-08-05 11:56:49'),(9,'خالد أحمد','user1@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','الرياض','0566666666','active','approved','2025-08-05 11:56:49'),(10,'نورة محمد','user2@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','جدة','0577777777','active','approved','2025-08-05 11:56:49'),(11,'سعد عبدالله','user3@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','الدمام','0588888888','active','approved','2025-08-05 11:56:49'),(12,'لطيفة سعيد','user4@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','الخبر','0599999999','active','approved','2025-08-05 11:56:49'),(13,'فيصل ناصر','user5@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','الطائف','0500000000','active','approved','2025-08-05 11:56:49'),(14,'M_Ak Nahari','nahari771353457@gmail.com','$2y$10$vPfLu.ULuLm56tzyboJM4e9S21t1FYniPDPHNl2ADlaAQq2.K59TO','admin','sanaa','77777777456','active','approved','2025-08-05 12:59:24'),(15,'M_Ak hari','maknahari5@gmail.com','$2y$10$qdWdlgd2OdjTsbt/mBf3i.P0Bt4/rfEyTI2/czFtNlJZdIRnCkJGO','family','sanaa','77777777','active','pending','2025-08-05 13:13:23');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'productive_families_system'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-05 13:33:49
