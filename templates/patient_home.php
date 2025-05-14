<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Patient Portal</h1>
        <?php session_start(); ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['userName']); ?> (Patient)</p>
        <a href="?action=logout" class="btn btn-secondary">Logout</a>
        <nav class="mt-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View My Prescriptions</a></li>
                <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link">My Details</a></li>
            </ul>
        </nav>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>