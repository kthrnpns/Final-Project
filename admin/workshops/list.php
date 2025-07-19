<?php
session_start();
include(__DIR__ . '/../../includes/config.php');
include(__DIR__ . '/../../includes/functions.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$workshops = $conn->query("
    SELECT w.*, COUNT(r.id) as registrations 
    FROM workshops w
    LEFT JOIN workshop_registrations r ON w.id = r.workshop_id
    GROUP BY w.id
    ORDER BY w.date DESC
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Workshops | Virlanie Admin</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --virlanie-red: #E74C3C;
            --virlanie-blue: #3498DB;
            --virlanie-dark: #2C3E50;
            --virlanie-light: #ECF0F1;
            --success-green: #2ECC71;
            --warning-orange: #F39C12;
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
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .page-title {
            font-size: 1.8rem;
            color: var(--virlanie-dark);
            margin: 0;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .btn-add {
            background-color: var(--virlanie-red);
            color: white;
            gap: 8px;
        }
        
        .btn-add:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.15);
            color: #27ae60;
            border-left: 4px solid var(--success-green);
        }
        
        .workshop-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .filter-label {
            font-weight: 500;
            color: var(--virlanie-dark);
        }
        
        .filter-select, .filter-input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
        }
        
        .workshop-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }
        
        .workshop-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border: 1px solid #eee;
        }
        
        .workshop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .workshop-image {
            height: 160px;
            background-color: #f5f5f5;
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .workshop-status {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-upcoming {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--virlanie-blue);
        }
        
        .status-past {
            background-color: rgba(149, 165, 166, 0.2);
            color: #7f8c8d;
        }
        
        .workshop-content {
            padding: 20px;
        }
        
        .workshop-title {
            font-size: 1.25rem;
            margin-bottom: 8px;
            color: var(--virlanie-dark);
            font-weight: 600;
        }
        
        .workshop-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
            font-size: 0.9rem;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #666;
        }
        
        .meta-item i {
            color: var(--virlanie-red);
        }
        
        .workshop-description {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .workshop-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        
        .action-btn {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .btn-edit {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--virlanie-blue);
        }
        
        .btn-edit:hover {
            background-color: rgba(52, 152, 219, 0.2);
        }
        
        .btn-delete {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--virlanie-red);
        }
        
        .btn-delete:hover {
            background-color: rgba(231, 76, 60, 0.2);
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            grid-column: 1 / -1;
        }
        
        .empty-icon {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-text {
            color: #666;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .workshop-grid {
                grid-template-columns: 1fr;
            }
            
            .admin-content-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../includes/admin-header.php'); ?>

    <div class="admin-container">
        <div class="admin-content-header">
            <h1 class="page-title">Workshop Management</h1>
            <a href="create.php" class="btn btn-add">
                <i class="fas fa-plus"></i> New Workshop
            </a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                Workshop <?php echo $_GET['success'] === 'edit' ? 'updated' : 'created'; ?> successfully!
            </div>
        <?php endif; ?>

        <div class="workshop-filters">
            <div class="filter-group">
                <label class="filter-label">Status:</label>
                <select class="filter-select">
                    <option>All Workshops</option>
                    <option>Upcoming</option>
                    <option>Past</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Search:</label>
                <input type="text" class="filter-input" placeholder="Workshop title...">
            </div>
        </div>

        <div class="workshop-grid">
            <?php if (!empty($workshops)): ?>
                <?php foreach ($workshops as $workshop): 
                    $isPast = strtotime($workshop['date']) < time();
                ?>
                    <div class="workshop-card">
                        <div class="workshop-image" style="background-image: url('../../assets/images/workshop-<?php echo $workshop['id'] % 4 + 1; ?>.jpg');">
                            <span class="workshop-status <?php echo $isPast ? 'status-past' : 'status-upcoming'; ?>">
                                <?php echo $isPast ? 'Past' : 'Upcoming'; ?>
                            </span>
                        </div>
                        <div class="workshop-content">
                            <h3 class="workshop-title"><?php echo htmlspecialchars($workshop['title']); ?></h3>
                            <div class="workshop-meta">
                                <div class="meta-item">
                                    <i class="far fa-calendar"></i>
                                    <?php echo date('M j, Y', strtotime($workshop['date'])); ?>
                                </div>
                                <div class="meta-item">
                                    <i class="far fa-clock"></i>
                                    <?php echo date('g:i A', strtotime($workshop['time'])); ?>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-users"></i>
                                    <?php echo $workshop['registrations']; ?> registered
                                </div>
                            </div>
                            <p class="workshop-description"><?php echo htmlspecialchars($workshop['description']); ?></p>
                            <div class="workshop-actions">
                                <a href="edit.php?id=<?php echo $workshop['id']; ?>" class="action-btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <a href="delete.php?id=<?php echo $workshop['id']; ?>" class="action-btn btn-delete" onclick="return confirm('Are you sure you want to delete this workshop?')">
                                    <i class="fas fa-trash"></i> Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <h3>No Workshops Found</h3>
                    <p class="empty-text">You haven't created any workshops yet. Get started by adding your first workshop.</p>
                    <a href="create.php" class="btn btn-add">
                        <i class="fas fa-plus"></i> Create Workshop
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include(__DIR__ . '/../includes/admin-footer.php'); ?>

    <script>
        // Simple filter functionality
        document.querySelector('.filter-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.workshop-card').forEach(card => {
                const title = card.querySelector('.workshop-title').textContent.toLowerCase();
                card.style.display = title.includes(searchTerm) ? 'block' : 'none';
            });
        });

        document.querySelector('.filter-select').addEventListener('change', function(e) {
            const filter = e.target.value;
            const now = new Date();
            
            document.querySelectorAll('.workshop-card').forEach(card => {
                const dateStr = card.querySelector('.meta-item:nth-child(1)').textContent.trim();
                const workshopDate = new Date(dateStr);
                const isPast = workshopDate < now;
                
                if (filter === 'All Workshops') {
                    card.style.display = 'block';
                } else if (filter === 'Upcoming' && !isPast) {
                    card.style.display = 'block';
                } else if (filter === 'Past' && isPast) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>