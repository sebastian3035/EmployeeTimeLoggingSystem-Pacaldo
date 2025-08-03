<?php
// create_employee.php

session_start();
require 'db.php';

ini_set('session.save_path', '/tmp'); // for InfinityFree
session_start();
require 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $position = trim($_POST['position']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'employee'; // Explicitly set role

    // Match the full column list including role
    $stmt = $pdo->prepare("INSERT INTO users (username, first_name, last_name, position, password, role) VALUES (?, ?, ?, ?, ?, ?)");
    
    if ($stmt->execute([$username, $first_name, $last_name, $position, $password, $role])) {
        $message = "Employee account created successfully.";
    } else {
        $message = "Failed to create employee account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Employee</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-white">
<div class="container mt-5">
    <h2 class="mb-4">Create New Employee</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-info text-dark bg-white"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <form method="post">
        <div class="mb-3">
            <label>Username</label>
            <input name="username" type="text" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input name="password" type="password" required class="form-control">
        </div>
        <div class="mb-3">
            <label>First Name</label>
            <input name="first_name" type="text" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Last Name</label>
            <input name="last_name" type="text" required class="form-control">
        </div>
        <div class="mb-3">
            <label>Position</label>
            <input name="position" type="text" required class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Create Employee</button>
        <a href="admin.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>
</body>
</html>
