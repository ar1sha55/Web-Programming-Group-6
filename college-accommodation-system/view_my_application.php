<?php
require 'auth.php';
checkLevel(3); // student level
require 'db_connect.php';

$student_id = $_SESSION['user_id'];

// Fetch latest application
$stmt = $conn->prepare("
    SELECT a.status, a.apply_date, c.college_name, a.remarks, a.room_type
    FROM applications a
    JOIN colleges c ON a.college_id = c.college_id
    WHERE a.student_id = ?
    ORDER BY a.application_id DESC
    LIMIT 1
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$latest_result = $stmt->get_result();
$latest_app = $latest_result->fetch_assoc();

// Fetch all applications
$all_stmt = $conn->prepare("
    SELECT a.apply_date, c.college_name, a.room_type, a.status, a.remarks
    FROM applications a
    JOIN colleges c ON a.college_id = c.college_id
    WHERE a.student_id = ?
    ORDER BY a.application_id DESC
");
$all_stmt->bind_param("i", $student_id);
$all_stmt->execute();
$all_result = $all_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Applications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Latest Accommodation Application</h2>

<?php if ($latest_app): ?>
    <p><strong>College:</strong> <?= htmlspecialchars($latest_app['college_name']) ?></p>
    <p><strong>Applied On:</strong> <?= htmlspecialchars($latest_app['apply_date']) ?></p>
    <p><strong>Room Type:</strong> <?= htmlspecialchars($latest_app['room_type']) ?></p>
    <p><strong>Status:</strong> <strong style="color:blue"><?= ucfirst(htmlspecialchars($latest_app['status'])) ?></strong></p>
    <p><strong>Remarks:</strong> <?= nl2br(htmlspecialchars($latest_app['remarks'])) ?></p>
<?php else: ?>
    <p>⚠️ No application found.</p>
<?php endif; ?>

<hr>
<h2>All Application History</h2>

<?php if ($all_result->num_rows > 0): ?>
    <table border="1" cellpadding="8">
        <tr>
            <th>Applied On</th>
            <th>College</th>
            <th>Room Type</th>
            <th>Status</th>
            <th>Remarks</th>
        </tr>
        <?php while ($row = $all_result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['apply_date']) ?></td>
                <td><?= htmlspecialchars($row['college_name']) ?></td>
                <td><?= htmlspecialchars($row['room_type']) ?></td>
                <td><strong><?= ucfirst($row['status']) ?></strong></td>
                <td><?= nl2br(htmlspecialchars($row['remarks'])) ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>No previous application records found.</p>
<?php endif; ?>

<a href="student_dashboard.php">← Back to Dashboard</a>
</body>
</html>
