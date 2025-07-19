<?php
session_start();
include(__DIR__ . '/../includes/config.php');
include(__DIR__ . '/../includes/functions.php');

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $confirm_password = sanitize_input($_POST['confirm_password']);
    $role = sanitize_input($_POST['role'] ?? 'user'); // Default to 'user' if not selected
    
    // Auto-assign admin role for specific emails (e.g., @virlanie.org)
    if (str_ends_with($email, '@virlanie.org')) {
        $role = 'admin';
    }
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already registered";
    }
    
    // If no errors, register user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['registration_success'] = true;
            header('Location: login.php');
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Virlanie Foundation</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
         body {
            background-color: #243357;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            margin: 20px;
        }
        .role-selection {
            margin: 20px 0;
        }
        .role-option {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .role-option input {
            margin-right: 10px;
        }
        .role-label {
            display: flex;
            align-items: center;
        }
        .role-label i {
            margin-right: 8px;
            color: #E74C3C;
        }
        .admin-note {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
            padding: 8px;
            background: #f5f5f5;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../assets/images/virlanie-logo-only.png" alt="Virlanie Foundation Logo">
            <h1>Create an Account</h1>
            <p>Join us in making a difference</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="register.php" method="POST" class="login-form" id="registerForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
                <small class="admin-note">@virlanie.org emails will automatically get admin privileges</small>
            </div>
            
            <div class="form-group">
                <label for="password">Password (min 8 characters)</label>
                <input type="password" id="password" name="password" minlength="8" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
        <div class="form-group role-selection">
            <div style="display: flex; align-items: center; gap: 15px;">
                <label style="margin-right: 10px; white-space: nowrap;">Account Type:</label>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div class="role-option" style="display: flex; align-items: center;">
                        <input type="radio" id="role-user" name="role" value="user" checked style="margin-right: 5px;">
                        <label for="role-user" style="display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-user"></i> Regular User
                        </label>
                    </div>
                    <div class="role-option" style="display: flex; align-items: center;">
                        <input type="radio" id="role-volunteer" name="role" value="volunteer" style="margin-right: 5px;">
                        <label for="role-volunteer" style="display: flex; align-items: center; gap: 5px;">
                            <i class="fas fa-hands-helping"></i> Volunteer
                        </label>
                    </div>
                </div>
            </div>
        </div>
            
            <button type="submit" class="btn-login">Register</button>
            
            <div class="login-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
            </div>
        </form>
    </div>
    
    <script src="../assets/js/auth.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>