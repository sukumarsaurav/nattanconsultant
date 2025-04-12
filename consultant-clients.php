<?php
$page_title = "My Clients | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Get clients data - only show clients for this specific consultant
$clients_query = "SELECT DISTINCT c.id, c.first_name, c.last_name, c.email, c.phone, c.country 
                 FROM customers c 
                 JOIN appointments a ON c.id = a.customer_id 
                 WHERE a.consultant_id = ? 
                 ORDER BY c.last_name, c.first_name";
$clients_result = executeQuery($clients_query, [$consultant_id]);
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>My Clients</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">View and manage your client information</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <!-- Clients Table -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 0; overflow: hidden; margin-bottom: 30px;">
            <div style="padding: 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">All Clients</h2>
                <div>
                    <input type="text" id="clientSearch" placeholder="Search clients..." style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 5px; width: 250px;">
                </div>
            </div>
            
            <div style="overflow-x: auto;">
                <table id="clientsTable" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Client Name</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Email</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Phone</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Country</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($clients_result && mysqli_num_rows($clients_result) > 0): ?>
                            <?php while($client = mysqli_fetch_assoc($clients_result)): ?>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($client['first_name'] . ' ' . $client['last_name']); ?></td>
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($client['phone']); ?></td>
                                    <td style="padding: 15px;"><?php echo htmlspecialchars($client['country'] ?? 'Not specified'); ?></td>
                                    <td style="padding: 15px;">
                                        <div style="display: flex; gap: 10px;">
                                            <a href="view-customer.php?id=<?php echo $client['id']; ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background-color: #e3f2fd; color: #2196f3; border-radius: 5px; text-decoration: none;" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="customer-appointments.php?customer_id=<?php echo $client['id']; ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background-color: #e8f5e9; color: #4caf50; border-radius: 5px; text-decoration: none;" title="View Appointments">
                                                <i class="fas fa-calendar-alt"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 30px; color: #666;">No clients found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
// Simple client search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('clientSearch');
    const table = document.getElementById('clientsTable');
    const rows = table.getElementsByTagName('tr');
    
    searchInput.addEventListener('keyup', function() {
        const searchTerm = searchInput.value.toLowerCase();
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            
            if (cells.length > 0) {
                let found = false;
                
                for (let j = 0; j < cells.length - 1; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    
                    if (cellText.includes(searchTerm)) {
                        found = true;
                        break;
                    }
                }
                
                row.style.display = found ? '' : 'none';
            }
        }
    });
});
</script>

<?php include('includes/footer.php'); ?>
