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
<html>
<head>
    <title>Activity Logs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>System Activity Logs</h2>
<a href="admin_dashboard.php">← Back to Admin Dashboard</a>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>Log ID</th>
        <th>Username</th>
        <th>Role</th>
        <th>Action Type</th>
        <th>Description</th>
        <th>Timestamp</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['log_id'] ?></td>
        <td><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
        <td>
            <?php
                switch ($row['user_level']) {
                    case 1: echo 'Admin'; break;
                    case 2: echo 'Manager'; break;
                    case 3: echo 'Student'; break;
                    default: echo 'Unknown';
                }
            ?>
        </td>
        <td><?= $row['action_type'] ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= $row['action_time'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
