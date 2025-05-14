<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Medication - Pharmacist Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Medication</h2>
        <p>Pharmacist: <?php echo htmlspecialchars($_SESSION['userName'] ?? 'Unknown'); ?></p>
        <nav class="mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="?action=pharmacist_home" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="?action=addPrescription" class="nav-link">Add Prescription</a></li>
                <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View All Prescriptions</a></li>
                <li class="nav-item"><a href="?action=addMedication" class="nav-link active">Add Medication</a></li>
                <li class="nav-item"><a href="?action=viewInventory" class="nav-link">View Inventory</a></li>
                <li class="nav-item"><a href="?action=processSale" class="nav-link">Process Sale</a></li>
                <li class="nav-item"><a href="?action=addUser" class="nav-link">Add User</a></li>
                <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link">My Details</a></li>
                <li class="nav-item"><a href="?action=logout" class="nav-link">Logout</a></li>
            </ul>
        </nav>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        <form method="POST" action="?action=addMedication">
            <div class="mb-3">
                <label for="medicationName" class="form-label">Medication Name</label>
                <input type="text" class="form-control" id="medicationName" name="medicationName" required>
            </div>
            <div class="mb-3">
                <label for="dosage" class="form-label">Dosage</label>
                <input type="text" class="form-control" id="dosage" name="dosage" required>
            </div>
            <div class="mb-3">
                <label for="manufacturer" class="form-label">Manufacturer</label>
                <input type="text" class="form-control" id="manufacturer" name="manufacturer" required>
            </div>
            <div class="mb-3">
                <label for="quantityAvailable" class="form-label">Quantity Available</label>
                <input type="number" class="form-control" id="quantityAvailable" name="quantityAvailable" required min="0" title="Quantity must be 0 or greater">
            </div>
            <button type="submit" class="btn btn-primary">Add Medication</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>