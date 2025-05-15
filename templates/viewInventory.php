<?php
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inventory - Pharmacy Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>View Inventory</h2>
        <p>Pharmacist: <?php echo htmlspecialchars($_SESSION['userName'] ?? 'Unknown'); ?></p>
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
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered" aria-describedby="inventory-table">
                <caption id="inventory-table">List of medications in inventory</caption>
                <thead>
                    <tr>
                        <th>Medication ID</th>
                        <th>Name</th>
                        <th>Dosage</th>
                        <th>Manufacturer</th>
                        <th>Quantity Available</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventory)): ?>
                        <?php foreach ($inventory as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['medicationId']); ?></td>
                                <td><?php echo htmlspecialchars($item['medicationName']); ?></td>
                                <td><?php echo htmlspecialchars($item['dosage']); ?></td>
                                <td><?php echo htmlspecialchars($item['manufacturer']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantityAvailable']); ?></td>
                                <td><?php echo htmlspecialchars($item['lastUpdated']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No inventory items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>