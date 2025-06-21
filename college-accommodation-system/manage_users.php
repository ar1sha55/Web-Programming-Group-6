<?php
require 'auth.php';
checkLevel(1); 

// dummy data
$users = [
    ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com', 'role' => 'Student'],
    ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com', 'role' => 'Manager'],
    ['id' => 3, 'name' => 'Charlie', 'email' => 'charlie@example.com', 'role' => 'Admin'],
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="manage_user.css">
</head>
<body>

<div class="dashboard-wrapper">
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


    <div class="dashboard-content">
        <h2 class="welcome-message">Manage Users</h2>

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
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="edit-btn">Edit</a>
                        <a href="delete_user.php?id=<?= $user['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function toggleMenu() {
        const links = document.getElementById("navbar-links");
        links.classList.toggle("active");
    }
</script>



</body>
</html>
