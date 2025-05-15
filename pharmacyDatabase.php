<?php
class PharmacyDatabase {
    private $host = "localhost";
    private $port = "3307";
    private $database = "pharmacy_portal_db";
    private $user = "root";
    private $password = "";
    private $connection;

    public function __construct() {
        $this->connect();
    }

    private function connect() {
        $this->connection = new mysqli($this->host, $this->user, $this->password, $this->database, $this->port);
        if ($this->connection->connect_error) {
            $error = "Connection failed: " . $this->connection->connect_error;
            error_log($error);
            throw new Exception($error);
        }
        error_log("Database connection successful");
    }

    public function addUser($userName, $contactInfo, $userType, $password) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->connection->prepare(
            "CALL AddOrUpdateUser(NULL, ?, ?, ?, ?)"
        );
        if (!$stmt) {
            $error = $this->connection->error;
            error_log("Prepare failed: " . $error);
            return "Failed to prepare statement: " . $error;
        }
        $stmt->bind_param("ssss", $userName, $contactInfo, $userType, $hashedPassword);
        $result = $stmt->execute();
        if (!$result) {
            $error = $this->connection->error;
            error_log("AddUser failed: " . $error);
            $stmt->close();
            return "Failed to add user: " . $error;
        }
        $stmt->close();
        return "User added successfully";
    }

    public function addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity, $refillCount) {
        if (strlen($dosageInstructions) > 255) {
            error_log("Dosage instructions too long for patient: " . $patientUserName);
            return "Dosage instructions must be 255 characters or less";
        }
        if ($refillCount < 0) {
            error_log("Invalid refill count for patient: " . $patientUserName . ", refillCount: " . $refillCount);
            return "Refills must be 0 or more";
        }

        $stmt = $this->connection->prepare(
            "SELECT userId FROM Users WHERE userName = ? AND userType = 'patient'"
        );
        if (!$stmt) {
            $error = $this->connection->error;
            error_log("Prepare failed for patient check: " . $error);
            return "Database error: " . $error;
        }
        $stmt->bind_param("s", $patientUserName);
        $stmt->execute();
        $stmt->bind_result($patientId);
        $stmt->fetch();
        $stmt->close();
        
        if (!$patientId) {
            error_log("Patient not found: " . $patientUserName);
            return "Patient not found";
        }

        $stmt = $this->connection->prepare(
            "SELECT m.medicationId, i.quantityAvailable 
             FROM Medications m 
             JOIN Inventory i ON m.medicationId = i.medicationId 
             WHERE m.medicationId = ?"
        );
        if (!$stmt) {
            $error = $this->connection->error;
            error_log("Prepare failed for medication check: " . $error);
            return "Database error: " . $error;
        }
        $stmt->bind_param("i", $medicationId);
        $stmt->execute();
        $stmt->bind_result($medId, $quantityAvailable);
        $stmt->fetch();
        $stmt->close();

        if (!$medId) {
            error_log("Medication not found: " . $medicationId);
            return "Medication not found";
        }
        if ($quantityAvailable < $quantity) {
            error_log("Insufficient stock for medication ID " . $medicationId . ": requested " . $quantity . ", available " . $quantityAvailable);
            return "Insufficient stock for this medication";
        }

        $this->connection->begin_transaction();
        try {
            $stmt = $this->connection->prepare(
                "INSERT INTO Prescriptions (userId, medicationId, prescribedDate, dosageInstructions, quantity, refillCount) 
                 VALUES (?, ?, NOW(), ?, ?, ?)"
            );
            if (!$stmt) {
                throw new Exception("Prepare failed for prescription insert: " . $this->connection->error);
            }
            $stmt->bind_param("iisii", $patientId, $medicationId, $dosageInstructions, $quantity, $refillCount);
            $stmt->execute();
            $prescriptionId = $this->connection->insert_id;
            $stmt->close();

            $stmt = $this->connection->prepare(
                "UPDATE Inventory SET quantityAvailable = quantityAvailable - ?, lastUpdated = NOW() WHERE medicationId = ?"
            );
            if (!$stmt) {
                throw new Exception("Prepare failed for inventory update: " . $this->connection->error);
            }
            $stmt->bind_param("ii", $quantity, $medicationId);
            $stmt->execute();
            $stmt->close();

            $this->connection->commit();
            error_log("Prescription added successfully, ID: " . $prescriptionId . ", patient: " . $patientUserName . ", medication ID: " . $medicationId . ", refills: " . $refillCount);
            return "Prescription added successfully";
        } catch (Exception $e) {
            $this->connection->rollback();
            $error = "addPrescription transaction failed: " . $e->getMessage();
            error_log($error);
            return "Failed to add prescription: " . $e->getMessage();
        }
    }

    public function addMedication($medicationName, $dosage, $manufacturer, $quantityAvailable) {
        $stmt = $this->connection->prepare(
            "INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES (?, ?, ?)"
        );
        if (!$stmt) {
            $error = $this->connection->error;
            error_log("Prepare failed for addMedication: " . $error);
            return "Database error: " . $error;
        }
        $stmt->bind_param("sss", $medicationName, $dosage, $manufacturer);
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            $medicationId = $this->connection->insert_id;
            $stmt = $this->connection->prepare(
                "INSERT INTO Inventory (medicationId, quantityAvailable, lastUpdated) VALUES (?, ?, NOW())"
            );
            if (!$stmt) {
                $error = $this->connection->error;
                error_log("Prepare failed for inventory insert: " . $error);
                return "Database error: " . $error;
            }
            $stmt->bind_param("ii", $medicationId, $quantityAvailable);
            $result = $stmt->execute();
            $stmt->close();
            return $result ? "Medication added successfully" : "Failed to add to inventory: " . $this->connection->error;
        }
        return "Failed to add medication: " . $this->connection->error;
    }

    public function getAllPrescriptions() {
        $result = $this->connection->query(
            "SELECT p.prescriptionId, p.userId, p.medicationId, p.prescribedDate, p.dosageInstructions, p.quantity, p.refillCount, 
                    m.medicationName, m.dosage, u.userName 
             FROM Prescriptions p 
             LEFT JOIN Medications m ON p.medicationId = m.medicationId 
             LEFT JOIN Users u ON p.userId = u.userId 
             ORDER BY p.prescribedDate DESC"
        );
        if (!$result) {
            $error = "getAllPrescriptions failed: " . $this->connection->error;
            error_log($error);
            return [];
        }
        $prescriptions = $result->fetch_all(MYSQLI_ASSOC);
        error_log("Fetched " . count($prescriptions) . " prescriptions");
        return $prescriptions;
    }

    public function getUserDetails($userId) {
        $stmt = $this->connection->prepare(
            "SELECT u.userId, u.userName, u.contactInfo, u.userType, 
                    p.prescriptionId, p.prescribedDate, p.dosageInstructions, p.quantity, p.refillCount,
                    m.medicationName, m.dosage
             FROM Users u
             LEFT JOIN Prescriptions p ON u.userId = p.userId
             LEFT JOIN Medications m ON p.medicationId = m.medicationId
             WHERE u.userId = ?"
        );
        if (!$stmt) {
            $error = $this->connection->error;
            error_log("Prepare failed for getUserDetails: " . $error);
            return [];
        }
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        error_log("Fetched user details for userId: " . $userId);
        return $result;
    }

    public function MedicationInventory() {
        $result = $this->connection->query(
            "SELECT m.medicationId, m.medicationName, m.dosage, m.manufacturer, i.quantityAvailable, i.lastUpdated
             FROM Medications m
             JOIN Inventory i ON m.medicationId = i.medicationId"
        );
        if (!$result) {
            $error = "MedicationInventory failed: " . $this->connection->error;
            error_log($error);
            return [];
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function verifyUser($userName, $password) {
        $stmt = $this->connection->prepare(
            "SELECT userId, userName, userType, password FROM Users WHERE userName = ?"
        );
        if (!$stmt) {
            $error = $this->connection->error;
            error_log("Prepare failed for verifyUser: " . $error);
            return false;
        }
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $stmt->bind_result($userId, $userName, $userType, $hashedPassword);
        $stmt->fetch();
        $stmt->close();
        
        if (password_verify($password, $hashedPassword)) {
            error_log("User verified: " . $userName);
            return [
                'userId' => $userId,
                'userName' => $userName,
                'userType' => $userType
            ];
        }
        error_log("User verification failed: " . $userName);
        return false;
    }

    public function processSale($prescriptionId, $quantitySold, $saleAmount) {
        $stmt = $this->connection->prepare(
            "CALL ProcessSale(?, ?, ?)"
        );
        if (!$stmt) {
            $error = $this->connection->error;
            error_log("Prepare failed for processSale: " . $error);
            return "Database error: " . $error;
        }
        $stmt->bind_param("iid", $prescriptionId, $quantitySold, $saleAmount);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? "Sale processed successfully" : "Failed to process sale: " . $this->connection->error;
    }

    public function getAvailableMedications() {
        $result = $this->connection->query(
            "SELECT m.medicationId, m.medicationName, m.dosage 
             FROM Medications m 
             JOIN Inventory i ON m.medicationId = i.medicationId 
             WHERE i.quantityAvailable > 0 
             ORDER BY m.medicationName"
        );
        if (!$result) {
            $error = "getAvailableMedications failed: " . $this->connection->error;
            error_log($error);
            return [];
        }
        $medications = $result->fetch_all(MYSQLI_ASSOC);
        error_log("Fetched " . count($medications) . " available medications");
        return $medications;
    }
}
?>