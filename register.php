

<?
require_once 'PharmacyDatabase.php';
$db = new PharmacyDatabase();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $userType = $_POST['user_type'];

    if ($db->registerUser($username, $password, $userType)) {
        header('Location: login.php');
        exit;
    } else {
        $error = "Registration failed. Try a different username.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Portal</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background-color: #f4f4f4; color: #333; }
        h1 { color: #2c3e50; }
        form { background: #fff; padding: 20px; border-radius: 5px; max-width: 400px; }
        input, select { width: 100%; padding: 8px; margin: 10px 0; }
        button { padding: 10px 15px; background-color: #2980b9; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #3498db; }
        .error { color: red; }
    </style>
</head>
<body>
<h1>Register</h1>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

<form method="post" action="processAddUser.php">
        Username: <input type="text" name="userName" required><br>
        Contact Info: <input type="text" name="contactInfo"><br>
        User Type: 
        <select name="userType" required>
            <option value="pharmacist">Pharmacist</option>
            <option value="patient">Patient</option>
        </select><br>
        Password: <input type="password" name="password" required><br>
        <input type="submit" value="Add User">
    </form>

<p>Already have an account? <a href="login.php">Login here</a>.</p>


</body>
</html>


