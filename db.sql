-- First drop all tables if they exist (to avoid errors)
DROP TABLE IF EXISTS avis;
DROP TABLE IF EXISTS article;
DROP TABLE IF EXISTS achat;
DROP TABLE IF EXISTS billets;
DROP TABLE IF EXISTS oeuvres;
DROP TABLE IF EXISTS evenement;
DROP TABLE IF EXISTS visiteur;
DROP TABLE IF EXISTS employe;
DROP TABLE IF EXISTS utilisateur;

-- TABLE utilisateur
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,  
    type_utilisateur VARCHAR(20) NOT NULL,
    password VARCHAR(500) NOT NULL,
    role VARCHAR(30) NOT NULL
);

-- TABLE employe
CREATE TABLE employe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,   
    password VARCHAR(500) NOT NULL,
    role VARCHAR(100) NOT NULL,
    date_embauche TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE visiteur
CREATE TABLE visiteur (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    email VARCHAR(255) NOT NULL,
    password VARCHAR(500) NOT NULL,
    tel VARCHAR(20),
    age INT NOT NULL,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    type_visiteur VARCHAR(30)
);

-- TABLE evenement
CREATE TABLE evenement (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    titre VARCHAR(100) NOT NULL,
    description VARCHAR(1000),
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    lieu VARCHAR(100) NOT NULL,
    capacite INT NOT NULL,
    image_evenement VARCHAR(255),
    id_employe INT,
    FOREIGN KEY (id_employe) REFERENCES employe(id)
);

-- TABLE billets
CREATE TABLE billets (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    tarif DECIMAL(10,2) NOT NULL,
    reduction DECIMAL(5,2) DEFAULT 0,
    type_billet VARCHAR(100) NOT NULL,
    id_evenement INT,
    id_visiteur INT,
    FOREIGN KEY (id_evenement) REFERENCES evenement(id),
    FOREIGN KEY (id_visiteur) REFERENCES visiteur(id)
);

-- TABLE achat
CREATE TABLE achat (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    id_visiteur INT,
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_visiteur) REFERENCES visiteur(id)
);

-- TABLE article
CREATE TABLE article (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    nom VARCHAR(100) NOT NULL,
    description VARCHAR(1000),
    prix DECIMAL(10,2) NOT NULL,
    quantite INT NOT NULL DEFAULT 0,
    id_achat INT,
    FOREIGN KEY (id_achat) REFERENCES achat(id)
);

-- TABLE oeuvres
CREATE TABLE oeuvres (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    titre VARCHAR(100) NOT NULL,
    artiste VARCHAR(100) NOT NULL,
    date_creation DATE,
    type_oeuvre VARCHAR(100) NOT NULL,
    materiaux VARCHAR(100),
    informations TEXT,
    image_oeuvre VARCHAR(255),
    id_employe INT,
    FOREIGN KEY (id_employe) REFERENCES employe(id)
);
-- TABLE avis
CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    id_visiteur INT NOT NULL,
    id_oeuvres INT,
    id_evenement INT,
    id_article INT,
    notes FLOAT CHECK (notes BETWEEN 0 AND 5),
    commentaire VARCHAR(1000),
    date_avis TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_visiteur) REFERENCES visiteur(id),
    FOREIGN KEY (id_evenement) REFERENCES evenement(id),
    FOREIGN KEY (id_article) REFERENCES article(id),
    FOREIGN KEY (id_oeuvres) REFERENCES oeuvres(id),
    CONSTRAINT chk_reference CHECK (
        (id_oeuvres IS NOT NULL) OR 
        (id_evenement IS NOT NULL) OR 
        (id_article IS NOT NULL)
    )
);

INSERT INTO employe (username, password, role) VALUES ('taha', '123123', 'oeuvres');
INSERT INTO employe (username, password, role) VALUES ('charaf', '123123', 'admin');
INSERT INTO employe (username, password, role) VALUES ('hajar', '123123', 'evenements');
INSERT INTO employe (username, password, role) VALUES ('ouissal', '123123', 'visiteurs');




