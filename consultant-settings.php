<?php
$page_title = "Account Settings | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Get consultant information
$consultant_query = "SELECT * FROM consultants WHERE id = ?";
$consultant_result = executeQuery($consultant_query, [$consultant_id]);
$consultant = mysqli_fetch_assoc($consultant_result);

// Handle form submission for updating settings
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    // Process form submission here (placeholder for now)
    $success_message = 'Settings updated successfully!';
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>Account Settings</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">Manage your account preferences and settings</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <?php if (!empty($success_message)): ?>
            <div style="background-color: #e8f5e9; border-left: 4px solid #4caf50; padding: 12px 20px; margin-bottom: 20px; border-radius: 4px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div style="background-color: #ffebee; border-left: 4px solid #f44336; padding: 12px 20px; margin-bottom: 20px; border-radius: 4px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Account Settings Form -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px; color: var(--color-burgundy); font-size: 1.3rem;">Account Settings</h2>
            
            <p>This page is under construction. Check back soon for account settings features.</p>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?> 