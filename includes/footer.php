<?php
// Check if consultant is logged in
$is_logged_in = isset($_SESSION['consultant_id']) && !empty($_SESSION['consultant_id']);
?>

<?php if ($is_logged_in): ?>
    </main> <!-- End of content-container -->
<?php endif; ?>

    </div> <!-- Close any remaining containers -->

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>CANEXT Immigration</h3>
                    <p>Your trusted partner in Canadian immigration services.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="../about.php">About Us</a></li>
                        <li><a href="../services.php">Services</a></li>
                        <li><a href="../contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Info</h3>
                    <p>
                        <i class="fas fa-phone"></i> +1 (123) 456-7890<br>
                        <i class="fas fa-envelope"></i> info@canext.com<br>
                        <i class="fas fa-map-marker-alt"></i> Toronto, ON, Canada
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> CANEXT Immigration. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Core JavaScript -->
    <script src="js/consultant-main.js"></script>

    <!-- Page specific JavaScript if available -->
    <?php if (isset($page_specific_js)): ?>
        <?php foreach ($page_specific_js as $js_file): ?>
            <script src="<?php echo $js_file; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Initialize user menu dropdown -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // User menu dropdown
        const userMenuTrigger = document.querySelector('.user-menu-trigger');
        if (userMenuTrigger) {
            userMenuTrigger.addEventListener('click', function() {
                const userMenu = this.closest('.user-menu');
                userMenu.classList.toggle('open');
                
                // Close when clicking outside
                document.addEventListener('click', function closeDropdown(e) {
                    if (!userMenu.contains(e.target)) {
                        userMenu.classList.remove('open');
                        document.removeEventListener('click', closeDropdown);
                    }
                });
            });
        }
        
        // Initialize notifications if function exists
        if (typeof initNotifications === 'function') {
            initNotifications();
        }
    });

    // Function to show notifications (can be called from other scripts)
    function showNotification(message, type = 'info') {
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            // Fallback if the notification system isn't loaded
            alert(message);
        }
    }
    </script>

<?php
// Close database connection if opened
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>

</body>
</html> 