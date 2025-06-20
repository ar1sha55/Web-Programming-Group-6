<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check user level
function checkLevel($requiredLevel) {
    if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== $requiredLevel) {
        header("Location: index.php");
        exit;
    }
}
?>
