<?php
require_once 'db_connect.php';

function logAction($user_id, $action_type, $description) {
    global $conn;

    //Make sure the user exists
    $check = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $check->bind_param("i", $user_id);
    $check->execute();
    $result = $check->get_result();
    
    if ($result->num_rows === 0) {
        // User no longer exists, skip logging
        return;
    }

    // Optional: validate input types before binding
    $user_id = (int)$user_id;
    $action_type = trim($action_type);
    $description = trim($description);

    $stmt = $conn->prepare("INSERT INTO history_log (user_id, action_type, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action_type, $description);
    $stmt->execute();

    // Optional: handle logging errors silently or log to a file
}