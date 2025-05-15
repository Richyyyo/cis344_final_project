<?php
session_start();

?>
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
        <h2 id="form-title">Add Prescription</h2>
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
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['flash_message']); unset($_SESSION['flash_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['flash_error'])): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
        <?php endif; ?>
        <form method="POST" action="?action=addPrescription" aria-labelledby="form-title">
            <div class="mb-3">
                <label for="patient_username" class="form-label">Patient Username</label>
                <input type="text" class="form-control" id="patient_username" name="patient_username" required pattern="[A-Za-z0-9_]{3,45}" title="Username must be 3-45 characters, letters, numbers, or underscores" aria-describedby="patient_username_help">
                <div id="patient_username_help" class="form-text">Enter the patient’s username .</div>
            </div>
            <div class="mb-3">
                <label for="medication_id" class="form-label">Medication</label>
                <select class="form-control" id="medication_id" name="medication_id" required aria-describedby="medication_id_help">
                    <option value="" disabled selected>Select a medication</option>
                    <?php
                    if (!empty($Medications)) {
                        foreach ($Medications as $medication) {
                            echo "<option value='" . htmlspecialchars($medication['medicationId']) . "'>" .
                                 "ID: " . htmlspecialchars($medication['medicationId']) . " - " .
                                 htmlspecialchars($medication['medicationName']) . " (" .
                                 htmlspecialchars($medication['dosage']) . ")</option>";
                        }
                    } else {
                        echo "<option value='' disabled>No medications available</option>";
                    }
                    ?>
                </select>
                <div id="medication_id_help" class="form-text">Select a medication from the inventory.</div>
            </div>
            <div class="mb-3">
                <label for="dosage_instructions" class="form-label">Dosage Instructions</label>
                <input type="text" class="form-control" id="dosage_instructions" name="dosage_instructions" required aria-describedby="dosage_instructions_help">
                <div id="dosage_instructions_help" class="form-text">Specify dosage instructions (e.g., "Take 1 tablet daily").</div>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required min="1" title="Quantity must be at least 1" aria-describedby="quantity_help">
                <div id="quantity_help" class="form-text">Enter the quantity to prescribe.</div>
            </div>
            <div class="mb-3">
                <label for="refill_count" class="form-label">Refills</label>
                <input type="number" class="form-control" id="refill_count" name="refill_count" required min="0" value="0" title="Number of refills must be 0 or more" aria-describedby="refill_count_help">
                <div id="refill_count_help" class="form-text">Enter the number of refills allowed (0 for no refills).</div>
            </div>
            <button type="submit" class="btn btn-primary">Add Prescription</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>