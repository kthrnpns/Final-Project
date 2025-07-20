<?php
session_start();
include('../../includes/config.php');
include('../../includes/functions.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../auth/login.php');
    exit;
}

// Get workshop data if editing
$workshop = null;
if (isset($_GET['id'])) {
    $workshop = get_workshop_by_id($_GET['id']);
    if (!$workshop) {
        header("Location: workshops.php");
        exit();
    }
}?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workshops List | Virlanie Foundation</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <?php include('../includes/header.php'); ?>

    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="../../assets/images/virlanie-logo.png" alt="Virlanie Logo" class="sidebar-logo">
                <h3>Admin Panel</h3>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="active"><a href="list.php"><i class="fas fa-calendar-alt"></i> Workshops</a></li>
                    <li><a href="../participants/list.php"><i class="fas fa-users"></i> Participants</a></li>
                    <li><a href="#"><i class="fas fa-cog"></i> Settings</a></li>
                    <li><a href="../../auth/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>

        <main class="main-content">
            <div class="content-header">
                <h1>Workshops List</h1>
            </div>

            <div class="upcoming-workshops">
                <h2>Create a Workshop</h2>

                <!-- <form action="edit.php" method="POST"> -->
                <form>
                <?php if ($workshop): ?>
                    <input type="hidden" name="id" value="<?php echo $workshop['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="title">Workshop Title</label>
                    <input type="text" id="title" name="title" value="<?php echo $workshop ? htmlspecialchars($workshop['title']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" required><?php echo $workshop ? htmlspecialchars($workshop['description']) : ''; ?></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="date">Date</label>
                        <input type="date" id="date" name="date" value="<?php echo $workshop ? $workshop['date'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Time</label>
                        <input type="time" id="time" name="time" value="<?php echo $workshop ? $workshop['time'] : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?php echo $workshop ? htmlspecialchars($workshop['location']) : ''; ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="capacity">Capacity</label>
                        <input type="number" id="capacity" name="capacity" min="1" value="<?php echo $workshop ? $workshop['capacity'] : '20'; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="instructor">Instructor/Facilitator</label>
                        <input type="text" id="instructor" name="instructor" value="<?php echo $workshop ? htmlspecialchars($workshop['instructor']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Workshop</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            </div>
        </main>
    </div>
    </div>
    </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>

</html>