<?php
require 'auth.php';
checkLevel(3);
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
$latest_app = $stmt->get_result()->fetch_assoc();

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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>My Applications</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="viewstudent.css">
</head>
<body>

  <nav class="navbar">
    <div class="navbar-logo"><h1>Student College Accommodation System</h1></div>
    <ul class="navbar-links">
      <li><a href="student_dashboard.php">Dashboard</a></li>
      <li><a href="apply_accommodation.php">Apply</a></li>
      <li><a href="view_my_application.php">My Applications</a></li>
      <li><a href="edit_profile.php">Profile</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

<div class="app-container">
    <h2 class="app-title">Latest Accommodation Application</h2>

    <?php if ($latest_app): ?>
      <div class="app-card">
        <p><strong>College:</strong> <?=htmlspecialchars($latest_app['college_name'])?></p>
        <p><strong>Applied On:</strong> <?=htmlspecialchars($latest_app['apply_date'])?></p>
        <p><strong>Room Type:</strong> <?=htmlspecialchars($latest_app['room_type'])?></p>
        <p><strong>Status:</strong> <span class="status"><?=ucfirst(htmlspecialchars($latest_app['status']))?></span></p>
        <p><strong>Remarks:</strong><br><?=nl2br(htmlspecialchars($latest_app['remarks']))?></p>
      </div>
    <?php else: ?>
      <div class="app-alert">⚠️ No application found.</div>
    <?php endif; ?>

    <h2 class="app-title">Application History</h2>
    <?php if ($all_result->num_rows > 0): ?>
      <div class="table-wrap">
        <table class="app-table">
          <thead>
            <tr>
              <th>Applied On</th>
              <th>College</th>
              <th>Room Type</th>
              <th>Status</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $all_result->fetch_assoc()): ?>
              <tr>
                <td><?=htmlspecialchars($row['apply_date'])?></td>
                <td><?=htmlspecialchars($row['college_name'])?></td>
                <td><?=htmlspecialchars($row['room_type'])?></td>
                <td><span class="status"><?=ucfirst(htmlspecialchars($row['status']))?></span></td>
                <td><?=nl2br(htmlspecialchars($row['remarks']))?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <div class="app-alert">No previous application records found.</div>
    <?php endif; ?>

    <div class="app-back">
        <a href="student_dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
