<?php
// Common functions used across the application

/**
 * Sanitize input data
 */
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect to specified page
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Display alert message
 */
function display_alert($message, $type = 'success') {
    $class = "alert alert-$type";
    return "<div class='$class'>$message</div>";
}
?>