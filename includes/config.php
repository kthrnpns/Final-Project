<?php
// Database configuration
define('DB_HOST', 'localhost:3307'); // or '127.0.0.1'
define('DB_USER', 'root');
define('DB_PASS', 'Oct2022105389'); // Default XAMPP password is empty
define('DB_NAME', 'virlanie_foundation');

// Create database connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to utf8
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>