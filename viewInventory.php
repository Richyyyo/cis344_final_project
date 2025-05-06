
<!DOCTYPE html>
<html>
<head><title>Inventory</title></head>
<body>
    <h1>Medication Inventory</h1>
    <table border="1">
        <tr><th>Name</th><th>Dosage</th><th>Manufacturer</th><th>Quantity</th></tr>
            <tr>
                <td><?= htmlspecialchars($item['medicationName']) ?></td>
                <td><?= htmlspecialchars($item['dosage']) ?></td>
                <td><?= htmlspecialchars($item['manufacturer']) ?></td>
                <td><?= htmlspecialchars($item['quantityAvailable']) ?></td>
            </tr>
       
    </table>
</body>
</html>
