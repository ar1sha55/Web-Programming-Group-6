<?php 
session_start(); 

// Redirect to dashboard if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_level'] === 1) {
        header("Location: admin_dashboard.php");
        exit;
    } elseif ($_SESSION['user_level'] === 2) {
        header("Location: manager_dashboard.php");
        exit;
    } elseif ($_SESSION['user_level'] === 3) {
        header("Location: student_dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>
<div class="login-wrapper">
    <!-- System Name Section -->
    <div class="system-name">
        <h1>Student College Accommodation System</h1>
    </div>

    <!-- Login Container -->
    <div class="login-container">
        <h2>User Login</h2>

        <!-- Display login error message if any -->
        <?php if (isset($_SESSION['login_error'])): ?>
            <div class="alert error">
                <?= $_SESSION['login_error']; ?>
            </div>
            <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="post" action="login_process.php">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required placeholder="Enter your username">

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required placeholder="Enter your password">

            <input type="submit" value="Login">
        </form>
    </div>
<div>
</body>

</html>
