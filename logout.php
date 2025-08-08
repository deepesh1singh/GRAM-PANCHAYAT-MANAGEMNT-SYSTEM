<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to entry page
header("Location: entry.php");
exit();
?>
