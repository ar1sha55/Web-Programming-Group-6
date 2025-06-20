<?php
require 'auth.php';
checkLevel(2); // 2 = manager
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
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
            <li><a href="manage_dashboard.php">Dashboard</a></li>
            <li><a href="view_applications.php">View and Manage Applications</a></li>
            <li><a href="edit_profile.php">Edit My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Welcome Message -->
    <div class="dashboard-content">
       <h2 class="welcome-message">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> (Manager)</h2>

        <!-- Menu Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <a href="view_applications.php">
                    <span class="card-icon">📝</span>
                    <p>View and Manage Applications</p>
                </a>
            </div>
            <div class="card">
                <a href="edit_profile.php">
                    <span class="card-icon">👤</span>
                    <p>Edit My Profile</p>
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
