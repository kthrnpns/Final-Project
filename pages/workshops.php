<?php
session_start();
include(__DIR__ . '/../includes/config.php');
include(__DIR__ . '/../includes/functions.php');


// Get featured workshops from database
$featured_workshops = [];
$query = "SELECT w.id, w.title, w.description, w.date, w.time, w.location, 
          COUNT(r.id) as registrations 
          FROM workshops w
          LEFT JOIN workshop_registrations r ON w.id = r.workshop_id
          WHERE w.date >= CURDATE()
          GROUP BY w.id
          ORDER BY w.date ASC
          LIMIT 3";

$result = $conn->query($query);
if ($result) {
    $featured_workshops = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virlanie Foundation - Workshops</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Main styles that match Virlanie's design */
        :root {
            --virlanie-red: #E74C3C;
            --virlanie-blue: #3498DB;
            --virlanie-dark: #2C3E50;
            --virlanie-light: #ECF0F1;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            background-color: var(--virlanie-dark);
            color: white;
            padding: 1rem 0;
            position: relative;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1190px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            height: 70px;
            margin-left: 10px;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin-left: 30px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s;
        }
        
        .nav-links a:hover {
            opacity: 0.8;
        }
        
        .hero {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
        }
        
        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 30px;
        }
        
        .btn {
            display: inline-block;
            background-color: var(--virlanie-red);
            color: white;
            padding: 12px 30px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #C0392B;
        }
        
        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 50px;
            color: var(--virlanie-dark);
        }
        
        .workshops-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .workshop-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .workshop-card:hover {
            transform: translateY(-5px);
        }
        
        .workshop-image {
            height: 200px;
            background-color: #eee;
            background-size: cover;
            background-position: center;
        }
        
        .workshop-content {
            padding: 20px;
        }
        
        .workshop-date {
            display: flex;
            align-items: center;
            color: var(--virlanie-red);
            margin-bottom: 10px;
        }
        
        .workshop-date i {
            margin-right: 8px;
        }
        
        .workshop-title {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: var(--virlanie-dark);
        }
        
        .workshop-meta {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .footer {
            background-color: var(--virlanie-dark);
            color: white;
            padding: 50px 0;
            text-align: center;
        }
        
        .user-greeting {
            text-align: right;
            padding: 10px 20px;
            background-color: rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        
        .user-greeting a {
            color: white;
            margin-left: 15px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="user-greeting">
            <?php if (isset($_SESSION['user_id'])): ?>
                Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!
                <a href="auth/logout.php">Logout</a>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php">Admin Dashboard</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="../auth/login.php">Login</a>
                <a href="../auth/register.php">Register</a>
            <?php endif; ?>
        </div>
        
        <nav class="navbar">
            <a href="index.php">
                <img src="../assets/images/virlanie-logo.png" alt="Virlanie Foundation" class="logo">
            </a>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="workshops.php">Workshops</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="volunteer.php">Volunteer</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <div class="container">
        <h2 class="section-title">Upcoming Workshops</h2>
        
        <div class="workshops-grid">
            <?php if (!empty($featured_workshops)): ?>
                <?php foreach ($featured_workshops as $workshop): ?>
                    <div class="workshop-card">
                        <div class="workshop-image" style="background-image: url('assets/images/workshop-<?php echo $workshop['id'] % 3 + 1; ?>.jpg');"></div>
                        <div class="workshop-content">
                            <div class="workshop-date">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo date('F j, Y', strtotime($workshop['date'])); ?>
                                at <?php echo date('g:i A', strtotime($workshop['time'])); ?>
                            </div>
                            <h3 class="workshop-title"><?php echo htmlspecialchars($workshop['title']); ?></h3>
                            <p><?php echo htmlspecialchars(substr($workshop['description'], 0, 100)); ?>...</p>
                            <div class="workshop-meta">
                                <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($workshop['location']); ?></span>
                                <span><i class="fas fa-users"></i> <?php echo $workshop['registrations']; ?> registered</span>
                            </div>
                            <a href="workshop-details.php?id=<?php echo $workshop['id']; ?>" class="btn" style="display: block; text-align: center; margin-top: 15px;">View Details</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center;">No upcoming workshops at the moment. Please check back later.</p>
            <?php endif; ?>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <img src="../assets/images/virlanie-logo.png" alt="Virlanie Foundation" style="height: 60px; margin-bottom: 20px;">
            <p>Empowering children and youth through education and skills development since 1992.</p>
            <div style="margin: 30px 0;">
                <a href="https://www.facebook.com/virlaniefoundation/" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                <a href="https://x.com/virlanie" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                <a href="https://www.instagram.com/virlanie/" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                <a href="https://www.youtube.com/user/virlaniefoundation" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-youtube"></i></a>
            </div>
            <p>&copy; <?php echo date('Y'); ?> Virlanie Foundation, Inc. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>