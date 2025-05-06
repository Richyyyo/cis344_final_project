<html>
<head><title>Pharmacy Portal</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Pharmacy Portal</h1>
    <h2>Welcome Patient</h2>
<p><a href="logout.php">Logout</a></p>

<?php if (isset($_SESSION['user']) && $_SESSION['user']['userType'] === 'patient'): ?>
    <h2>Patient Portal</h2>
    <nav>
    <ul>
        <li><a href="viewPrescriptions.php">View My Prescriptions</a></li>
    </ul>
    </nav>
<?php endif; ?>
</html>
</body>