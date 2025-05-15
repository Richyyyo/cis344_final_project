<?php
require_once 'PharmacyDatabase.php';

class PharmacyPortal {
    private $db;

    public function __construct() {
        $this->db = new PharmacyDatabase();
    }

    public function handleRequest() {
        session_start();
        $action = $_GET['action'] ?? 'home';

        // Redirect to appropriate portal if authenticated, except for home, login, or addUser
        if (isset($_SESSION['userId']) && in_array($action, ['home', 'login', 'addUser'])) {
            if ($_SESSION['userType'] === 'pharmacist') {
                header("Location: ?action=pharmacist_home");
                exit;
            } else {
                header("Location: ?action=patient_home");
                exit;
            }
        }

        switch ($action) {
            case 'login':
                $this->login();
                break;
            case 'logout':
                $this->logout();
                break;
            case 'pharmacist_home':
                $this->pharmacistHome();
                break;
            case 'patient_home':
                $this->patientHome();
                break;
            case 'addPrescription':
                $this->addPrescription();
                break;
            case 'viewPrescriptions':
                $this->viewPrescriptions();
                break;
            case 'viewInventory':
                $this->viewInventory();
                break;
            case 'addUser':
                $this->addUser();
                break;
            case 'addMedication':
                $this->addMedication();
                break;
            case 'viewUserDetails':
                $this->viewUserDetails();
                break;
            case 'processSale':
                $this->processSale();
                break;
            case 'home':
                $this->home();
                break;
            default:
                $this->home();
        }
    }

