<?php
session_start();
include(__DIR__ . '/../../includes/config.php');
include(__DIR__ . '/../../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $title = sanitize_input($_POST['title']);
    $description = sanitize_input($_POST['description']);
    $date = sanitize_input($_POST['date']);
    $time = sanitize_input($_POST['time']);
    $location = sanitize_input($_POST['location']);
    $capacity = (int)$_POST['capacity'];

    $stmt = $conn->prepare("UPDATE workshops SET title = ?, description = ?, date = ?, time = ?, location = ?, capacity = ? WHERE id = ?");
    $stmt->bind_param("sssssii", $title, $description, $date, $time, $location, $capacity, $id);

    if ($stmt->execute()) {
        header("Location: list.php?success=edit");
        exit;
    } else {
        header("Location: edit.php?id=$id&error=database");
        exit;
    }
} else {
    header("Location: list.php");
    exit;
}
?>