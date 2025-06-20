<?php
session_start();

?>
<!DOCTYPE html>
<html>
<body>

<?php
// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Redirect to login.php
header("Location: index.php");

?>



</body>
</html>