<?php
session_start();
include(__DIR__ . '/../includes/config.php');
include(__DIR__ . '/../includes/functions.php');

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch stats for dashboard
$workshop_count = $conn->query("SELECT COUNT(*) as count FROM workshops")->fetch_assoc()['count'];
$participant_count = $conn->query("SELECT COUNT(DISTINCT user_id) as count FROM workshop_registrations")->fetch_assoc()['count'];
$upcoming_workshops = $conn->query("
    SELECT w.id, w.title, w.description, w.date, w.time, w.location, COUNT(r.id) as registrations
    FROM workshops w
    LEFT JOIN workshop_registrations r ON w.id = r.workshop_id
    WHERE w.date >= CURDATE()
    GROUP BY w.id
    ORDER BY w.date ASC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $_SESSION['role'] === 'admin' ? 'Admin' : 'User'; ?> Dashboard | Virlanie Foundation</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --virlanie-red: #E74C3C;
            --virlanie-blue: #3498DB;
            --virlanie-dark: #2C3E50;
            --virlanie-light: #ECF0F1;
        }
        
        .admin-header {
            background-color: var(--virlanie-dark);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .admin-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .admin-logo {
            height: 50px;
        }
        
        .admin-nav-links {
            display: flex;
            list-style: none;
        }
        
        .admin-nav-links li {
            margin-left: 20px;
        }
        
        .admin-nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        .admin-nav-links a:hover {
            opacity: 0.8;
        }
        
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .admin-content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .user-badge {
            background-color: <?php echo $_SESSION['role'] === 'admin' ? 'var(--virlanie-red)' : 'var(--virlanie-blue)'; ?>;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: white;
            font-size: 1.5rem;
        }
        
        .stat-icon.bg-red {
            background-color: var(--virlanie-red);
        }
        
        .stat-icon.bg-blue {
            background-color: var(--virlanie-blue);
        }
        
        .stat-info h3 {
            font-size: 1.8rem;
            margin-bottom: 5px;
            color: var(--virlanie-dark);
        }
        
        .stat-info p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .admin-section-title {
            font-size: 1.5rem;
            color: var(--virlanie-dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .admin-section-title i {
            margin-right: 10px;
            color: var(--virlanie-white);
        }
        
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .admin-table th, 
        .admin-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .admin-table th {
            background-color: var(--virlanie-dark);
            color: white;
            font-weight: 500;
        }
        
        .admin-table tr:hover td {
            background-color: #f9f9f9;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .btn-edit {
            background-color: var(--virlanie-blue);
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #2980b9;
        }
        
        .btn-add {
            background-color: var(--virlanie-red);
            color: white;
            padding: 5px 17px;
        }
        
        .btn-add:hover {
            background-color: #c0392b;
        }
        
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .admin-footer {
            background-color: var(--virlanie-dark);
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
        }
        
        .admin-footer p {
            margin: 10px 0;
        }
        
        @media (max-width: 768px) {
            .admin-nav-links {
                display: none;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <nav class="admin-navbar">
            <a href="../index.php">
                <img src="../assets/images/virlanie-logo.png" alt="Virlanie Foundation" class="admin-logo">
            </a>
            <ul class="admin-nav-links">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="workshops/list.php"><i class="fas fa-calendar-alt"></i> Workshops</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="participants/list.php"><i class="fas fa-users"></i> Participants</a></li>
                <?php endif; ?>
                <li><a href="../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="admin-container">
        <div class="admin-content-header">
            <h1>Dashboard</h1>
            <div>
                <span class="user-badge">
                    <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['user_name']); ?> (<?php echo ucfirst($_SESSION['role']); ?>)
                </span>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-red">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $workshop_count; ?></h3>
                    <p>Total Workshops</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon bg-blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $participant_count; ?></h3>
                    <p>Total Participants</p>
                </div>
            </div>
        </div>

        <section>
            <h2 class="admin-section-title">
                <i class="fas fa-calendar-check"></i> Upcoming Workshops
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="workshops/create.php" class="btn-add" style="margin-left: auto;">
                        <i class="fas fa-plus"></i> Add Workshop
                    </a>
                <?php endif; ?>
            </h2>
            
            <?php if (!empty($upcoming_workshops)): ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Participants</th>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($upcoming_workshops as $workshop): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($workshop['title']); ?></strong>
                                    <p style="color: #666; font-size: 0.9rem; margin-top: 5px;">
                                        <?php echo htmlspecialchars(substr($workshop['description'], 0, 50)); ?>...
                                    </p>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($workshop['date'])); ?>
                                    <br>
                                    <small><?php echo date('g:i A', strtotime($workshop['time'])); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($workshop['location']); ?></td>
                                <td><?php echo $workshop['registrations']; ?></td>
                                <?php if ($_SESSION['role'] === 'admin'): ?>
                                    <td>
                                        <a href="workshops/edit.php?id=<?php echo $workshop['id']; ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>No upcoming workshops found.</p>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="workshops/create.php" class="btn-add" style="margin-top: 15px;">
                            <i class="fas fa-plus"></i> Create Your First Workshop
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>
    </div>

    <footer class="admin-footer">
        <div class="container">
            <img src="../assets/images/virlanie-logo.png" alt="Virlanie Foundation" style="height: 60px; margin-bottom: 10px;">
            <p>Empowering children through education and skills development</p>
            <p>&copy; <?php echo date('Y'); ?> Virlanie Foundation, Inc. All rights reserved.</p>
        </div>
    </footer>

    <script src="../assets/js/admin.js"></script>
</body>
</html>