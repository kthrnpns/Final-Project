<?php
// admin-header.php
$base_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', realpath(__DIR__.'/../..')));
$admin_path = $base_path . '/admin';
$assets_path = $base_path . '/assets';
?>

<style>
    .admin-header {
        background-color: #2C3E50;
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
        margin: 0;
        padding: 0;
    }
    
    .admin-nav-links li {
        margin-left: 20px;
    }
    
    .admin-nav-links a {
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.3s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .admin-nav-links a:hover {
        opacity: 0.8;
    }
    
    .admin-nav-links i {
        font-size: 1.1rem;
    }
</style>

<header class="admin-header">
    <nav class="admin-navbar">
        <a href="<?php echo $base_path; ?>/index.php">
            <img src="<?php echo $assets_path; ?>/images/virlanie-logo.png" alt="Virlanie Foundation" class="admin-logo">
        </a>
        <ul class="admin-nav-links">
            <li><a href="<?php echo $admin_path; ?>/dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <li><a href="<?php echo $admin_path; ?>/workshops/list.php">
                <i class="fas fa-calendar-alt"></i> Workshops
            </a></li>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="<?php echo $admin_path; ?>/participants/list.php">
                    <i class="fas fa-users"></i> Participants
                </a></li>
            <?php endif; ?>
            <li><a href="<?php echo $base_path; ?>/auth/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a></li>
        </ul>
    </nav>
</header>