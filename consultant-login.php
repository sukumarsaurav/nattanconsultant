<?php
// Include database configuration
require_once 'includes/config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['consultant_id'])) {
    header("Location: consultant-dashboard.php");
    exit();
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    
    // Use prepared statement for secure login
    $query = "SELECT * FROM consultants WHERE email = ? AND status = 'approved'";
    $result = executeQuery($query, [$email]);
    
    if ($result && mysqli_num_rows($result) == 1) {
        $consultant = mysqli_fetch_assoc($result);
        if (password_verify($password, $consultant['password'])) {
            // Set session variables
            $_SESSION['consultant_id'] = $consultant['id'];
            $_SESSION['consultant_name'] = $consultant['first_name'] . ' ' . $consultant['last_name'];
            $_SESSION['consultant_role'] = 'consultant';
            
            // Redirect to dashboard
            header("Location: consultant-dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "Email not found or account not approved";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultant Login | CANEXT Immigration</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --color-burgundy: #800020;
            --color-gold: #FFD700;
            --color-light: #FFFFFF;
            --color-dark: #333333;
            --color-gray: #666666;
            --color-error: #dc3545;
            --color-success: #28a745;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .login-header {
            background-color: var(--color-light);
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .logo-container {
            max-width: 200px;
        }

        .logo-container img {
            width: 100%;
            height: auto;
        }

        .login-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
        }

        .login-box {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-title {
            color: var(--color-burgundy);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
        }

        .error-message {
            background-color: #ffe6e6;
            color: var(--color-error);
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--color-dark);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--color-burgundy);
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 500;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--color-burgundy);
            color: var(--color-light);
        }

        .btn-primary:hover {
            background-color: #600018;
        }

        .register-link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .register-link p {
            color: var(--color-gray);
            margin-bottom: 0.5rem;
        }

        .register-link a {
            color: var(--color-burgundy);
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Simple Header -->
    <header class="login-header">
        <div class="container">
            <div class="logo-container">
                <a href="../index.php">
                    <img src="../assets/img/logo.png" alt="CANEXT Immigration">
                </a>
            </div>
        </div>
    </header>

    <!-- Login Section -->
    <section class="login-section">
        <div class="login-box">
            <h1 class="login-title">Consultant Login</h1>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    Login
                </button>
            </form>
            
            <div class="register-link">
                <p>Don't have an account?</p>
                <a href="consultant-register.php">Register as a Consultant</a>
            </div>
        </div>
    </section>
</body>
</html> 