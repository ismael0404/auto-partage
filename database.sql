-- =====================================================
-- AutoPartage - Base de données complète
-- Application d'autopartage
-- =====================================================

CREATE DATABASE IF NOT EXISTS autopartage CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE autopartage;

-- =====================================================
-- Table des utilisateurs
-- =====================================================
DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS vehicules;
DROP TABLE IF EXISTS utilisateurs;

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    adresse TEXT DEFAULT NULL,
    role ENUM('client', 'admin') NOT NULL DEFAULT 'client',
    photo VARCHAR(255) DEFAULT NULL,
    statut ENUM('actif', 'inactif', 'suspendu') NOT NULL DEFAULT 'actif',
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table des véhicules
-- =====================================================
CREATE TABLE vehicules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee INT DEFAULT NULL,
    type_carburant ENUM('Essence', 'Diesel', 'Électrique', 'Hybride') NOT NULL DEFAULT 'Essence',
    transmission ENUM('Manuelle', 'Automatique') NOT NULL DEFAULT 'Manuelle',
    nombre_places INT NOT NULL DEFAULT 5,
    image VARCHAR(255) DEFAULT NULL,
    prix_heure DECIMAL(10,2) DEFAULT NULL,
    prix_jour DECIMAL(10,2) NOT NULL,
    description TEXT DEFAULT NULL,
    caracteristiques TEXT DEFAULT NULL,
    statut ENUM('disponible', 'reserve', 'maintenance') NOT NULL DEFAULT 'disponible',
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table des réservations
-- =====================================================
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    vehicule_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    duree_jours INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    prix_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'annulee', 'terminee') NOT NULL DEFAULT 'en_attente',
    notes TEXT DEFAULT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicule_id) REFERENCES vehicules(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table des messages / notifications
-- =====================================================
CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    titre VARCHAR(255) NOT NULL,
    contenu TEXT NOT NULL,
    lu TINYINT(1) NOT NULL DEFAULT 0,
    type ENUM('info', 'success', 'warning', 'error') NOT NULL DEFAULT 'info',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Données de démonstration
-- =====================================================

-- Admin par défaut (mot de passe: admin123)
INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, role, statut) VALUES
('Admin', 'System', 'admin@autopartage.com', '$2y$10$jh8e0XV4ZwQZbS6YpbWEh.BFEos6U7E88ifr5ATBgYdLUFrHVcnna', 'admin', 'actif');

-- Clients de démonstration (mot de passe: password)
INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, telephone, role, statut) VALUES
('Jean', 'Dupont', 'jean.dupont@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 07 00 00 01', 'client', 'actif'),
('Marie', 'Martin', 'marie.martin@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 07 00 00 02', 'client', 'actif'),
('Pierre', 'Durand', 'pierre.durand@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 07 00 00 03', 'client', 'actif'),
('Sophie', 'Leroy', 'sophie.leroy@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+225 07 00 00 04', 'client', 'actif');

