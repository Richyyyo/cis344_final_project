<?
session_start();
require_once 'PharmacyDatabase.php';
$db = new PharmacyDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $db->loginUser($_POST['username'], $_POST['password']);
    if ($user) {
        $_SESSION['user'] = $user;
        if ($user['userType'] === 'pharmacist') {
            header('Location: doctorPortal.php');
        } else {
            header('Location: patientPortal.php');
        }
        exit;
    } else {
        $error = "Invalid login credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Pharmacy Portal</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<h1>Login</h1>
<?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    <button type="submit">Login</button>
</form>
<p>No account? <a href="register.php">Register here</a>.</p>
</body>
</html>
