<?php
require 'auth.php';
checkLevel(1); // Admin only
require 'db_connect.php';
require_once 'log_helper.php';

// Form processing starts here
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = trim($_POST['full_name']);
    $username    = trim($_POST['username']);
    $password    = trim($_POST['password']); // plain-text for now
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone_number']);
    $address     = trim($_POST['address']);
    $user_level  = (int) $_POST['user_level'];

    $matric      = trim($_POST['matric_number'] ?? '');
    $program     = trim($_POST['program'] ?? '');
    $year        = (int) ($_POST['year'] ?? 0);
    $department  = trim($_POST['department'] ?? '');
    $office      = trim($_POST['office_phone'] ?? '');

    if (!$full_name || !$username || !$password || !$user_level) {
        $_SESSION['user_message'] = "⚠️ All fields except email are required.";
        header("Location: add_user.php");
        exit;
    }

    if (!in_array($user_level, [1, 2, 3])) {
        $_SESSION['user_message'] = "❌ Invalid user level.";
        header("Location: add_user.php");
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, email, phone_number, address, user_level) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $full_name, $username, $password, $email, $phone, $address, $user_level);

    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;

        if ($user_level === 2) {
            $stmt2 = $conn->prepare("INSERT INTO managers (manager_id, department, office_phone) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $newUserId, $department, $office);
            $stmt2->execute();
        } elseif ($user_level === 3) {
            $stmt3 = $conn->prepare("INSERT INTO students (student_id, matric_number, program, year) VALUES (?, ?, ?, ?)");
            $stmt3->bind_param("issi", $newUserId, $matric, $program, $year);
            $stmt3->execute();
        }

        logAction($_SESSION['user_id'], 'add_user', "Admin {$_SESSION['username']} added user $username (level $user_level).");

        $_SESSION['user_message'] = "✅ New user $username added successfully.";
        header("Location: manage_users.php");
        exit;
    } else {
        $_SESSION['user_message'] = "❌ Failed to add user: " . $stmt->error;
        header("Location: add_user.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New User</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="add_users.css">
    <script>
        function showFields() {
            const role = document.querySelector('select[name="user_level"]').value;
            document.getElementById('studentFields').style.display = (role === '3') ? 'block' : 'none';
            document.getElementById('managerFields').style.display = (role === '2') ? 'block' : 'none';
        }
    </script>
</head>
<body>

<!-- Shared Navbar -->
<nav class="navbar">
    <div class="navbar-logo">
        <h1>Student College Accommodation System</h1>
    </div>
    <div class="hamburger" onclick="toggleMenu()">☰</div>
    <ul class="navbar-links" id="navbar-links">
        <li><a href="manager_dashboard.php">Dashboard</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="view_logs.php">View System Logs</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<script>
    function toggleMenu() {
        const links = document.getElementById("navbar-links");
        links.classList.toggle("active");
    }
</script>

<!-- Main Container -->
<div class="container">
    <h2>➕ Add New User</h2>
    <a href="manage_users.php">← Back to User List</a>

    <?php if (isset($_SESSION['user_message'])): ?>
        <div class="message">
            <?= $_SESSION['user_message']; unset($_SESSION['user_message']); ?>
        </div>
    <?php endif; ?>

    <form method="post">
    <h4>🔒 Account Info</h4>
    <div class="form-row">
        <div class="form-group">
            <label>Full Name:</label>
            <input type="text" name="full_name" required>
        </div>
        <div class="form-group">
            <label>Username:</label>
            <input type="text" name="username" required>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label>Phone:</label>
            <input type="text" name="phone_number">
        </div>
        <div class="form-group">
            <label>Address:</label>
            <textarea name="address"></textarea>
        </div>
    </div>

    <div class="form-row">
        <div class="form-group full">
            <label>Role:</label>
            <select name="user_level" onchange="showFields()" required>
                <option value="">-- Select --</option>
                <option value="1">Admin</option>
                <option value="2">Manager</option>
                <option value="3">Student</option>
            </select>
        </div>
    </div>

    <!-- Manager Info -->
    <div id="managerFields" style="display:none;">
        <h4>🏢 Manager Info</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Department:</label>
                <input type="text" name="department">
            </div>
            <div class="form-group">
                <label>Office Phone:</label>
                <input type="text" name="office_phone">
            </div>
        </div>
    </div>

    <!-- Student Info -->
    <div id="studentFields" style="display:none;">
        <h4>🎓 Student Info</h4>
        <div class="form-row">
            <div class="form-group">
                <label>Matric Number:</label>
                <input type="text" name="matric_number">
            </div>
            <div class="form-group">
                <label>Program:</label>
                <input type="text" name="program">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group full">
                <label>Year:</label>
                <input type="number" name="year">
            </div>
        </div>
    </div>

    <input type="submit" value="Add User">
</form>

</div>

</body>
</html>
