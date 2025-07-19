<?php
session_start();
include(__DIR__ . '/../../includes/config.php');
include(__DIR__ . '/../../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $date = sanitize_input($_POST['date']);
    $time = sanitize_input($_POST['time']);
    $location = sanitize_input($_POST['location']);
    $capacity = (int)$_POST['capacity'];
    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO workshops (title, description, date, time, location, capacity, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssii", $title, $description, $date, $time, $location, $capacity, $created_by);

    if ($stmt->execute()) {
        header("Location: list.php?success=create");
        exit;
    } else {
        header("Location: create.php?error=database");
        exit;
    }
} else {
    header("Location: create.php");
    exit;
}
?>