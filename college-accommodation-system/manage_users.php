<?php
require 'auth.php';
checkLevel(1); 

require 'db_connect.php';

$users = [];

$sql = "SELECT user_id, full_name AS name, email,  
        CASE 
            WHEN user_level = 1 THEN 'Admin'
            WHEN user_level = 2 THEN 'Manager'
            WHEN user_level = 3 THEN 'Student'
            ELSE 'Unknown'
        END AS role
        FROM users";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="manage.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-logo">
        <h1>Student College Accommodation System</h1>
    </div>
    <ul class="navbar-links">
        <li><a href="admin_dashboard.php">Dashboard</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="view_logs.php">View System Logs</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="manage-section">
    <div class="manage-container">
        <h2 class="manage-title">Manage Users</h2>

        <div class="user-actions">
            <a href="add_user.php" class="add-btn">+ Add New User</a>
        </div>

        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['user_id'] ?>" class="edit-btn">Edit</a>
                        <a href="delete_user.php?id=<?= $user['user_id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
