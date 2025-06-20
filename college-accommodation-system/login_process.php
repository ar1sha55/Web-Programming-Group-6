<?php
session_start();
require 'db_connect.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    // Compare plain text password (replace with password_verify() for security)
    if ($user['password'] === $password) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_level'] = (int)$user['user_level'];
        $_SESSION['full_name'] = $user['full_name'];

        // Log login activity
        require_once 'log_helper.php';
        logAction($user['user_id'], 'login', "User {$user['username']} logged in.");

        // Redirect by user_level
        if ($user['user_level'] === 1) {
            header("Location: admin_dashboard.php");
        } elseif ($user['user_level'] === 2) {
            header("Location: manager_dashboard.php");
        } elseif ($user['user_level'] === 3) {
            header("Location: student_dashboard.php");
        } else {
            $_SESSION['login_error'] = "Unknown user level.";
            header("Location: login.php");
        }
        exit;
    }
}

// Invalid login
$_SESSION['login_error'] = "Invalid username or password.";
header("Location: index.php");
exit;