    private function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userName = filter_var($_POST['userName'] ?? '', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';
            
            if (empty($userName) || empty($password)) {
                $error = "Username and password are required.";
                include 'templates/login.php';
                return;
            }

            $user = $this->db->verifyUser($userName, $password);
            if ($user) {
                session_start();
                $_SESSION['userId'] = $user['userId'];
                $_SESSION['userName'] = $user['userName'];
                $_SESSION['userType'] = $user['userType'];
                error_log("User logged in: " . $userName);
                if ($user['userType'] === 'pharmacist') {
                    header("Location: ?action=pharmacist_home");
                } else {
                    header("Location: ?action=patient_home");
                }
            } else {
                $error = "Invalid credentials";
                include 'templates/login.php';
            }
        } else {
            include 'templates/login.php';
        }
    }

    private function logout() {
        session_start();
        session_destroy();
        header("Location: ?action=home");
    }

    private function home() {
        include 'templates/home.php';
    }

    private function pharmacistHome() {
        if ($_SESSION['userType'] !== 'pharmacist') {
            header("Location: ?action=patient_home");
            exit;
        }
        include 'templates/pharmacist_home.php';
    }

    private function patientHome() {
        if ($_SESSION['userType'] !== 'patient') {
            header("Location: ?action=pharmacist_home");
            exit;
        }
        include 'templates/patient_home.php';
    }

    private function addPrescription() {
        if ($_SESSION['userType'] !== 'pharmacist') {
            header("Location: ?action=patient_home");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $patientUserName = filter_var($_POST['patient_username'] ?? '', FILTER_SANITIZE_STRING);
            $medicationId = filter_var($_POST['medication_id'] ?? 0, FILTER_VALIDATE_INT);
            $dosageInstructions = filter_var($_POST['dosage_instructions'] ?? '', FILTER_SANITIZE_STRING);
            $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_VALIDATE_INT);
            $refillCount = filter_var($_POST['refill_count'] ?? 0, FILTER_VALIDATE_INT);

            if (!$patientUserName || !preg_match('/^[A-Za-z0-9_]{3,45}$/', $patientUserName)) {
                $_SESSION['flash_error'] = "Invalid patient username";
                error_log("Invalid patient username: " . $patientUserName);
                header("Location: ?action=addPrescription");
                exit;
            }
            if (!$medicationId || $medicationId <= 0) {
                $_SESSION['flash_error'] = "Invalid medication selected";
                error_log("Invalid medication ID: " . $medicationId);
                header("Location: ?action=addPrescription");
                exit;
            }
            if (empty($dosageInstructions)) {
                $_SESSION['flash_error'] = "Dosage instructions required";
                error_log("Dosage instructions missing");
                header("Location: ?action=addPrescription");
                exit;
            }
            if (!$quantity || $quantity <= 0) {
                $_SESSION['flash_error'] = "Quantity must be at least 1";
                error_log("Invalid quantity: " . $quantity);
                header("Location: ?action=addPrescription");
                exit;
            }
            if ($refillCount === false || $refillCount < 0) {
                $_SESSION['flash_error'] = "Refills must be 0 or more";
                error_log("Invalid refill count: " . $_POST['refill_count']);
                header("Location: ?action=addPrescription");
                exit;
            }

            try {
                $message = $this->db->addPrescription($patientUserName, $medicationId, $dosageInstructions, $quantity, $refillCount);
                $_SESSION['flash_message'] = $message;
                error_log("Redirecting to viewPrescriptions after adding prescription for " . $patientUserName . ", refills: " . $refillCount);
                header("Location: ?action=viewPrescriptions");
            } catch (Exception $e) {
                $_SESSION['flash_error'] = "Failed to add prescription: " . $e->getMessage();
                error_log("addPrescription failed: " . $e->getMessage());
                header("Location: ?action=addPrescription");
            }
        } else {
            $Medications = $this->db->getAvailableMedications();
            include 'templates/addPrescription.php';
        }
    }

    private function viewPrescriptions() {
        try {
            if ($_SESSION['userType'] === 'pharmacist') {
                $prescriptions = $this->db->getAllPrescriptions();
            } else {
                $prescriptions = $this->db->getUserDetails($_SESSION['userId']);
                // Filter to include only prescription-related data
                $prescriptions = array_filter($prescriptions, function($detail) {
                    return !empty($detail['prescriptionId']);
                });
            }
            error_log("Rendering viewPrescriptions for user: " . $_SESSION['userName'] . ", type: " . $_SESSION['userType']);
            include 'templates/viewPrescriptions.php';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = "Failed to load prescriptions: " . $e->getMessage();
            error_log("viewPrescriptions failed: " . $e->getMessage());
            header("Location: ?action=" . ($_SESSION['userType'] === 'pharmacist' ? 'pharmacist_home' : 'patient_home'));
        }
    }

    private function viewInventory() {
        if ($_SESSION['userType'] !== 'pharmacist') {
            header("Location: ?action=patient_home");
            exit;
        }
        $inventory = $this->db->MedicationInventory();
        include 'templates/viewInventory.php';
    }

    private function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userName = filter_var($_POST['userName'] ?? '', FILTER_SANITIZE_STRING);
            $contactInfo = filter_var($_POST['contactInfo'] ?? '', FILTER_SANITIZE_STRING);
            $userType = filter_var($_POST['userType'] ?? '', FILTER_SANITIZE_STRING);
            $password = $_POST['password'] ?? '';

            if (empty($userName) || empty($contactInfo) || empty($userType) || empty($password)) {
                $error = "All fields are required.";
                include 'templates/addUser.php';
                return;
            }

            if (strlen($userName) > 50 || strlen($contactInfo) > 100) {
                $error = "Username or contact info exceeds allowed length.";
                include 'templates/addUser.php';
                return;
            }

            if (in_array($userType, ['patient', 'pharmacist'])) {
                $message = $this->db->addUser($userName, $contactInfo, $userType, $password);
                if ($message === "User added successfully") {
                    header("Location: ?action=home&message=" . urlencode("User created successfully. Please log in."));
                    exit;
                } else {
                    $error = strpos($message, "Duplicate entry") !== false 
                        ? "Username '$userName' is already taken. Please choose another."
                        : $message;
                    include 'templates/addUser.php';
                }
            } else {
                $error = "Invalid user type selected.";
                include 'templates/addUser.php';
            }
        } else {
            include 'templates/addUser.php';
        }
    }

    private function addMedication() {
        if ($_SESSION['userType'] !== 'pharmacist') {
            header("Location: ?action=patient_home");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $medicationName = filter_var($_POST['medicationName'] ?? '', FILTER_SANITIZE_STRING);
            $dosage = filter_var($_POST['dosage'] ?? '', FILTER_SANITIZE_STRING);
            $manufacturer = filter_var($_POST['manufacturer'] ?? '', FILTER_SANITIZE_STRING);
            $quantityAvailable = filter_var($_POST['quantityAvailable'] ?? 0, FILTER_VALIDATE_INT);

            if (!$medicationName || !$dosage || !$manufacturer || $quantityAvailable < 0) {
                header("Location: ?action=addMedication&error=" . urlencode("Invalid input data"));
                exit;
            }

            $message = $this->db->addMedication($medicationName, $dosage, $manufacturer, $quantityAvailable);
            header("Location: ?action=viewInventory&message=" . urlencode($message));
        } else {
            include 'templates/addMedication.php';
        }
    }

    private function viewUserDetails() {
        $userId = filter_var($_GET['userId'] ?? $_SESSION['userId'], FILTER_VALIDATE_INT);
        if ($_SESSION['userType'] !== 'pharmacist' && $userId !== $_SESSION['userId']) {
            header("Location: ?action=patient_home");
            exit;
        }
        $userDetails = $this->db->getUserDetails($userId);
        include 'templates/viewUserDetails.php';
    }

    private function processSale() {
        if ($_SESSION['userType'] !== 'pharmacist') {
            header("Location: ?action=patient_home");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prescriptionId = filter_var($_POST['prescriptionId'] ?? 0, FILTER_VALIDATE_INT);
            $quantitySold = filter_var($_POST['quantitySold'] ?? 0, FILTER_VALIDATE_INT);
            $saleAmount = filter_var($_POST['saleAmount'] ?? 0, FILTER_VALIDATE_FLOAT);

            if (!$prescriptionId || $quantitySold <= 0 || $saleAmount <= 0) {
                header("Location: ?action=processSale&error=" . urlencode("Invalid input data"));
                exit;
            }

            $message = $this->db->processSale($prescriptionId, $quantitySold, $saleAmount);
            header("Location: ?action=viewPrescriptions&message=" . urlencode($message));
        } else {
            $prescriptions = $this->db->getAllPrescriptions();
            include 'templates/processSale.php';
        }
    }
}

$portal = new PharmacyPortal();
$portal->handleRequest();
?>