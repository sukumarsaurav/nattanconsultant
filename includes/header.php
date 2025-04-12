<?php
// Include database configuration
require_once __DIR__ . '/config.php';

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page filename
$current_page = basename($_SERVER['PHP_SELF']);

// Check if consultant is logged in
$is_logged_in = isset($_SESSION['consultant_id']) && !empty($_SESSION['consultant_id']);

// DB connection to get real-time data for the sidebar
$conn = null;
if ($is_logged_in) {
    $conn = mysqli_connect("193.203.184.121", "u911550082_nattan", "Milk@sdk14", "u911550082_nattan");
    
    // Get pending appointments count for badge
    $consultant_id = $_SESSION['consultant_id'];
    $pending_count = 0;
    
    if ($conn) {
        $query = "SELECT COUNT(*) as count FROM appointments WHERE consultant_id = $consultant_id AND status = 'pending'";
        $result = mysqli_query($conn, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $pending_count = $row['count'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Consultant Portal | CANEXT Immigration'; ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="../assets/img/favicon.ico" type="image/x-icon">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Consultant CSS -->
    <link rel="stylesheet" href="css/consultant-style.css">
    <link rel="stylesheet" href="css/navigation.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/forms.css">
    <link rel="stylesheet" href="css/notifications.css">
    <link rel="stylesheet" href="css/responsive.css">
    <link rel="stylesheet" href="css/topbar.css">
    
    <!-- Page specific CSS if available -->
    <?php if (isset($page_specific_css)): ?>
        <?php foreach ($page_specific_css as $css_file): ?>
            <link rel="stylesheet" href="<?php echo $css_file; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container top-bar-container">
            <div class="top-bar-contact">
                <a href="mailto:info@canext.com">
                    <i class="fas fa-envelope"></i> info@canext.com
                </a>
            </div>
            <div class="top-bar-actions">
                <?php if (!$is_logged_in): ?>
                    <a href="index.php">
                        <i class="fas fa-sign-in-alt"></i> Member Login
                    </a>
                    <a href="consultant-register.php">
                        <i class="fas fa-user-plus"></i> Register
                    </a>
                <?php else: ?>
                    <a href="consultant-dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($is_logged_in): ?>
        <!-- Mobile Menu Toggle -->
        <button id="mobile-menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Side Navigation -->
        <aside id="side-navigation" class="side-navigation">
            <div class="side-navigation-inner">
                <div class="logo-container">
                    <a href="consultant-dashboard.php">
                        <img src="../assets/img/logo.png" alt="CANEXT Immigration">
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <ul class="nav-menu">
                        <li class="nav-menu-item">
                            <a href="consultant-dashboard.php" class="nav-menu-link <?php echo $current_page == 'consultant-dashboard.php' ? 'active' : ''; ?>">
                                <div class="nav-menu-icon">
                                    <i class="fas fa-tachometer-alt"></i>
                                </div>
                                <span class="nav-menu-text">Dashboard</span>
                            </a>
                        </li>
                        <li class="nav-menu-item">
                            <a href="consultant-bookings.php" class="nav-menu-link <?php echo $current_page == 'consultant-bookings.php' ? 'active' : ''; ?>">
                                <div class="nav-menu-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <span class="nav-menu-text">Appointments</span>
                                <?php if ($pending_count > 0): ?>
                                    <span class="nav-menu-badge"><?php echo $pending_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-menu-item">
                            <a href="consultant-schedule.php" class="nav-menu-link <?php echo $current_page == 'consultant-schedule.php' ? 'active' : ''; ?>">
                                <div class="nav-menu-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <span class="nav-menu-text">Availability</span>
                            </a>
                        </li>
                        <li class="nav-menu-item">
                            <a href="consultant-clients.php" class="nav-menu-link <?php echo $current_page == 'consultant-clients.php' ? 'active' : ''; ?>">
                                <div class="nav-menu-icon">
                                    <i class="fas fa-users"></i>
                                </div>
                                <span class="nav-menu-text">Clients</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <ul class="nav-menu">
                        <li class="nav-menu-item">
                            <a href="consultant-profile.php" class="nav-menu-link <?php echo $current_page == 'consultant-profile.php' ? 'active' : ''; ?>">
                                <div class="nav-menu-icon">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <span class="nav-menu-text">My Profile</span>
                            </a>
                        </li>
                        <li class="nav-menu-item">
                            <a href="consultant-settings.php" class="nav-menu-link <?php echo $current_page == 'consultant-settings.php' ? 'active' : ''; ?>">
                                <div class="nav-menu-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <span class="nav-menu-text">Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="user-menu">
                    <div class="user-menu-trigger">
                        <div class="user-avatar">
                            <?php 
                            // Get consultant profile image
                            $profile_image = '';
                            if ($conn) {
                                $query = "SELECT profile_image FROM consultants WHERE id = $consultant_id";
                                $result = mysqli_query($conn, $query);
                                if ($result && mysqli_num_rows($result) > 0) {
                                    $row = mysqli_fetch_assoc($result);
                                    $profile_image = $row['profile_image'];
                                }
                            }
                            
                            if (!empty($profile_image)): 
                            ?>
                                <img src="<?php echo $profile_image; ?>" alt="Profile">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                        <div class="user-info">
                            <div class="user-name"><?php echo $_SESSION['consultant_name']; ?></div>
                            <div class="user-role">Consultant</div>
                        </div>
                    </div>
                    <div class="user-menu-dropdown">
                        <a href="consultant-profile.php" class="user-menu-item">
                            <i class="fas fa-user-circle"></i> My Profile
                        </a>
                        <a href="consultant-settings.php" class="user-menu-item">
                            <i class="fas fa-cog"></i> Settings
                        </a>
                        <a href="logout.php" class="user-menu-item logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content Container -->
        <main class="content-container">
    <?php else: ?>
        <!-- Simple header for login/register pages -->
        <header class="login-header">
            <div class="container">
                <div class="logo-container">
                    <a href="../index.php">
                        <img src="../assets/img/logo.png" alt="CANEXT Immigration">
                    </a>
                </div>
            </div>
        </header>
    <?php endif; ?> 