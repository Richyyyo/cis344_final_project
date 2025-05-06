<?
require 'PharmacyDatabase.php';
$db = new PharmacyDatabase();

$userName = $_POST['userName'];
$contactInfo = $_POST['contactInfo'];
$userType = $_POST['userType'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Save user
$stmt = $db->connection->prepare("INSERT INTO Users (userName, contactInfo, userType, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $userName, $contactInfo, $userType, $password);
$stmt->execute();

header("Location: pharmacistDashboard.php?msg=User Added");
exit;
?>
