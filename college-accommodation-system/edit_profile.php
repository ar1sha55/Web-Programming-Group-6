<?php
require 'auth.php';
require 'db_connect.php';

$user_id = $_SESSION['user_id'];
$user_level = $_SESSION['user_level'];

$stmt = $conn->prepare("SELECT full_name, email, phone_number, address FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userResult = $stmt->get_result();
if ($userResult->num_rows === 0) {
    die("❌ User not found.");
}
$user = $userResult->fetch_assoc();

$extra = [];
if ($user_level === 3) {
    $stmt = $conn->prepare("SELECT matric_number, program, year FROM students WHERE student_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $extra = $stmt->get_result()->fetch_assoc();
} elseif ($user_level === 2) {
    $stmt = $conn->prepare("SELECT department, office_phone FROM managers WHERE manager_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $extra = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="editprofile.css">
  <script src="form_validation_editprofile.js" defer></script>
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

<div class="edit-container">
    <h2 class="edit-title">Edit My Profile</h2>

    <?php if (isset($_SESSION['profile_update_success'])): ?>
      <div class="edit-success"><?= $_SESSION['profile_update_success'] ?></div>
      <?php unset($_SESSION['profile_update_success']); ?>
    <?php endif; ?>

    <form class="edit-form" method="post" action="update_profile.php">
      <fieldset>
        <legend>🔒 Account Info</legend>
        <label>Full Name:</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

        <label>Phone:</label>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>">

        <label>Address:</label>
        <textarea name="address"><?= htmlspecialchars($user['address']) ?></textarea>
      </fieldset>

      <?php if ($user_level === 3): ?>
        <fieldset>
          <legend>🎓 Student Info</legend>
          <p><strong>Matric Number:</strong> <?= htmlspecialchars($extra['matric_number']) ?></p>
          <p><strong>Program:</strong> <?= htmlspecialchars($extra['program']) ?></p>
          <p><strong>Year:</strong> <?= htmlspecialchars($extra['year']) ?></p>
        </fieldset>
      <?php elseif ($user_level === 2): ?>
        <fieldset>
          <legend>🏢 Manager Info</legend>
          <p><strong>Department:</strong> <?= htmlspecialchars($extra['department']) ?></p>
          <p><strong>Office Phone:</strong> <?= htmlspecialchars($extra['office_phone']) ?></p>
        </fieldset>
      <?php endif; ?>

      <input class="edit-submit" type="submit" value="Update My Profile">
    </form>

    <div class="edit-back">
        <a href="<?= $user_level === 2 ? 'manager_dashboard.php' : 'student_dashboard.php' ?>">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
