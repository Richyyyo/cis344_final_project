<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacy Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome to the Pharmacy Portal</h1>
        <p>Please choose an option to continue:</p>
        <nav class="mt-3">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="?action=addUser" class="nav-link">Create User</a>
                </li>
                <li class="nav-item">
                    <a href="?action=login" class="nav-link">Login</a>
                </li>
            </ul>
        </nav>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>