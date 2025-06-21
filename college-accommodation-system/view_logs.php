<?php
require_once 'auth.php';
checkLevel(1); // admin only
require_once 'db_connect.php';

$query = "
    SELECT hl.*, u.username, u.user_level
    FROM history_log hl
    LEFT JOIN users u ON hl.user_id = u.user_id
    ORDER BY hl.action_time DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>System Activity Logs</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="logs.css">
</head>
<body>

<div class="dashboard-wrapper">

    <!-- === Navbar === -->
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
        <h2 class="welcome-message">System Activity Logs</h2>
        <div style="text-align: left; width: 90%; margin: 0 auto;">
        <a href="admin_dashboard.php">← Back to Admin Dashboard</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Log ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Action Type</th>
                    <th>Description</th>
                    <th>Timestamp</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td data-label="Log ID"><?= $row['log_id'] ?></td>
                        <td data-label="Username"><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
                        <td data-label="Role">
                            <?php
                                switch ($row['user_level']) {
                                    case 1: echo 'Admin'; break;
                                    case 2: echo 'Manager'; break;
                                    case 3: echo 'Student'; break;
                                    default: echo 'Unknown';
                                }
                            ?>
                        </td>
                        <td data-label="Action Type"><?= htmlspecialchars($row['action_type']) ?></td>
                        <td data-label="Description"><?= htmlspecialchars($row['description']) ?></td>
                        <td data-label="Timestamp"><?= $row['action_time'] ?></td>
                    </tr>
                <?php endwhile; ?>
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
