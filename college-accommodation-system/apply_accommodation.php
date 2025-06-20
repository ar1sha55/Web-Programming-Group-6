<?php
require 'auth.php';
checkLevel(3); // 3 = student
require 'db_connect.php';

// Fetch colleges with available student slots
$collegeResult = $conn->query("
    SELECT college_id, college_name, available_slots 
    FROM colleges 
    WHERE available_slots > 0
");
$colleges = $collegeResult->fetch_all(MYSQLI_ASSOC);

// Fetch available room types per college
$roomResult = $conn->query("
    SELECT college_id, room_type, available_beds 
    FROM rooms 
    WHERE available_beds > 0
");

$rooms = [];
while ($row = $roomResult->fetch_assoc()) {
    $rooms[$row['college_id']][] = [
        'room_type' => $row['room_type'],
        'available_beds' => $row['available_beds']
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Accommodation</title>
    <link rel="stylesheet" href="style.css">
    <script>
        const roomOptions = <?= json_encode($rooms) ?>;

        function updateRoomTypes() {
            const collegeId = document.getElementById('college').value;
            const roomTypeSelect = document.getElementById('room_type');
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
<h2>Apply for College Accommodation</h2>

<?php if (isset($_SESSION['application_success'])): ?>
    <div style="padding: 10px; background-color: #e0f7fa; border: 1px solid #4caf50; color: #00796b; margin-bottom: 15px;">
        <?= $_SESSION['application_success'] ?>
    </div>
<?php unset($_SESSION['application_success']); endif; ?>

<form method="post" action="submit_application.php">
    <label for="college">College:</label>
    <select name="college_id" id="college" onchange="updateRoomTypes()" required>
        <option value="">-- Select College --</option>
        <?php foreach ($colleges as $college): ?>
            <option value="<?= htmlspecialchars($college['college_id']) ?>">
                <?= htmlspecialchars($college['college_name']) ?> (<?= $college['available_slots'] ?> slots)
            </option>
        <?php endforeach; ?>
    </select><br><br>

    <label for="room_type">Room Type:</label>
    <select name="room_type" id="room_type" disabled required>
        <option value="">-- Select Room Type --</option>
    </select><br><br>

    <input type="submit" value="Submit Application">
</form>

<a href="student_dashboard.php">← Back to Dashboard</a>
</body>
</html>
