<?php
require 'auth.php';
checkLevel(2); // 2 = manager
require 'db_connect.php';

// Fetch applications joined with necessary tables
$result = $conn->query("
    SELECT a.application_id, u.full_name, u.username, c.college_name,
           a.room_type, a.status, a.apply_date
    FROM applications a
    JOIN users u ON a.student_id = u.user_id
    JOIN colleges c ON a.college_id = c.college_id
    ORDER BY a.status ASC, a.apply_date DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Applications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>All Student Applications</h2>

<?php if (isset($_SESSION['app_msg'])): ?>
    <div style="padding: 10px; background-color: #fffde7; border: 1px solid #fbc02d; margin-bottom: 15px;">
        <?= $_SESSION['app_msg']; unset($_SESSION['app_msg']); ?>
    </div>
<?php endif; ?>

<table border="1" cellpadding="8">
    <tr>
        <th>Student</th>
        <th>Username</th>
        <th>College</th>
        <th>Applied On</th>
        <th>Room Type</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['college_name']) ?></td>
            <td><?= htmlspecialchars($row['apply_date']) ?></td>
            <td><?= htmlspecialchars($row['room_type']) ?></td>
            <td><strong><?= ucfirst(htmlspecialchars($row['status'])) ?></strong></td>
            <td>
                <?php if ($row['status'] === 'pending'): ?>
                    <form method="post" action="process_application.php" style="display:inline;">
                        <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                        <input type="hidden" name="action" value="approve">
                        <input type="submit" value="Approve">
                    </form>
                    <form method="post" action="process_application.php" style="display:inline;">
                        <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                        <input type="hidden" name="action" value="reject">
                        <input type="submit" value="Reject">
                    </form>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<a href="manager_dashboard.php">← Back to Dashboard</a>
</body>
</html>
