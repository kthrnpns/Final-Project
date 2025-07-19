<?php
// create_admin.php - Run this once to create the first admin manually

// Only allow this script to run from command line (for security)
if (php_sapi_name() !== 'cli') {
    die("This script can only be run from command line.");
}

require __DIR__ . '/includes/config.php';

// Admin credentials (change these before running!)
$name = 'Admin User';
$email = 'admin@virlanie.org';
$password = 'StrongPassword123!'; // Change this
$role = 'admin';

// Check if admin already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    die("Error: Admin user already exists!\n");
}

// Create admin
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

if ($stmt->execute()) {
    echo "✅ Admin user created successfully!\n";
    echo "Email: $email\n";
    // Note: In production, DON'T show the password in logs
} else {
    echo "❌ Error: " . $conn->error . "\n";
}
