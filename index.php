<?php
session_start();
include(__DIR__ . '/includes/config.php');
include(__DIR__ . '/includes/functions.php');


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
    <title>Virlanie Foundation - Skills Development Programs</title>
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
                <a href="auth/login.php">Login</a>
                <a href="auth/register.php">Register</a>
            <?php endif; ?>
        </div>
        
        <nav class="navbar">
            <a href="index.php">
                <img src="assets/images/virlanie-logo.png" alt="Virlanie Foundation" class="logo">
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="workshops.php">Workshops</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="volunteer.php">Volunteer</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </nav>
    </header>
    
    <section class="hero">
        <h1>Empowering Children Through Skills Development</h1>
        <p>Join our workshops and programs designed to help vulnerable children and youth develop essential life skills for a brighter future.</p>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="workshops.php" class="btn">Browse Workshops</a>
        <?php else: ?>
            <a href="auth/register.php" class="btn">Join Our Programs</a>
        <?php endif; ?>
    </section>
    
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
    
        <section style="background-color: #bcc5d4; padding: 60px 0;">
        <div class="container">
            <h2 class="section-title">Our Vision, Mission & Values</h2>
            <div style="display: flex; flex-wrap: wrap; gap: 40px;">
                <!-- Vision Card -->
                <div style="flex: 1; min-width: 300px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div style="font-size: 3rem; color: var(--virlanie-red); margin-bottom: 15px; text-align: center;">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3 style="color: var(--virlanie-red); text-align: center;">Our Vision</h3>
                    <p style="font-style: italic; text-align: center;">By 2028, VFI is an innovative organization with highly competent staff and volunteers who provide services to children and families at risk with stable funding from donors, sponsors, and partners who preferentially engage with the Foundation because of the consistent results they produce.</p>
                </div>
                
                <!-- Mission Card -->
                <div style="flex: 1; min-width: 300px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div style="font-size: 3rem; color: var(--virlanie-red); margin-bottom: 15px; text-align: center;">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3 style="color: var(--virlanie-red); text-align: center;">Our Mission</h3>
                    <p style="font-style: italic; text-align: center;">We provide the most disadvantaged children with a caring family environment, opportunities for their healing and development through a multi-disciplinary approach, and collaboration with all stakeholders so they can reach their full potential and be mainstreamed into society.</p>
                </div>
                
                <!-- Values Card -->
                <div style="flex: 1; min-width: 300px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                    <div style="font-size: 3rem; color: var(--virlanie-red); margin-bottom: 15px; text-align: center;">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3 style="color: var(--virlanie-red); text-align: center;">Our Core Values</h3>
                    <p style="font-style: italic; text-align: center;">We provide our services with utmost excellence, integrity, and accountability; and, in harmony with our networks and partners, exhibit leadership in childcare and the promotion and protection of children's rights. We demonstrate vitality, resilience, and innovation in our approaches to childcare, street children engagement, and family strengthening.</p>
                </div>
            </div>
        </div>
    </section>
    
    <div class="container">
        <h2 class="section-title">How You Can Help</h2>
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; text-align: center;">
            <div style="padding: 20px;">
                <div style="font-size: 3rem; color: var(--virlanie-red); margin-bottom: 15px;">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <h3>Donate</h3>
                <p>Your financial support helps us maintain and expand our skills development programs.</p>
                <a href="donate.php" class="btn">Donate Now</a>
            </div>
            <div style="padding: 20px;">
                <div style="font-size: 3rem; color: var(--virlanie-red); margin-bottom: 15px;">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h3>Volunteer</h3>
                <p>Share your skills and time by leading or assisting with our workshops.</p>
                <a href="volunteer.php" class="btn">Volunteer</a>
            </div>
            <div style="padding: 20px;">
                <div style="font-size: 3rem; color: var(--virlanie-red); margin-bottom: 15px;">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3>Spread the Word</h3>
                <p>Help us reach more children by sharing our programs with your network.</p>
                <a href="contact.php" class="btn">Contact Us</a>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <img src="assets/images/virlanie-logo.png" alt="Virlanie Foundation" style="height: 60px; margin-bottom: 20px;">
            <p>Empowering children and youth through education and skills development since 1992.</p>
            <div style="margin: 30px 0;">
                <a href="#" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-facebook"></i></a>
                <a href="#" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-twitter"></i></a>
                <a href="#" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-instagram"></i></a>
                <a href="#" style="color: white; margin: 0 10px; font-size: 1.5rem;"><i class="fab fa-youtube"></i></a>
            </div>
            <p>&copy; <?php echo date('Y'); ?> Virlanie Foundation, Inc. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>