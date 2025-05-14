<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Prescription - Pharmacist Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Prescription</h2>
        <p>Pharmacist: <?php echo htmlspecialchars($_SESSION['userName'] ?? 'Unknown'); ?></p>
        <nav class="mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item"><a href="?action=pharmacist_home" class="nav-link">Home</a></li>
                <li class="nav-item"><a href="?action=addPrescription" class="nav-link active">Add Prescription</a></li>
                <li class="nav-item"><a href="?action=viewPrescriptions" class="nav-link">View All Prescriptions</a></li>
                <li class="nav-item"><a href="?action=addMedication" class="nav-link">Add Medication</a></li>
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
        <form method="POST" action="?action=addPrescription">
            <div class="mb-3">
                <label for="patient_username" class="form-label">Patient Username</label>
                <input type="text" class="form-control" id="patient_username" name="patient_username" required pattern="[A-Za-z0-9_]{3,45}" title="Username must be 3-45 characters, letters, numbers, or underscores">
            </div>
            <div class="mb-3">
                <label for="medication_id" class="form-label">Medication</label>
                <select class="form-control" id="medication_id" name="medication_id" required>
                    <option value="" disabled selected>Select a medication</option>
                    <?php
                    try {
                        require_once 'PharmacyDatabase.php';
                        $db = new PharmacyDatabase();
                        error_log("Attempting to query Medications table at " . date('Y-m-d H:i:s'));
                        $result = $db->connection->query("SELECT medicationId, medicationName, dosage FROM Medications");
                        if ($result === false) {
                            $error = $db->connection->error;
                            error_log("Medication query failed: $error");
                            echo "<div class='alert alert-danger'>Query failed: " . htmlspecialchars($error) . "</div>";
                            echo "<option value='' disabled>Query failed: " . htmlspecialchars($error) . "</option>";
                        } else {
                            $numRows = $result->num_rows;
                            error_log("Found $numRows medications in Medications table");
                            if ($numRows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    error_log("Found medication: " . $row['medicationName'] . " (ID: " . $row['medicationId'] . ")");
                                    echo "<option value='{$row['medicationId']}'>{$row['medicationName']} ({$row['dosage']})</option>";
                                }
                            } else {
                                echo "<div class='alert alert-warning'>No medications found. <a href='?action=addMedication'>Add a medication</a>.</div>";
                                echo "<option value='' disabled>No medications available</option>";
                            }
                        }
                        echo "<!-- Debug: Medications found: $numRows, Error: " . ($result === false ? $db->connection->error : 'None') . " -->";
                    } catch (Exception $e) {
                        error_log("Database error in addPrescription.php: " . $e->getMessage());
                        echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
                        echo "<option value='' disabled>Database error: " . htmlspecialchars($e->getMessage()) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="dosage_instructions" class="form-label">Dosage Instructions</label>
                <input type="text" class="form-control" id="dosage_instructions" name="dosage_instructions" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required min="1" title="Quantity must be at least 1">
            </div>
            <button type="submit" class="btn btn-primary">Add Prescription</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>