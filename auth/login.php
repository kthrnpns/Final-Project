<?php
session_start();
include(__DIR__ . '/../includes/config.php');
include(__DIR__ . '/../includes/functions.php');

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    
    // Validate inputs
    $errors = [];
    
    if (empty($email) || empty($password)) {
        $errors[] = "Please fill in all fields";
    } else {
        // Check credentials in database
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Regenerate session ID for security
                session_regenerate_id(true);
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: ../index.php');
                }
                exit;
            } else {
                $errors[] = "Invalid email or password";
            }
        } else {
            $errors[] = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Virlanie Foundation</title>
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
        .alert-danger {
            background-color: #F8D7DA;
            color: #721C24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #F5C6CB;
        }
        .alert-danger p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../assets/images/virlanie-logo-only.png" alt="Virlanie Foundation Logo">
            <h1>Welcome Back</h1>
            <p>Please login to your account</p>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
            
            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Register here</a></p>
                <p><a href="forgot-password.php">Forgot password?</a></p>
            </div>
        </form>
    </div>
    
    <script src="../assets/js/auth.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>