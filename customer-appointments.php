<?php
$page_title = "Customer Appointments | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Check if customer ID is provided
if (!isset($_GET['customer_id']) || empty($_GET['customer_id'])) {
    // Redirect to customers list if no ID is provided
    header('Location: my-customers.php');
    exit;
}

$customer_id = (int)$_GET['customer_id'];

// Verify this customer belongs to the consultant by checking appointments
$verify_query = "SELECT COUNT(*) as count FROM appointments 
                WHERE customer_id = ? AND consultant_id = ?";
$verify_result = executeQuery($verify_query, [$customer_id, $consultant_id]);
$verify_row = mysqli_fetch_assoc($verify_result);

if ($verify_row['count'] == 0) {
    // This customer doesn't have appointments with this consultant
    setFlashMessage('error', 'You do not have permission to view this customer\'s appointments.');
    header('Location: my-customers.php');
    exit;
}

// Get customer details
$customer_query = "SELECT * FROM customers WHERE id = ?";
$customer_result = executeQuery($customer_query, [$customer_id]);

if (mysqli_num_rows($customer_result) == 0) {
    // Customer not found
    setFlashMessage('error', 'Customer not found.');
    header('Location: my-customers.php');
    exit;
}

$customer = mysqli_fetch_assoc($customer_result);

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;

// Filter setup
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// Build filter conditions
$filter_conditions = '';
$filter_params = [$customer_id, $consultant_id];

if (!empty($status_filter)) {
    $filter_conditions .= " AND status = ?";
    $filter_params[] = $status_filter;
}

if (!empty($date_filter)) {
    switch ($date_filter) {
        case 'upcoming':
            $filter_conditions .= " AND appointment_datetime >= NOW()";
            break;
        case 'past':
            $filter_conditions .= " AND appointment_datetime < NOW()";
            break;
        case 'today':
            $filter_conditions .= " AND DATE(appointment_datetime) = CURDATE()";
            break;
        case 'this_week':
            $filter_conditions .= " AND YEARWEEK(appointment_datetime, 1) = YEARWEEK(CURDATE(), 1)";
            break;
        case 'this_month':
            $filter_conditions .= " AND MONTH(appointment_datetime) = MONTH(CURDATE()) AND YEAR(appointment_datetime) = YEAR(CURDATE())";
            break;
    }
}

// Get total count of appointments
$count_query = "SELECT COUNT(*) AS total 
                FROM appointments 
                WHERE customer_id = ? AND consultant_id = ? $filter_conditions";
$count_result = executeQuery($count_query, $filter_params);
$count_row = mysqli_fetch_assoc($count_result);
$total_appointments = $count_row['total'];

$total_pages = ceil($total_appointments / $items_per_page);

// Get appointments with pagination and filters
$appointments_query = "SELECT * FROM appointments 
                      WHERE customer_id = ? AND consultant_id = ? $filter_conditions
                      ORDER BY appointment_datetime DESC 
                      LIMIT ? OFFSET ?";
$appointments_params = $filter_params;
$appointments_params[] = $items_per_page;
$appointments_params[] = $offset;

$appointments_result = executeQuery($appointments_query, $appointments_params);

// Count appointments by status for the statistics
$status_counts = [
    'upcoming' => 0,
    'completed' => 0,
    'cancelled' => 0,
    'total' => $total_appointments
];

$status_count_query = "SELECT 
                        SUM(CASE WHEN appointment_datetime >= NOW() AND status != 'cancelled' THEN 1 ELSE 0 END) as upcoming,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
                      FROM appointments 
                      WHERE customer_id = ? AND consultant_id = ?";
$status_count_result = executeQuery($status_count_query, [$customer_id, $consultant_id]);
$status_counts_row = mysqli_fetch_assoc($status_count_result);

