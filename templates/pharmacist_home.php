<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pharmacist Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Pharmacist Portal</h1>
        <?php session_start(); ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['userName']); ?> (Pharmacist)</p>
        <a href="?action=logout" class="btn btn-secondary">Logout</a>
        <nav class="mt-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="?action=addPrescription" class="nav-link">Add Prescription</a></li>
                <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View All Prescriptions</a></li>
                <li class="nav-item"><a href="?action=addMedication" class="nav-link">Add Medication</a></li>
                <li class="nav-item"><a href="?action=viewInventory" class="nav-link">View Inventory</a></li>
                <li class="nav-item"><a href="?action=processSale" class="nav-link">Process Sale</a></li>
                <li class="nav-item"><a href="?action=addUser" class="nav-link">Add User</a></li>
                <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link">My Details</a></li>
            </ul>
        </nav>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success mt-3"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
    </div>
</body>
</html>