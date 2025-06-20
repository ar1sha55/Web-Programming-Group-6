<?php
require 'auth.php';
checkLevel(3); // 3 = student
require 'db_connect.php';

$student_id = $_SESSION['user_id'];
$college_id = intval($_POST['college_id']);
$room_type = $_POST['room_type'];
$apply_date = date('Y-m-d');

// Check for any pending application
$check = $conn->prepare("SELECT * FROM applications WHERE student_id = ? AND status = 'pending'");
$check->bind_param("i", $student_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // Show error if there's already a pending application
    $_SESSION['application_success'] = "⚠️ You already have a pending application.";
    header("Location: apply_accommodation.php");
    exit;
}

// Check if the student has an approved application and block re-application
$check_approved = $conn->prepare("SELECT * FROM applications WHERE student_id = ? AND status = 'approved'");
$check_approved->bind_param("i", $student_id);
$check_approved->execute();
$res_approved = $check_approved->get_result();

if ($res_approved->num_rows > 0) {
    // If approved, prevent the student from applying again
    $_SESSION['application_success'] = "❌ You already have an approved application. You cannot apply again until your accommodation status is reset.";
    header("Location: apply_accommodation.php");
    exit;
}

// Check room availability
$availability_check = $conn->prepare("SELECT available_beds FROM rooms WHERE college_id = ? AND room_type = ?");
$availability_check->bind_param("is", $college_id, $room_type);
$availability_check->execute();
$availability_result = $availability_check->get_result();
$row = $availability_result->fetch_assoc();

if (!$row || $row['available_beds'] <= 0) {
    $_SESSION['application_success'] = "❌ Selected room type is not available at this college.";
    header("Location: apply_accommodation.php");
    exit;
}

// Insert application
$stmt = $conn->prepare("INSERT INTO applications (student_id, college_id, room_type, apply_date) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiss", $student_id, $college_id, $room_type, $apply_date);

if ($stmt->execute()) {
    // Don't update available_beds yet, wait for manager's approval

    // Sync available_slots
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

    // Log action
    require_once 'log_helper.php';
    logAction($student_id, 'apply', "Student {$_SESSION['username']} applied for college ID $college_id, room type $room_type.");

    $_SESSION['application_success'] = "✅ Your application has been submitted successfully.";
    header("Location: apply_accommodation.php");
    exit;
} else {
    $_SESSION['application_success'] = "❌ Failed to submit application: " . $stmt->error;
    header("Location: apply_accommodation.php");
    exit;
}
