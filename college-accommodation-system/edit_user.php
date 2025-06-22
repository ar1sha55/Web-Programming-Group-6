<?php
require 'auth.php';
checkLevel(1); // Admin only
require 'db_connect.php';
require_once 'log_helper.php';

// Get the user_id from the URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($user_id <= 0) {
    die("⚠️ Invalid user ID.");
}

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();

if ($userResult->num_rows === 0) {
    die("❌ User not found.");
}

$user = $userResult->fetch_assoc();

// Fetch role-specific info
$extra = [];
if ($user['user_level'] === 3) { // Student
    $stmt = $conn->prepare("SELECT matric_number, program, year FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $extra = $stmt->get_result()->fetch_assoc();
} elseif ($user['user_level'] === 2) { // Manager
    $stmt = $conn->prepare("SELECT department, office_phone FROM managers WHERE manager_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $extra = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated data from the form
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone_number']);
    $address = trim($_POST['address']);
    
    $matric = trim($_POST['matric_number'] ?? '');
    $program = trim($_POST['program'] ?? '');
    $year = (int)($_POST['year'] ?? 0);
    $department = trim($_POST['department'] ?? '');
    $office = trim($_POST['office_phone'] ?? '');

    // Basic validation
    if (!$full_name || !$username || !$user['user_level']) {
        $_SESSION['user_message'] = "⚠️ All fields except email are required.";
        header("Location: edit_user.php?id=$user_id");
        exit;
    }

    // Update the user
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, password = ?, email = ?, phone_number = ?, address = ? WHERE user_id = ?");
    $stmt->bind_param("ssssssi", $full_name, $username, $password, $email, $phone, $address, $user_id);

    if ($stmt->execute()) {
        // Update role-specific data
        if ($user['user_level'] === 2) { // Manager
            $stmt2 = $conn->prepare("UPDATE managers SET department = ?, office_phone = ? WHERE manager_id = ?");
            $stmt2->bind_param("ssi", $department, $office, $user_id);
            $stmt2->execute();
        } elseif ($user['user_level'] === 3) { // Student
            $stmt3 = $conn->prepare("UPDATE students SET matric_number = ?, program = ?, year = ? WHERE student_id = ?");
            $stmt3->bind_param("ssii", $matric, $program, $year, $user_id);
            $stmt3->execute();
        }

        // Log action
        logAction($_SESSION['user_id'], 'edit_user', "Admin {$_SESSION['username']} edited user $username (level {$user['user_level']}).");

        // Success message
        $_SESSION['user_message'] = "✅ User $username updated successfully.";
        header("Location: manage_users.php"); // Redirect back to user list
        exit;
    } else {
        $_SESSION['user_message'] = "❌ Failed to update user: " . $stmt->error;
        header("Location: edit_user.php?id=$user_id");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-logo">
        <h1>Student College Accommodation System</h1>
    </div>
    <ul class="navbar-links">
        <li><a href="manager_dashboard.php">Dashboard</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="view_logs.php">View System Logs</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="edit-section">
    <div class="edit-container">
        <h2 class="edit-title">✏️ Edit User</h2>
        <div class="edit-back"><a href="manage_users.php">← Back to User List</a></div>

        <?php if (isset($_SESSION['user_message'])): ?>
        <div class="edit-alert">
            <?= $_SESSION['user_message']; unset($_SESSION['user_message']); ?>
        </div>
        <?php endif; ?>

        <form method="post" class="edit-form">
            <label>Full Name:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label>Password:</label>
            <input type="password" name="password" value="<?= htmlspecialchars($user['password']) ?>">

            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

            <label>Phone:</label>
            <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">

            <label>Address:</label>
            <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea>

            <h4>Role: 
                <strong><?= $user['user_level'] == 1 ? 'Admin' : ($user['user_level'] == 2 ? 'Manager' : 'Student') ?></strong>
            </h4>

            <?php if ($user['user_level'] === 2): ?>
                <label>Department:</label>
                <input type="text" name="department" value="<?= htmlspecialchars($extra['department']) ?>">

                <label>Office Phone:</label>
                <input type="text" name="office_phone" value="<?= htmlspecialchars($extra['office_phone']) ?>">
            <?php endif; ?>

            <?php if ($user['user_level'] === 3): ?>
                <label>Matric Number:</label>
                <input type="text" name="matric_number" value="<?= htmlspecialchars($extra['matric_number']) ?>">

                <label>Program:</label>
                <input type="text" name="program" value="<?= htmlspecialchars($extra['program']) ?>">

                <label>Year:</label>
                <input type="number" name="year" value="<?= htmlspecialchars($extra['year']) ?>">
            <?php endif; ?>

            <input type="submit" value="Update User" class="edit-button">
        </form>
    </div>
</div>
<script src="form_validation.js"></script>

</body>
</html>
