<?php
require_once 'config.php';

// Connect without selecting DB (needed to create DB first)
$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}

// Create the database
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
$conn->select_db($db_name);

// Schema
$schema = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    user_level INT NOT NULL COMMENT '1=admin, 2=manager, 3=student',
    phone_number VARCHAR(15),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS students (
    student_id INT PRIMARY KEY,
    matric_number VARCHAR(20) UNIQUE NOT NULL,
    program VARCHAR(100),
    year INT,
    FOREIGN KEY (student_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS managers (
    manager_id INT PRIMARY KEY,
    department VARCHAR(100),
    office_phone VARCHAR(20),
    FOREIGN KEY (manager_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS colleges (
    college_id INT AUTO_INCREMENT PRIMARY KEY,
    college_name VARCHAR(100) NOT NULL,
    capacity INT NOT NULL,
    available_slots INT NOT NULL
);

CREATE TABLE IF NOT EXISTS rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT NOT NULL,
    room_type ENUM('single', 'double') NOT NULL,
    total_rooms INT NOT NULL,
    available_rooms INT NOT NULL,
    FOREIGN KEY (college_id) REFERENCES colleges(college_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    college_id INT NOT NULL,
    room_type ENUM('single', 'double') NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    remarks TEXT, 
    apply_date DATE NOT NULL,
    FOREIGN KEY (student_id) REFERENCES users(user_id),
    FOREIGN KEY (college_id) REFERENCES colleges(college_id)
);

CREATE TABLE IF NOT EXISTS accommodation_records (
    record_id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    college_id INT NOT NULL,
    approved_by INT NOT NULL,
    approve_date DATE,
    FOREIGN KEY (student_id) REFERENCES users(user_id),
    FOREIGN KEY (college_id) REFERENCES colleges(college_id),
    FOREIGN KEY (approved_by) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS history_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action_type ENUM('login', 'apply', 'approve', 'reject', 'edit_profile', 'add_user', 'delete_user'),
    description TEXT,
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
SQL;

// Execute schema
$conn->multi_query($schema);
while ($conn->more_results() && $conn->next_result()) {}

// Insert users and extend with students/managers
$users = [
    ['Adam', 'admin1', 'admin1', 1],
    ['Nurul', 'admin2', 'admin2', 1],
    ['Haziq', 'manager1', 'manager1', 2],
    ['Siti', 'manager2', 'manager2', 2],
    ['Hazirah', 'student1', 'student1', 3],
    ['Akmal', 'student2', 'student2', 3]
];

foreach ($users as $u) {
    $stmt = $conn->prepare("INSERT IGNORE INTO users (full_name, username, password, user_level) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $u[0], $u[1], $u[2], $u[3]);
    $stmt->execute();
    $userId = $conn->insert_id;
if ($u[3] === 3) { // student
        $matric = "A20CS00$userId";
        $program = "Bachelor of Computer Science";
        $year = 2;
        $stmt2 = $conn->prepare("INSERT IGNORE INTO students (student_id, matric_number, program, year) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("issi", $userId, $matric, $program, $year);
        $stmt2->execute();
    } elseif ($u[3] === 2) { // manager
        $dept = "Accommodation Office";
        $phone = "03-8921$userId";
        $stmt3 = $conn->prepare("INSERT IGNORE INTO managers (manager_id, department, office_phone) VALUES (?, ?, ?)");
        $stmt3->bind_param("iss", $userId, $dept, $phone);
        $stmt3->execute();
    }
}

// Insert colleges
$colleges = [
    ['Kolej Perdana (KP)', 100], //1
    ['Kolej Tun Dr. Ismail (KTDI)', 0], //2 (demonstrate for full capacity) 
    ['Kolej Tun Hussein Onn (KTHO)', 60], //3
    ['Kolej Tuanku Rahman Putra (KTR)', 90], //4
    ['Kolej Datin Seri Endon (KDSE)', 75], //5
    ['Kolej Dato Onn Jaafar (KDOJ)', 110], //6
];

foreach ($colleges as $c) {
    $stmt = $conn->prepare("INSERT IGNORE INTO colleges (college_name, capacity, available_slots) VALUES (?, ?, ?)");
    $stmt->bind_param("sii", $c[0], $c[1], $c[1]);
    $stmt->execute();
}

// Insert rooms for each college
$rooms = [
    [1, 'Single', 50, 50],
    [1, 'Double', 30, 30],
    [2, 'Single', 0, 0],
    [2, 'Double', 0, 0],
    [3, 'Single', 35, 35],
    [3, 'Double', 25, 25],
    [4, 'Single', 45, 45],
    [4, 'Double', 45, 45],
    [5, 'Single', 40, 40],
    [5, 'Double', 35, 35],
    [6, 'Single', 50, 50],
    [6, 'Double', 60, 60],
];

foreach ($rooms as $room) {
    $stmt = $conn->prepare("INSERT IGNORE INTO rooms (college_id, room_type, total_rooms, available_rooms) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $room[0], $room[1], $room[2], $room[3]);
    $stmt->execute();
}

echo "<h3>✅ Database, tables, users, colleges, rooms created successfully!</h3>";
$conn->close();
?>
