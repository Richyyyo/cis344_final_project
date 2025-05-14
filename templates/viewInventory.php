<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Inventory - Pharmacist Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Medication Inventory</h2>
        <?php session_start(); ?>
        <p>Pharmacist: <?php echo htmlspecialchars($_SESSION['userName']); ?></p>
        <nav class="mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="?action=pharmacist_home" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="?action=addPrescription" class="nav-link">Add Prescription</a></li>
                <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View All Prescriptions</a></li>
                <li class="nav-item"><a href="?action=addMedication" class="nav-link">Add Medication</a></li>
                <li class="nav-item"><a href="?action=viewInventory" class="nav-link active">View Inventory</a></li>
                <li class="nav-item"><a href="?action=processSale" class="nav-link">Process Sale</a></li>
                <li class="nav-item"><a href="?action=addUser" class="nav-link">Add User</a></li>
                <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link">My Details</a></li>
                <li class="nav-item"><a href="?action=logout" class="nav-link">Logout</a></li>
            </ul>
        </nav>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Medication Name</th>
                    <th>Dosage</th>
                    <th>Manufacturer</th>
                    <th>Quantity Available</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['medicationName']); ?></td>
                        <td><?php echo htmlspecialchars($item['dosage']); ?></td>
                        <td><?php echo htmlspecialchars($item['manufacturer']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantityAvailable']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>