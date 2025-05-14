-- Stored Procedure: AddOrUpdateUser
DELIMITER //
CREATE PROCEDURE AddOrUpdateUser(
    IN p_userId INT,
    IN p_userName VARCHAR(45),
    IN p_contactInfo VARCHAR(200),
    IN p_userType ENUM('pharmacist', 'patient'),
    IN p_password VARCHAR(255)
)
BEGIN
    IF p_userId IS NULL THEN
        INSERT INTO Users (userName, contactInfo, userType, password)
        VALUES (p_userName, p_contactInfo, p_userType, p_password);
    ELSE
        UPDATE Users
        SET userName = p_userName, contactInfo = p_contactInfo, userType = p_userType, password = p_password
        WHERE userId = p_userId;
    END IF;
END //
DELIMITER ;

-- Stored Procedure: ProcessSale
DELIMITER //
CREATE PROCEDURE ProcessSale(
    IN p_prescriptionId INT,
    IN p_quantitySold INT,
    IN p_saleAmount DECIMAL(10, 2)
)
BEGIN
    DECLARE v_medicationId INT;
    SELECT medicationId INTO v_medicationId FROM Prescriptions WHERE prescriptionId = p_prescriptionId;
    
    -- Update Inventory
    UPDATE Inventory
    SET quantityAvailable = quantityAvailable - p_quantitySold,
        lastUpdated = NOW()
    WHERE medicationId = v_medicationId;
    
    -- Insert Sale
    INSERT INTO Sales (prescriptionId, saleDate, quantitySold, saleAmount)
    VALUES (p_prescriptionId, NOW(), p_quantitySold, p_saleAmount);
END //
DELIMITER ;

-- View: MedicationInventoryView
CREATE VIEW MedicationInventoryView AS
SELECT 
    m.medicationName,
    m.dosage,
    m.manufacturer,
    i.quantityAvailable
FROM Medications m
JOIN Inventory i ON m.medicationId = i.medicationId;

-- Trigger: AfterPrescriptionInsert
DELIMITER //
CREATE TRIGGER AfterPrescriptionInsert
AFTER INSERT ON Prescriptions
FOR EACH ROW
BEGIN
    DECLARE stock_level INT;
    -- Update Inventory
    UPDATE Inventory
    SET quantityAvailable = quantityAvailable - NEW.quantity,
        lastUpdated = NOW()
    WHERE medicationId = NEW.medicationId;
    
    -- Check stock level
    SELECT quantityAvailable INTO stock_level
    FROM Inventory
    WHERE medicationId = NEW.medicationId;
    
    -- Log low stock (for simplicity, insert into a log table or handle as needed)
    IF stock_level < 10 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Low stock warning: Medication stock below 10 units';
    END IF;
END //
DELIMITER ;