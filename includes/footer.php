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

    <!-- Additional Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-toggle')?.addEventListener('click', function() {
            document.getElementById('side-navigation')?.classList.toggle('active');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const sideNav = document.getElementById('side-navigation');
            const mobileToggle = document.getElementById('mobile-menu-toggle');
            
            if (sideNav && mobileToggle) {
                if (!sideNav.contains(event.target) && !mobileToggle.contains(event.target)) {
                    sideNav.classList.remove('active');
                }
            }
        });
    </script>

<?php
// Close database connection if opened
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
?>

</body>
</html> 