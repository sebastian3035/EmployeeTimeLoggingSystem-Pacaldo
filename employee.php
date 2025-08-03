<?php
session_start();
date_default_timezone_set('Asia/Manila');
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Time In/Out log
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_type'])) {
    $type = $_POST['log_type'];
    $date = date('Y-m-d');
    $time = date('H:i:s');

    $stmt = $pdo->prepare("INSERT INTO timelogs (employee_id, log_date, log_time, type) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $date, $time, $type]);
    $success = "Time $type recorded.";
}

// Profile update 
if (isset($_POST['update_profile'])) {
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $position = trim($_POST['position']);

    // Update the profile
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, position = ? WHERE id = ?");
    $stmt->execute([$fname, $lname, $position, $user_id]);
    $success = "Profile updated successfully.";

    // Refresh user data
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}


// Password update
if (isset($_POST['update_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (!password_verify($current, $user['password'])) {
        $error = "Current password is incorrect.";
    } elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    } elseif (empty($new)) {
        $error = "New password cannot be empty.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $user_id]);
        $success = "Password updated successfully.";
    }
}

// Username update
if (isset($_POST['update_username'])) {
    $new_username = trim($_POST['new_username']);
    if ($new_username && $new_username !== $user['username']) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
        $stmt->execute([$new_username, $user_id]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Username already taken.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$new_username, $user_id]);
            $_SESSION['username'] = $new_username;
            $success = "Username updated successfully.";
        }
    } else {
        $error = "No change in username.";
    }
}

// Time log filtering
$filter_date = $_GET['filter_date'] ?? '';
$stmt = $pdo->prepare($filter_date ?
    "SELECT * FROM timelogs WHERE employee_id = ? AND log_date = ? ORDER BY log_date DESC, log_time DESC" :
    "SELECT * FROM timelogs WHERE employee_id = ? ORDER BY log_date DESC, log_time DESC");
$stmt->execute($filter_date ? [$user_id, $filter_date] : [$user_id]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Welcome, <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php if ($error): ?><div class="alert alert-danger"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

    <ul class="nav nav-tabs" id="myTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#logs">Time Logs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#profile">Edit Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#credentials">Change Credentials</a>
        </li>
    </ul>

    <div class="tab-content border p-3 bg-white">
        <div class="tab-pane fade show active" id="logs">
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>" class="form-control">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="employee.php" class="btn btn-secondary">Clear</a>
                </div>
            </form>

            <form method="POST" class="mb-3 d-flex gap-2">
                <button name="log_type" value="in" class="btn btn-success">Time In</button>
                <button name="log_type" value="out" class="btn btn-danger">Time Out</button>
            </form>

            <?php if ($logs): ?>
                <table class="table table-bordered">
                    <thead><tr><th>Date</th><th>Time</th><th>Type</th></tr></thead>
                    <tbody>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td><?php echo $log['log_date']; ?></td>
                            <td><?php echo $log['log_time']; ?></td>
                            <td><?php echo ucfirst($log['type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No logs found.</p>
            <?php endif; ?>
        </div>

        <div class="tab-pane fade" id="profile">
            <form method="POST">
                <input type="hidden" name="update_profile" value="1">
                <div class="mb-3">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Position</label>
                    <input type="text" name="position" value="<?php echo htmlspecialchars($user['position']); ?>" class="form-control">
                </div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <div class="tab-pane fade" id="credentials">
            <div class="row">
                <div class="col-md-6">
                    <h5>Change Password</h5>
                    <form method="POST">
                        <input type="hidden" name="update_password" value="1">
                        <div class="mb-3">
                            <label>Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Update Password</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <h5>Change Username</h5>
                    <form method="POST">
                        <input type="hidden" name="update_username" value="1">
                        <div class="mb-3">
                            <label>New Username</label>
                            <input type="text" name="new_username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-info">Update Username</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
