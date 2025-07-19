<?php
session_start();
include(__DIR__ . '/../../includes/config.php');
include(__DIR__ . '/../../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // First delete registrations for this workshop
    $conn->query("DELETE FROM workshop_registrations WHERE workshop_id = $id");
    
    // Then delete the workshop
    if ($conn->query("DELETE FROM workshops WHERE id = $id")) {
        header("Location: list.php?success=delete");
        exit;
    }
}

header("Location: list.php?error=delete");
exit;
?>