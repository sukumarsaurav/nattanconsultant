<?php
$page_title = "My Bookings | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Use the database connection from config.php instead of creating a new one
// The following lines replace the custom database connection and executeQuery function
/*
// Database connection
$conn = mysqli_connect("localhost", "root", "", "immigration_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to execute query
function executeQuery($sql, $params = []) {
    global $conn;
    
    // If we have parameters, prepare and execute statement
    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            // Build the parameter type string (s for string, i for integer, etc.)
            $types = '';
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= 'i';
                } elseif (is_float($param)) {
                    $types .= 'd';
                } elseif (is_string($param)) {
                    $types .= 's';
                } else {
                    $types .= 'b';
                }
            }
            
            // Bind parameters
            mysqli_stmt_bind_param($stmt, $types, ...$params);
            
            // Execute and get result
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            return $result;
        }
        return false;
    }
    
    // Regular query without parameters
    return mysqli_query($conn, $sql);
}
*/

// Set up pagination
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$results_per_page = 10;
$offset = ($current_page - 1) * $results_per_page;

// Set up filters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$date_filter = isset($_GET['date']) ? $_GET['date'] : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Build query conditions
$conditions = [];

if (!empty($status_filter)) {
    $conditions[] = "status = '" . sanitizeInput($status_filter) . "'";
}

if (!empty($type_filter)) {
    $conditions[] = "consultation_type = '" . sanitizeInput($type_filter) . "'";
}

if (!empty($search_term)) {
    $search_term = sanitizeInput($search_term);
    $conditions[] = "(first_name LIKE '%$search_term%' OR last_name LIKE '%$search_term%' OR email LIKE '%$search_term%')";
}

switch ($date_filter) {
    case 'today':
        $conditions[] = "DATE(appointment_datetime) = CURDATE()";
        break;
    case 'tomorrow':
        $conditions[] = "DATE(appointment_datetime) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
        break;
    case 'this-week':
        $conditions[] = "YEARWEEK(appointment_datetime, 1) = YEARWEEK(CURDATE(), 1)";
        break;
    case 'next-week':
        $conditions[] = "YEARWEEK(appointment_datetime, 1) = YEARWEEK(DATE_ADD(CURDATE(), INTERVAL 1 WEEK), 1)";
        break;
    case 'this-month':
        $conditions[] = "MONTH(appointment_datetime) = MONTH(CURDATE()) AND YEAR(appointment_datetime) = YEAR(CURDATE())";
        break;
    case 'upcoming':
        $conditions[] = "appointment_datetime >= NOW() AND status IN ('pending', 'confirmed')";
        break;
    case 'past':
        $conditions[] = "appointment_datetime < NOW()";
        break;
}

// Build the query
$where_clause = !empty($conditions) ? implode(" AND ", $conditions) : "1=1";

// Check if consultant_id or user_id column exists in the appointments table
$column_check_query = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                       WHERE TABLE_NAME = 'appointments' 
                       AND COLUMN_NAME = 'consultant_id' 
                       AND TABLE_SCHEMA = DATABASE()";
$column_check_result = executeQuery($column_check_query);
$column_check_row = mysqli_fetch_assoc($column_check_result);

// Decide which column to use (consultant_id or user_id)
$consultant_column = ($column_check_row['count'] > 0) ? 'consultant_id' : 'user_id';

// Always filter by the current consultant's ID
$where_clause .= " AND $consultant_column = " . (int)$consultant_id;

$sql = "SELECT a.*, CONCAT(c.first_name, ' ', c.last_name) as customer_name 
        FROM appointments a
        LEFT JOIN customers c ON a.customer_id = c.id  
        WHERE $where_clause 
        ORDER BY a.appointment_datetime DESC 
        LIMIT $offset, $results_per_page";
