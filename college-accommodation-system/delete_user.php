<?php
require 'auth.php';
checkLevel(1); // admin only
require 'db_connect.php';
require_once 'log_helper.php';

if (!isset($_GET['id'])) {
    die("❌ User ID not specified.");
}

$user_id = intval($_GET['id']);

// Get username before deletion for logging
$stmt = $conn->prepare("SELECT username FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("❌ User not found.");
}
$user = $result->fetch_assoc();

//Log before delete
logAction($_SESSION['user_id'], 'delete_user', "Admin {$_SESSION['username']} deleted user {$user['username']} (ID: $user_id)");

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    header("Location: manage_users.php");
    exit;
} else {
    echo "❌ Failed to delete user: " . $stmt->error;
}
