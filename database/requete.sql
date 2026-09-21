CREATE DATABASE IF NOT EXISTS mediatheque CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mediatheque;

CREATE USER IF NOT EXISTS 'webmaster'@'localhost' IDENTIFIED BY 'Admin123';
GRANT ALL PRIVILEGES ON mediatheque.* TO 'webmaster'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE IF NOT EXISTS CATEGORIE (
    id_categorie INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(80) UNIQUE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS LIVRE (
    id_livre INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    isbn VARCHAR(20) UNIQUE NOT NULL,
    annee_publication INT NOT NULL,
    disponible BOOLEAN NOT NULL DEFAULT 1,
    id_categorie INT NOT NULL,
    CONSTRAINT fk_livre_categorie FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS AUTEUR (
    id_auteur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(80) NOT NULL,
    prenom VARCHAR(80) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS LIVRE_AUTEUR (
    id_livre INT,
    id_auteur INT,
    PRIMARY KEY (id_livre, id_auteur),
    CONSTRAINT fk_la_livre FOREIGN KEY (id_livre) REFERENCES LIVRE(id_livre) ON DELETE CASCADE,
    CONSTRAINT fk_la_auteur FOREIGN KEY (id_auteur) REFERENCES AUTEUR(id_auteur) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ADHERENT (
    id_adherent INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(80) NOT NULL,
    prenom VARCHAR(80) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    date_inscription DATE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS EMPRUNT (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    id_adherent INT NOT NULL,
    id_livre INT NOT NULL,
    date_emprunt DATE NOT NULL,
    date_retour_prevue DATE NOT NULL,
    date_retour DATE DEFAULT NULL,
    CONSTRAINT fk_emprunt_adherent FOREIGN KEY (id_adherent) REFERENCES ADHERENT(id_adherent),
    CONSTRAINT fk_emprunt_livre FOREIGN KEY (id_livre) REFERENCES LIVRE(id_livre)
) ENGINE=InnoDB;
