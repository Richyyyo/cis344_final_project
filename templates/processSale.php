<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Process Sale - Pharmacist Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Process Sale</h2>
        <p>Pharmacist: <?php echo htmlspecialchars($_SESSION['userName'] ?? 'Unknown'); ?></p>
        <nav class="mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="?action=pharmacist_home" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="?action=addPrescription" class="nav-link">Add Prescription</a></li>
                <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View All Prescriptions</a></li>
                <li class="nav-item"><a href="?action=addMedication" class="nav-link">Add Medication</a></li>
                <li class="nav-item"><a href="?action=viewInventory" class="nav-link">View Inventory</a></li>
                <li class="nav-item"><a href="?action=processSale" class="nav-link active">Process Sale</a></li>
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
        <form method="POST" action="?action=processSale">
            <div class="mb-3">
                <label for="prescriptionId" class="form-label">Prescription</label>
                <select class="form-control" id="prescriptionId" name="prescriptionId" required>
                    <option value="" disabled selected>Select a prescription</option>
                    <?php
                    if (!empty($prescriptions)) {
                        foreach ($prescriptions as $prescription) {
                            echo "<option value='{$prescription['prescriptionId']}'>ID: {$prescription['prescriptionId']} - {$prescription['userName']} - {$prescription['medicationName']}</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No prescriptions available</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="quantitySold" class="form-label">Quantity Sold</label>
                <input type="number" class="form-control" id="quantitySold" name="quantitySold" required min="1" title="Quantity must be at least 1">
            </div>
            <div class="mb-3">
                <label for="saleAmount" class="form-label">Sale Amount ($)</label>
                <input type="number" step="0.01" class="form-control" id="saleAmount" name="saleAmount" required min="0.01" title="Sale amount must be positive">
            </div>
            <button type="submit" class="btn btn-primary">Process Sale</button>
        </form>
    </div>
</body>
</html>