<?php
session_start();
include(__DIR__ . '/../../includes/config.php');
include(__DIR__ . '/../../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: list.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$participant = $stmt->get_result()->fetch_assoc();

$workshops = $conn->query("
    SELECT w.title, w.date, w.time, w.location 
    FROM workshop_registrations r
    JOIN workshops w ON r.workshop_id = w.id
    WHERE r.user_id = {$_GET['id']}
    ORDER BY w.date DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Details | Virlanie Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include(__DIR__ . '/../../assets/css/admin.css'); ?>
        
        .participant-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .participant-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: var(--virlanie-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 2rem;
            font-weight: bold;
        }
        
        .participant-info {
            flex: 1;
        }
        
        .participant-name {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        
        .participant-email {
            color: #666;
        }
        
        .workshop-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .workshop-item:last-child {
            border-bottom: none;
        }
        
        .workshop-title {
            font-weight: 500;
            margin-bottom: 5px;
        }
        
        .workshop-meta {
            display: flex;
            color: #666;
            font-size: 0.9rem;
        }
        
        .workshop-meta span {
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../includes/admin-header.php'); ?>

    <div class="admin-container">
        <div class="admin-content-header">
            <h1>Participant Details</h1>
            <a href="list.php" class="btn-add">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="participant-header">
            <div class="participant-avatar">
                <?php echo strtoupper(substr($participant['name'], 0, 1)); ?>
            </div>
            <div class="participant-info">
                <div class="participant-name"><?php echo htmlspecialchars($participant['name']); ?></div>
                <div class="participant-email"><?php echo htmlspecialchars($participant['email']); ?></div>
            </div>
        </div>

        <h2 class="admin-section-title">
            <i class="fas fa-calendar-alt"></i> Registered Workshops
        </h2>
        
        <?php if (!empty($workshops)): ?>
            <div style="background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                <?php foreach ($workshops as $workshop): ?>
                    <div class="workshop-item">
                        <div class="workshop-title"><?php echo htmlspecialchars($workshop['title']); ?></div>
                        <div class="workshop-meta">
                            <span><i class="far fa-calendar"></i> <?php echo date('M j, Y', strtotime($workshop['date'])); ?></span>
                            <span><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($workshop['time'])); ?></span>
                            <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($workshop['location']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-data">This participant hasn't registered for any workshops yet.</div>
        <?php endif; ?>
    </div>

    <?php include(__DIR__ . '/../includes/admin-footer.php'); ?>
</body>
</html>