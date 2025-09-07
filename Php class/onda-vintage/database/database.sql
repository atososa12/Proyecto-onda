CREATE DATABASE  IF NOT EXISTS `transportehistorico` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `transportehistorico`;
-- MySQL dump 10.13  Distrib 8.0.42, for Win64 (x86_64)
--
-- Host: localhost    Database: transportehistorico
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

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
-- Table structure for table `agencia`
--

DROP TABLE IF EXISTS `agencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `agencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(100) DEFAULT NULL,
  `link_foto_agencia` varchar(500) DEFAULT NULL,
  `trayecto_id` int(11) DEFAULT NULL,
  `km_en_ruta` decimal(8,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_agencia_trayecto` (`trayecto_id`),
  CONSTRAINT `fk_agencia_trayecto` FOREIGN KEY (`trayecto_id`) REFERENCES `trayecto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agencia`
--

LOCK TABLES `agencia` WRITE;
/*!40000 ALTER TABLE `agencia` DISABLE KEYS */;
INSERT INTO `agencia` VALUES (1,'Montevideo, Montevideo','-34.9053,-56.1886',NULL,1,0.00),(2,'Carmelo, Colonia','-34.0009,-58.2859',NULL,NULL,NULL),(3,'Colonia del Sacramento, Colonia','-34.46317170814427,-57.83222238941788',NULL,1,176.00),(4,'Rosario, Colonia','-34.3167,-57.3500',NULL,1,135.00),(5,'Colonia Suiza (Nueva Helvecia), Colonia','-34.2999,-57.2333',NULL,1,128.00),(6,'La Paz, Colonia','-34.3360,-57.3560',NULL,1,130.00),(7,'Manantiales, Colonia','-34.2400,-57.3500',NULL,NULL,NULL),(8,'Tarariras, Colonia','-34.2723,-57.6185',NULL,NULL,NULL),(9,'Rincón del Pino, San José','-34.50346376169817,-56.83353057405767',NULL,1,80.30),(10,'Juan Lacaze, Colonia','-34.36237086986502,-57.44694259404337',NULL,1,149.00),(16,'Melo, Cerro Largo','-32.364,-54.170',NULL,NULL,NULL),(17,'Río Branco, Cerro Largo','-32.598,-53.390',NULL,NULL,NULL),(18,'Tacuarembó, Tacuarembó','-31.733,-55.983',NULL,NULL,NULL),(19,'Paso de los Toros, Tacuarembó','-32.817,-56.511',NULL,NULL,NULL),(20,'Rivera, Rivera','-30.902,-55.551',NULL,NULL,NULL),(21,'Bella Unión, Artigas','-30.258289662839548,-57.59161774103027',NULL,NULL,NULL),(22,'Artigas, Artigas','-30.401,-56.471',NULL,NULL,NULL),(23,'Young, Río Negro','-32.703262937009065,-57.632733057182186',NULL,NULL,NULL),(24,'Fray Bentos, Río Negro','-33.116,-58.316',NULL,NULL,NULL),(25,'Mercedes, Soriano','-33.257,-58.030',NULL,NULL,NULL),(26,'Colonia Valdense, Colonia','-34.338,-57.234',NULL,1,124.00),(27,'Piriápolis, Maldonado','-34.865,-55.274',NULL,NULL,NULL),(28,'Est. Las Flores, Maldonado','-34.770,-55.462',NULL,NULL,NULL),(29,'Nueva Palmira, Colonia','-33.872,-58.413',NULL,NULL,NULL),(30,'Agraciada, Colonia','-33.792,-58.242',NULL,NULL,NULL),(31,'Paso de los Toros, Tacuarembó','-32.816,-56.510',NULL,NULL,NULL),(32,'Barra de Maldonado, Maldonado','-34.918,-54.933',NULL,NULL,NULL),(33,'Paysandú, Paysandú','-32.32278035193446,-58.04840044228739',NULL,NULL,NULL),(34,'Balneario Solís, Canelones','-34.784,-55.396',NULL,NULL,NULL),(35,'Vergara, Treinta y Tres','-32.929,-53.933',NULL,NULL,NULL),(36,'Cardona, Soriano','-33.870,-57.380',NULL,NULL,NULL),(37,'Drabble, Soriano','-33.950,-57.633',NULL,NULL,NULL),(38,'Mercedes, Soriano','-33.252,-58.030',NULL,NULL,NULL),(39,'Fray Bentos, Río Negro','-33.116,-58.317',NULL,NULL,NULL),(40,'Dolores, Soriano','-33.530,-58.216',NULL,NULL,NULL),(41,'Palmitas, Soriano','-33.615,-57.850',NULL,NULL,NULL),(42,'Ombúes de Lavalle, Colonia','-33.944,-57.809',NULL,NULL,NULL),(43,'Molles, Durazno','-33.05689450285426,-56.46582866792365',NULL,NULL,NULL),(44,'Nuevo Berlín, Río Negro','-32.983,-58.050',NULL,NULL,NULL),(45,'Velázquez, Rocha','-33.600,-54.200',NULL,NULL,NULL),(46,'Salto, Salto','-31.39788718727523,-57.91262029301729',NULL,NULL,NULL),(47,'San José, San José','-34.33464503616247,-56.73215644276751',NULL,NULL,NULL),(48,'Santa Catalina, Soriano','-33.666,-57.416',NULL,NULL,NULL),(49,'Mariscala, Lavalleja','-34.040,-54.770',NULL,NULL,NULL),(50,'Conchillas, Colonia','-34.084,-58.053',NULL,NULL,NULL),(51,'Pirarajá, Lavalleja','-33.901,-54.958',NULL,NULL,NULL),(52,'Libertad, San José','-34.633,-56.617',NULL,1,51.00),(53,'Peralta, Tacuarembó','-32.49349481184217,-56.35715146083845',NULL,NULL,NULL),(54,'Achar, Tacuarembó','-32.36090195298994,-56.26598132181075',NULL,NULL,NULL),(55,'Curtina, Tacuarembó','-32.14515286996296,-56.11078469802931',NULL,NULL,NULL),(56,'Quebracho, Paysandú','-31.931341816778435,-57.85788896544172',NULL,NULL,NULL),(57,'Melo, Cerro Largo','-32.364,-54.170',NULL,NULL,NULL),(58,'Tacuarembó, Tacuarembó','-31.733,-55.983',NULL,NULL,NULL),(59,'Tres Bocas, Soriano','-33.433,-57.683',NULL,NULL,NULL),(60,'Queguay, Paysandú','-32.10404301286532,-57.90096577830833',NULL,NULL,NULL),(61,'Carmen, Durazno','-33.250,-56.283',NULL,NULL,NULL),(62,'Young, Río Negro','-32.703262937009065,-57.632733057182186',NULL,NULL,NULL),(63,'Guichón, Paysandú','-32.358,-57.198',NULL,NULL,NULL),(64,'Artigas, Artigas','-30.401,-56.471',NULL,NULL,NULL),(65,'Algorta, Río Negro','-32.650,-57.283',NULL,NULL,NULL),(66,'Poblado Sauce, Lavalleja','-34.353,-55.515',NULL,NULL,NULL),(67,'La Paloma, Rocha','-34.663,-54.168',NULL,NULL,NULL),(68,'Bella Unión, Artigas','-30.258289662839548,-57.59161774103027',NULL,NULL,NULL),(69,'Rivera, Rivera','-30.902,-55.551',NULL,NULL,NULL),(70,'Minas de Corrales, Rivera','-31.573,-55.470',NULL,NULL,NULL),(71,'Pan de Azúcar, Maldonado','-34.778,-55.235',NULL,NULL,NULL),(72,'San Carlos, Maldonado','-34.791,-54.918',NULL,NULL,NULL),(73,'Maldonado, Maldonado','-34.908,-54.959',NULL,NULL,NULL),(74,'Punta del Este, Maldonado','-34.962,-54.951',NULL,NULL,NULL),(75,'Rocha, Rocha','-34.482,-54.333',NULL,NULL,NULL),(76,'18 de Julio, Rocha','-33.233,-54.383',NULL,NULL,NULL),(77,'Castillos, Rocha','-34.198,-53.858',NULL,NULL,NULL),(78,'Chuy, Rocha','-33.697,-53.455',NULL,NULL,NULL),(79,'La Coronilla, Rocha','-33.906,-53.454',NULL,NULL,NULL),(80,'Paso Antolín, Colonia','-34.033,-57.567',NULL,NULL,NULL),(81,'Tomás Gomensoro, Artigas','-30.30792713627464,-57.57655600806859',NULL,NULL,NULL),(82,'Colonia Palma, Artigas','-30.582757605918506,-57.67957919468959',NULL,NULL,NULL),(83,'Belén, Salto','-30.839923128964536,-57.697283532375565',NULL,NULL,NULL),(84,'Costa Azul, Rocha','-34.733,-55.650',NULL,NULL,NULL),(85,'Punta Ballena/Portezuelo, Maldonado','-34.911,-55.028',NULL,NULL,NULL),(86,'Constitución, Salto','-31.085979915124234,-57.72365870419442',NULL,NULL,NULL),(87,'Las Piedras, Canelones','-34.730,-56.220',NULL,NULL,NULL),(88,'La Pedrera, Rocha','-34.594,-54.168',NULL,NULL,NULL),(89,'San Gregorio, Tacuarembó','-32.633,-55.600',NULL,NULL,NULL),(90,'Colonia Miguelete, Colonia','-34.238,-57.532',NULL,NULL,NULL),(91,'19 de Abril, Rocha','-33.600,-54.167',NULL,NULL,NULL),(92,'Termas del Arapey, Salto','-30.900,-57.550',NULL,NULL,NULL),(93,'Canelones, Canelones','-34.522,-56.277',NULL,NULL,NULL),(94,'Río Branco, Cerro Largo','-32.600,-53.383',NULL,NULL,NULL),(95,'Aceguá, Cerro Largo','-31.872,-54.170',NULL,NULL,NULL),(96,'San Javier, Río Negro','-32.664,-58.133',NULL,NULL,NULL),(97,'Soca, Canelones','-34.683,-55.617',NULL,NULL,NULL),(98,'Pando, Canelones','-34.717,-55.958',NULL,NULL,NULL),(99,'La Cruz, Florida','-33.933597244171786,-56.2478732128376',NULL,NULL,NULL),(100,'Ecilda Paulier, San José','-34.36575601008714,-57.06102528382421',NULL,1,105.00),(101,'Durazno, Durazno','-33.379,-56.523',NULL,NULL,NULL),(102,'Sarandí Grande, Florida','-33.726079374018674,-56.32126230866647',NULL,NULL,NULL),(103,'Trinidad, Flores','-33.5151912845456,-56.90696246172489',NULL,NULL,NULL);
/*!40000 ALTER TABLE `agencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historia`
--

DROP TABLE IF EXISTS `historia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(200) NOT NULL,
  `fecha` date NOT NULL,
  `id_trayecto` int(11) DEFAULT NULL,
  `uri_historia` varchar(500) DEFAULT NULL,
  `uri_fotos` varchar(500) DEFAULT NULL,
  `id_agencia` int(11) DEFAULT NULL,
  `id_agencia_origen` int(11) DEFAULT NULL,
  `id_agencia_destino` int(11) DEFAULT NULL,
  `id_omnibus` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_trayecto` (`id_trayecto`),
  KEY `fk_hist_omnibus` (`id_omnibus`),
  KEY `fk_hist_origen` (`id_agencia_origen`),
  KEY `fk_hist_destino` (`id_agencia_destino`),
  KEY `fk_hist_agencia` (`id_agencia`),
  CONSTRAINT `fk_hist_agencia` FOREIGN KEY (`id_agencia`) REFERENCES `agencia` (`id`),
  CONSTRAINT `fk_hist_destino` FOREIGN KEY (`id_agencia_destino`) REFERENCES `agencia` (`id`),
  CONSTRAINT `fk_hist_omnibus` FOREIGN KEY (`id_omnibus`) REFERENCES `omnibus` (`id`),
  CONSTRAINT `fk_hist_origen` FOREIGN KEY (`id_agencia_origen`) REFERENCES `agencia` (`id`),
  CONSTRAINT `historia_ibfk_1` FOREIGN KEY (`id_trayecto`) REFERENCES `trayecto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historia`
--

LOCK TABLES `historia` WRITE;
/*!40000 ALTER TABLE `historia` DISABLE KEYS */;
/*!40000 ALTER TABLE `historia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `omnibus`
--

DROP TABLE IF EXISTS `omnibus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `omnibus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modelo` enum('Leyland','Scania','Mercedes','Marcopolo','Otros') NOT NULL,
  `anio` int(4) NOT NULL,
  `estado` enum('activo','vintage','baja') DEFAULT 'vintage',
  `link_foto_omnibus` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `omnibus`
--

LOCK TABLES `omnibus` WRITE;
/*!40000 ALTER TABLE `omnibus` DISABLE KEYS */;
INSERT INTO `omnibus` VALUES (1,'Mercedes',1978,'vintage','https://upload.wikimedia.org/wikipedia/commons/6/64/Mercedes-Benz_O305_in_London.jpg'),(2,'Scania',1985,'activo','https://upload.wikimedia.org/wikipedia/commons/0/0a/Scania_K-series_bus.jpg'),(3,'Leyland',1972,'baja','https://upload.wikimedia.org/wikipedia/commons/4/42/Leyland_National_in_Manchester.jpg');
/*!40000 ALTER TABLE `omnibus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trayecto`
--

DROP TABLE IF EXISTS `trayecto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trayecto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion_trayecto` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trayecto`
--

LOCK TABLES `trayecto` WRITE;
/*!40000 ALTER TABLE `trayecto` DISABLE KEYS */;
INSERT INTO `trayecto` VALUES (1,'RUTA 1','Montevideo → Colonia (histórico)'),(2,'RUTA 5',NULL),(3,'RUTA 3',NULL);
/*!40000 ALTER TABLE `trayecto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trayecto_agencia`
--

DROP TABLE IF EXISTS `trayecto_agencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `trayecto_agencia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trayecto_id` int(11) NOT NULL,
  `agencia_id` int(11) NOT NULL,
  `km_en_ruta` decimal(7,2) DEFAULT NULL,
  `rol` enum('origen','intermedia','destino') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_trayecto_agencia` (`trayecto_id`,`agencia_id`),
  KEY `agencia_id` (`agencia_id`),
  CONSTRAINT `trayecto_agencia_ibfk_1` FOREIGN KEY (`trayecto_id`) REFERENCES `trayecto` (`id`),
  CONSTRAINT `trayecto_agencia_ibfk_2` FOREIGN KEY (`agencia_id`) REFERENCES `agencia` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trayecto_agencia`
--

LOCK TABLES `trayecto_agencia` WRITE;
/*!40000 ALTER TABLE `trayecto_agencia` DISABLE KEYS */;
INSERT INTO `trayecto_agencia` VALUES (1,1,1,0.00,'origen'),(2,1,3,176.00,NULL),(3,1,4,135.00,NULL),(4,1,5,128.00,NULL),(5,1,6,130.00,NULL),(6,1,9,80.30,NULL),(7,1,10,149.00,NULL),(8,1,26,124.00,NULL),(9,1,52,51.00,NULL),(10,1,100,105.00,NULL),(17,2,1,0.00,'origen'),(18,2,18,388.00,'intermedia'),(19,2,19,263.00,'intermedia'),(20,2,20,500.00,'destino'),(21,2,31,263.00,'intermedia'),(22,2,43,220.00,'intermedia'),(23,2,53,289.00,'intermedia'),(24,2,54,317.00,'intermedia'),(25,2,55,336.00,'intermedia'),(26,2,58,388.00,'intermedia'),(27,2,69,500.00,'destino'),(30,2,93,38.00,'intermedia'),(31,2,99,105.00,'intermedia'),(32,2,101,195.00,NULL),(33,2,102,126.00,NULL),(34,3,1,0.00,NULL),(35,3,21,626.00,NULL),(36,3,23,310.00,NULL),(37,3,33,375.00,NULL),(38,3,46,488.00,NULL),(39,3,56,426.00,NULL),(40,3,60,402.00,NULL),(41,3,62,310.00,NULL),(43,3,68,626.00,NULL),(44,3,81,640.00,NULL),(45,3,82,587.00,NULL),(46,3,83,568.00,NULL),(47,3,86,542.00,NULL),(49,3,103,193.00,NULL),(50,3,47,95.00,NULL);
/*!40000 ALTER TABLE `trayecto_agencia` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-07 18:43:48
