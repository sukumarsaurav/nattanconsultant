<?php
$page_title = "My Customers | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 20;
$offset = ($page - 1) * $items_per_page;

// Search functionality
$search_term = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_condition = '';
if (!empty($search_term)) {
    $search_term = '%' . $search_term . '%';
    $search_condition = "AND (c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)";
}

// Get total count of customers for this consultant
$count_query = "SELECT COUNT(DISTINCT c.id) AS total 
                FROM customers c 
                JOIN appointments a ON c.id = a.customer_id 
                WHERE a.consultant_id = ? $search_condition";

$count_params = [$consultant_id];
if (!empty($search_term)) {
    $count_params = array_merge($count_params, [$search_term, $search_term, $search_term, $search_term]);
}

$count_result = executeQuery($count_query, $count_params);
$count_row = mysqli_fetch_assoc($count_result);
$total_customers = $count_row['total'];

$total_pages = ceil($total_customers / $items_per_page);

// Get customers for this consultant with pagination
$customers_query = "SELECT DISTINCT c.*, 
                   (SELECT MAX(a2.appointment_datetime) FROM appointments a2 WHERE a2.customer_id = c.id AND a2.consultant_id = ?) AS last_appointment,
                   (SELECT COUNT(*) FROM appointments a3 WHERE a3.customer_id = c.id AND a3.consultant_id = ?) AS appointment_count
                   FROM customers c 
                   JOIN appointments a ON c.id = a.customer_id 
                   WHERE a.consultant_id = ? $search_condition
                   ORDER BY last_appointment DESC 
                   LIMIT ? OFFSET ?";

$customers_params = [$consultant_id, $consultant_id, $consultant_id];
if (!empty($search_term)) {
    $customers_params = array_merge($customers_params, [$search_term, $search_term, $search_term, $search_term]);
}
$customers_params[] = $items_per_page;
$customers_params[] = $offset;

$customers_result = executeQuery($customers_query, $customers_params);
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/customers-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>My Customers</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">View and manage your customer information</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <!-- Search Bar -->
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <form action="" method="GET" style="flex: 1; max-width: 500px;">
                <div style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search_term ?? ''); ?>" placeholder="Search customers..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <button type="submit" style="background-color: var(--color-burgundy); color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Search</button>
                    <?php if (!empty($search_term)): ?>
                        <a href="my-customers.php" style="display: inline-flex; align-items: center; text-decoration: none; padding: 10px; border: 1px solid #ddd; border-radius: 5px; color: #666;">
                            <i class="fas fa-times" style="margin-right: 5px;"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
            
            <div>
                <p style="margin: 0; color: #666;">
                    <strong>Total Customers:</strong> <?php echo $total_customers; ?>
                </p>
            </div>
        </div>
        
        <!-- Customers Table -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px; overflow-x: auto;">
            <?php if (mysqli_num_rows($customers_result) > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Name</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Email</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Phone</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Last Appointment</th>
                            <th style="padding: 15px; text-align: center; border-bottom: 1px solid #eee; color: #444;">Appointments</th>
                            <th style="padding: 15px; text-align: center; border-bottom: 1px solid #eee; color: #444;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($customer = mysqli_fetch_assoc($customers_result)): ?>
                            <tr>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($customer['email']); ?></td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;"><?php echo htmlspecialchars($customer['phone']); ?></td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <?php if (!empty($customer['last_appointment'])): ?>
                                        <?php echo date('M j, Y g:i A', strtotime($customer['last_appointment'])); ?>
                                    <?php else: ?>
                                        <span style="color: #999;">No appointments</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; text-align: center;">
                                    <span style="background-color: var(--color-burgundy); color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.9em;">
                                        <?php echo $customer['appointment_count']; ?>
                                    </span>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; text-align: center;">
                                    <a href="view-customer.php?id=<?php echo $customer['id']; ?>" style="display: inline-block; padding: 5px 10px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px; margin-right: 5px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    <a href="customer-appointments.php?customer_id=<?php echo $customer['id']; ?>" style="display: inline-block; padding: 5px 10px; background-color: #4caf50; color: white; text-decoration: none; border-radius: 5px;">
                                        <i class="fas fa-calendar-alt"></i> Appointments
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                        <div style="display: flex; gap: 5px;">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #666;">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $start_page + 4);
                            
                            if ($end_page - $start_page < 4 && $total_pages > 5) {
                                $start_page = max(1, $end_page - 4);
                            }
                            
                            for ($i = $start_page; $i <= $end_page; $i++): 
                            ?>
                                <a href="?page=<?php echo $i; ?><?php echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; <?php echo $i == $page ? 'background-color: var(--color-burgundy); color: white; border-color: var(--color-burgundy);' : 'color: #666;'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #666;">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px;">
                    <div style="font-size: 4rem; color: #eee; margin-bottom: 20px;">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 style="margin-bottom: 10px; color: #666;">No Customers Found</h3>
                    <?php if (!empty($search_term)): ?>
                        <p style="color: #888; margin-bottom: 20px;">No customers match your search criteria.</p>
                        <a href="my-customers.php" style="display: inline-block; padding: 10px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px;">
                            <i class="fas fa-times"></i> Clear Search
                        </a>
                    <?php else: ?>
                        <p style="color: #888;">You don't have any customers yet.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?> 