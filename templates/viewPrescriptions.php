<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Prescriptions - <?php echo $_SESSION['userType'] === 'pharmacist' ? 'Pharmacist' : 'Patient'; ?> Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2><?php echo $_SESSION['userType'] === 'pharmacist' ? 'All Prescriptions' : 'My Prescriptions'; ?></h2>
        <?php session_start(); ?>
        <p><?php echo htmlspecialchars($_SESSION['userType'] === 'pharmacist' ? 'Pharmacist' : 'Patient'); ?>: <?php echo htmlspecialchars($_SESSION['userName']); ?></p>
        <nav class="mb-3">
            <ul class="nav nav-pills">
                <?php if ($_SESSION['userType'] === 'pharmacist'): ?>
                    <li class="nav-item"><a href="?action=pharmacist_home" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="?action=addPrescription" class="nav-link">Add Prescription</a></li>
                    <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link active">View All Prescriptions</a></li>
                    <li class="nav-item"><a href="?action=addMedication" class="nav-link">Add Medication</a></li>
                    <li class="nav-item"><a href="?action=viewInventory" class="nav-link">View Inventory</a></li>
                    <li class="nav-item"><a href="?action=processSale" class="nav-link">Process Sale</a></li>
                    <li class="nav-item"><a href="?action=addUser" class="nav-link">Add User</a></li>
                    <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link">My Details</a></li>
                    <li class="nav-item"><a href="?action=logout" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="?action=patient_home" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link active">View My Prescriptions</a></li>
                    <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link">My Details</a></li>
                    <li class="nav-item"><a href="?action=logout" class="nav-link">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        <?php if (!empty($prescriptions)): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Prescription ID</th>
                        <th>Patient</th>
                        <th>Medication</th>
                        <th>Dosage</th>
                        <th>Date</th>
                        <th>Instructions</th>
                        <th>Quantity</th>
                        <th>Refills</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prescriptions as $prescription): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($prescription['prescriptionId']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['userName'] ?? $_SESSION['userName']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['medicationName']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['dosage']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['prescribedDate']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['dosageInstructions']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['quantity']); ?></td>
                            <td><?php echo htmlspecialchars($prescription['refillCount']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No prescriptions found.</p>
        <?php endif; ?>
    </div>
</body>
</html>