if ($status_counts_row) {
    $status_counts['upcoming'] = $status_counts_row['upcoming'];
    $status_counts['completed'] = $status_counts_row['completed'];
    $status_counts['cancelled'] = $status_counts_row['cancelled'];
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/appointments-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>Customer Appointments</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">View all appointments for <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <!-- Page Navigation -->
        <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
            <a href="view-customer.php?id=<?php echo $customer_id; ?>" style="display: inline-flex; align-items: center; text-decoration: none; color: var(--color-burgundy);">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back to Customer Details
            </a>
            
            <a href="new-appointment.php?customer_id=<?php echo $customer_id; ?>" style="display: inline-flex; align-items: center; padding: 10px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px;">
                <i class="fas fa-plus" style="margin-right: 5px;"></i> Schedule New Appointment
            </a>
        </div>
        
        <!-- Appointment Statistics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; text-align: center;">
                <div style="font-size: 2.5rem; color: var(--color-burgundy); margin-bottom: 10px;"><?php echo $status_counts['total']; ?></div>
                <div style="color: #666; font-weight: 500;">Total Appointments</div>
            </div>
            
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; text-align: center;">
                <div style="font-size: 2.5rem; color: #4caf50; margin-bottom: 10px;"><?php echo $status_counts['upcoming']; ?></div>
                <div style="color: #666; font-weight: 500;">Upcoming Appointments</div>
            </div>
            
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; text-align: center;">
                <div style="font-size: 2.5rem; color: #1976d2; margin-bottom: 10px;"><?php echo $status_counts['completed']; ?></div>
                <div style="color: #666; font-weight: 500;">Completed Appointments</div>
            </div>
            
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; text-align: center;">
                <div style="font-size: 2.5rem; color: #e53935; margin-bottom: 10px;"><?php echo $status_counts['cancelled']; ?></div>
                <div style="color: #666; font-weight: 500;">Cancelled Appointments</div>
            </div>
        </div>
        
        <!-- Filter Options -->
        <form action="" method="GET" style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; margin-bottom: 30px;">
            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>">
            
            <div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                <div style="flex: 1; min-width: 200px;">
                    <label for="status" style="display: block; margin-bottom: 5px; font-weight: 500; color: #666;">Status</label>
                    <select id="status" name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div style="flex: 1; min-width: 200px;">
                    <label for="date_filter" style="display: block; margin-bottom: 5px; font-weight: 500; color: #666;">Date Range</label>
                    <select id="date_filter" name="date_filter" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">All Dates</option>
                        <option value="upcoming" <?php echo $date_filter === 'upcoming' ? 'selected' : ''; ?>>Upcoming</option>
                        <option value="past" <?php echo $date_filter === 'past' ? 'selected' : ''; ?>>Past</option>
                        <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="this_week" <?php echo $date_filter === 'this_week' ? 'selected' : ''; ?>>This Week</option>
                        <option value="this_month" <?php echo $date_filter === 'this_month' ? 'selected' : ''; ?>>This Month</option>
                    </select>
                </div>
                
                <div style="align-self: flex-end;">
                    <button type="submit" style="padding: 10px 20px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer;">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
                
                <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                    <div style="align-self: flex-end;">
                        <a href="?customer_id=<?php echo $customer_id; ?>" style="display: inline-block; padding: 10px 15px; text-decoration: none; color: #666; border: 1px solid #ddd; border-radius: 5px;">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </form>
        
        <!-- Appointments Table -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px; overflow-x: auto;">
            <?php if (mysqli_num_rows($appointments_result) > 0): ?>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Date & Time</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Type</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Status</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Fee</th>
                            <th style="padding: 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Payment Status</th>
                            <th style="padding: 15px; text-align: center; border-bottom: 1px solid #eee; color: #444;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                            <tr>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <?php echo date('M j, Y g:i A', strtotime($appointment['appointment_datetime'])); ?>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <?php echo htmlspecialchars($appointment['consultation_type']); ?>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <?php 
                                    $status_class = '';
                                    $status_text = $appointment['status'];
                                    
                                    switch ($appointment['status']) {
                                        case 'confirmed':
                                            $status_class = 'background-color: #e8f5e9; color: #2e7d32;';
                                            $status_text = 'Confirmed';
                                            break;
                                        case 'pending':
                                            $status_class = 'background-color: #fff8e1; color: #f57f17;';
                                            $status_text = 'Pending';
                                            break;
                                        case 'cancelled':
                                            $status_class = 'background-color: #ffebee; color: #c62828;';
                                            $status_text = 'Cancelled';
                                            break;
                                        case 'completed':
                                            $status_class = 'background-color: #e3f2fd; color: #1565c0;';
                                            $status_text = 'Completed';
                                            break;
                                    }
                                    ?>
                                    <span style="padding: 5px 10px; border-radius: 4px; font-size: 0.85em; <?php echo $status_class; ?>">
                                        <?php echo $status_text; ?>
                                    </span>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    $<?php echo number_format($appointment['fee'], 2); ?>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee;">
                                    <?php 
                                    $payment_class = '';
                                    $payment_text = $appointment['payment_status'] ?? 'unpaid';
                                    
                                    switch ($payment_text) {
                                        case 'paid':
                                            $payment_class = 'background-color: #e8f5e9; color: #2e7d32;';
                                            $payment_text = 'Paid';
                                            break;
                                        case 'unpaid':
                                            $payment_class = 'background-color: #fff8e1; color: #f57f17;';
                                            $payment_text = 'Unpaid';
                                            break;
                                        case 'refunded':
                                            $payment_class = 'background-color: #e3f2fd; color: #1565c0;';
                                            $payment_text = 'Refunded';
                                            break;
                                    }
                                    ?>
                                    <span style="padding: 5px 10px; border-radius: 4px; font-size: 0.85em; <?php echo $payment_class; ?>">
                                        <?php echo $payment_text; ?>
                                    </span>
                                </td>
                                <td style="padding: 15px; border-bottom: 1px solid #eee; text-align: center;">
                                    <a href="view-appointment.php?id=<?php echo $appointment['id']; ?>" style="display: inline-block; padding: 5px 10px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 4px; font-size: 0.9em; margin-right: 5px;">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                    
                                    <?php if ($appointment['status'] !== 'cancelled' && $appointment['status'] !== 'completed' && strtotime($appointment['appointment_datetime']) > time()): ?>
                                        <a href="edit-appointment.php?id=<?php echo $appointment['id']; ?>" style="display: inline-block; padding: 5px 10px; background-color: #1976d2; color: white; text-decoration: none; border-radius: 4px; font-size: 0.9em;">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div style="margin-top: 30px; display: flex; justify-content: center;">
                        <div style="display: flex; gap: 5px;">
                            <?php 
                            // Build query string for pagination links
                            $query_params = [];
                            $query_params[] = 'customer_id=' . $customer_id;
                            if (!empty($status_filter)) $query_params[] = 'status=' . urlencode($status_filter);
                            if (!empty($date_filter)) $query_params[] = 'date_filter=' . urlencode($date_filter);
                            $query_string = implode('&', $query_params);
                            ?>
                            
                            <?php if ($page > 1): ?>
                                <a href="?<?php echo $query_string; ?>&page=<?php echo $page - 1; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #666;">
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
                                <a href="?<?php echo $query_string; ?>&page=<?php echo $i; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; <?php echo $i == $page ? 'background-color: var(--color-burgundy); color: white; border-color: var(--color-burgundy);' : 'color: #666;'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?<?php echo $query_string; ?>&page=<?php echo $page + 1; ?>" style="padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; text-decoration: none; color: #666;">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px;">
                    <div style="font-size: 4rem; color: #eee; margin-bottom: 20px;">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h3 style="margin-bottom: 10px; color: #666;">No Appointments Found</h3>
                    <?php if (!empty($status_filter) || !empty($date_filter)): ?>
                        <p style="color: #888; margin-bottom: 20px;">No appointments match your filter criteria.</p>
                        <a href="?customer_id=<?php echo $customer_id; ?>" style="display: inline-block; padding: 10px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px; margin-bottom: 10px;">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    <?php else: ?>
                        <p style="color: #888; margin-bottom: 20px;">This customer doesn't have any appointments yet.</p>
                    <?php endif; ?>
                    
                    <a href="new-appointment.php?customer_id=<?php echo $customer_id; ?>" style="display: inline-block; padding: 10px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px;">
                        <i class="fas fa-plus"></i> Schedule New Appointment
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?> 