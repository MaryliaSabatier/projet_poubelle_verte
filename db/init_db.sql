-- Création de la base de données
CREATE DATABASE IF NOT EXISTS poubelle_verte;
USE poubelle_verte;

-- Table des rôles (avec le rôle administrateur et gestionnaire de réseau)
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE
);
INSERT INTO roles (nom) VALUES ('administrateur'), ('RH'), ('cycliste'), ('gestionnaire de réseau');

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100),
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role_id INT NOT NULL,
    date_embauche DATE,
    disponibilite VARCHAR(255),
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Table des vélos
CREATE TABLE velos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero VARCHAR(50) UNIQUE NOT NULL,
    etat VARCHAR(50) NOT NULL DEFAULT 'operationnel',
    autonomie_km INT NOT NULL DEFAULT 50,
    date_derniere_revision DATE
);

-- Table des rues
CREATE TABLE rues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL,
    longueur_m INT,
    sens_circulation ENUM('aller', 'retour', 'double_sens')
);

-- Table des arrêts
CREATE TABLE arrets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rue_id INT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    type_dechets VARCHAR(50),
    FOREIGN KEY (rue_id) REFERENCES rues(id)
);

-- Table des tournées
CREATE TABLE tournees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    cycliste_id INT,
    velo_id INT,
    heure_debut TIME,
    heure_fin TIME,
    etat ENUM('planifiee', 'en_cours', 'terminee', 'interrompue') NOT NULL DEFAULT 'planifiee', 
    FOREIGN KEY (cycliste_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (velo_id) REFERENCES velos(id)
);

-- Table des points de passage
CREATE TABLE points_passage (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournee_id INT NOT NULL,
    arret_id INT NOT NULL,
    ordre_passage INT NOT NULL,
    heure_passage TIME,
    FOREIGN KEY (tournee_id) REFERENCES tournees(id),
    FOREIGN KEY (arret_id) REFERENCES arrets(id)
);

-- Table des incidents
CREATE TABLE incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tournee_id INT,
    type_incident VARCHAR(100) NOT NULL,
    date DATE NOT NULL,
    heure TIME NOT NULL,
    description TEXT,
    FOREIGN KEY (tournee_id) REFERENCES tournees(id)
);

-- Table des affectations (vélos - cyclistes)
CREATE TABLE affectations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cycliste_id INT NOT NULL,
    velo_id INT NOT NULL,
    date_affectation DATE NOT NULL,
    FOREIGN KEY (cycliste_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (velo_id) REFERENCES velos(id)
);

-- Table etat_rues
CREATE TABLE etat_rues (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rue_id INT NOT NULL,
    tournee_id INT NOT NULL,
    date_traitement DATE,
    heure_traitement TIME,
    FOREIGN KEY (rue_id) REFERENCES rues(id),
    FOREIGN KEY (tournee_id) REFERENCES tournees(id)
);

-- Table etat_arrets
CREATE TABLE etat_arrets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arret_id INT NOT NULL,
    tournee_id INT NOT NULL,
    date_traitement DATE,
    heure_traitement TIME,
    FOREIGN KEY (arret_id) REFERENCES arrets(id),
    FOREIGN KEY (tournee_id) REFERENCES tournees(id)
);

-- Creation admin
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role_id)
VALUES ('AdminPoubelleVerte', 'Admin', 'admin@poubelleverte.com', PASSWORD('123456789'), 1); 
