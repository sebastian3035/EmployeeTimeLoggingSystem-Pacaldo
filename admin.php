<?php
session_start();
date_default_timezone_set('Asia/Manila');
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success = '';
$error = '';

// Fetch admin profile
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

$admin_name = $admin ? $admin['first_name'] . ' ' . $admin['last_name'] : 'Admin';

// Profile Update
if (isset($_POST['update_profile'])) {
    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $position = trim($_POST['position']);

    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, position = ? WHERE id = ?");
    $stmt->execute([$fname, $lname, $position, $_SESSION['user_id']]);

    $success = "Profile updated successfully.";
    $admin['first_name'] = $fname;
    $admin['last_name'] = $lname;
    $admin['position'] = $position;
    $admin_name = $fname . ' ' . $lname;
}

// Update Username
if (isset($_POST['update_username'])) {
    $new_username = trim($_POST['username']);
    $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
    $stmt->execute([$new_username, $_SESSION['user_id']]);
    $success = "Username updated successfully.";
    $admin['username'] = $new_username;
}

// Update Password
if (isset($_POST['update_password'])) {
    $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->execute([$new_pass, $_SESSION['user_id']]);
    $success = "Password updated successfully.";
}

// Filters
$filter_employee = $_GET['employee'] ?? '';
$filter_date = $_GET['date'] ?? '';
$filter_type = $_GET['type'] ?? '';

$query = "SELECT t.*, u.first_name, u.last_name 
          FROM timelogs t
          JOIN users u ON t.employee_id = u.id
          WHERE 1=1";

$params = [];

if (!empty($filter_employee)) {
    $query .= " AND t.employee_id = ?";
    $params[] = $filter_employee;
}
if (!empty($filter_date)) {
    $query .= " AND t.log_date = ?";
    $params[] = $filter_date;
}
if (!empty($filter_type)) {
    $query .= " AND t.type = ?";
    $params[] = $filter_type;
}

// Sort by log_date and log_time descending
$query .= " ORDER BY t.log_date DESC, t.log_time DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);


// Get all employees
$employees = $pdo->query("SELECT id, first_name, last_name FROM users WHERE role = 'employee'")->fetchAll(PDO::FETCH_ASSOC);

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="timelogs.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Employee', 'Date', 'Time', 'Type']);

    foreach ($logs as $log) {
        fputcsv($output, [
            $log['first_name'] . ' ' . $log['last_name'],
            $log['log_date'],
            $log['log_time'],
            $log['type']
        ]);
    }
    fclose($output);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        height: 38px !important;
        padding: 6px 12px !important;
        width: 100% !important;
        box-sizing: border-box;
    }

    /* Optional: Set a fixed width for the dropdown itself */
    .select2-container {
        width: 100% !important;
        max-width: 100% !important;
    }
</style>

</head>
<body class="bg-light">
<a href="create_employee.php" class="btn btn-primary mb-3">+ Create New Employee</a>
<div class="container mt-5">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <h2>Welcome, Admin <?php echo htmlspecialchars($admin_name); ?></h2>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="logs-tab" data-bs-toggle="tab" href="#logs" role="tab">Employee Time Logs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="edit-tab" data-bs-toggle="tab" href="#edit" role="tab">Edit Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="creds-tab" data-bs-toggle="tab" href="#creds" role="tab">Change Credentials</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Employee Logs -->
        <div class="tab-pane fade show active" id="logs" role="tabpanel">
          <form method="GET" class="row g-2 mb-3 align-items-end">
                <div class="col-md-3">
                    <select name="employee" class="form-select employee-select">
                        <option value="">All Employees</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" <?= ($filter_employee == $emp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filter_date) ?>">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="in" <?= ($filter_type == 'in') ? 'selected' : '' ?>>Time In</option>
                        <option value="out" <?= ($filter_type == 'out') ? 'selected' : '' ?>>Time Out</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="admin.php" class="btn btn-secondary">Clear Filters</a>
                    <a href="?export=csv<?= ($filter_employee ? '&employee=' . $filter_employee : '') ?><?= ($filter_date ? '&date=' . $filter_date : '') ?><?= ($filter_type ? '&type=' . $filter_type : '') ?>" class="btn btn-success">Export CSV</a>
                </div>
            </form>


            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Employee</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($logs): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars($log['first_name'] . ' ' . $log['last_name']) ?></td>
                                <td><?= htmlspecialchars($log['log_date']) ?></td>
                                <td><?= htmlspecialchars($log['log_time']) ?></td>
                                <td><?= htmlspecialchars(ucfirst($log['type'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center">No logs found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Edit Profile -->
        <div class="tab-pane fade" id="edit" role="tabpanel">
            <form method="POST" class="mt-3">
                <div class="mb-2">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($admin['first_name']) ?>" required>
                </div>
                <div class="mb-2">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($admin['last_name']) ?>" required>
                </div>
                <div class="mb-2">
                    <label>Position</label>
                    <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($admin['position'] ?? '') ?>">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>
        </div>

        <!-- Change Credentials -->
        <div class="tab-pane fade" id="creds" role="tabpanel">
            <form method="POST" class="mt-3">
                <div class="mb-2">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($admin['username']) ?>" required>
                </div>
                <button type="submit" name="update_username" class="btn btn-warning mb-3">Update Username</button>
            </form>
            <form method="POST">
                <div class="mb-2">
                    <label>New Password</label>
                    <input type="password" name="new_password" class="form-control" required>
                </div>
                <button type="submit" name="update_password" class="btn btn-danger">Update Password</button>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function () {
        $('.employee-select').select2({
            placeholder: 'All Employees',
            allowClear: true,
            width: '100%'
        });
    });
</script>
</body>
</html>
