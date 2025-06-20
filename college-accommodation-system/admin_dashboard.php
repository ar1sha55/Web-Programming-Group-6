<?php
require 'auth.php';
checkLevel(1); // 1 = admin
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

<div class="dashboard-wrapper">
    <!-- Navbar -->
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

    <!-- Welcome Message -->
    <div class="dashboard-content">
       <h2 class="welcome-message">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> (Admin)</h2>

        <!-- Menu Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <a href="manage_users.php">
                    <span class="card-icon">📝</span>
                    <p>Manage Users</p>
                </a>
            </div>
            <div class="card">
                <a href="view_logs.php">
                    <span class="card-icon">📄</span>
                    <p>View System Logs</p>
                </a>
            </div>
            <div class="card">
                <a href="logout.php">
                    <span class="card-icon">🚪</span>
                    <p>Logout</p>
                </a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
