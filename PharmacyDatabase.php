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
            die("Connection failed: " . $this->connection->connect_error);
        }
        echo "Successfully connected to the database";
    }

    public function addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity)  {
        $stmt = $this->connection->prepare(
            "SELECT userId FROM Users WHERE userName = ? AND userType = 'patient'"
        );
        $stmt->bind_param("s", $patientUserName);
        $stmt->execute();
        $stmt->bind_result($patientId);
        $stmt->fetch();
        $stmt->close();
        
        if ($patientId){
            $stmt = $this->connection->prepare(
                "INSERT INTO prescriptions (userId, medicationId, dosageInstructions, quantity) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("iisi", $patientId, $medicationId, $dosageInstructions, $quantity);
            $stmt->execute();
            $stmt->close();
            echo "Prescription added successfully";
        }else{
            echo "failed to add prescription";
        }
    }

    public function getAllPrescriptions() {
        $result = $this->connection->query("SELECT * FROM  prescriptions join medications on prescriptions.medicationId= medications.medicationId");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    public function MedicationInventory() {
        /*
        Complete this function to test the functionality of
        MedicationInventoryView and implement it in the server
        */
        //Wrire code here
        $result = $this->conn->query("SELECT * FROM MedicationInventoryView");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    }

    public function addUser($userName, $contactInfo, $userType) {
     //Write Code here
     $stmt = $this->conn->prepare("CALL AddOrUpdateUser(?, ?, ?)");
     $stmt->bind_param("sss", $userName, $contactInfo, $userType);
     return $stmt->execute();
     }

     public function addMedication($medicationName, $dosage, $manufacturer) {
        $stmt = $this->conn->prepare("INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $medicationName, $dosage, $manufacturer);
        return $stmt->execute();
    }
    
    public function getUserDetails($userId) {
        $stmt = $this->conn->prepare("SELECT * FROM Users WHERE userId = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    
 
    public function registerUser($username, $password, $userType) {
        $stmt = $this->connection->prepare("SELECT userId FROM Users WHERE userName = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
    
        if ($stmt->num_rows > 0) {
            return false; // Username exists
        }
        $stmt->close();
    
        // For now we'll store password in contactInfo for compatibility (or you can add password column!)
        $stmt = $this->connection->prepare("INSERT INTO Users (userName, contactInfo, userType) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $password, $userType);
        $result = $stmt->execute();
        $stmt->close();
    
        return $result;
    }
    
    public function loginUser($username, $password) {
        $stmt = $this->connection->prepare("SELECT userId, contactInfo, userType FROM Users WHERE userName = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->bind_result($userId, $hashedPassword, $userType);
        if ($stmt->fetch() && password_verify($password, $hashedPassword)) {
            return [
                'userId' => $userId,
                'username' => $username,
                'userType' => $userType
            ];
        }
        return false;
    }
    
    //Add Other needed functions here
}
?>
