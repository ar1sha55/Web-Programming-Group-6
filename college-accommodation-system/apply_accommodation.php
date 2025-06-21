<?php
require 'auth.php';
checkLevel(3);
require 'db_connect.php';

$collegeResult = $conn->query("SELECT college_id, college_name, available_slots FROM colleges WHERE available_slots > 0");
$colleges = $collegeResult->fetch_all(MYSQLI_ASSOC);

$roomResult = $conn->query("SELECT college_id, room_type, available_beds FROM rooms WHERE available_beds > 0");
$rooms = [];
while ($row = $roomResult->fetch_assoc()) {
    $rooms[$row['college_id']][] = [
        'room_type' => $row['room_type'],
        'available_beds' => $row['available_beds']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply for Accommodation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="apply.css">
    <script>
        const roomOptions = <?= json_encode($rooms) ?>;
        function updateRoomTypes() {
            const collegeId = document.getElementById('college-select').value;
            const roomTypeSelect = document.getElementById('room-select');
            roomTypeSelect.innerHTML = '<option value="">-- Select Room Type --</option>';
            if (collegeId && roomOptions[collegeId]) {
                roomOptions[collegeId].forEach(room => {
                    const option = document.createElement('option');
                    option.value = room.room_type;
                    option.textContent = `${room.room_type} (${room.available_beds} beds available)`;
                    roomTypeSelect.appendChild(option);
                });
                roomTypeSelect.disabled = false;
            } else {
                roomTypeSelect.disabled = true;
            }
        }
    </script>
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="navbar-logo">
        <h1>Student College Accommodation System</h1>
    </div>
    <ul class="navbar-links">
        <li><a href="student_dashboard.php">Dashboard</a></li>
        <li><a href="apply_accommodation.php">Apply</a></li>
        <li><a href="view_my_application.php">My Application</a></li>
        <li><a href="edit_profile.php">Edit Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<!-- Application Section -->
<div class="apply-section">
    <div class="apply-container">
        <h2 class="apply-title">Apply for College Accommodation</h2>

        <?php if (isset($_SESSION['application_success'])): ?>
            <div class="apply-alert">
                <?= $_SESSION['application_success'] ?>
            </div>
        <?php unset($_SESSION['application_success']); endif; ?>

        <form method="post" action="submit_application.php" class="apply-form">
            <label for="college-select">College:</label>
            <select name="college_id" id="college-select" onchange="updateRoomTypes()" required>
                <option value="">-- Select College --</option>
                <?php foreach ($colleges as $college): ?>
                    <option value="<?= htmlspecialchars($college['college_id']) ?>">
                        <?= htmlspecialchars($college['college_name']) ?> (<?= $college['available_slots'] ?> slots)
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="room-select">Room Type:</label>
            <select name="room_type" id="room-select" disabled required>
                <option value="">-- Select Room Type --</option>
            </select>

            <input type="submit" value="Submit Application" class="apply-button">
        </form>

        <div class="apply-back">
            <a href="student_dashboard.php">← Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
