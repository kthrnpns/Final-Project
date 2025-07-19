<?php
session_start();
include('../includes/config.php');
include('../includes/functions.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Get statistics for dashboard
$workshop_count = 0;
$participant_count = 0;
$upcoming_workshops = [];

// Get total workshops
$result = $conn->query("SELECT COUNT(*) as count FROM workshops");
if ($result) {
    $workshop_count = $result->fetch_assoc()['count'];
}

// Get total participants
$result = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM workshop_registrations");
if ($result) {
    $participant_count = $result->fetch_assoc()['count'];
}

// Get upcoming workshops (next 7 days)
$result = $conn->query("
    SELECT w.id, w.title, w.date, w.time, COUNT(r.id) as registrations 
    FROM workshops w
    LEFT JOIN workshop_registrations r ON w.id = r.workshop_id
    WHERE w.date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    GROUP BY w.id
    ORDER BY w.date, w.time
    LIMIT 5
");

if ($result) {
    $upcoming_workshops = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Virlanie Foundation</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <?php include('includes/header.php'); ?>
    
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../assets/images/virlanie-logo.png" alt="Virlanie Logo" class="sidebar-logo">
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="workshops/list.php"><i class="fas fa-calendar-alt"></i> Workshops</a></li>
                    <li><a href="participants/list.php"><i class="fas fa-users"></i> Participants</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
        
        <main class="main-content">
            <div class="content-header">
                <h1>Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            </div>
            
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #4CAF50;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $workshop_count; ?></h3>
                        <p>Total Workshops</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background-color: #2196F3;">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo $participant_count; ?></h3>
                        <p>Total Participants</p>
                    </div>
                </div>
            </div>
            
            <div class="upcoming-workshops">
                <h2>Upcoming Workshops</h2>
                <?php if (empty($upcoming_workshops)): ?>
                    <p>No upcoming workshops in the next 7 days.</p>
                <?php else: ?>
                    <table class="workshop-table">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Registrations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($upcoming_workshops as $workshop): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($workshop['title']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($workshop['date'])); ?></td>
                                    <td><?php echo date('g:i A', strtotime($workshop['time'])); ?></td>
                                    <td><?php echo $workshop['registrations']; ?></td>
                                    <td>
                                        <a href="workshops/view.php?id=<?php echo $workshop['id']; ?>" class="btn-view">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <div class="table-footer">
                    <a href="workshops/list.php" class="btn-view-all">View All Workshops</a>
                </div>
            </div>
        </main>
    </div>
    
    <script src="../assets/js/admin.js"></script>
</body>
</html>