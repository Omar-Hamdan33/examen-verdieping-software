-- DATABASE AANMAKEN
DROP DATABASE IF EXISTS windkracht12;
CREATE DATABASE windkracht12;
USE windkracht12;

-- Gebruikers
CREATE TABLE gebruikers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voornaam VARCHAR(50),
    tussenvoegsel VARCHAR(10),
    achternaam VARCHAR(50),
    email VARCHAR(100) UNIQUE,
    telefoon VARCHAR(20),
    wachtwoord VARCHAR(255),
    rol ENUM('klant', 'instructeur', 'beheerder') NOT NULL DEFAULT 'klant',
    datum_aangemaakt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actief BOOLEAN DEFAULT TRUE,
    HASHEDId VARCHAR(255) NULL,
    adres VARCHAR(255),
    woonplaats VARCHAR(100),
    geboortedatum DATE,
    bsn VARCHAR(20)
) ENGINE=InnoDB;

-- Locaties
CREATE TABLE IF NOT EXISTS locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- Pakketten
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    duration_hours DECIMAL(4,1) NOT NULL,
    sessions_count INT NOT NULL,
    max_persons INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT
) ENGINE=InnoDB;

-- Reserveringen
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    package_id INT NOT NULL,
    location_id INT NOT NULL,
    aantal INT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Duo-gebruiker info
    duo_voornaam VARCHAR(50),
    duo_tussenvoegsel VARCHAR(10),
    duo_achternaam VARCHAR(50),
    duo_geboortedatum DATE,

    -- Status
    betaald BOOLEAN DEFAULT FALSE,
    definitief BOOLEAN DEFAULT FALSE,

    FOREIGN KEY (user_id) REFERENCES gebruikers(id),
    FOREIGN KEY (package_id) REFERENCES packages(id),
    FOREIGN KEY (location_id) REFERENCES locations(id)
) ENGINE=InnoDB;

-- Beschikbaarheid instructeurs
CREATE TABLE IF NOT EXISTS instructor_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id INT NOT NULL,
    reservationid INT,
    available_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    cancel_reason VARCHAR(255),
    status ENUM(
        'gepland',
        'voltooid',
        'geannuleerd',
        'annulering_in_afwachting',
        'beschikbaar',
        'herpland'
    ) NOT NULL DEFAULT 'beschikbaar',
    beschikbaar BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (instructor_id) REFERENCES gebruikers(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Login logs
CREATE TABLE IF NOT EXISTS login_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100),
    actie ENUM('login', 'logout') NOT NULL,
    logtijd DATETIME(6) DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB;

