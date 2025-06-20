<?php
require 'auth.php';
checkLevel(2); // Manager
require 'db_connect.php';
require_once 'log_helper.php';

session_start();

$application_id = intval($_POST['application_id']);
$action = $_POST['action'];
$manager_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get application info
$stmt = $conn->prepare("
    SELECT a.student_id, a.college_id, a.room_type
    FROM applications a
    WHERE a.application_id = ? AND a.status = 'pending'
");
$stmt->bind_param("i", $application_id);
$stmt->execute();
$app = $stmt->get_result()->fetch_assoc();

if (!$app) {
    $_SESSION['app_msg'] = "⚠️ Application not found or already processed.";
    header("Location: view_applications.php");
    exit;
}

$student_id = $app['student_id'];
$college_id = $app['college_id'];
$room_type = $app['room_type'];

if ($action === 'approve') {
    // Check room availability
    $check = $conn->prepare("SELECT available_beds FROM rooms WHERE college_id = ? AND room_type = ?");
    $check->bind_param("is", $college_id, $room_type);
    $check->execute();
    $room = $check->get_result()->fetch_assoc();

    if (!$room || $room['available_beds'] <= 0) {
        $_SESSION['app_msg'] = "❌ Cannot approve. No available $room_type rooms in this college.";
        header("Location: view_applications.php");
        exit;
    }

    // 1. Update application status
    $stmt = $conn->prepare("UPDATE applications SET status = 'approved', remarks = 'Approved by manager' WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();

    // 2. Decrease available_beds by 1
    $updateBeds = $conn->prepare("UPDATE rooms SET available_beds = available_beds - 1 WHERE college_id = ? AND room_type = ?");
    $updateBeds->bind_param("is", $college_id, $room_type);
    $updateBeds->execute();

    // 3. Sync college slots
    $conn->query("
        UPDATE colleges c
        JOIN (
            SELECT college_id,
                   SUM(available_beds) AS slots
            FROM rooms
            GROUP BY college_id
        ) r ON c.college_id = r.college_id
        SET c.available_slots = r.slots
    ");

    // 4. Insert approval record
    $date = date('Y-m-d');
    $stmt = $conn->prepare("INSERT INTO accommodation_records (student_id, college_id, approved_by, approve_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $student_id, $college_id, $manager_id, $date);
    $stmt->execute();

    // 5. Log action
    logAction($manager_id, 'approve', "Manager $username approved application ID $application_id.");
    $_SESSION['app_msg'] = "✅ Application approved.";

} elseif ($action === 'reject') {
    $stmt = $conn->prepare("UPDATE applications SET status = 'rejected', remarks = 'Rejected by manager' WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();

    logAction($manager_id, 'reject', "Manager $username rejected application ID $application_id.");
    $_SESSION['app_msg'] = "⚠️ Application rejected.";

} else {
    $_SESSION['app_msg'] = "❌ Invalid action.";
}

header("Location: view_applications.php");
exit;
