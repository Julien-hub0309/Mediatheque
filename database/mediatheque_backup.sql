
DROP TABLE IF EXISTS `ADHERENT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ADHERENT` (
  `id_adherent` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(80) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  `email` varchar(150) NOT NULL,
  `date_inscription` date NOT NULL,
  PRIMARY KEY (`id_adherent`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ADHERENT`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `ADHERENT` WRITE;
/*!40000 ALTER TABLE `ADHERENT` DISABLE KEYS */;
/*!40000 ALTER TABLE `ADHERENT` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `AUTEUR`
--

DROP TABLE IF EXISTS `AUTEUR`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `AUTEUR` (
  `id_auteur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(80) NOT NULL,
  `prenom` varchar(80) NOT NULL,
  PRIMARY KEY (`id_auteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `AUTEUR`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `AUTEUR` WRITE;
/*!40000 ALTER TABLE `AUTEUR` DISABLE KEYS */;
/*!40000 ALTER TABLE `AUTEUR` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `CATEGORIE`
--

DROP TABLE IF EXISTS `CATEGORIE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `CATEGORIE` (
  `id_categorie` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(80) NOT NULL,
  PRIMARY KEY (`id_categorie`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `CATEGORIE`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `CATEGORIE` WRITE;
/*!40000 ALTER TABLE `CATEGORIE` DISABLE KEYS */;
/*!40000 ALTER TABLE `CATEGORIE` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `EMPRUNT`
--

DROP TABLE IF EXISTS `EMPRUNT`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `EMPRUNT` (
  `id_emprunt` int(11) NOT NULL AUTO_INCREMENT,
  `id_adherent` int(11) NOT NULL,
  `id_livre` int(11) NOT NULL,
  `date_emprunt` date NOT NULL,
  `date_retour_prevue` date NOT NULL,
  `date_retour` date DEFAULT NULL,
  PRIMARY KEY (`id_emprunt`),
  KEY `fk_emprunt_adherent` (`id_adherent`),
  KEY `fk_emprunt_livre` (`id_livre`),
  CONSTRAINT `fk_emprunt_adherent` FOREIGN KEY (`id_adherent`) REFERENCES `ADHERENT` (`id_adherent`),
  CONSTRAINT `fk_emprunt_livre` FOREIGN KEY (`id_livre`) REFERENCES `LIVRE` (`id_livre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `EMPRUNT`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `EMPRUNT` WRITE;
/*!40000 ALTER TABLE `EMPRUNT` DISABLE KEYS */;
/*!40000 ALTER TABLE `EMPRUNT` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `LIVRE`
--

DROP TABLE IF EXISTS `LIVRE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `LIVRE` (
  `id_livre` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `isbn` varchar(20) NOT NULL,
  `annee_publication` int(11) NOT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT 1,
  `id_categorie` int(11) NOT NULL,
  PRIMARY KEY (`id_livre`),
  UNIQUE KEY `isbn` (`isbn`),
  KEY `fk_livre_categorie` (`id_categorie`),
  CONSTRAINT `fk_livre_categorie` FOREIGN KEY (`id_categorie`) REFERENCES `CATEGORIE` (`id_categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LIVRE`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `LIVRE` WRITE;
/*!40000 ALTER TABLE `LIVRE` DISABLE KEYS */;
/*!40000 ALTER TABLE `LIVRE` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

--
-- Table structure for table `LIVRE_AUTEUR`
--

DROP TABLE IF EXISTS `LIVRE_AUTEUR`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `LIVRE_AUTEUR` (
  `id_livre` int(11) NOT NULL,
  `id_auteur` int(11) NOT NULL,
  PRIMARY KEY (`id_livre`,`id_auteur`),
  KEY `fk_la_auteur` (`id_auteur`),
  CONSTRAINT `fk_la_auteur` FOREIGN KEY (`id_auteur`) REFERENCES `AUTEUR` (`id_auteur`) ON DELETE CASCADE,
  CONSTRAINT `fk_la_livre` FOREIGN KEY (`id_livre`) REFERENCES `LIVRE` (`id_livre`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LIVRE_AUTEUR`
--

SET @OLD_AUTOCOMMIT=@@AUTOCOMMIT, @@AUTOCOMMIT=0;
LOCK TABLES `LIVRE_AUTEUR` WRITE;
/*!40000 ALTER TABLE `LIVRE_AUTEUR` DISABLE KEYS */;
/*!40000 ALTER TABLE `LIVRE_AUTEUR` ENABLE KEYS */;
UNLOCK TABLES;
COMMIT;
SET AUTOCOMMIT=@OLD_AUTOCOMMIT;

