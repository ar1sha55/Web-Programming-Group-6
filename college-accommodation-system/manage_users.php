<?php
require 'auth.php';
checkLevel(1); // Admin only
require 'db_connect.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>👥 Manage Users</h2>
<a href="admin_dashboard.php">← Back to Admin Page</a> | 
<a href="add_user.php">➕ Add New User</a><br><br>



<?php if (isset($_SESSION['user_message'])): ?>
    <div style="padding: 10px; background-color: #e0f7fa; border: 1px solid #4caf50; color: #00796b; margin-bottom: 15px;">
        <?= $_SESSION['user_message'] ?>
    </div>
<?php unset($_SESSION['user_message']); endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Username</th>
        <th>Role</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Actions</th>
    </tr>

    <?php
    $result = $conn->query("SELECT * FROM users ORDER BY user_level, full_name");
    while ($row = $result->fetch_assoc()):
        $roleMap = [1 => 'Admin', 2 => 'Manager', 3 => 'Student'];
    ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['full_name']) ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $roleMap[$row['user_level']] ?? 'Unknown' ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone_number']) ?></td>
            <td>
                <a href="edit_user.php?id=<?= $row['user_id'] ?>">✏️ Edit</a> | 
                <a href="delete_user.php?id=<?= $row['user_id'] ?>" onclick="return confirm('Are you sure?')">🗑️ Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
</body>
</html>
