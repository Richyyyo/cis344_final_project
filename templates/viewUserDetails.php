<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Details - <?php echo $_SESSION['userType'] === 'pharmacist' ? 'Pharmacist' : 'Patient'; ?> Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>User Details</h2>
        <?php session_start(); ?>
        <p><?php echo htmlspecialchars($_SESSION['userType'] === 'pharmacist' ? 'Pharmacist' : 'Patient'); ?>: <?php echo htmlspecialchars($_SESSION['userName']); ?></p>
        <nav class="mb-3">
            <ul class="nav nav-pills">
                <?php if ($_SESSION['userType'] === 'pharmacist'): ?>
                    <li class="nav-item"><a href="?action=pharmacist_home" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="?action=addPrescription" class="nav-link">Add Prescription</a></li>
                    <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View All Prescriptions</a></li>
                    <li class="nav-item"><a href="?action=addMedication" class="nav-link">Add Medication</a></li>
                    <li class="nav-item"><a href="?action=viewInventory" class="nav-link">View Inventory</a></li>
                    <li class="nav-item"><a href="?action=processSale" class="nav-link">Process Sale</a></li>
                    <li class="nav-item"><a href="?action=addUser" class="nav-link">Add User</a></li>
                    <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link active">My Details</a></li>
                    <li class="nav-item"><a href="?action=logout" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="?action=patient_home" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View My Prescriptions</a></li>
                    <li class="nav-item"><a href="?action=viewUserDetails" class="nav-link active">My Details</a></li>
                    <li class="nav-item"><a href="?action=logout" class="nav-link">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php if (!empty($userDetails)): ?>
            <h4><?php echo htmlspecialchars($userDetails[0]['userName']); ?></h4>
            <p><strong>Contact Info:</strong> <?php echo htmlspecialchars($userDetails[0]['contactInfo']); ?></p>
            <p><strong>User Type:</strong> <?php echo htmlspecialchars($userDetails[0]['userType']); ?></p>
            <h5>Prescriptions</h5>
            <?php if (count($userDetails) > 0 && !empty($userDetails[0]['prescriptionId'])): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Prescription ID</th>
                            <th>Medication</th>
                            <th>Dosage</th>
                            <th>Date</th>
                            <th>Instructions</th>
                            <th>Quantity</th>
                            => <th>Refills</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($userDetails as $detail): ?>
                            <?php if (!empty($detail['prescriptionId'])): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($detail['prescriptionId']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['medicationName']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['dosage']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['prescribedDate']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['dosageInstructions']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['quantity']); ?></td>
                                    <td><?php echo htmlspecialchars($detail['refillCount']); ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No prescriptions found.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>No user details found.</p>
        <?php endif; ?>
    </div>
</body>
</html>