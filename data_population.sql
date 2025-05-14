-- Users
INSERT INTO Users (userName, contactInfo, userType, password) VALUES
('john_doe', 'john@example.com', 'patient', '$2y$10$examplehashedpassword1'),
('jane_smith', 'jane@example.com', 'patient', '$2y$10$examplehashedpassword2'),
('pharma1', 'pharma1@example.com', 'pharmacist', '$2y$10$examplehashedpassword3');

-- Medications
INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES
('Ibuprofen', '200mg', 'Pfizer'),
('Amoxicillin', '500mg', 'GSK'),
('Lisinopril', '10mg', 'Merck');

-- Inventory
INSERT INTO Inventory (medicationId, quantityAvailable, lastUpdated) VALUES
(1, 100, NOW()),
(2, 50, NOW()),
(3, 75, NOW());

-- Prescriptions
INSERT INTO Prescriptions (userId, medicationId, prescribedDate, dosageInstructions, quantity, refillCount) VALUES
(1, 1, NOW(), 'Take 1 tablet every 6 hours', 30, 1),
(2, 2, NOW(), 'Take 1 capsule every 8 hours', 20, 0),
(1, 3, NOW(), 'Take 1 tablet daily', 30, 2);

-- Sales
INSERT INTO Sales (prescriptionId, saleDate, quantitySold, saleAmount) VALUES
(1, NOW(), 30, 15.00),
(2, NOW(), 20, 25.00),
(3, NOW(), 30, 20.00);