-- Véhicules de démonstration
INSERT INTO vehicules (marque, modele, annee, type_carburant, transmission, nombre_places, image, prix_heure, prix_jour, description, caracteristiques, statut) VALUES
('Tesla', 'Model 3', 2023, 'Électrique', 'Automatique', 5, 'Tesla Model 3.jfif', 4500, 25000, 'La Tesla Model 3 est une berline électrique haute performance offrant une autonomie exceptionnelle et une technologie de pointe. Son intérieur minimaliste et son écran tactile central en font une voiture du futur.', 'Autonomie 580km, Écran tactile 15 pouces, Autopilot, Coffre 425L, Supercharge compatible', 'disponible'),
('BMW', 'X5', 2022, 'Diesel', 'Automatique', 5, 'CARS _ BMW X5.jfif', 5500, 35000, 'Le BMW X5 est un SUV premium alliant luxe, confort et performances. Avec son moteur diesel puissant et son intérieur cuir premium, il offre une expérience de conduite inégalée.', 'Intérieur cuir premium, Système de navigation, Caméra de recul, Bluetooth / USB, Toit panoramique', 'disponible'),
('Peugeot', '3008', 2023, 'Essence', 'Automatique', 5, 'image voiture.jfif', 3500, 20000, 'Le Peugeot 3008 est un SUV compact au design audacieux. Son i-Cockpit innovant et ses équipements technologiques en font un choix idéal pour la ville comme pour les longs trajets.', 'i-Cockpit, Aide au stationnement, Régulateur adaptatif, Écran tactile 10 pouces, GPS intégré', 'disponible'),
('Renault', 'Clio', 2023, 'Essence', 'Manuelle', 5, 'Renault Clio.jfif', 2200, 15000, 'La Renault Clio est la citadine par excellence. Compacte, économique et agréable à conduire, elle est parfaite pour les déplacements urbains quotidiens.', 'Écran multimédia 9.3 pouces, Climatisation auto, Aide au parking, Régulateur de vitesse, Apple CarPlay', 'disponible'),
('Audi', 'A4', 2022, 'Diesel', 'Automatique', 5, '2020 BMW M850 Gran Coupe.jfif', 4800, 30000, 'L''Audi A4 incarne l''élégance allemande. Avec ses finitions haut de gamme et sa technologie Quattro disponible, elle offre une conduite raffinée et sportive.', 'Virtual Cockpit, MMI Navigation, Sièges chauffants, Bang & Olufsen, Matrix LED', 'disponible'),
('Mercedes', 'C300', 2023, 'Essence', 'Automatique', 5, 'image voiture.jfif', 5000, 32000, 'La Mercedes C300 est une berline de luxe qui allie performances et confort. Son intérieur sophistiqué et ses technologies MBUX en font une référence dans sa catégorie.', 'MBUX, Burmester Audio, Conduite semi-autonome, Ambient lighting, Sièges ventilés', 'disponible'),
('Toyota', 'Corolla', 2023, 'Hybride', 'Automatique', 5, 'image voiture.jfif', 3000, 18000, 'La Toyota Corolla hybride combine efficacité énergétique et fiabilité légendaire. Son système hybride auto-rechargeable offre une consommation remarquablement basse.', 'Hybride auto-rechargeable, Toyota Safety Sense, Écran 8 pouces, Caméra 360°, Régulateur adaptatif', 'disponible'),
('Volkswagen', 'Golf', 2022, 'Essence', 'Manuelle', 5, 'image voiture.jfif', 3200, 19000, 'La Volkswagen Golf est une compacte iconique reconnue pour sa qualité de fabrication et son agrément de conduite. Un choix sûr et polyvalent.', 'Digital Cockpit, App-Connect, ACC, Lane Assist, Kessy', 'disponible');

-- Réservations de démonstration
INSERT INTO reservations (utilisateur_id, vehicule_id, date_debut, date_fin, duree_jours, prix_unitaire, prix_total, statut) VALUES
(2, 2, '2024-09-25 10:00:00', '2024-09-27 10:00:00', 2, 35000, 70000, 'confirmee'),
(3, 1, '2024-10-01 08:00:00', '2024-10-03 08:00:00', 2, 25000, 50000, 'terminee'),
(4, 3, '2024-10-05 09:00:00', '2024-10-08 09:00:00', 3, 20000, 60000, 'en_attente'),
(5, 5, '2024-10-04 10:00:00', '2024-10-07 10:00:00', 3, 30000, 90000, 'annulee'),
(2, 1, '2024-10-10 08:00:00', '2024-10-12 08:00:00', 2, 25000, 50000, 'en_attente');

-- Messages de démonstration
INSERT INTO messages (utilisateur_id, titre, contenu, type) VALUES
(2, 'Bienvenue sur AutoPartage', 'Bienvenue Jean ! Votre compte a été créé avec succès. Découvrez nos véhicules disponibles.', 'success'),
(2, 'Réservation confirmée', 'Votre réservation du BMW X5 du 25/09 au 27/09 a été confirmée.', 'success'),
(3, 'Bienvenue sur AutoPartage', 'Bienvenue Marie ! Votre compte a été créé avec succès.', 'success');
