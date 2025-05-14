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

    public function addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity) {
        // Check patient exists
        $stmt = $this->connection->prepare(
            "SELECT userId FROM Users WHERE userName = ? AND userType = 'patient'"
        );
        $stmt->bind_param("s", $patientUserName);
        $stmt->execute();
        $stmt->bind_result($patientId);
        $stmt->fetch();
        $stmt->close();
        
        if (!$patientId) {
            return "Patient not found";
        }

        // Check medication exists
        $stmt = $this->connection->prepare(
            "SELECT medicationId FROM Medications WHERE medicationId = ?"
        );
        $stmt->bind_param("i", $medicationId);
        $stmt->execute();
        $stmt->bind_result($medId);
        $stmt->fetch();
        $stmt->close();

        if (!$medId) {
            return "Medication not found";
        }

        // Insert prescription
        $stmt = $this->connection->prepare(
            "INSERT INTO Prescriptions (userId, medicationId, prescribedDate, dosageInstructions, quantity) VALUES (?, ?, NOW(), ?, ?)"
        );
        $stmt->bind_param("iisi", $patientId, $medicationId, $dosageInstructions, $quantity);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? "Prescription added successfully" : "Failed to add prescription: " . $this->connection->error;
    }

    public function addMedication($medicationName, $dosage, $manufacturer, $quantityAvailable) {
        $stmt = $this->connection->prepare(
            "INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $medicationName, $dosage, $manufacturer);
        $result = $stmt->execute();
        $stmt->close();
        
        if ($result) {
            $medicationId = $this->connection->insert_id;
            $stmt = $this->connection->prepare(
                "INSERT INTO Inventory (medicationId, quantityAvailable, lastUpdated) VALUES (?, ?, NOW())"
            );
            $stmt->bind_param("ii", $medicationId, $quantityAvailable);
            $result = $stmt->execute();
            $stmt->close();
            return $result ? "Medication added successfully" : "Failed to add to inventory";
        }
        return "Failed to add medication";
    }

    public function getAllPrescriptions() {
        $result = $this->connection->query(
            "SELECT p.*, m.medicationName, m.dosage, u.userName 
             FROM Prescriptions p 
             JOIN Medications m ON p.medicationId = m.medicationId 
             JOIN Users u ON p.userId = u.userId"
        );
        return $result->fetch_all(MYSQLI_ASSOC);
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
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $result;
    }

    public function MedicationInventory() {
        $result = $this->connection->query("SELECT * FROM MedicationInventoryView");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function verifyUser($userName, $password) {
        $stmt = $this->connection->prepare(
            "SELECT userId, userName, userType, password FROM Users WHERE userName = ?"
        );
        $stmt->bind_param("s", $userName);
        $stmt->execute();
        $stmt->bind_result($userId, $userName, $userType, $hashedPassword);
        $stmt->fetch();
        $stmt->close();
        
        if (password_verify($password, $hashedPassword)) {
            return [
                'userId' => $userId,
                'userName' => $userName,
                'userType' => $userType
            ];
        }
        return false;
    }

    public function processSale($prescriptionId, $quantitySold, $saleAmount) {
        $stmt = $this->connection->prepare(
            "CALL ProcessSale(?, ?, ?)"
        );
        $stmt->bind_param("iid", $prescriptionId, $quantitySold, $saleAmount);
        $result = $stmt->execute();
        $stmt->close();
        return $result ? "Sale processed successfully" : "Failed to process sale";
    }
}
?>