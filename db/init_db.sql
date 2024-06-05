CREATE DATABASE poubelle_verte;

USE poubelle_verte;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('cycliste', 'gestionnaire', 'RH', 'administrateur') NOT NULL
);
