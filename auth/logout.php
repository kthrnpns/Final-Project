<?php
// Start session first
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page with a query parameter
header("Location: ../index.php?logout=success");
exit();
?>