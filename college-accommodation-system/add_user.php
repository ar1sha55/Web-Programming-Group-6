<?php
require 'auth.php';
checkLevel(1); // Admin only
require 'db_connect.php';
require_once 'log_helper.php';

// Form processing starts here (when POST request is made)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name   = trim($_POST['full_name']);
    $username    = trim($_POST['username']);
    $password    = trim($_POST['password']); // plain-text password for now
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone_number']);
    $address     = trim($_POST['address']);
    $user_level  = (int) $_POST['user_level'];

    // Additional fields based on user level
    $matric      = trim($_POST['matric_number'] ?? '');
    $program     = trim($_POST['program'] ?? '');
    $year        = (int) ($_POST['year'] ?? 0);
    $department  = trim($_POST['department'] ?? '');
    $office      = trim($_POST['office_phone'] ?? '');

    // Basic validation
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

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, username, password, email, phone_number, address, user_level) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssi", $full_name, $username, $password, $email, $phone, $address, $user_level);

    if ($stmt->execute()) {
        $newUserId = $conn->insert_id;  // Get the user ID of the newly inserted user

        // Insert role-specific data
        if ($user_level === 2) { // Manager
            $stmt2 = $conn->prepare("INSERT INTO managers (manager_id, department, office_phone) VALUES (?, ?, ?)");
            $stmt2->bind_param("iss", $newUserId, $department, $office);
            $stmt2->execute();
        } elseif ($user_level === 3) { // Student
            $stmt3 = $conn->prepare("INSERT INTO students (student_id, matric_number, program, year) VALUES (?, ?, ?, ?)");
            $stmt3->bind_param("issi", $newUserId, $matric, $program, $year);
            $stmt3->execute();
        }

        // Log action
        logAction($_SESSION['user_id'], 'add_user', "Admin {$_SESSION['username']} added user $username (level $user_level).");

        // Success message
        $_SESSION['user_message'] = "✅ New user $username added successfully.";
        header("Location: manage_users.php"); // Redirect to manage users page
        exit;
    } else {
        // Error handling
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
    <script>
    function showFields() {
        const role = document.querySelector('select[name="user_level"]').value;
        document.getElementById('studentFields').style.display = (role === '3') ? 'block' : 'none';
        document.getElementById('managerFields').style.display = (role === '2') ? 'block' : 'none';
    }
    </script>
</head>
<body>
<h2>➕ Add New User</h2>
<a href="manage_users.php">← Back to User List</a><br><br>

<!-- Display success or error message -->
<?php if (isset($_SESSION['user_message'])): ?>
    <div class="message" style="padding: 10px; background-color: #e0f7fa; border: 1px solid #4caf50; color: #00796b; margin-bottom: 15px;">
        <?= $_SESSION['user_message']; unset($_SESSION['user_message']); ?>
    </div>
<?php endif; ?>

<form method="post">
    <h4>🔒 Account Info</h4>
    Full Name: <input type="text" name="full_name" required><br>
    Username: <input type="text" name="username" required><br>
    Password: <input type="password" name="password" required><br>
    Email: <input type="email" name="email"><br>
    Phone: <input type="text" name="phone_number"><br>
    Address: <textarea name="address"></textarea><br><br>

    <h4>Role:</h4>
    <select name="user_level" onchange="showFields()" required>
        <option value="">-- Select --</option>
        <option value="1">Admin</option>
        <option value="2">Manager</option>
        <option value="3">Student</option>
    </select><br><br>

    <!-- Manager Info -->
    <div id="managerFields" style="display:none;">
        <h4>🏢 Manager Info</h4>
        Department: <input type="text" name="department"><br>
        Office Phone: <input type="text" name="office_phone"><br>
    </div>

    <!-- Student Info -->
    <div id="studentFields" style="display:none;">
        <h4>🎓 Student Info</h4>
        Matric Number: <input type="text" name="matric_number"><br>
        Program: <input type="text" name="program"><br>
        Year: <input type="number" name="year"><br>
    </div>

    <br><input type="submit" value="Add User">
</form>

</body>
</html>
