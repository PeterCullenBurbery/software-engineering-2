CREATE DATABASE  IF NOT EXISTS `patient_portal_system` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `patient_portal_system`;
-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: patient_portal_system
-- ------------------------------------------------------
-- Server version	8.0.34

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
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `booking` (
  `idbooking` binary(16) NOT NULL,
  `doctor` binary(16) NOT NULL,
  `patient` binary(16) NOT NULL,
  `startdatetime` datetime(6) NOT NULL COMMENT 'This is when the booking is scheduled to start.',
  `enddatetime` datetime(6) NOT NULL COMMENT 'This is when the meeting will end.',
  `comments` longtext COMMENT 'This is for the reason for the booking.',
  PRIMARY KEY (`idbooking`,`doctor`,`patient`),
  KEY `fk_booking_doctor_idx` (`doctor`),
  KEY `fk_booking_patient1_idx` (`patient`),
  CONSTRAINT `fk_booking_doctor` FOREIGN KEY (`doctor`) REFERENCES `doctor` (`iddoctor`),
  CONSTRAINT `fk_booking_patient1` FOREIGN KEY (`patient`) REFERENCES `patient` (`idpatient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking`
--

LOCK TABLES `booking` WRITE;
/*!40000 ALTER TABLE `booking` DISABLE KEYS */;
INSERT INTO `booking` VALUES (_binary '\�!��\�\�\�;\0	�\0',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',_binary '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0','2024-02-12 20:00:00.000000','2024-02-12 21:00:00.000000','This is a routine checkup.');
/*!40000 ALTER TABLE `booking` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctor` (
  `iddoctor` binary(16) NOT NULL COMMENT 'This is a UUID.',
  `first_name` varchar(45) NOT NULL,
  `middle_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) NOT NULL,
  `email_address` varchar(45) NOT NULL,
  `phone` varchar(45) NOT NULL,
  `birthdatetime` datetime NOT NULL,
  `zipcode` varchar(45) NOT NULL,
  PRIMARY KEY (`iddoctor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctor`
--

LOCK TABLES `doctor` WRITE;
/*!40000 ALTER TABLE `doctor` DISABLE KEYS */;
INSERT INTO `doctor` VALUES (_binary '��\�5\�\�\�;\0	�\0','John','James','Smith','john.james.smith@gmail.com','12718281828','2003-02-18 00:00:00','25701');
/*!40000 ALTER TABLE `doctor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `patient`
--

DROP TABLE IF EXISTS `patient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `patient` (
  `idpatient` binary(16) NOT NULL COMMENT 'This is a UUID.',
  `first_name` varchar(45) NOT NULL,
  `middle_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) NOT NULL,
  `email_address` varchar(45) NOT NULL COMMENT 'We will make the assumption that a person has only one email address.',
  `phone` varchar(45) NOT NULL COMMENT 'We will assume one phone number per person.',
  `birthdatetime` datetime NOT NULL,
  `zipcode` varchar(45) NOT NULL,
  PRIMARY KEY (`idpatient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='This is for user, patient, customer, etc.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `patient`
--

LOCK TABLES `patient` WRITE;
/*!40000 ALTER TABLE `patient` DISABLE KEYS */;
INSERT INTO `patient` VALUES (_binary '=�L\�\�\�;\0	�\0','Peter','Cullen','Burbery','peter.cullen.burbery@gmail.com','13043607492','2002-02-19 04:45:45','25701');
/*!40000 ALTER TABLE `patient` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-02-13 17:51:44