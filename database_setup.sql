CREATE DATABASE pharmacy_portal_db;
USE pharmacy_portal_db;

-- Users Table
CREATE TABLE Users (
    userId INT NOT NULL UNIQUE AUTO_INCREMENT,
    userName VARCHAR(45) NOT NULL UNIQUE,
    contactInfo VARCHAR(200),
    userType ENUM('pharmacist', 'patient') NOT NULL,
    password VARCHAR(255) NOT NULL,
    PRIMARY KEY (userId)
);

-- Medications Table
CREATE TABLE Medications (
    medicationId INT NOT NULL UNIQUE AUTO_INCREMENT,
    medicationName VARCHAR(45) NOT NULL,
    dosage VARCHAR(45) NOT NULL,
    manufacturer VARCHAR(100),
    PRIMARY KEY (medicationId)
);

-- Prescriptions Table
CREATE TABLE Prescriptions (
    prescriptionId INT NOT NULL UNIQUE AUTO_INCREMENT,
    userId INT NOT NULL,
    medicationId INT NOT NULL,
    prescribedDate DATETIME NOT NULL,
    dosageInstructions VARCHAR(200),
    quantity INT NOT NULL,
    refillCount INT DEFAULT 0,
    PRIMARY KEY (prescriptionId),
    FOREIGN KEY (userId) REFERENCES Users(userId),
    FOREIGN KEY (medicationId) REFERENCES Medications(medicationId)
);

-- Inventory Table
CREATE TABLE Inventory (
    inventoryId INT NOT NULL UNIQUE AUTO_INCREMENT,
    medicationId INT NOT NULL,
    quantityAvailable INT NOT NULL,
    lastUpdated DATETIME NOT NULL,
    PRIMARY KEY (inventoryId),
    FOREIGN KEY (medicationId) REFERENCES Medications(medicationId)
);

-- Sales Table
CREATE TABLE Sales (
    saleId INT NOT NULL UNIQUE AUTO_INCREMENT,
    prescriptionId INT NOT NULL,
    saleDate DATETIME NOT NULL,
    quantitySold INT NOT NULL,
    saleAmount DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (saleId),
    FOREIGN KEY (prescriptionId) REFERENCES Prescriptions(prescriptionId)
);