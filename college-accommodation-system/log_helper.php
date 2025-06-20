<?php
require_once 'db_connect.php';

function logAction($user_id, $action_type, $description) {
    global $conn;

    // Optional: validate input types before binding
    $user_id = (int)$user_id;
    $action_type = trim($action_type);
    $description = trim($description);

    $stmt = $conn->prepare("INSERT INTO history_log (user_id, action_type, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action_type, $description);
    $stmt->execute();

    // Optional: handle logging errors silently or log to a file
}
