<?php
require 'auth.php';
checkLevel(3); // 3 = student
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
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
            <li><a href="student_dashboard.php">Dashboard</a></li>
            <li><a href="apply_accommodation.php">Apply for Accommodation</a></li>
            <li><a href="view_my_application.php">View My Application</a></li>
            <li><a href="edit_profile.php">Edit My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <!-- Welcome Message -->
    <div class="dashboard-content">
       <h2 class="welcome-message">Welcome, <?= htmlspecialchars($_SESSION['full_name']) ?> (Student)</h2>

        <!-- Menu Cards -->
        <div class="dashboard-cards">
            <div class="card">
                <a href="apply_accommodation.php">
                    <span class="card-icon">📝</span>
                    <p>Apply for Accommodation</p>
                </a>
            </div>
            <div class="card">
                <a href="view_my_application.php">
                    <span class="card-icon">📄</span>
                    <p>View My Application</p>
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
