<?php
session_start();
include(__DIR__ . '/../../includes/config.php');
include(__DIR__ . '/../../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$workshop = [];
if (isset($_GET['id'])) {
$stmt = $conn->prepare("SELECT * FROM workshops WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$workshop = $result->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($workshop) ? 'Edit' : 'Create'; ?> Workshop | Virlanie Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include(__DIR__ . '/../../assets/css/admin.css'); ?>
        
        .workshop-form {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--virlanie-dark);
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group textarea {
            min-height: 120px;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn-submit {
            background-color: var(--virlanie-red);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../includes/admin-header.php'); ?>

    <div class="admin-container">
        <div class="admin-content-header">
            <h1><?php echo isset($workshop) ? 'Edit Workshop' : 'Create New Workshop'; ?></h1>
            <a href="list.php" class="btn-add">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <form class="workshop-form" method="POST" action="<?php echo isset($workshop) ? 'update.php' : 'store.php'; ?>">
            <?php if (isset($workshop)): ?>
                <input type="hidden" name="id" value="<?php echo $workshop['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="title">Workshop Title</label>
                <input type="text" id="title" name="title" required 
                       value="<?php echo isset($workshop) ? htmlspecialchars($workshop['title']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php 
                    echo isset($workshop) ? htmlspecialchars($workshop['description']) : ''; 
                ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" required 
                           value="<?php echo isset($workshop) ? $workshop['date'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="time">Time</label>
                    <input type="time" id="time" name="time" required 
                           value="<?php echo isset($workshop) ? substr($workshop['time'], 0, 5) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" required 
                           value="<?php echo isset($workshop) ? htmlspecialchars($workshop['location']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="capacity">Capacity</label>
                    <input type="number" id="capacity" name="capacity" min="1" required 
                           value="<?php echo isset($workshop) ? $workshop['capacity'] : '20'; ?>">
                </div>
            </div>
            
            <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> <?php echo isset($workshop) ? 'Update' : 'Create'; ?> Workshop
            </button>
        </form>
    </div>

    <?php include(__DIR__ . '/../includes/admin-footer.php'); ?>
</body>
</html>