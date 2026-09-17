-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: geprek_rejo
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('geprek-rejo-cache-17615ddf803344d08d74fbabc64263cc','i:1;',1780924564),('geprek-rejo-cache-17615ddf803344d08d74fbabc64263cc:timer','i:1780924564;',1780924564),('geprek-rejo-cache-31c016303abeba40892946241e20fb81:timer','i:1780922313;',1780922313),('geprek-rejo-cache-59d1053699d941154da18119aeb2f52f','i:1;',1780921756),('geprek-rejo-cache-59d1053699d941154da18119aeb2f52f:timer','i:1780921756;',1780921756),('geprek-rejo-cache-78d785079bcffdf703a86670be97f7e9','i:1;',1780921791),('geprek-rejo-cache-78d785079bcffdf703a86670be97f7e9:timer','i:1780921791;',1780921791),('geprek-rejo-cache-79ea4216a73d3f506fa30744102943ae','i:1;',1780925102),('geprek-rejo-cache-79ea4216a73d3f506fa30744102943ae:timer','i:1780925102;',1780925102),('geprek-rejo-cache-83bbb73624db2fadbdcd00c548c2d3fa','i:1;',1780915455),('geprek-rejo-cache-83bbb73624db2fadbdcd00c548c2d3fa:timer','i:1780915455;',1780915455),('geprek-rejo-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0','i:1;',1780921220),('geprek-rejo-cache-da4b9237bacccdf19c0760cab7aec4a8359010b0:timer','i:1780921220;',1780921220),('geprek-rejo-cache-f208732b4ce445d9f75f0b15086b803b','i:1;',1780924540),('geprek-rejo-cache-f208732b4ce445d9f75f0b15086b803b:timer','i:1780924540;',1780924540);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Emoji or icon class',
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Ayam Geprek','ayam-geprek',NULL,0,1,'2026-06-05 11:27:23','2026-06-05 11:27:23'),(2,'Minuman','minuman',NULL,0,1,'2026-06-05 11:27:23','2026-06-05 11:27:23'),(3,'Paket Nasi','paket-nasi',NULL,0,1,'2026-06-05 11:27:23','2026-06-05 11:27:23'),(4,'Camilan','camilan',NULL,0,1,'2026-06-05 11:27:23','2026-06-05 11:27:23'),(5,'Ekstra','ekstra',NULL,0,1,'2026-06-05 11:27:23','2026-06-05 11:27:23');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` enum('bahan_baku','operasional','gaji','perawatan','marketing','lainnya') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operasional',
  `amount` decimal(14,2) unsigned NOT NULL,
  `expense_date` date NOT NULL,
  `receipt_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `recorded_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_recorded_by_foreign` (`recorded_by`),
  KEY `expenses_expense_date_index` (`expense_date`),
  KEY `expenses_category_expense_date_index` (`category`,`expense_date`),
  CONSTRAINT `expenses_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,'Pengeluaran dummy 1','lainnya',160000.00,'2026-05-12',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(2,'Pengeluaran dummy 2','gaji',360000.00,'2026-05-23',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(3,'Pengeluaran dummy 3','operasional',160000.00,'2026-05-11',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(4,'Pengeluaran dummy 4','operasional',320000.00,'2026-05-22',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(5,'Pengeluaran dummy 5','bahan_baku',320000.00,'2026-06-03',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(6,'Pengeluaran dummy 6','gaji',450000.00,'2026-05-09',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(7,'Pengeluaran dummy 7','bahan_baku',110000.00,'2026-05-31',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(8,'Pengeluaran dummy 8','gaji',460000.00,'2026-05-15',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(9,'Pengeluaran dummy 9','perawatan',170000.00,'2026-05-11',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(10,'Pengeluaran dummy 10','gaji',430000.00,'2026-05-10',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(11,'Pengeluaran dummy 11','lainnya',70000.00,'2026-05-14',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(12,'Pengeluaran dummy 12','lainnya',330000.00,'2026-05-15',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(13,'Pengeluaran dummy 13','lainnya',290000.00,'2026-06-01',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(14,'Pengeluaran dummy 14','gaji',230000.00,'2026-05-23',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(15,'Pengeluaran dummy 15','operasional',190000.00,'2026-05-17',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(16,'Pengeluaran dummy 16','bahan_baku',470000.00,'2026-06-03',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(17,'Pengeluaran dummy 17','gaji',330000.00,'2026-05-23',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(18,'Pengeluaran dummy 18','marketing',130000.00,'2026-05-30',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(19,'Pengeluaran dummy 19','operasional',220000.00,'2026-05-17',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(20,'Pengeluaran dummy 20','operasional',420000.00,'2026-05-23',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(21,'Pengeluaran dummy 21','bahan_baku',200000.00,'2026-05-14',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(22,'Pengeluaran dummy 22','lainnya',450000.00,'2026-05-21',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(23,'Pengeluaran dummy 23','gaji',440000.00,'2026-05-14',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(24,'Pengeluaran dummy 24','gaji',130000.00,'2026-05-12',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(25,'Pengeluaran dummy 25','perawatan',430000.00,'2026-05-22',NULL,NULL,1,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(26,'aaaaaaaaaaa','gaji',2000000.00,'2026-06-04',NULL,NULL,NULL,'2026-06-05 22:22:49','2026-06-07 22:18:33'),(29,'ggggggg','operasional',10000.00,'2026-06-08',NULL,NULL,NULL,'2026-06-07 18:00:08','2026-06-07 18:00:08');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `members`
--

DROP TABLE IF EXISTS `members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `members` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'No. HP sebagai identifier login',
  `pin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Hashed PIN untuk login',
  `points` int unsigned NOT NULL DEFAULT '0' COMMENT 'Saldo poin loyalty',
  `total_orders` int unsigned NOT NULL DEFAULT '0',
  `total_spent` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
  `tier` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bronze' COMMENT 'bronze, silver, gold, platinum',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_order_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `members_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `members`
--

LOCK TABLES `members` WRITE;
/*!40000 ALTER TABLE `members` DISABLE KEYS */;
INSERT INTO `members` VALUES (1,'Member Dummy 1','081200000001','$2y$12$pzxgA9ykC69QLAkl8V0jou/TOVVvGNUFAK0s0tEK8hWqRevyiBOFC',1333,39,317000.00,'bronze',1,NULL,'2026-06-05 11:27:24','2026-06-05 11:27:24',NULL),(2,'Member Dummy 2','081200000002','$2y$12$rF5PZYw7xwOFbEfa0GluVemRfmWkYh8xTLNxtnF7Xh7bSYv2NyGv6',3895,49,218000.00,'bronze',1,NULL,'2026-06-05 11:27:25','2026-06-05 23:35:16',NULL),(3,'Member Dummy 3','081200000003','$2y$12$FKl5CGkiNK1TQdZ3JjQSLOPt4uWCvtlDQjJRQtNNr9nwSGZym.P02',4123,26,795000.00,'silver',1,NULL,'2026-06-05 11:27:26','2026-06-05 11:27:26',NULL),(4,'Member Dummy 4','081200000004','$2y$12$8Qa92Rcv2nTT5QtxyAHBpO2/Yp.hIdVc5Yxmhxo2htEgRzRLBTS/O',1907,22,487000.00,'bronze',1,NULL,'2026-06-05 11:27:26','2026-06-05 11:27:26',NULL),(5,'Member Dummy 5','081200000005','$2y$12$Hgk.vTfwsMQt5De2w.Pgeu8EogIDrCEdy4Tl7dd5Ol/jG0CfNHuJq',4912,37,85000.00,'bronze',1,NULL,'2026-06-05 11:27:27','2026-06-05 23:25:45',NULL),(6,'Member Dummy 6','081200000006','$2y$12$UYszRnJjoBkg.YVtO4D7.e6EMhfVWV5YYfvIuqz/RPp8JkTZivFtq',1298,48,654000.00,'silver',1,NULL,'2026-06-05 11:27:27','2026-06-05 11:27:27',NULL),(7,'Member Dummy 7','081200000007','$2y$12$N5oqTQFkiSPqk27mgQUi9OckOLsmpnrsrwYJkg27XTX7vmxm1iACC',2590,4,346000.00,'bronze',1,NULL,'2026-06-05 11:27:28','2026-06-05 11:27:28',NULL),(8,'Member Dummy 8','081200000008','$2y$12$Jy8m8G3RM3MAgJqnZSULteNTU4tcmDMgTxrrd8y9h4mK7QKqf/Bia',2774,8,763000.00,'silver',1,NULL,'2026-06-05 11:27:29','2026-06-05 11:27:29',NULL),(9,'Member Dummy 9','081200000009','$2y$12$nd0P7z1fd6SAwQqOvo6Skum/FvqIKV8e0elx8U67JdS4Ya46PAOXi',2309,34,204000.00,'bronze',1,NULL,'2026-06-05 11:27:29','2026-06-05 11:27:29',NULL),(10,'Member Dummy 10','081200000010','$2y$12$H9d6iiPGUAN3IxuqTR7el.GMWJAxO65.EhV1/Rn1ioiO3S5NWJvtW',662,5,521000.00,'silver',1,NULL,'2026-06-05 11:27:30','2026-06-05 11:27:30',NULL),(11,'Member Dummy 11','081200000011','$2y$12$n/uRJXxj4MEqRbOYDQ6FpeDsLUHCcmLM6xL44nnrC92MRIBQIS7K6',4297,38,890000.00,'silver',1,NULL,'2026-06-05 11:27:31','2026-06-05 11:27:31',NULL),(12,'Member Dummy 12','081200000012','$2y$12$EKBX5u/ZaLZ2aCtoDMGfe.U6wDxerLtoHBvDFfdLZjUiphO/G.j/G',2836,42,389000.00,'bronze',1,NULL,'2026-06-05 11:27:31','2026-06-05 11:27:31',NULL),(13,'Member Dummy 13','081200000013','$2y$12$A69k7H2BDHpEQoFV.vUEDeyiF1ekgGAFTLWBfXqSCvLO1oCrIJ6Fa',581,42,491000.00,'bronze',1,NULL,'2026-06-05 11:27:32','2026-06-05 11:27:32',NULL),(14,'Member Dummy 14','081200000014','$2y$12$nUlQ1P9ksJdEHeh1b4YfPuhRQf/2sdDPDy58okLN8yOMIxpnSIApm',2221,49,210000.00,'bronze',1,NULL,'2026-06-05 11:27:33','2026-06-05 11:27:33',NULL),(15,'Member Dummy 15','081200000015','$2y$12$G2etXhWuDjRIEWA4HuSCXe/53he.PXXbA5ONlKalEPlSeYLjh1GZC',1106,6,103000.00,'bronze',1,NULL,'2026-06-05 11:27:33','2026-06-05 11:27:33',NULL),(16,'Member Dummy 16','081200000016','$2y$12$EPkL0Leu1mu7hcf/tTd8beS.DtdtzrJXG31OvpsZIQ9t/8qBEAC3W',99,8,290000.00,'bronze',1,NULL,'2026-06-05 11:27:34','2026-06-05 11:27:34',NULL),(17,'Member Dummy 17','081200000017','$2y$12$3bGoPHrhNZRc1oV6W3DCOuE1lcVQDSpXHriKal.t0M/jlLR.Bguhe',2126,11,109000.00,'bronze',1,NULL,'2026-06-05 11:27:35','2026-06-05 11:27:35',NULL),(18,'Member Dummy 18','081200000018','$2y$12$Aoc1AJLCVZqYGuPc9TMJneE5YmAYGSfKp1Sj3d0KYgKJ68ATB/9/q',736,45,772000.00,'silver',1,NULL,'2026-06-05 11:27:35','2026-06-05 11:27:35',NULL),(19,'Member Dummy 19','081200000019','$2y$12$6O6qF49oU7SuTyjQQRG5SePaj1lJsc8NMPVyUjZGmnTGrIm7pqd9G',831,45,715000.00,'silver',1,NULL,'2026-06-05 11:27:36','2026-06-05 11:27:36',NULL),(20,'Member Dummy 20','081200000020','$2y$12$3ni4TJV4dJi1Dqh1zVIodegZQZcvodTBfVWa3m8P8LXM8HJex7nFK',4300,13,902000.00,'silver',1,NULL,'2026-06-05 11:27:37','2026-06-05 11:27:37',NULL),(21,'Member Dummy 21','081200000021','$2y$12$1wcx9fhHADI10arqleSVC.P0Aw71bvBGmFH/5hoEJafbh/IjvjmWC',2228,42,185000.00,'bronze',1,NULL,'2026-06-05 11:27:37','2026-06-05 11:27:37',NULL),(22,'Member Dummy 22','081200000022','$2y$12$pO4LaotrCL/SPhvSMvzGneNgmooxxnhgMgAXf1b0r5wFAeuJ.v5HK',736,22,203000.00,'bronze',1,NULL,'2026-06-05 11:27:38','2026-06-05 11:27:38',NULL),(23,'Member Dummy 23','081200000023','$2y$12$G4tmhluiVbLf/TuDaecUW.CqP4HEXdo4FnZfIiXXN13EgxJAakOci',448,27,169000.00,'bronze',1,NULL,'2026-06-05 11:27:38','2026-06-05 11:27:38',NULL),(24,'Member Dummy 24','081200000024','$2y$12$CQhOtvR57RsrnNKeOROMDOtJIn8UBtGdzS/kkXsc9x4qQ.q1va9cG',3591,45,329000.00,'bronze',1,NULL,'2026-06-05 11:27:39','2026-06-05 11:27:39',NULL),(25,'Member Dummy 25','081200000025','$2y$12$f.eOClIaiCGQTupfv6TfQOTgaiKEkT0yxFehCbpdlSbvz.g3YqWGq',1368,4,530000.00,'silver',1,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(26,'ajeng','086736923983','$2y$12$/vZ03ZMcwGpXxjBPxyjdhu9jf532RSqEf7Ifp4xETaNcKhFN3DpAu',64,2,64000.00,'bronze',1,'2026-06-06 22:44:35','2026-06-06 22:12:10','2026-06-06 22:44:35',NULL),(27,'saryy','081122223333','$2y$12$3b.vTsUpHUdxNclZkC/doeENGaC3pH1Np7FSsjEVtobApzzLX6/My',0,0,0.00,'bronze',1,NULL,'2026-06-07 02:41:19','2026-06-07 02:41:19',NULL),(28,'latifanika','089501234567','$2y$12$Oafho8IM9oklnY.aL4.oTOq/PyutY/92NXNZerIzCouDheFBnGS.u',32,7,332000.00,'bronze',1,'2026-06-08 06:09:20','2026-06-07 02:45:49','2026-06-08 06:09:20',NULL),(29,'Oikawa Toru','087708123456','$2y$12$LUrShjHUTyVns7XRT9z3ke0kAW6lp/mvaFGZQl1.fjHJuK3WSBwdy',231,2,231000.00,'bronze',1,'2026-06-07 19:01:01','2026-06-07 17:24:08','2026-06-07 19:01:01',NULL),(30,'Mega Wati','081234567890','$2y$12$yc2c/YV7r7jhrNYTdyj8uOYB.nWIUamrJQEmG8vECrngKq6tHAWHS',88,1,88000.00,'bronze',1,'2026-06-08 00:11:21','2026-06-08 00:07:36','2026-06-08 00:11:21',NULL),(31,'nayli','08987654321','$2y$12$cjVAl.8nlWQwxcNwkmcanee6cOfqVR0DH57cx0q8WV19rMQBgO9c2',34,1,34000.00,'bronze',1,'2026-06-08 02:00:24','2026-06-08 01:29:06','2026-06-08 02:00:24',NULL);
/*!40000 ALTER TABLE `members` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) unsigned NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `menu_items_slug_unique` (`slug`),
  KEY `menu_items_category_id_foreign` (`category_id`),
  CONSTRAINT `menu_items_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_items`
--

LOCK TABLES `menu_items` WRITE;
/*!40000 ALTER TABLE `menu_items` DISABLE KEYS */;
INSERT INTO `menu_items` VALUES (1,1,'Ayam Geprek Dada','ayam-geprek-original','Ayam krispi bagian dada dengan daging lebih banyak, digeprek dengan sambal khas yang pedas dan gurih.','menu/FDz6HamflP0jyhfJBF7fKYZTTqRIlubyF2oKPnlB.jpg',10000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:04:16',NULL),(2,3,'Paket Nasi Geprek Paha Bawah','ayam-geprek-keju','Ayam paha bawah yang renyah dan gurih, disajikan bersama nasi putih hangat.','menu/mLtk0uAKts1OSzUW6r5xoFA8LAEReuDUXBoNvmqD.jpg',10000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:11:26',NULL),(3,3,'Paket Nasi Geprek Dada','ayam-geprek-mozzarella','Ayam geprek dada yang renyah dengan sambal khas, disajikan bersama nasi putih hangat yang mengenyangkan.','menu/KyAKBmkvj2FuKKAfBxxBWN44yO92p55RGMP9mGTc.jpg',12000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:10:32',NULL),(4,1,'Ayam Geprek Sambal Matah','ayam-geprek-sambal-matah','Deskripsi enak untuk Ayam Geprek Sambal Matah.',NULL,13000.00,1,0,'2026-06-05 11:27:23','2026-06-08 02:45:15','2026-06-08 02:45:15'),(5,3,'Paket Nasi Geprek Paha Atas','ayam-geprek-level-dewa','','menu/M6O2GdYT240OeXYFOMoep7tsunCEJZyQOiOgf6lO.jpg',12000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:10:45','2026-06-08 03:10:45'),(6,2,'Es Teh Manis','es-teh-manis','','menu/uoLTglIrPb4yFVOWssf6fmOVczclrbjbOjmcTVJi.jpg',3000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:00:51','2026-06-08 03:00:51'),(7,2,'Es Jeruk','es-jeruk','Deskripsi enak untuk Es Jeruk.','menu/3NQupxbAIvZKf0HgG1M8G1OeGijAn55sdRU1BmNV.png',5000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:00:40','2026-06-08 03:00:40'),(8,2,'Es Lemon Tea','es-lemon-tea','Deskripsi enak untuk Es Lemon Tea.',NULL,5000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:00:48','2026-06-08 03:00:48'),(9,2,'Kopi Hitam Panas','kopi-hitam-panas','Deskripsi enak untuk Kopi Hitam Panas.','menu/qT02Db2pA151G4SY5R2Be4WJ6y9q6ipL6nTrU3D3.png',13000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:01:00','2026-06-08 03:01:00'),(10,2,'Es Kopi Susu','es-kopi-susu','Deskripsi enak untuk Es Kopi Susu.','menu/inhQfrNcdLnfqhMB1FcHT61NzFYLH3FaeQpaJVdX.jpg',8000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:00:45','2026-06-08 03:00:45'),(11,3,'Paket Nasi Geprek Ori','paket-nasi-geprek-ori','Deskripsi enak untuk Paket Nasi Geprek Ori.',NULL,12000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:11:50','2026-06-08 03:11:50'),(12,3,'Paket Nasi Geprek Keju','paket-nasi-geprek-keju','Deskripsi enak untuk Paket Nasi Geprek Keju.',NULL,22000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:06:35','2026-06-08 03:06:35'),(13,3,'Paket Nasi Geprek Mozza','paket-nasi-geprek-mozza','Deskripsi enak untuk Paket Nasi Geprek Mozza.','menu/yF1OKBJ2JSV7YDdL9lfsOqaL7lFtJDwD5DebLO77.jpg',25000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:06:39','2026-06-08 03:06:39'),(14,3,'Paket Nasi Kulit Krispi','paket-nasi-kulit-krispi','Deskripsi enak untuk Paket Nasi Kulit Krispi.',NULL,22000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:11:43','2026-06-08 03:11:43'),(15,3,'Paket Nasi Geprek Paha Atas','paket-komplit-rejo','Paha atas ayam yang empuk dan juicy, dipadukan dengan nasi hangat untuk hidangan yang lebih nikmat.','menu/E6h07GdSaOkjFRHEWIYPxRctiHzVnrKp6Zlukdks.jpg',12000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:10:01',NULL),(16,4,'Jamur Krispi','jamur-krispi','Deskripsi enak untuk Jamur Krispi.',NULL,8000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:00:54','2026-06-08 03:00:54'),(17,4,'Tahu Crispy','tahu-crispy','Deskripsi enak untuk Tahu Crispy.',NULL,18000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:11:57','2026-06-08 03:11:57'),(18,4,'Tempe Mendoan','tempe-mendoan','Deskripsi enak untuk Tempe Mendoan.',NULL,18000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:12:13','2026-06-08 03:12:13'),(19,4,'Kulit Ayam Krispi','kulit-ayam-krispi','Deskripsi enak untuk Kulit Ayam Krispi.',NULL,13000.00,1,0,'2026-06-05 11:27:23','2026-06-08 03:01:06','2026-06-08 03:01:06'),(20,4,'Sosis Bakar','sosis-bakar','Deskripsi enak untuk Sosis Bakar.',NULL,18000.00,1,0,'2026-06-05 11:27:24','2026-06-08 03:12:18','2026-06-08 03:12:18'),(21,5,'Nasi Putih','nasi-putih','Deskripsi enak untuk Nasi Putih.',NULL,12000.00,1,0,'2026-06-05 11:27:24','2026-06-08 03:01:11','2026-06-08 03:01:11'),(22,5,'Telur Dadar','telur-dadar','Deskripsi enak untuk Telur Dadar.',NULL,16000.00,1,0,'2026-06-05 11:27:24','2026-06-08 03:12:03','2026-06-08 03:12:03'),(23,5,'Telur Mata Sapi','telur-mata-sapi','Deskripsi enak untuk Telur Mata Sapi.',NULL,18000.00,1,0,'2026-06-05 11:27:24','2026-06-08 03:12:09','2026-06-08 03:12:09'),(24,5,'Sambal Tambahan','sambal-tambahan','Deskripsi enak untuk Sambal Tambahan.',NULL,7000.00,1,0,'2026-06-05 11:27:24','2026-06-08 03:12:30','2026-06-08 03:12:30'),(25,5,'Kerupuk','kerupuk','Deskripsi enak untuk Kerupuk.',NULL,12000.00,1,0,'2026-06-05 11:27:24','2026-06-08 03:00:57','2026-06-08 03:00:57'),(26,4,'aaaaaaaaaa','aaaaaaaaaa','aaaaabbbbbbbbbb','menu/ZhqCKolO7tdgLTlXcPYzzDRtYlU3rTAkKFNPKQsJ.png',10000000.00,1,0,'2026-06-05 23:31:18','2026-06-05 23:32:53','2026-06-05 23:32:53'),(27,NULL,'bbbbbbbbb','aaaaasss','ddddddddddddd','menu/KkO5wcrWFc9xOFrRLxCoKFualTxoOYxu2rUB7sYh.png',100000.00,1,0,'2026-06-05 23:33:44','2026-06-07 01:44:23','2026-06-07 01:44:23'),(28,1,'Ayam Geprek Paha Bawah','paha-bawah-geprek','Ayam paha bawah yang renyah di luar dan lembut di dalam, cocok untuk pecinta ayam geprek.','menu/Na82jtQa1FHz7eigRPZcPiehtnG30RDwEFo00k6d.jpg',8000.00,1,0,'2026-06-07 04:58:31','2026-06-08 03:06:19',NULL),(29,5,'contoh','contoh','','menu/P3HXFWkWJ1ydmFW9afoMb0qTlsBT4q3A4m9sYuj3.png',1000.00,1,0,'2026-06-07 07:47:47','2026-06-07 09:19:42','2026-06-07 09:19:42'),(30,1,'nct 90\'s love exampleee','nct-90s-love-exampleee','ini adalahhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh','menu/8ErwL6HQq0ISkCN8uXfuH8DoR3NUOBniXomkr5QD.png',1000000.00,0,0,'2026-06-07 09:17:21','2026-06-08 03:01:21','2026-06-08 03:01:21'),(31,1,'Ayam Geprek Paha Atas','ayam-geprek-paha-atas','Ayam krispi renyah dengan sambal geprek khas yang pedas dan gurih.','menu/OhW65MrMCslmOutjhtfa9uuW6saolJb0eUghGCMc.jpg',10000.00,1,0,'2026-06-08 02:49:34','2026-06-08 03:04:28',NULL),(32,1,'Ayam Geprek Sayap','ayam-geprek-sayap','Sayap ayam krispi yang renyah dipadukan dengan sambal geprek yang menggugah selera.','menu/RTd8ExOyN7LMOqsovJLQJmdjdrufYfzy3ZPhrt3G.jpg',8000.00,1,0,'2026-06-08 03:16:31','2026-06-08 03:16:31',NULL),(33,3,'Paket Nasi Geprek Sayap','paket-nasi-geprek-sayap','Sayap ayam krispi dengan sambal pedas pilihan, lengkap dengan nasi putih hangat.','menu/N2ttEtYiurQSgZi6zDX6gL0s9lQ0xHKD0AfGiRnV.jpg',10000.00,1,0,'2026-06-08 03:17:38','2026-06-08 03:17:53',NULL),(34,5,'Ceker Crispy','ceker-crispy','Ceker ayam berbumbu gurih yang cocok sebagai pelengkap hidangan.','menu/2ZbacZAoLdjHDlIvfYxIzL9a7QOYU9kaYjyCECK1.jpg',2000.00,1,0,'2026-06-08 03:22:03','2026-06-08 03:22:03',NULL),(35,2,'Taro','taro','Minuman taro yang lembut dengan rasa manis khas dan warna ungu yang menarik.','menu/KE3HZWVXDgyIYrHIE3SQtawp7JXmk9UkkRnDS50a.jpg',8000.00,1,0,'2026-06-08 04:15:36','2026-06-08 04:59:19',NULL),(36,2,'Matcha','matcha','Minuman rasa matcha/green tea','menu/DeFl0WfIIuP7KDC2O6pgefK7FOwxGwlUAUrOG8XN.jpg',12000.00,1,0,'2026-06-08 04:16:31','2026-06-08 04:45:55',NULL),(37,2,'Jus Mangga','jus-mangga','Jus yang dibuat dengan buah mangga asli','menu/Niu5nhodsPpSMR6w62F6dIuTwhvGNjVu3go1bElS.jpg',8000.00,1,0,'2026-06-08 04:19:08','2026-06-08 05:34:59','2026-06-08 05:34:59'),(38,2,'Es Teh','es-teh','Es Teh Manis ','menu/pUE2VTxoeHBRR5ygWmDhGp5NUjij2fFFK7pwshgu.jpg',2500.00,1,0,'2026-06-08 04:46:55','2026-06-08 04:46:55',NULL),(39,2,'Lemon Tea Ice','lemon-tea-ice','Es Lemon tea','menu/USDwg2XwE7TUk4m1FBavEFjI3oHIGMCnjUu9mDBi.jpg',6000.00,1,0,'2026-06-08 04:48:18','2026-06-08 04:48:18',NULL),(40,2,'Vanilla Latte','vanilla-latte','Perpaduan kopi dan vanilla yang lembut dengan rasa yang seimbang.','menu/OEOLRyifPyjJOt0X5pi0HQLyEYYNQar0UQWtNzMz.jpg',7000.00,1,0,'2026-06-08 04:51:16','2026-06-08 04:57:08',NULL),(41,2,'Vanilla Latte','vanilla-latte-1','Perpaduan kopi dan vanilla yang lembut dengan rasa yang seimbang.','menu/F9DhFTdTx6jXcE5M97beN6593NMhEPzvXA9X8ctf.jpg',12000.00,1,0,'2026-06-08 04:54:12','2026-06-08 04:55:08','2026-06-08 04:55:08'),(42,5,'Kepala Ayam Crispy','kepala-ayam-crispy','Kepala ayam goreng tepung yang renyah dan gurih di setiap gigitan.','menu/5689Cs1pm1fwr72LvHL8Nn0qz7P1e1KGfKFvz3o5.jpg',4000.00,1,0,'2026-06-08 05:05:04','2026-06-08 05:05:04',NULL),(43,2,'Strawberry ','strawberry','Minuman stroberi dengan rasa manis dan sedikit asam yang menyegarkan.','menu/J6FD0GLzTTqqwq9MSTB4ty3TDAhDGMDyJ8HZYWNv.jpg',8000.00,1,0,'2026-06-08 05:07:22','2026-06-08 05:07:22',NULL),(44,2,'Choco Hazelnut','choco-hazelnut','Minuman cokelat dengan sentuhan hazelnut yang manis dan aromatik.','menu/MCW4DG8oykZr0ov5Azm7W5a2P0VAXKofpRbxZF7I.jpg',8000.00,1,0,'2026-06-08 05:11:01','2026-06-08 05:14:45',NULL),(45,2,'Mango Ice','mango-ice','','menu/8w7g18LfiVRK5P5bbXRUyXRRBkFPToIGsWd5VQDa.jpg',8000.00,1,0,'2026-06-08 05:12:46','2026-06-08 05:14:52',NULL),(46,2,'Cappuccino','cappuccino','Minuman kopi klasik dengan cita rasa creamy dan aroma yang khas.','menu/15e8PyAkEKUOwZqwUBPOlHzNpOpRjqQD9ZqwVKH2.jpg',7000.00,1,0,'2026-06-08 05:15:53','2026-06-08 05:16:12',NULL),(47,2,'Dark Choco','dark-choco','Minuman cokelat dengan cita rasa pekat, lembut, dan memanjakan pecinta cokelat.','menu/DdiyGkKIcBRfsY8LMwvsuGREBRuifrA9dbKxpPDx.jpg',10000.00,1,0,'2026-06-08 05:17:50','2026-06-08 05:17:50',NULL),(48,2,'Black Oreo Ice','black-oreo-ice','','menu/gHaMQcqaesXIzx3tR5iev9UgwDjjKZ1I7BPrWJJT.jpg',10000.00,1,0,'2026-06-08 05:18:08','2026-06-08 05:18:08',NULL);
/*!40000 ALTER TABLE `menu_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2024_01_01_000000_create_passkeys_table',1),(5,'2025_08_14_170933_add_two_factor_columns_to_users_table',1),(6,'2026_01_01_000001_create_categories_table',1),(7,'2026_01_01_000002_create_menu_items_table',1),(8,'2026_01_01_000003_create_stock_ingredients_table',1),(9,'2026_01_01_000004_create_recipes_table',1),(10,'2026_01_01_000005_create_members_table',1),(11,'2026_01_01_000006_create_vouchers_table',1),(12,'2026_01_01_000007_create_orders_table',1),(13,'2026_01_01_000008_create_order_details_table',1),(14,'2026_01_01_000009_create_expenses_table',1),(15,'2026_01_01_000010_create_voucher_uses_table',1),(16,'2026_01_01_000011_create_point_logs_table',1),(17,'2026_01_01_000012_create_settings_table',1),(18,'2026_06_06_043130_add_role_to_users_table',2),(19,'2026_06_08_042515_add_member_id_to_vouchers_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `menu_item_id` bigint unsigned DEFAULT NULL,
  `menu_item_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Snapshot nama menu saat order dibuat',
  `quantity` smallint unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(12,2) unsigned NOT NULL COMMENT 'Harga jual per item saat order dibuat (snapshot)',
  `subtotal` decimal(12,2) unsigned NOT NULL COMMENT 'unit_price ├ù quantity',
  `hpp_snapshot` decimal(12,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT 'HPP per unit saat konfirmasi pembayaran: ╬ú(unit_cost ├ù qty_used)',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan khusus per item (misal: tidak pedas)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_details_menu_item_id_foreign` (`menu_item_id`),
  KEY `order_details_order_id_index` (`order_id`),
  CONSTRAINT `order_details_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=157 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` VALUES (1,1,8,'Es Lemon Tea',3,8000.00,24000.00,14400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(2,1,14,'Paket Nasi Kulit Krispi',3,22000.00,66000.00,39600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(3,2,10,'Es Kopi Susu',3,8000.00,24000.00,14400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(4,2,11,'Paket Nasi Geprek Ori',2,12000.00,24000.00,14400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(5,2,17,'Tahu Crispy',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(6,2,18,'Tempe Mendoan',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(7,3,1,'Ayam Geprek Original',2,18000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(8,3,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,7800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(9,3,6,'Es Teh Manis',2,8000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(10,3,13,'Paket Nasi Geprek Mozza',2,25000.00,50000.00,30000.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(11,4,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,7800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(12,4,7,'Es Jeruk',2,21000.00,42000.00,25200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(13,4,15,'Paket Komplit Rejo',1,12000.00,12000.00,7200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(14,4,18,'Tempe Mendoan',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(15,4,20,'Sosis Bakar',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(16,5,4,'Ayam Geprek Sambal Matah',3,13000.00,39000.00,23400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(17,5,22,'Telur Dadar',2,16000.00,32000.00,19200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(18,6,16,'Jamur Krispi',2,8000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(19,6,22,'Telur Dadar',1,16000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(20,7,7,'Es Jeruk',3,21000.00,63000.00,37800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(21,7,20,'Sosis Bakar',3,18000.00,54000.00,32400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(22,8,7,'Es Jeruk',2,21000.00,42000.00,25200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(23,8,8,'Es Lemon Tea',1,8000.00,8000.00,4800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(24,8,13,'Paket Nasi Geprek Mozza',3,25000.00,75000.00,45000.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(25,8,23,'Telur Mata Sapi',3,18000.00,54000.00,32400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(26,9,4,'Ayam Geprek Sambal Matah',2,13000.00,26000.00,15600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(27,9,11,'Paket Nasi Geprek Ori',1,12000.00,12000.00,7200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(28,9,12,'Paket Nasi Geprek Keju',3,22000.00,66000.00,39600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(29,9,19,'Kulit Ayam Krispi',1,13000.00,13000.00,7800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(30,9,21,'Nasi Putih',3,12000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(31,10,8,'Es Lemon Tea',1,8000.00,8000.00,4800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(32,10,19,'Kulit Ayam Krispi',3,13000.00,39000.00,23400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(33,10,22,'Telur Dadar',1,16000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(34,11,20,'Sosis Bakar',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(35,11,23,'Telur Mata Sapi',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(36,12,9,'Kopi Hitam Panas',2,13000.00,26000.00,15600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(37,12,25,'Kerupuk',2,12000.00,24000.00,14400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(38,13,2,'Ayam Geprek Keju',2,14000.00,28000.00,16800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(39,13,8,'Es Lemon Tea',2,8000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(40,13,12,'Paket Nasi Geprek Keju',1,22000.00,22000.00,13200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(41,13,14,'Paket Nasi Kulit Krispi',3,22000.00,66000.00,39600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(42,13,16,'Jamur Krispi',2,8000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(43,14,13,'Paket Nasi Geprek Mozza',1,25000.00,25000.00,15000.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(44,14,21,'Nasi Putih',3,12000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(45,14,23,'Telur Mata Sapi',3,18000.00,54000.00,32400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(46,14,25,'Kerupuk',3,12000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(47,15,12,'Paket Nasi Geprek Keju',3,22000.00,66000.00,39600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(48,15,13,'Paket Nasi Geprek Mozza',2,25000.00,50000.00,30000.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(49,15,17,'Tahu Crispy',3,18000.00,54000.00,32400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(50,16,8,'Es Lemon Tea',1,8000.00,8000.00,4800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(51,16,16,'Jamur Krispi',3,8000.00,24000.00,14400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(52,16,24,'Sambal Tambahan',2,7000.00,14000.00,8400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(53,17,5,'Ayam Geprek Level Dewa',2,10000.00,20000.00,12000.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(54,17,23,'Telur Mata Sapi',2,18000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(55,18,6,'Es Teh Manis',1,8000.00,8000.00,4800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(56,18,8,'Es Lemon Tea',2,8000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(57,18,17,'Tahu Crispy',2,18000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(58,18,21,'Nasi Putih',3,12000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(59,18,25,'Kerupuk',3,12000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(60,19,2,'Ayam Geprek Keju',2,14000.00,28000.00,16800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(61,19,4,'Ayam Geprek Sambal Matah',3,13000.00,39000.00,23400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(62,19,21,'Nasi Putih',1,12000.00,12000.00,7200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(63,20,8,'Es Lemon Tea',1,8000.00,8000.00,4800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(64,20,11,'Paket Nasi Geprek Ori',2,12000.00,24000.00,14400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(65,20,19,'Kulit Ayam Krispi',3,13000.00,39000.00,23400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(66,21,2,'Ayam Geprek Keju',1,14000.00,14000.00,8400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(67,21,12,'Paket Nasi Geprek Keju',3,22000.00,66000.00,39600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(68,21,13,'Paket Nasi Geprek Mozza',2,25000.00,50000.00,30000.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(69,21,18,'Tempe Mendoan',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(70,21,21,'Nasi Putih',1,12000.00,12000.00,7200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(71,22,14,'Paket Nasi Kulit Krispi',1,22000.00,22000.00,13200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(72,22,16,'Jamur Krispi',2,8000.00,16000.00,9600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(73,22,21,'Nasi Putih',1,12000.00,12000.00,7200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(74,23,6,'Es Teh Manis',1,8000.00,8000.00,4800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(75,23,10,'Es Kopi Susu',3,8000.00,24000.00,14400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(76,23,17,'Tahu Crispy',2,18000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(77,23,24,'Sambal Tambahan',1,7000.00,7000.00,4200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(78,24,12,'Paket Nasi Geprek Keju',2,22000.00,44000.00,26400.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(79,24,14,'Paket Nasi Kulit Krispi',1,22000.00,22000.00,13200.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(80,24,18,'Tempe Mendoan',2,18000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(81,24,20,'Sosis Bakar',2,18000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(82,24,22,'Telur Dadar',3,16000.00,48000.00,28800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(83,25,7,'Es Jeruk',1,21000.00,21000.00,12600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(84,25,13,'Paket Nasi Geprek Mozza',3,25000.00,75000.00,45000.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(85,25,14,'Paket Nasi Kulit Krispi',3,22000.00,66000.00,39600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(86,25,18,'Tempe Mendoan',1,18000.00,18000.00,10800.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(87,25,20,'Sosis Bakar',2,18000.00,36000.00,21600.0000,NULL,'2026-06-05 11:27:40','2026-06-05 11:27:40'),(88,26,2,'Ayam Geprek Banyumas',1,14000.00,14000.00,0.0000,NULL,'2026-06-06 21:51:56','2026-06-06 21:51:56'),(89,27,5,'Ayam Geprek Level Dewa',1,10000.00,10000.00,0.0000,NULL,'2026-06-06 21:59:07','2026-06-06 21:59:07'),(90,28,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,0.0000,NULL,'2026-06-06 22:00:02','2026-06-06 22:00:02'),(91,29,10,'Es Kopi Susu',1,8000.00,8000.00,0.0000,NULL,'2026-06-06 22:00:38','2026-06-06 22:00:38'),(92,30,18,'Tempe Mendoan',1,18000.00,18000.00,0.0000,NULL,'2026-06-06 22:01:30','2026-06-06 22:01:30'),(93,30,23,'Telur Mata Sapi',1,18000.00,18000.00,0.0000,NULL,'2026-06-06 22:01:30','2026-06-06 22:01:30'),(94,31,23,'Telur Mata Sapi',1,18000.00,18000.00,0.0000,NULL,'2026-06-06 22:09:31','2026-06-06 22:09:31'),(95,31,2,'Ayam Geprek Banyumas',1,14000.00,14000.00,0.0000,NULL,'2026-06-06 22:09:31','2026-06-06 22:09:31'),(96,32,2,'Ayam Geprek Banyumas',1,14000.00,14000.00,0.0000,NULL,'2026-06-06 22:12:42','2026-06-06 22:12:42'),(97,33,1,'Ayam Geprek Original',1,18000.00,18000.00,0.0000,NULL,'2026-06-06 22:22:21','2026-06-06 22:22:21'),(98,33,3,'Ayam Geprek Mozzarella',1,19000.00,19000.00,0.0000,NULL,'2026-06-06 22:22:21','2026-06-06 22:22:21'),(99,33,9,'Kopi Hitam Panas',1,13000.00,13000.00,0.0000,NULL,'2026-06-06 22:22:21','2026-06-06 22:22:21'),(100,34,2,'Ayam Geprek Banyumas',1,14000.00,14000.00,0.0000,NULL,'2026-06-07 00:48:59','2026-06-07 00:48:59'),(101,35,1,'Ayam Geprek Original',2,18000.00,36000.00,0.0000,NULL,'2026-06-07 02:47:26','2026-06-07 02:47:26'),(102,36,15,'Paket Komplit Rejo',1,12000.00,12000.00,0.0000,NULL,'2026-06-07 02:50:00','2026-06-07 02:50:00'),(103,36,12,'Paket Nasi Geprek Keju',1,22000.00,22000.00,0.0000,NULL,'2026-06-07 02:50:00','2026-06-07 02:50:00'),(104,36,13,'Paket Nasi Geprek Mozza',1,25000.00,25000.00,0.0000,NULL,'2026-06-07 02:50:00','2026-06-07 02:50:00'),(105,37,10,'Es Kopi Susu',1,8000.00,8000.00,0.0000,NULL,'2026-06-07 17:24:25','2026-06-07 17:24:25'),(106,37,8,'Es Lemon Tea',1,5000.00,5000.00,0.0000,NULL,'2026-06-07 17:24:25','2026-06-07 17:24:25'),(107,37,11,'Paket Nasi Geprek Ori',1,12000.00,12000.00,0.0000,NULL,'2026-06-07 17:24:25','2026-06-07 17:24:25'),(108,37,22,'Telur Dadar',1,16000.00,16000.00,0.0000,NULL,'2026-06-07 17:24:25','2026-06-07 17:24:25'),(109,37,18,'Tempe Mendoan',2,18000.00,36000.00,0.0000,NULL,'2026-06-07 17:24:25','2026-06-07 17:24:25'),(110,38,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,0.0000,NULL,'2026-06-07 18:43:49','2026-06-07 18:43:49'),(111,39,6,'Es Teh Manis',4,3000.00,12000.00,0.0000,NULL,'2026-06-07 19:00:40','2026-06-07 19:00:40'),(112,39,16,'Jamur Krispi',2,8000.00,16000.00,0.0000,NULL,'2026-06-07 19:00:40','2026-06-07 19:00:40'),(113,39,19,'Kulit Ayam Krispi',5,13000.00,65000.00,0.0000,NULL,'2026-06-07 19:00:40','2026-06-07 19:00:40'),(114,39,13,'Paket Nasi Geprek Mozza',1,25000.00,25000.00,0.0000,NULL,'2026-06-07 19:00:40','2026-06-07 19:00:40'),(115,39,18,'Tempe Mendoan',1,18000.00,18000.00,0.0000,NULL,'2026-06-07 19:00:40','2026-06-07 19:00:40'),(116,39,20,'Sosis Bakar',1,18000.00,18000.00,0.0000,NULL,'2026-06-07 19:00:40','2026-06-07 19:00:40'),(117,40,7,'Es Jeruk',1,5000.00,5000.00,0.0000,NULL,'2026-06-07 20:15:33','2026-06-07 20:15:33'),(118,40,1,'Ayam Geprek Original',1,18000.00,18000.00,0.0000,NULL,'2026-06-07 20:15:33','2026-06-07 20:15:33'),(119,40,12,'Paket Nasi Geprek Keju',1,22000.00,22000.00,0.0000,NULL,'2026-06-07 20:15:33','2026-06-07 20:15:33'),(120,40,13,'Paket Nasi Geprek Mozza',1,25000.00,25000.00,0.0000,NULL,'2026-06-07 20:15:33','2026-06-07 20:15:33'),(121,41,10,'Es Kopi Susu',1,8000.00,8000.00,0.0000,NULL,'2026-06-07 20:17:26','2026-06-07 20:17:26'),(122,41,1,'Ayam Geprek Original',1,18000.00,18000.00,0.0000,NULL,'2026-06-07 20:17:26','2026-06-07 20:17:26'),(123,41,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,0.0000,NULL,'2026-06-07 20:17:26','2026-06-07 20:17:26'),(124,42,24,'Sambal Tambahan',1,7000.00,7000.00,0.0000,NULL,'2026-06-07 23:58:05','2026-06-07 23:58:05'),(125,42,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,0.0000,NULL,'2026-06-07 23:58:05','2026-06-07 23:58:05'),(126,43,1,'Ayam Geprek Original',1,18000.00,18000.00,0.0000,NULL,'2026-06-08 00:07:45','2026-06-08 00:07:45'),(127,43,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,0.0000,NULL,'2026-06-08 00:07:45','2026-06-08 00:07:45'),(128,43,7,'Es Jeruk',1,5000.00,5000.00,0.0000,NULL,'2026-06-08 00:07:45','2026-06-08 00:07:45'),(129,43,6,'Es Teh Manis',1,3000.00,3000.00,0.0000,NULL,'2026-06-08 00:07:45','2026-06-08 00:07:45'),(130,43,19,'Kulit Ayam Krispi',1,13000.00,13000.00,0.0000,NULL,'2026-06-08 00:07:45','2026-06-08 00:07:45'),(131,43,20,'Sosis Bakar',1,18000.00,18000.00,0.0000,NULL,'2026-06-08 00:07:45','2026-06-08 00:07:45'),(132,43,23,'Telur Mata Sapi',1,18000.00,18000.00,0.0000,NULL,'2026-06-08 00:07:45','2026-06-08 00:07:45'),(133,44,1,'Ayam Geprek Original',1,18000.00,18000.00,0.0000,NULL,'2026-06-08 00:11:00','2026-06-08 00:11:00'),(134,44,13,'Paket Nasi Geprek Mozza',1,25000.00,25000.00,0.0000,NULL,'2026-06-08 00:11:00','2026-06-08 00:11:00'),(135,44,23,'Telur Mata Sapi',1,18000.00,18000.00,0.0000,NULL,'2026-06-08 00:11:00','2026-06-08 00:11:00'),(136,45,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,0.0000,NULL,'2026-06-08 00:11:03','2026-06-08 00:11:03'),(137,46,4,'Ayam Geprek Sambal Matah',1,13000.00,13000.00,0.0000,NULL,'2026-06-08 00:29:17','2026-06-08 00:29:17'),(138,46,7,'Es Jeruk',1,5000.00,5000.00,0.0000,NULL,'2026-06-08 00:29:17','2026-06-08 00:29:17'),(139,46,14,'Paket Nasi Kulit Krispi',1,22000.00,22000.00,0.0000,NULL,'2026-06-08 00:29:17','2026-06-08 00:29:17'),(140,47,2,'Paket Nasi Geprek Paha Bawah',1,10000.00,10000.00,0.0000,NULL,'2026-06-08 00:39:16','2026-06-08 00:39:16'),(141,47,14,'Paket Nasi Kulit Krispi',1,22000.00,22000.00,0.0000,NULL,'2026-06-08 00:39:16','2026-06-08 00:39:16'),(142,47,24,'Sambal Tambahan',1,7000.00,7000.00,0.0000,NULL,'2026-06-08 00:39:16','2026-06-08 00:39:16'),(143,48,1,'Ayam Geprek Original',1,18000.00,18000.00,0.0000,NULL,'2026-06-08 01:33:16','2026-06-08 01:33:16'),(144,48,6,'Es Teh Manis',1,3000.00,3000.00,0.0000,NULL,'2026-06-08 01:33:16','2026-06-08 01:33:16'),(145,48,19,'Kulit Ayam Krispi',1,13000.00,13000.00,0.0000,NULL,'2026-06-08 01:33:16','2026-06-08 01:33:16'),(146,49,28,'Ayam Geprek Paha Bawah',1,8000.00,8000.00,0.0000,NULL,'2026-06-08 03:37:35','2026-06-08 03:37:35'),(147,49,1,'Ayam Geprek Dada',1,10000.00,10000.00,0.0000,NULL,'2026-06-08 03:37:35','2026-06-08 03:37:35'),(148,49,2,'Paket Nasi Geprek Paha Bawah',2,10000.00,20000.00,0.0000,NULL,'2026-06-08 03:37:35','2026-06-08 03:37:35'),(149,50,1,'Ayam Geprek Dada',1,10000.00,10000.00,0.0000,NULL,'2026-06-08 03:41:01','2026-06-08 03:41:01'),(150,50,28,'Ayam Geprek Paha Bawah',1,8000.00,8000.00,0.0000,NULL,'2026-06-08 03:41:01','2026-06-08 03:41:01'),(151,50,33,'Paket Nasi Geprek Sayap',1,10000.00,10000.00,0.0000,NULL,'2026-06-08 03:41:01','2026-06-08 03:41:01'),(152,51,31,'Ayam Geprek Paha Atas',1,10000.00,10000.00,0.0000,NULL,'2026-06-08 06:08:44','2026-06-08 06:08:44'),(153,51,28,'Ayam Geprek Paha Bawah',1,8000.00,8000.00,0.0000,NULL,'2026-06-08 06:08:44','2026-06-08 06:08:44'),(154,51,32,'Ayam Geprek Sayap',1,8000.00,8000.00,0.0000,NULL,'2026-06-08 06:08:44','2026-06-08 06:08:44'),(155,51,34,'Ceker Crispy',1,2000.00,2000.00,0.0000,NULL,'2026-06-08 06:08:44','2026-06-08 06:08:44'),(156,51,46,'Cappuccino',1,7000.00,7000.00,0.0000,NULL,'2026-06-08 06:08:44','2026-06-08 06:08:44');
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Format: GR-YYYYMMDD-XXXX, contoh: GR-20260101-0001',
  `queue_number` smallint unsigned NOT NULL COMMENT 'Nomor antrean harian, reset tiap hari (1ΓÇô999)',
  `member_id` bigint unsigned DEFAULT NULL,
  `voucher_id` bigint unsigned DEFAULT NULL,
  `table_number` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor meja dari scan QR',
  `type` enum('dine_in','takeaway') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'dine_in',
  `status` enum('pending','confirmed','preparing','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `payment_method` enum('qris','cash') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subtotal` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(14,2) unsigned NOT NULL DEFAULT '0.00',
  `points_redeemed_amount` decimal(14,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Nilai rupiah dari poin yang digunakan',
  `points_redeemed` int unsigned NOT NULL DEFAULT '0' COMMENT 'Jumlah poin yang digunakan',
  `total_amount` decimal(14,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Grand total yang harus dibayar',
  `total_hpp` decimal(14,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Total HPP order (╬ú hpp_snapshot ├ù qty), diisi saat konfirmasi',
  `points_earned` int unsigned NOT NULL DEFAULT '0' COMMENT 'Poin yang didapat dari transaksi ini',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `confirmed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `confirmed_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_voucher_id_foreign` (`voucher_id`),
  KEY `orders_confirmed_by_foreign` (`confirmed_by`),
  KEY `orders_status_created_at_index` (`status`,`created_at`),
  KEY `orders_member_id_status_index` (`member_id`,`status`),
  KEY `orders_created_at_status_index` (`created_at`,`status`),
  CONSTRAINT `orders_confirmed_by_foreign` FOREIGN KEY (`confirmed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,'GR-20260506-0001',1,NULL,NULL,NULL,'dine_in','cancelled','cash',90000.00,0.00,0.00,0,90000.00,54000.00,0,NULL,NULL,NULL,NULL,'2026-06-03 11:27:40','2026-06-05 11:27:40',NULL),(2,'GR-20260507-0002',2,18,NULL,NULL,'dine_in','cancelled','cash',84000.00,0.00,0.00,0,84000.00,50400.00,0,NULL,NULL,NULL,NULL,'2026-05-31 11:27:40','2026-06-05 11:27:40',NULL),(3,'GR-20260530-0003',3,16,NULL,NULL,'takeaway','completed','qris',115000.00,0.00,0.00,0,115000.00,69000.00,0,NULL,NULL,NULL,NULL,'2026-06-04 11:27:40','2026-06-05 11:27:40',NULL),(4,'GR-20260517-0004',4,8,NULL,NULL,'takeaway','pending','qris',103000.00,0.00,0.00,0,103000.00,61800.00,0,NULL,NULL,NULL,NULL,'2026-06-02 11:27:40','2026-06-05 11:27:40',NULL),(5,'GR-20260528-0005',5,NULL,NULL,NULL,'takeaway','preparing','cash',71000.00,0.00,0.00,0,71000.00,42600.00,0,NULL,NULL,NULL,NULL,'2026-05-18 11:27:40','2026-06-05 11:27:40',NULL),(6,'GR-20260605-0006',6,NULL,NULL,NULL,'takeaway','completed','qris',32000.00,0.00,0.00,0,32000.00,19200.00,0,NULL,NULL,NULL,NULL,'2026-06-01 11:27:40','2026-06-05 11:27:40',NULL),(7,'GR-20260524-0007',7,9,NULL,NULL,'dine_in','preparing','qris',117000.00,0.00,0.00,0,117000.00,70200.00,0,NULL,NULL,NULL,NULL,'2026-05-16 11:27:40','2026-06-05 11:27:40',NULL),(8,'GR-20260524-0008',8,NULL,NULL,NULL,'dine_in','cancelled','qris',179000.00,0.00,0.00,0,179000.00,107400.00,0,NULL,NULL,NULL,NULL,'2026-05-28 11:27:40','2026-06-05 11:27:40',NULL),(9,'GR-20260523-0009',9,3,NULL,NULL,'dine_in','cancelled','qris',153000.00,0.00,0.00,0,153000.00,91800.00,0,NULL,NULL,NULL,NULL,'2026-05-29 11:27:40','2026-06-05 11:27:40',NULL),(10,'GR-20260513-0010',10,7,NULL,NULL,'dine_in','completed','qris',63000.00,0.00,0.00,0,63000.00,37800.00,0,NULL,NULL,NULL,NULL,'2026-05-29 11:27:40','2026-06-05 11:27:40',NULL),(11,'GR-20260506-0011',11,NULL,NULL,NULL,'takeaway','cancelled','qris',36000.00,0.00,0.00,0,36000.00,21600.00,0,NULL,NULL,NULL,NULL,'2026-05-19 11:27:40','2026-06-05 11:27:40',NULL),(12,'GR-20260518-0012',12,24,NULL,NULL,'dine_in','pending','cash',50000.00,0.00,0.00,0,50000.00,30000.00,0,NULL,NULL,NULL,NULL,'2026-05-06 11:27:40','2026-06-05 11:27:40',NULL),(13,'GR-20260517-0013',13,22,NULL,NULL,'takeaway','cancelled','cash',148000.00,0.00,0.00,0,148000.00,88800.00,0,NULL,NULL,NULL,NULL,'2026-05-06 11:27:40','2026-06-05 11:27:40',NULL),(14,'GR-20260508-0014',14,3,NULL,NULL,'dine_in','completed','qris',151000.00,0.00,0.00,0,151000.00,90600.00,0,NULL,NULL,NULL,NULL,'2026-05-13 11:27:40','2026-06-05 11:27:40',NULL),(15,'GR-20260509-0015',15,NULL,NULL,NULL,'takeaway','completed','qris',170000.00,0.00,0.00,0,170000.00,102000.00,0,NULL,NULL,NULL,NULL,'2026-06-01 11:27:40','2026-06-05 11:27:40',NULL),(16,'GR-20260516-0016',16,NULL,NULL,NULL,'takeaway','confirmed','qris',46000.00,0.00,0.00,0,46000.00,27600.00,0,NULL,NULL,NULL,NULL,'2026-05-11 11:27:40','2026-06-05 11:27:40',NULL),(17,'GR-20260509-0017',17,NULL,NULL,NULL,'dine_in','cancelled','cash',56000.00,0.00,0.00,0,56000.00,33600.00,0,NULL,NULL,NULL,NULL,'2026-05-22 11:27:40','2026-06-05 11:27:40',NULL),(18,'GR-20260508-0018',18,NULL,NULL,NULL,'takeaway','confirmed','cash',132000.00,0.00,0.00,0,132000.00,79200.00,0,NULL,NULL,NULL,NULL,'2026-05-07 11:27:40','2026-06-05 11:27:40',NULL),(19,'GR-20260602-0019',19,NULL,NULL,NULL,'dine_in','preparing','cash',79000.00,0.00,0.00,0,79000.00,47400.00,0,NULL,NULL,NULL,NULL,'2026-05-13 11:27:40','2026-06-05 11:27:40',NULL),(20,'GR-20260524-0020',20,NULL,NULL,NULL,'takeaway','pending','qris',71000.00,0.00,0.00,0,71000.00,42600.00,0,NULL,NULL,NULL,NULL,'2026-05-24 11:27:40','2026-06-05 11:27:40',NULL),(21,'GR-20260526-0021',21,20,NULL,NULL,'takeaway','cancelled','qris',160000.00,0.00,0.00,0,160000.00,96000.00,0,NULL,NULL,NULL,NULL,'2026-05-25 11:27:40','2026-06-05 11:27:40',NULL),(22,'GR-20260519-0022',22,1,NULL,NULL,'takeaway','cancelled','qris',50000.00,0.00,0.00,0,50000.00,30000.00,0,NULL,NULL,NULL,NULL,'2026-05-09 11:27:40','2026-06-05 11:27:40',NULL),(23,'GR-20260530-0023',23,NULL,NULL,NULL,'dine_in','completed','cash',75000.00,0.00,0.00,0,75000.00,45000.00,0,NULL,NULL,NULL,NULL,'2026-05-08 11:27:40','2026-06-05 11:27:40',NULL),(24,'GR-20260521-0024',24,NULL,NULL,NULL,'dine_in','cancelled','qris',186000.00,0.00,0.00,0,186000.00,111600.00,0,NULL,NULL,NULL,NULL,'2026-05-14 11:27:40','2026-06-05 11:27:40',NULL),(25,'GR-20260520-0025',25,NULL,NULL,NULL,'takeaway','cancelled','cash',216000.00,0.00,0.00,0,216000.00,129600.00,0,NULL,NULL,NULL,NULL,'2026-05-25 11:27:40','2026-06-05 11:27:40',NULL),(26,'GR-20260607-0001',1,NULL,NULL,NULL,'takeaway','completed','qris',14000.00,0.00,0.00,0,14000.00,0.00,0,NULL,'2026-06-06 21:55:48','2026-06-06 21:56:25',3,'2026-06-06 21:51:56','2026-06-06 21:56:25',NULL),(27,'GR-20260607-0002',2,NULL,NULL,NULL,'takeaway','completed','cash',10000.00,0.00,0.00,0,10000.00,0.00,0,NULL,'2026-06-06 22:01:59','2026-06-06 22:04:59',3,'2026-06-06 21:59:07','2026-06-06 22:04:59',NULL),(28,'GR-20260607-0003',3,NULL,NULL,NULL,'takeaway','completed','qris',13000.00,0.00,0.00,0,13000.00,0.00,0,NULL,'2026-06-06 22:02:12','2026-06-06 22:05:10',3,'2026-06-06 22:00:02','2026-06-06 22:05:10',NULL),(29,'GR-20260607-0004',4,NULL,NULL,NULL,'takeaway','completed','qris',8000.00,0.00,0.00,0,8000.00,0.00,0,NULL,'2026-06-06 22:05:15','2026-06-06 22:08:39',3,'2026-06-06 22:00:38','2026-06-06 22:08:39',NULL),(30,'GR-20260607-0005',5,NULL,NULL,NULL,'takeaway','completed','qris',36000.00,0.00,0.00,0,36000.00,0.00,0,NULL,'2026-06-06 22:05:23','2026-06-06 22:08:43',3,'2026-06-06 22:01:30','2026-06-06 22:08:43',NULL),(31,'GR-20260607-0006',6,NULL,16,NULL,'takeaway','completed','qris',32000.00,3200.00,0.00,0,28800.00,0.00,0,NULL,'2026-06-06 22:13:12','2026-06-06 22:13:30',3,'2026-06-06 22:09:31','2026-06-06 22:13:30',NULL),(32,'GR-20260607-0007',7,26,NULL,NULL,'takeaway','completed','qris',14000.00,0.00,0.00,0,14000.00,0.00,14,NULL,'2026-06-06 22:13:37','2026-06-06 22:13:47',3,'2026-06-06 22:12:42','2026-06-06 22:13:47',NULL),(33,'GR-20260607-0008',8,26,NULL,NULL,'takeaway','completed','qris',50000.00,0.00,0.00,0,50000.00,0.00,50,NULL,'2026-06-06 22:44:35','2026-06-06 23:36:05',3,'2026-06-06 22:22:21','2026-06-06 23:36:05',NULL),(34,'GR-20260607-0009',9,NULL,NULL,NULL,'takeaway','completed','qris',14000.00,0.00,0.00,0,14000.00,0.00,0,NULL,'2026-06-07 01:01:43','2026-06-07 02:52:06',3,'2026-06-07 00:48:59','2026-06-07 02:52:06',NULL),(35,'GR-20260607-0010',10,28,NULL,NULL,'takeaway','completed','qris',36000.00,0.00,0.00,0,36000.00,0.00,36,NULL,'2026-06-07 02:51:09','2026-06-07 02:52:10',3,'2026-06-07 02:47:26','2026-06-07 02:52:10',NULL),(36,'GR-20260607-0011',11,28,NULL,NULL,'takeaway','completed','qris',59000.00,0.00,0.00,0,59000.00,0.00,59,NULL,'2026-06-07 02:51:03','2026-06-07 02:52:12',3,'2026-06-07 02:50:00','2026-06-07 02:52:12',NULL),(37,'GR-20260608-0001',1,29,NULL,NULL,'takeaway','completed','qris',77000.00,0.00,0.00,0,77000.00,0.00,77,NULL,'2026-06-07 17:25:56','2026-06-07 18:39:58',3,'2026-06-07 17:24:25','2026-06-07 18:39:58',NULL),(38,'GR-20260608-0002',2,NULL,NULL,NULL,'takeaway','completed','qris',13000.00,0.00,0.00,0,13000.00,0.00,0,NULL,'2026-06-07 18:46:47','2026-06-07 18:49:12',3,'2026-06-07 18:43:49','2026-06-07 18:49:12',NULL),(39,'GR-20260608-0003',3,29,NULL,NULL,'takeaway','completed','qris',154000.00,0.00,0.00,0,154000.00,0.00,154,NULL,'2026-06-07 19:01:01','2026-06-07 19:01:24',3,'2026-06-07 19:00:40','2026-06-07 19:01:24',NULL),(40,'GR-20260608-0004',4,28,NULL,NULL,'takeaway','completed','cash',70000.00,0.00,0.00,0,70000.00,0.00,70,NULL,'2026-06-07 20:15:45','2026-06-07 20:16:20',3,'2026-06-07 20:15:33','2026-06-07 20:16:20',NULL),(41,'GR-20260608-0005',5,28,NULL,NULL,'takeaway','completed','qris',39000.00,0.00,0.00,0,39000.00,0.00,39,NULL,'2026-06-07 20:17:36','2026-06-07 20:17:46',3,'2026-06-07 20:17:26','2026-06-07 20:17:46',NULL),(42,'GR-20260608-0006',6,NULL,NULL,NULL,'takeaway','completed','qris',20000.00,0.00,0.00,0,20000.00,0.00,0,'pedes level 1000','2026-06-07 23:58:29','2026-06-07 23:58:40',3,'2026-06-07 23:58:05','2026-06-07 23:58:40',NULL),(43,'GR-20260608-0007',7,30,NULL,NULL,'takeaway','completed','qris',88000.00,0.00,0.00,0,88000.00,0.00,88,NULL,'2026-06-08 00:11:21','2026-06-08 00:33:58',3,'2026-06-08 00:07:45','2026-06-08 00:33:58',NULL),(44,'GR-20260608-0008',8,28,NULL,NULL,'takeaway','completed','cash',61000.00,0.00,0.00,0,61000.00,0.00,61,NULL,'2026-06-08 00:11:31','2026-06-08 00:33:54',3,'2026-06-08 00:11:00','2026-06-08 00:33:54',NULL),(45,'GR-20260608-0009',9,NULL,NULL,NULL,'takeaway','completed','qris',13000.00,0.00,0.00,0,13000.00,0.00,0,NULL,'2026-06-08 00:33:01','2026-06-08 00:34:18',3,'2026-06-08 00:11:03','2026-06-08 00:34:18',NULL),(46,'GR-20260608-0010',10,28,NULL,NULL,'takeaway','completed','qris',40000.00,0.00,0.00,0,40000.00,0.00,40,NULL,'2026-06-08 00:34:24','2026-06-08 00:34:34',3,'2026-06-08 00:29:17','2026-06-08 00:34:34',NULL),(47,'GR-20260608-0011',11,NULL,NULL,NULL,'takeaway','completed','cash',39000.00,0.00,0.00,0,39000.00,0.00,0,NULL,'2026-06-08 00:39:29','2026-06-08 01:05:18',3,'2026-06-08 00:39:16','2026-06-08 01:05:18',NULL),(48,'GR-20260608-0012',12,31,NULL,NULL,'takeaway','completed','qris',34000.00,0.00,0.00,0,34000.00,0.00,34,NULL,'2026-06-08 02:00:23','2026-06-08 02:06:04',3,'2026-06-08 01:33:16','2026-06-08 02:06:04',NULL),(49,'GR-20260608-0013',13,NULL,NULL,NULL,'takeaway','completed','cash',38000.00,0.00,0.00,0,38000.00,0.00,0,NULL,'2026-06-08 03:38:17','2026-06-08 06:01:01',3,'2026-06-08 03:37:35','2026-06-08 06:01:01',NULL),(50,'GR-20260608-0014',14,NULL,NULL,NULL,'takeaway','completed','qris',28000.00,0.00,0.00,0,28000.00,0.00,0,NULL,'2026-06-08 03:45:36','2026-06-08 06:01:03',3,'2026-06-08 03:41:01','2026-06-08 06:01:03',NULL),(51,'GR-20260608-0015',15,28,2,NULL,'takeaway','completed','qris',35000.00,8000.00,0.00,0,27000.00,0.00,27,NULL,'2026-06-08 06:09:20','2026-06-08 06:09:41',3,'2026-06-08 06:08:44','2026-06-08 06:09:41',NULL);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `passkeys`
--

DROP TABLE IF EXISTS `passkeys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `passkeys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credential_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `credential` json NOT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `passkeys_credential_id_unique` (`credential_id`),
  KEY `passkeys_user_id_index` (`user_id`),
  CONSTRAINT `passkeys_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `passkeys`
--

LOCK TABLES `passkeys` WRITE;
/*!40000 ALTER TABLE `passkeys` DISABLE KEYS */;
/*!40000 ALTER TABLE `passkeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point_logs`
--

DROP TABLE IF EXISTS `point_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `point_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `type` enum('earn','redeem','adjustment','expire') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipe mutasi poin',
  `points` int NOT NULL COMMENT 'Positif untuk earn/adjustment+, negatif untuk redeem/expire',
  `balance_after` int unsigned NOT NULL COMMENT 'Saldo poin setelah mutasi ini',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `point_logs_member_id_created_at_index` (`member_id`,`created_at`),
  KEY `point_logs_order_id_index` (`order_id`),
  CONSTRAINT `point_logs_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE,
  CONSTRAINT `point_logs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point_logs`
--

LOCK TABLES `point_logs` WRITE;
/*!40000 ALTER TABLE `point_logs` DISABLE KEYS */;
INSERT INTO `point_logs` VALUES (1,26,32,'earn',14,14,'Poin dari order #GR-20260607-0007','2026-06-06 22:13:37','2026-06-06 22:13:37'),(2,26,33,'earn',50,64,'Poin dari order #GR-20260607-0008','2026-06-06 22:44:35','2026-06-06 22:44:35'),(3,28,36,'earn',59,59,'Poin dari order #GR-20260607-0011','2026-06-07 02:51:04','2026-06-07 02:51:04'),(4,28,35,'earn',36,95,'Poin dari order #GR-20260607-0010','2026-06-07 02:51:09','2026-06-07 02:51:09'),(5,29,37,'earn',77,77,'Poin dari order #GR-20260608-0001','2026-06-07 17:25:57','2026-06-07 17:25:57'),(6,29,39,'earn',154,231,'Poin dari order #GR-20260608-0003','2026-06-07 19:01:01','2026-06-07 19:01:01'),(7,28,40,'earn',70,165,'Poin dari order #GR-20260608-0004','2026-06-07 20:15:45','2026-06-07 20:15:45'),(8,28,NULL,'redeem',-150,15,'Auto-redemption 150 poin untuk Voucher Paket Nasi Ayam Geprek Gratis','2026-06-07 20:15:57','2026-06-07 20:15:57'),(9,28,41,'earn',39,54,'Poin dari order #GR-20260608-0005','2026-06-07 20:17:36','2026-06-07 20:17:36'),(10,30,43,'earn',88,88,'Poin dari order #GR-20260608-0007','2026-06-08 00:11:21','2026-06-08 00:11:21'),(11,28,44,'earn',61,115,'Poin dari order #GR-20260608-0008','2026-06-08 00:11:31','2026-06-08 00:11:31'),(12,28,46,'earn',40,155,'Poin dari order #GR-20260608-0010','2026-06-08 00:34:24','2026-06-08 00:34:24'),(13,28,NULL,'redeem',-150,5,'Auto-redemption 150 poin untuk Voucher Paket Nasi Ayam Geprek Gratis','2026-06-08 00:37:28','2026-06-08 00:37:28'),(14,31,48,'earn',34,34,'Poin dari order #GR-20260608-0012','2026-06-08 02:00:24','2026-06-08 02:00:24'),(15,28,51,'earn',27,32,'Poin dari order #GR-20260608-0015','2026-06-08 06:09:20','2026-06-08 06:09:20');
/*!40000 ALTER TABLE `point_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recipes`
--

DROP TABLE IF EXISTS `recipes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recipes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `menu_item_id` bigint unsigned NOT NULL,
  `stock_ingredient_id` bigint unsigned NOT NULL,
  `qty_used` decimal(14,4) unsigned NOT NULL COMMENT 'Jumlah bahan yang dipakai per 1 porsi menu',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `recipe_unique` (`menu_item_id`,`stock_ingredient_id`),
  KEY `recipes_stock_ingredient_id_foreign` (`stock_ingredient_id`),
  CONSTRAINT `recipes_menu_item_id_foreign` FOREIGN KEY (`menu_item_id`) REFERENCES `menu_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `recipes_stock_ingredient_id_foreign` FOREIGN KEY (`stock_ingredient_id`) REFERENCES `stock_ingredients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recipes`
--

LOCK TABLES `recipes` WRITE;
/*!40000 ALTER TABLE `recipes` DISABLE KEYS */;
/*!40000 ALTER TABLE `recipes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('4t9G7YtVf8yO0t2wrcf6zrTCgJzCSlIJhx9TMOi0',2,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0','eyJfdG9rZW4iOiI1eEljS1NNYUVZVXhJam12ZmJFbHFjMEUzV3RzY1hkQ2RjMko4Zk9iIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvc2VsZi1vcmRlci11bWttLWdlcHJlay1yZWpvLnRlc3RcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyfQ==',1780925494),('fCc2mkBN3cpgj9Ni3HVlmtR6AFKGQZE4YsmiLmoT',2,'103.247.23.137','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0','eyJfdG9rZW4iOiJYUmdoekVScEE3ZUVUYUZidFY5ZFRqZld4dTczT29ETXBLekRwdjE3IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2pva2VzdGVyLXJlZnJpZWQtdW5tb3Zpbmcubmdyb2stZnJlZS5kZXZcL2Rhc2hib2FyZCIsInJvdXRlIjoiZGFzaGJvYXJkIn0sImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjoyfQ==',1780922317),('HI3q8ujJGqkLqpYJ8CQwjaB53uRSI9GEmnFa7iDR',NULL,'103.247.23.137','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/149.0.0.0 Safari/537.36 Edg/149.0.0.0','eyJfdG9rZW4iOiJEZnptcTZxY0prT1ZSWnlzTkJtQ3djS1VsVEZNT2ZpcTFLOWhCVHptIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2pva2VzdGVyLXJlZnJpZWQtdW5tb3Zpbmcubmdyb2stZnJlZS5kZXYiLCJyb3V0ZSI6ImhvbWUifX0=',1780922316),('hJsc0uliAHVBhyKXcdcLxhwXGxz8MldnNZsqj3gr',2,'103.247.23.137','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJpaFdkWjIwR1BjQUZOczBwcVdSN0VBYzRuZGplM0JkeFhsck1Oa080IiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9qb2tlc3Rlci1yZWZyaWVkLXVubW92aW5nLm5ncm9rLWZyZWUuZGV2XC9tZW51Iiwicm91dGUiOiJtZW51LmluZGV4In0sIl9mbGFzaCI6eyJvbGQiOltdLCJuZXciOltdfSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjIsImNhcnQiOnsiMSI6eyJpZCI6MSwibmFtZSI6IkF5YW0gR2VwcmVrIERhZGEiLCJwcmljZSI6MTAwMDAsInF1YW50aXR5Ijo1LCJzdWJ0b3RhbCI6NTAwMDAsImltYWdlIjoibWVudVwvRkR6NkhhbWZsUDBqeWhmSkJGN2ZLWVpUVHFSSWx1YnlGMm9LUG5sQi5qcGcifX0sImNoZWNrb3V0X21lbWJlcl9pZCI6Mjd9',1780922327),('luQVxZdOXvRr7ACCugR1M7zmTTRyP6YzjceWSkSB',2,'103.247.23.137','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJZYWJBN2FZUUtrTGd1NDZxZ25rMzBXMEJBZkFKVHd5c3N6MnRRUHl4IiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cHM6XC9cL2pva2VzdGVyLXJlZnJpZWQtdW5tb3Zpbmcubmdyb2stZnJlZS5kZXZcL21lbnUiLCJyb3V0ZSI6Im1lbnUuaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjJ9',1780922284),('uHuJf971z5l0OQp9obqXy6XMsSLzIx36FR45GCUd',NULL,'103.247.23.137','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:151.0) Gecko/20100101 Firefox/151.0','eyJfdG9rZW4iOiJXWDlOOHphaGltcFhyV1E2R1ZCaEVab2VGUTFjNnY3STlaZHpkZU5aIiwiX3ByZXZpb3VzIjp7InVybCI6Imh0dHBzOlwvXC9qb2tlc3Rlci1yZWZyaWVkLXVubW92aW5nLm5ncm9rLWZyZWUuZGV2XC9sb2dpbiIsInJvdXRlIjoibG9naW4ifSwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119fQ==',1780922304);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'qris_image_path','qris/xJKQh6loXiVQEozJ0aUNurY4MZ8AYT9X9SLKX81X.jpg','2026-06-07 02:43:22','2026-06-08 01:27:34');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_ingredients`
--

DROP TABLE IF EXISTS `stock_ingredients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_ingredients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g. gram, ml, pcs, liter',
  `current_stock` decimal(14,3) unsigned NOT NULL DEFAULT '0.000',
  `minimum_stock` decimal(14,3) unsigned NOT NULL DEFAULT '0.000' COMMENT 'Alert threshold',
  `unit_cost` decimal(14,4) unsigned NOT NULL DEFAULT '0.0000' COMMENT 'Harga beli per unit (untuk kalkulasi HPP)',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_ingredients`
--

LOCK TABLES `stock_ingredients` WRITE;
/*!40000 ALTER TABLE `stock_ingredients` DISABLE KEYS */;
INSERT INTO `stock_ingredients` VALUES (1,'Daging Ayam','liter',91.000,10.000,5000.0000,'Stok dummy Daging Ayam','2026-06-05 11:27:24','2026-06-05 11:27:24'),(2,'Beras','pcs',93.000,10.000,3000.0000,'Stok dummy Beras','2026-06-05 11:27:24','2026-06-05 11:27:24'),(3,'Minyak Goreng','gram',77.000,10.000,3000.0000,'Stok dummy Minyak Goreng','2026-06-05 11:27:24','2026-06-05 11:27:24'),(4,'Cabai Rawit','liter',90.000,10.000,4000.0000,'Stok dummy Cabai Rawit','2026-06-05 11:27:24','2026-06-05 11:27:24'),(5,'Bawang Putih','liter',72.000,10.000,1000.0000,'Stok dummy Bawang Putih','2026-06-05 11:27:24','2026-06-05 11:27:24'),(6,'Bawang Merah','kg',26.000,10.000,5000.0000,'Stok dummy Bawang Merah','2026-06-05 11:27:24','2026-06-05 11:27:24'),(7,'Garam','gram',42.000,10.000,3000.0000,'Stok dummy Garam','2026-06-05 11:27:24','2026-06-05 11:27:24'),(8,'Gula Pasir','ikat',60.000,10.000,5000.0000,'Stok dummy Gula Pasir','2026-06-05 11:27:24','2026-06-05 11:27:24'),(9,'Tepung Terigu','gram',79.000,10.000,4000.0000,'Stok dummy Tepung Terigu','2026-06-05 11:27:24','2026-06-05 11:27:24'),(10,'Tepung Bumbu','gram',63.000,10.000,2000.0000,'Stok dummy Tepung Bumbu','2026-06-05 11:27:24','2026-06-05 11:27:24'),(11,'Keju Cheddar','gram',68.000,10.000,5000.0000,'Stok dummy Keju Cheddar','2026-06-05 11:27:24','2026-06-05 11:27:24'),(12,'Keju Mozzarella','liter',40.000,10.000,1000.0000,'Stok dummy Keju Mozzarella','2026-06-05 11:27:24','2026-06-05 11:27:24'),(13,'Teh Celup','liter',76.000,10.000,3000.0000,'Stok dummy Teh Celup','2026-06-05 11:27:24','2026-06-05 11:27:24'),(14,'Jeruk Peras','ikat',73.000,10.000,1000.0000,'Stok dummy Jeruk Peras','2026-06-05 11:27:24','2026-06-05 11:27:24'),(15,'Kopi Bubuk','kg',44.000,10.000,5000.0000,'Stok dummy Kopi Bubuk','2026-06-05 11:27:24','2026-06-05 11:27:24'),(16,'Susu Kental Manis','liter',51.000,10.000,3000.0000,'Stok dummy Susu Kental Manis','2026-06-05 11:27:24','2026-06-05 11:27:24'),(17,'Jamur Tiram','gram',49.000,10.000,4000.0000,'Stok dummy Jamur Tiram','2026-06-05 11:27:24','2026-06-05 11:27:24'),(18,'Tahu Putih','liter',54.000,10.000,1000.0000,'Stok dummy Tahu Putih','2026-06-05 11:27:24','2026-06-05 11:27:24'),(19,'Tempe','gram',67.000,10.000,2000.0000,'Stok dummy Tempe','2026-06-05 11:27:24','2026-06-05 11:27:24'),(20,'Kulit Ayam','liter',78.000,10.000,2000.0000,'Stok dummy Kulit Ayam','2026-06-05 11:27:24','2026-06-05 11:27:24'),(21,'Sosis Sapi','gram',63.000,10.000,2000.0000,'Stok dummy Sosis Sapi','2026-06-05 11:27:24','2026-06-05 11:27:24'),(22,'Telur Ayam','ikat',62.000,10.000,5000.0000,'Stok dummy Telur Ayam','2026-06-05 11:27:24','2026-06-05 11:27:24'),(23,'Tomat','pcs',85.000,10.000,1000.0000,'Stok dummy Tomat','2026-06-05 11:27:24','2026-06-05 11:27:24'),(24,'Jeruk Nipis','gram',27.000,10.000,1000.0000,'Stok dummy Jeruk Nipis','2026-06-05 11:27:24','2026-06-05 11:27:24'),(25,'Es Batu','ikat',14.000,10.000,2000.0000,'Stok dummy Es Batu','2026-06-05 11:27:24','2026-06-05 11:27:24'),(28,'aaaaaaaaaaaaaaa','pcs',8.000,10.000,1000.0000,NULL,'2026-06-05 22:18:13','2026-06-05 23:35:44'),(31,'ccccccc','kg',9.000,10.000,1000.0000,NULL,'2026-06-07 17:58:44','2026-06-07 17:59:16');
/*!40000 ALTER TABLE `stock_ingredients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('owner','kasir','kds') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'kasir',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin Kasir','admin@geprek.com','kasir',NULL,'$2y$12$XqlgaqzszsUTCl8OOB.7i.0.2/vt4RhLKTnDcor.KSohevYjuGtme',NULL,NULL,NULL,NULL,'2026-06-05 11:27:23','2026-06-05 11:27:23'),(2,'Yono (Owner)','owner@geprekrejo.com','owner','2026-06-05 21:33:26','$2y$12$9rElILjFtfFzr2lqsvOrb.AgHNVt5eAoK/lTRZY7VxEIIqbtPDISW',NULL,NULL,NULL,NULL,'2026-06-05 21:33:26','2026-06-05 22:54:48'),(3,'Kasir Outlet 1','kasir1@geprekrejo.com','kasir','2026-06-05 21:33:26','$2y$12$wmYUebQC1.7lm5fzpfBViOnBndeeHRkmG..Oa7w9EIjP/U966me0W',NULL,NULL,NULL,'uMZUKp6xLRzBCmUcwAgw1cxb5cleHIJ2OlLjeYVOFvjjHpJ0fkQqJp6saCYb','2026-06-05 21:33:26','2026-06-05 21:33:26'),(4,'Kasir Outlet 2','kasir2@geprekrejo.com','kasir','2026-06-05 21:33:26','$2y$12$y51PepL5lcS5/TfoAZHB0.WrhKsuHtgjPVNfg/ZjkTjNuXOeCNb7i',NULL,NULL,NULL,NULL,'2026-06-05 21:33:26','2026-06-05 21:33:26'),(5,'Tim Dapur (KDS)','dapur@geprekrejo.com','kds','2026-06-05 21:33:26','$2y$12$hGcMkVYqmKpAYSxEZHT80e9JJL4Z01I8B9IgX0gJb4r.A3p/V4aoe',NULL,NULL,NULL,NULL,'2026-06-05 21:33:26','2026-06-05 21:33:26'),(6,'Test User','test@example.com','kasir',NULL,'$2y$12$Toanqe.caAQUhm/wKMulWOWF99uyv072uO7xhq6fCrsaaj49ZBioK',NULL,NULL,NULL,NULL,'2026-06-07 05:46:46','2026-06-07 05:46:46'),(7,'Kasir','test2@example.com','kasir',NULL,'$2y$12$WB53hchr0lR4Bd.bGXQLIOd0uF72PzFMKegS4YzbNBs.QpQT9AYB.',NULL,NULL,NULL,NULL,'2026-06-07 05:48:21','2026-06-07 05:48:21'),(8,'Admin','admin@example.com','kasir',NULL,'$2y$12$.DkmW3KtMmVwCsiOUbxJj.5HfOUuyl4mcmHN9PevHjNw7WUzKFCHu',NULL,NULL,NULL,NULL,'2026-06-07 05:49:33','2026-06-07 05:49:33');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher_uses`
--

DROP TABLE IF EXISTS `voucher_uses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `voucher_uses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `voucher_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `member_id` bigint unsigned DEFAULT NULL,
  `discount_applied` decimal(12,2) unsigned NOT NULL COMMENT 'Nilai diskon aktual yang diterapkan ke order ini',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `voucher_uses_voucher_id_order_id_unique` (`voucher_id`,`order_id`),
  KEY `voucher_uses_order_id_foreign` (`order_id`),
  KEY `voucher_uses_member_id_foreign` (`member_id`),
  KEY `voucher_uses_voucher_id_index` (`voucher_id`),
  CONSTRAINT `voucher_uses_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE SET NULL,
  CONSTRAINT `voucher_uses_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `voucher_uses_voucher_id_foreign` FOREIGN KEY (`voucher_id`) REFERENCES `vouchers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher_uses`
--

LOCK TABLES `voucher_uses` WRITE;
/*!40000 ALTER TABLE `voucher_uses` DISABLE KEYS */;
INSERT INTO `voucher_uses` VALUES (1,16,31,NULL,3200.00,'2026-06-06 22:13:12','2026-06-06 22:13:12'),(2,2,51,28,8000.00,'2026-06-08 06:09:20','2026-06-08 06:09:20');
/*!40000 ALTER TABLE `voucher_uses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vouchers`
--

DROP TABLE IF EXISTS `vouchers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vouchers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint unsigned DEFAULT NULL,
  `code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `discount_type` enum('percentage','fixed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(12,2) unsigned NOT NULL COMMENT 'Nilai diskon: persen (0-100) atau nominal rupiah',
  `minimum_order` decimal(12,2) unsigned NOT NULL DEFAULT '0.00' COMMENT 'Minimum total belanja untuk menggunakan voucher',
  `maximum_discount` decimal(12,2) DEFAULT NULL COMMENT 'Cap diskon (untuk tipe percentage)',
  `max_uses` int unsigned NOT NULL DEFAULT '1' COMMENT '0 = unlimited',
  `uses_count` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `member_only` tinyint(1) NOT NULL DEFAULT '0',
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `vouchers_code_unique` (`code`),
  KEY `vouchers_member_id_foreign` (`member_id`),
  CONSTRAINT `vouchers_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vouchers`
--

LOCK TABLES `vouchers` WRITE;
/*!40000 ALTER TABLE `vouchers` DISABLE KEYS */;
INSERT INTO `vouchers` VALUES (1,NULL,'PROMO2026_1','Promo Dummy 1',NULL,'percentage',31.00,88000.00,18000.00,24,7,1,0,'2026-05-26 11:27:40','2026-06-16 11:27:40','2026-06-05 11:27:40','2026-06-05 23:43:08',NULL),(2,NULL,'PROMO2026_2','Promo Dummy 2',NULL,'fixed',8000.00,26000.00,NULL,15,8,1,0,'2026-05-20 11:27:40','2026-06-23 11:27:40','2026-06-05 11:27:40','2026-06-08 06:09:20',NULL),(3,NULL,'PROMO2026_3','Promo Dummy 3',NULL,'percentage',40.00,92000.00,33000.00,10,7,1,0,'2026-05-20 11:27:40','2026-07-03 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(4,NULL,'PROMO2026_4','Promo Dummy 4',NULL,'percentage',24.00,42000.00,26000.00,30,8,1,0,'2026-05-29 11:27:40','2026-06-19 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(5,NULL,'PROMO2026_5','Promo Dummy 5',NULL,'percentage',49.00,97000.00,39000.00,49,0,1,0,'2026-05-22 11:27:40','2026-07-05 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(6,NULL,'PROMO2026_6','Promo Dummy 6',NULL,'fixed',13000.00,70000.00,NULL,32,10,1,0,'2026-05-22 11:27:40','2026-06-22 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(7,NULL,'PROMO2026_7','Promo Dummy 7',NULL,'percentage',20.00,51000.00,22000.00,63,3,1,0,'2026-05-15 11:27:40','2026-06-17 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(8,NULL,'PROMO2026_8','Promo Dummy 8',NULL,'fixed',8000.00,36000.00,NULL,62,0,1,0,'2026-05-22 11:27:40','2026-07-03 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(9,NULL,'PROMO2026_9','Promo Dummy 9',NULL,'percentage',35.00,93000.00,39000.00,37,9,0,0,'2026-05-18 17:00:00','2026-06-25 17:00:00','2026-06-05 11:27:40','2026-06-07 09:47:41',NULL),(10,NULL,'PROMO2026_10','Promo Dummy 10',NULL,'fixed',14000.00,73000.00,NULL,87,6,1,0,'2026-05-19 11:27:40','2026-06-27 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(11,NULL,'PROMO2026_11','Promo Dummy 11',NULL,'percentage',30.00,88000.00,26000.00,32,7,1,0,'2026-05-14 11:27:40','2026-06-29 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(12,NULL,'PROMO2026_12','Promo Dummy 12',NULL,'fixed',19000.00,84000.00,NULL,21,6,0,0,'2026-05-28 17:00:00','2026-06-29 17:00:00','2026-06-05 11:27:40','2026-06-07 09:47:53',NULL),(13,NULL,'PROMO2026_13','Promo Dummy 13',NULL,'fixed',18000.00,79000.00,NULL,89,5,1,0,'2026-05-25 11:27:40','2026-06-17 11:27:40','2026-06-05 11:27:40','2026-06-05 11:27:40',NULL),(14,NULL,'PROMO2026_14','Promo Dummy 14',NULL,'percentage',47.00,76000.00,42000.00,73,10,1,0,'2026-05-14 11:27:40','2026-07-01 11:27:40','2026-06-05 11:27:40','2026-06-07 09:48:00','2026-06-07 09:48:00'),(15,NULL,'PROMO2026_15','Promo Dummy 15',NULL,'fixed',14000.00,89000.00,NULL,40,7,1,0,'2026-05-16 11:27:40','2026-06-21 11:27:40','2026-06-05 11:27:40','2026-06-05 23:42:56','2026-06-05 23:42:56'),(16,NULL,'GEPREK1','GEPREK1',NULL,'percentage',10.00,20000.00,NULL,1,1,1,0,'2026-06-06 17:00:00','2026-07-06 17:00:00','2026-06-06 22:08:12','2026-06-06 22:13:12',NULL),(17,NULL,'GEPREK2','GEPREK2',NULL,'fixed',3000.00,15000.00,NULL,1,0,1,0,'2026-06-06 17:00:00','2026-07-06 17:00:00','2026-06-06 22:08:48','2026-06-06 22:08:48',NULL),(18,NULL,'GEPREK3','GEPREK3',NULL,'fixed',1000.00,20000.00,NULL,5,0,1,0,'2026-06-06 17:00:00','2026-06-30 17:00:00','2026-06-07 09:48:36','2026-06-07 09:48:36',NULL),(19,NULL,'FREE-GEPREK-AZYVN','Gratis 1 Paket Nasi Ayam Geprek','Reward member penukaran 150 poin. Berlaku untuk 1 Paket Nasi Ayam Geprek gratis.','fixed',15000.00,0.00,22000.00,1,0,1,1,'2026-06-07 20:15:57','2026-06-14 20:15:57','2026-06-07 20:15:57','2026-06-07 20:15:57',NULL),(20,NULL,'FREE-GEPREK-VQJIU','Gratis 1 Paket Nasi Ayam Geprek','Reward member penukaran 150 poin. Berlaku untuk 1 Paket Nasi Ayam Geprek gratis.','fixed',15000.00,0.00,22000.00,1,0,1,1,'2026-06-08 00:37:28','2026-06-15 00:37:28','2026-06-08 00:37:28','2026-06-08 00:37:28',NULL);
/*!40000 ALTER TABLE `vouchers` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-08 20:58:25