$result = executeQuery($sql);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM appointments WHERE $where_clause";
$count_result = executeQuery($count_sql);
$total_rows = mysqli_fetch_assoc($count_result)['total'];
$total_pages = ceil($total_rows / $results_per_page);
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1 data-aos="fade-up">My Bookings</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 600px; margin: 20px auto 0;">View and manage all your consultation appointments</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <!-- Filters and Search -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 20px; margin-bottom: 30px;">
            <h2 style="margin: 0 0 20px; color: var(--color-burgundy); font-size: 1.3rem;">Filter Bookings</h2>
            
            <form action="consultant-bookings.php" method="GET" style="display: flex; flex-wrap: wrap; gap: 15px;">
                <!-- Search -->
                <div style="flex: 1; min-width: 200px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--color-dark);">Search</label>
                    <div style="position: relative;">
                        <input type="text" name="search" placeholder="Client name or email" value="<?php echo htmlspecialchars($search_term); ?>" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                        <button type="submit" style="position: absolute; right: 0; top: 0; bottom: 0; background: none; border: none; padding: 0 15px; cursor: pointer; color: #666;">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div style="flex: 1; min-width: 150px;">
                    <label for="status" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--color-dark);">Status</label>
                    <select id="status" name="status" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $status_filter === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="no-show" <?php echo $status_filter === 'no-show' ? 'selected' : ''; ?>>No-Show</option>
                    </select>
                </div>
                
                <!-- Date Filter -->
                <div style="flex: 1; min-width: 150px;">
                    <label for="date" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--color-dark);">Date</label>
                    <select id="date" name="date" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                        <option value="">All Dates</option>
                        <option value="today" <?php echo $date_filter === 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="tomorrow" <?php echo $date_filter === 'tomorrow' ? 'selected' : ''; ?>>Tomorrow</option>
                        <option value="this-week" <?php echo $date_filter === 'this-week' ? 'selected' : ''; ?>>This Week</option>
                        <option value="next-week" <?php echo $date_filter === 'next-week' ? 'selected' : ''; ?>>Next Week</option>
                        <option value="this-month" <?php echo $date_filter === 'this-month' ? 'selected' : ''; ?>>This Month</option>
                        <option value="upcoming" <?php echo $date_filter === 'upcoming' ? 'selected' : ''; ?>>All Upcoming</option>
                        <option value="past" <?php echo $date_filter === 'past' ? 'selected' : ''; ?>>Past Appointments</option>
                    </select>
                </div>
                
                <!-- Type Filter -->
                <div style="flex: 1; min-width: 150px;">
                    <label for="type" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--color-dark);">Type</label>
                    <select id="type" name="type" style="width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                        <option value="">All Types</option>
                        <option value="Video Consultation" <?php echo $type_filter === 'Video Consultation' ? 'selected' : ''; ?>>Video</option>
                        <option value="Phone Consultation" <?php echo $type_filter === 'Phone Consultation' ? 'selected' : ''; ?>>Phone</option>
                        <option value="In-Person Consultation" <?php echo $type_filter === 'In-Person Consultation' ? 'selected' : ''; ?>>In-Person</option>
                    </select>
                </div>
                
                <!-- Filter Buttons -->
                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <button type="submit" style="padding: 10px 20px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                        Apply Filters
                    </button>
                    
                    <a href="consultant-bookings.php" style="padding: 10px 20px; background-color: #f1f1f1; color: #666; border: none; border-radius: 5px; text-decoration: none; font-weight: 500;">
                        Reset
                    </a>
                </div>
            </form>
        </div>
        
        <!-- Bookings Table -->
        <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 0; overflow: hidden; margin-bottom: 30px;">
            <div style="padding: 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">All Appointments</h2>
                <div style="color: #666;">
                    Showing <?php echo min($total_rows, $offset + 1); ?> - <?php echo min($total_rows, $offset + $results_per_page); ?> of <?php echo $total_rows; ?> appointments
                </div>
            </div>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 1px solid #f0f0f0;">
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">ID</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Client</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Type</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Date & Time</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Status</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Payment</th>
                            <th style="text-align: left; padding: 15px; color: #666; font-weight: 500;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while($booking = mysqli_fetch_assoc($result)): ?>
                                <tr style="border-bottom: 1px solid #f0f0f0;">
                                    <td style="padding: 15px;">#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td style="padding: 15px;">
                                        <div>
                                            <div style="font-weight: 500; color: var(--color-dark);"><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></div>
                                            <div style="font-size: 0.8rem; color: #666;"><?php echo $booking['email']; ?></div>
                                        </div>
                                    </td>
                                    <td style="padding: 15px;"><?php echo $booking['consultation_type']; ?></td>
                                    <td style="padding: 15px;">
                                        <div style="font-weight: 500; color: var(--color-dark);"><?php echo date('M j, Y', strtotime($booking['appointment_datetime'])); ?></div>
                                        <div style="font-size: 0.8rem; color: #666;"><?php echo date('g:i A', strtotime($booking['appointment_datetime'])); ?></div>
                                    </td>
                                    <td style="padding: 15px;">
                                        <?php 
                                            $status_color = '';
                                            switch(strtolower($booking['status'])) {
                                                case 'pending': $status_color = '#f5a623'; break;
                                                case 'confirmed': $status_color = '#4caf50'; break;
                                                case 'completed': $status_color = '#2196f3'; break;
                                                case 'cancelled': $status_color = '#f44336'; break;
                                                case 'no-show': $status_color = '#9e9e9e'; break;
                                                default: $status_color = '#9e9e9e';
                                            }
                                        ?>
                                        <span style="display: inline-block; padding: 5px 10px; background-color: <?php echo $status_color; ?>; color: white; border-radius: 20px; font-size: 0.8rem;">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px;">
                                        <?php 
                                            $payment_color = '';
                                            switch(strtolower($booking['payment_status'])) {
                                                case 'paid': $payment_color = '#4caf50'; break;
                                                case 'unpaid': $payment_color = '#f5a623'; break;
                                                case 'refunded': $payment_color = '#f44336'; break;
                                                default: $payment_color = '#9e9e9e';
                                            }
                                        ?>
                                        <span style="display: inline-block; padding: 5px 10px; background-color: <?php echo $payment_color; ?>; color: white; border-radius: 20px; font-size: 0.8rem;">
                                            <?php echo ucfirst($booking['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td style="padding: 15px;">
                                        <div style="display: flex; gap: 10px;">
                                            <a href="consultant-booking-view.php?id=<?php echo $booking['id']; ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background-color: #e3f2fd; color: #2196f3; border-radius: 5px; text-decoration: none;" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <a href="consultant-booking-edit.php?id=<?php echo $booking['id']; ?>" style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; background-color: #e8f5e9; color: #4caf50; border-radius: 5px; text-decoration: none;" title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 30px; color: #666;">No bookings found matching your criteria</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
        <div style="display: flex; justify-content: center; margin-top: 30px;">
            <ul style="display: flex; list-style: none; gap: 5px;">
                <?php if ($current_page > 1): ?>
                    <li>
                        <a href="?page=1<?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; echo !empty($type_filter) ? '&type=' . urlencode($type_filter) : ''; echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #f7f8fc; color: var(--color-burgundy); border-radius: 5px; text-decoration: none;">
                            <i class="fas fa-angle-double-left"></i>
                        </a>
                    </li>
                    <li>
                        <a href="?page=<?php echo $current_page - 1; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; echo !empty($type_filter) ? '&type=' . urlencode($type_filter) : ''; echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #f7f8fc; color: var(--color-burgundy); border-radius: 5px; text-decoration: none;">
                            <i class="fas fa-angle-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php 
                // Calculate which page numbers to show
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                for ($i = $start_page; $i <= $end_page; $i++): 
                ?>
                    <li>
                        <a href="?page=<?php echo $i; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; echo !empty($type_filter) ? '&type=' . urlencode($type_filter) : ''; echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: <?php echo $i === $current_page ? 'var(--color-burgundy)' : '#f7f8fc'; ?>; color: <?php echo $i === $current_page ? 'white' : 'var(--color-burgundy)'; ?>; border-radius: 5px; text-decoration: none; font-weight: <?php echo $i === $current_page ? '600' : '400'; ?>;">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <li>
                        <a href="?page=<?php echo $current_page + 1; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; echo !empty($type_filter) ? '&type=' . urlencode($type_filter) : ''; echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #f7f8fc; color: var(--color-burgundy); border-radius: 5px; text-decoration: none;">
                            <i class="fas fa-angle-right"></i>
                        </a>
                    </li>
                    <li>
                        <a href="?page=<?php echo $total_pages; ?><?php echo !empty($status_filter) ? '&status=' . urlencode($status_filter) : ''; echo !empty($date_filter) ? '&date=' . urlencode($date_filter) : ''; echo !empty($type_filter) ? '&type=' . urlencode($type_filter) : ''; echo !empty($search_term) ? '&search=' . urlencode($search_term) : ''; ?>" style="display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; background-color: #f7f8fc; color: var(--color-burgundy); border-radius: 5px; text-decoration: none;">
                            <i class="fas fa-angle-double-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include('includes/footer.php'); ?> 