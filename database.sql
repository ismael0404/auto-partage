CREATE DATABASE IF NOT EXISTS auto_partage DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE auto_partage;

-- Table users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(100) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('client', 'admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table vehicles
CREATE TABLE IF NOT EXISTS vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marque VARCHAR(100) NOT NULL,
    modele VARCHAR(100) NOT NULL,
    annee YEAR NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    prix_heure DECIMAL(10,2) NOT NULL,
    prix_jour DECIMAL(10,2) NOT NULL,
    carburant ENUM('Essence', 'Diesel', 'Electrique', 'Hybride') NOT NULL,
    transmission ENUM('Manuelle', 'Automatique') NOT NULL,
    places INT NOT NULL,
    climatisation BOOLEAN DEFAULT TRUE,
    description TEXT,
    statut ENUM('disponible', 'reserve', 'maintenance') DEFAULT 'disponible',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table reservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    vehicle_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    prix_total DECIMAL(10,2) NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'en_cours', 'terminee', 'annulee') DEFAULT 'en_attente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin User (password: admin123)
INSERT INTO users (prenom, nom, email, telephone, password, role) 
VALUES ('Admin', 'System', 'admin@autopartage.com', '0123456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE email='admin@autopartage.com';
