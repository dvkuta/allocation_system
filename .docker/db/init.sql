-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: allocation_system
-- ------------------------------------------------------
-- Server version	5.5.5-10.10.2-MariaDB-1:10.10.2+maria~ubu2204

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
-- Table structure for table `project`
--

DROP TABLE IF EXISTS `project`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project` (
                           `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                           `name` varchar(100) NOT NULL,
                           `user_id` int(11) unsigned NOT NULL COMMENT 'Project manager id only',
                           `from` date NOT NULL,
                           `to` date DEFAULT NULL,
                           `description` text DEFAULT NULL,
                           PRIMARY KEY (`id`),
                           KEY `project_user_fk` (`user_id`),
                           CONSTRAINT `project_user_fk` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project`
--

LOCK TABLES `project` WRITE;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` VALUES (2,'Projekt 1',61,'2023-03-01','2023-03-31','aaa'),(3,'Nový projekt s manažerem',61,'2023-03-01',NULL,''),(4,'Fiktivní testovací zadání',61,'2023-03-07','2023-03-31','aa'),(5,'Nyní',61,'2023-01-29','2023-02-25',''),(6,'Nyní 2',61,'2023-01-30',NULL,'a'),(7,'Testovací projekt 3',61,'2022-01-01','2022-01-02',''),(8,'Testovací projekt 5',61,'2023-03-01',NULL,''),(9,'test',62,'2023-03-01','2023-03-05','aaa');
/*!40000 ALTER TABLE `project` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_user`
--

DROP TABLE IF EXISTS `project_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_user` (
                                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                `user_id` int(11) unsigned NOT NULL,
                                `project_id` int(11) unsigned NOT NULL,
                                PRIMARY KEY (`id`),
                                KEY `project_user_user_FK` (`user_id`),
                                KEY `project_user_project_FK` (`project_id`),
                                CONSTRAINT `project_user_project_FK` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
                                CONSTRAINT `project_user_user_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_user`
--

LOCK TABLES `project_user` WRITE;
/*!40000 ALTER TABLE `project_user` DISABLE KEYS */;
INSERT INTO `project_user` VALUES (8,58,2),(9,59,2),(10,58,3),(11,61,3),(14,63,4),(15,63,5),(16,58,5),(17,58,6),(18,58,4),(19,63,2),(20,63,9);
/*!40000 ALTER TABLE `project_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `project_user_allocation`
--

DROP TABLE IF EXISTS `project_user_allocation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `project_user_allocation` (
                                           `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                           `project_user_id` int(11) unsigned NOT NULL,
                                           `allocation` tinyint(4) unsigned NOT NULL,
                                           `from` date NOT NULL,
                                           `to` date NOT NULL,
                                           `description` text DEFAULT NULL,
                                           `state` enum('active','draft','cancel') NOT NULL DEFAULT 'draft',
                                           PRIMARY KEY (`id`),
                                           KEY `project_user_assignment_PROJECT_USER_FK` (`project_user_id`),
                                           CONSTRAINT `project_user_assignment_PROJECT_USER_FK` FOREIGN KEY (`project_user_id`) REFERENCES `project_user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `project_user_allocation`
--

LOCK TABLES `project_user_allocation` WRITE;
/*!40000 ALTER TABLE `project_user_allocation` DISABLE KEYS */;
INSERT INTO `project_user_allocation` VALUES (10,8,1,'2023-03-01','2023-03-01','','active'),(11,8,5,'2023-03-06','2023-03-08','','active'),(12,10,10,'2023-03-01','2023-03-05','','active'),(17,8,0,'2023-03-01','2023-03-05','','active'),(18,8,5,'2023-03-07','2023-03-12','','active'),(20,8,40,'2023-03-01','2023-03-05','lll','cancel'),(21,8,1,'2023-03-09','2023-03-16','','active'),(22,9,5,'2023-03-01','2023-03-08','','active'),(23,14,25,'2023-03-07','2023-03-31','','active'),(24,14,0,'2023-03-07','2023-03-31','','active'),(25,16,15,'2023-01-29','2023-02-05','','active'),(26,15,7,'2023-01-29','2023-02-05','','active'),(27,16,7,'2023-01-29','2023-02-05','','active'),(28,17,18,'2023-01-30','2023-02-05','','active'),(29,20,5,'2023-03-01','2023-03-05','','active');
/*!40000 ALTER TABLE `project_user_allocation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
                        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                        `type` varchar(100) NOT NULL,
                        PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'worker'),(2,'superior'),(3,'project_manager'),(4,'department_manager'),(5,'secretariat');
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `superior_user`
--

DROP TABLE IF EXISTS `superior_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `superior_user` (
                                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                 `superior_id` int(11) unsigned NOT NULL COMMENT 'tabulka user',
                                 `worker_id` int(11) unsigned NOT NULL COMMENT 'tabulka user',
                                 PRIMARY KEY (`id`),
                                 KEY `superior_user_user_superior_fk` (`superior_id`),
                                 KEY `superior_user_user_worker_fk` (`worker_id`),
                                 CONSTRAINT `superior_user_user_superior_fk` FOREIGN KEY (`superior_id`) REFERENCES `user` (`id`),
                                 CONSTRAINT `superior_user_user_worker_fk` FOREIGN KEY (`worker_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `superior_user`
--

LOCK TABLES `superior_user` WRITE;
/*!40000 ALTER TABLE `superior_user` DISABLE KEYS */;
INSERT INTO `superior_user` VALUES (2,61,58),(3,61,63),(4,62,63);
/*!40000 ALTER TABLE `superior_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
                        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                        `firstname` varchar(255) NOT NULL,
                        `lastname` varchar(255) NOT NULL,
                        `email` varchar(200) NOT NULL,
                        `login` varchar(100) NOT NULL,
                        `password` varchar(60) NOT NULL,
                        `workplace` varchar(255) DEFAULT NULL,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `user_email_unique` (`email`),
                        UNIQUE KEY `user_login_unique` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (58,'Jarda','Blatníček','aa@ss.cz','jarda','$2y$10$5ktuZauzHAFFNOLX2VeYgeMNjrmjmbNgi31c8UuipS01302WnmqdG','asd'),(59,'Petr','Manaž','aaa@ss.cz','dep','$2y$10$DbTIhaEoK9EyerD4eabMqeaxRDgseYT9Ysmrcu2Od71xwIDykATcm','asd'),(61,'Pan','ManažerProjektu','pas.ds@sa.cz','pman1','$2y$10$K0YOgJ0H5lgDw46UzRQVPuci1OujNC/1sh94ISbDT86eg8odAGSRK','PROJ'),(62,'Pan1','ManažerProjektu1','pas.ds@saa.cz','pman2','$2y$10$/B6SUdXPW52jS7hlOsrVo.sxOA68h5b1ZFTpynZuvsSQx5nJJhBy.','PROJ'),(63,'David','Kůta','dkuta@students.zcu.cz','kuta','$2y$10$TTbetGdNQ1TATFQp4DjA3uBi.H2RIqHvSk3AT7AfrhkudlE76QVii','PES');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_role`
--

DROP TABLE IF EXISTS `user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_role` (
                             `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                             `user_id` int(11) unsigned NOT NULL,
                             `role_id` int(11) unsigned NOT NULL,
                             PRIMARY KEY (`id`),
                             KEY `user_role_USER_FK` (`user_id`),
                             KEY `user_role_ROLE_FK` (`role_id`),
                             CONSTRAINT `user_role_ROLE_FK` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`),
                             CONSTRAINT `user_role_USER_FK` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='role uživatelů';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_role`
--

LOCK TABLES `user_role` WRITE;
/*!40000 ALTER TABLE `user_role` DISABLE KEYS */;
INSERT INTO `user_role` VALUES (82,58,1),(87,63,1),(88,63,5),(92,61,2),(93,61,3),(94,59,4),(95,62,1),(96,62,3);
/*!40000 ALTER TABLE `user_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'allocation_system'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2023-01-31 18:07:40
