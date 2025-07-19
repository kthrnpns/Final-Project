<?php
session_start();
include(__DIR__ . '/../../includes/config.php');
include(__DIR__ . '/../../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$participants = $conn->query("
    SELECT u.id, u.name, u.email, COUNT(r.id) as workshops_count
    FROM users u
    LEFT JOIN workshop_registrations r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY u.name ASC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Participants | Virlanie Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        <?php include(__DIR__ . '/../../assets/css/admin.css'); ?>
        
        .participant-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--virlanie-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .participant-name {
            display: flex;
            align-items: center;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../includes/admin-header.php'); ?>

    <div class="admin-container">
        <div class="admin-content-header">
            <h1>Manage Participants</h1>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>Participant</th>
                    <th>Email</th>
                    <th>Workshops</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participants as $participant): ?>
                    <tr>
                        <td>
                            <div class="participant-name">
                                <div class="participant-avatar">
                                    <?php echo strtoupper(substr($participant['name'], 0, 1)); ?>
                                </div>
                                <?php echo htmlspecialchars($participant['name']); ?>
                            </div>
                        </td>
                        <td><?php echo htmlspecialchars($participant['email']); ?></td>
                        <td><?php echo $participant['workshops_count']; ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $participant['id']; ?>" class="btn-edit">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php include(__DIR__ . '/../includes/admin-footer.php'); ?>
</body>
</html>