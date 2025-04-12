<?php
$page_title = "Customer Details | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Check if customer ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Redirect to customers list if no ID is provided
    header('Location: my-customers.php');
    exit;
}

$customer_id = (int)$_GET['id'];

// Verify this customer belongs to the consultant by checking appointments
$verify_query = "SELECT COUNT(*) as count FROM appointments 
                WHERE customer_id = ? AND consultant_id = ?";
$verify_result = executeQuery($verify_query, [$customer_id, $consultant_id]);
$verify_row = mysqli_fetch_assoc($verify_result);

if ($verify_row['count'] == 0) {
    // This customer doesn't have appointments with this consultant
    setFlashMessage('error', 'You do not have permission to view this customer.');
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

// Get recent appointments with this consultant
$appointments_query = "SELECT * FROM appointments 
                      WHERE customer_id = ? AND consultant_id = ? 
                      ORDER BY appointment_datetime DESC 
                      LIMIT 5";
$appointments_result = executeQuery($appointments_query, [$customer_id, $consultant_id]);

// Get customer notes added by this consultant
$notes_query = "SELECT * FROM customer_notes 
               WHERE customer_id = ? AND consultant_id = ? 
               ORDER BY created_at DESC";
$notes_result = executeQuery($notes_query, [$customer_id, $consultant_id]);

// Handle adding a new note
$note_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_note'])) {
    $note_content = trim($_POST['note_content']);
    
    if (!empty($note_content)) {
        $insert_note_query = "INSERT INTO customer_notes (customer_id, consultant_id, content, created_at) 
                            VALUES (?, ?, ?, NOW())";
        executeQuery($insert_note_query, [$customer_id, $consultant_id, $note_content]);
        
        $note_message = 'Note added successfully!';
        
        // Refresh the page to show the new note
        header('Location: view-customer.php?id=' . $customer_id . '&note_added=1');
        exit;
    } else {
        $note_message = 'Note cannot be empty.';
    }
}

// Check for note_added parameter
if (isset($_GET['note_added']) && $_GET['note_added'] == 1) {
    $note_message = 'Note added successfully!';
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/customer-detail-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1>Customer Details</h1>
        <p style="max-width: 600px; margin: 20px auto 0;">View information and appointments for <?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <!-- Page Navigation -->
        <div style="margin-bottom: 30px;">
            <a href="my-customers.php" style="display: inline-flex; align-items: center; text-decoration: none; color: var(--color-burgundy);">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back to Customers
            </a>
        </div>
        
        <!-- Customer Information -->
        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; margin-bottom: 40px;">
            <!-- Left Column: Basic Info -->
            <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <div style="width: 100px; height: 100px; background-color: var(--color-burgundy); color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; margin: 0 auto 15px; font-size: 2.5rem;">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2 style="margin: 0; color: var(--color-dark); font-size: 1.5rem;"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></h2>
                    <p style="margin: 5px 0 0; color: #666;">Customer ID: <?php echo $customer['id']; ?></p>
                </div>
                
                <div style="margin-top: 30px;">
                    <h3 style="font-size: 1.1rem; margin: 0 0 15px; color: var(--color-dark); border-bottom: 1px solid #eee; padding-bottom: 10px;">Contact Information</h3>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Email</div>
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-envelope" style="margin-right: 10px; color: var(--color-burgundy);"></i>
                            <a href="mailto:<?php echo htmlspecialchars($customer['email']); ?>" style="text-decoration: none; color: var(--color-dark);">
                                <?php echo htmlspecialchars($customer['email']); ?>
                            </a>
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Phone</div>
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-phone" style="margin-right: 10px; color: var(--color-burgundy);"></i>
                            <a href="tel:<?php echo htmlspecialchars($customer['phone']); ?>" style="text-decoration: none; color: var(--color-dark);">
                                <?php echo htmlspecialchars($customer['phone']); ?>
                            </a>
                        </div>
                    </div>
                    
                    <?php if (!empty($customer['address'])): ?>
                    <div style="margin-bottom: 15px;">
                        <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Address</div>
                        <div style="display: flex; align-items: flex-start;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 10px; margin-top: 3px; color: var(--color-burgundy);"></i>
                            <div>
                                <?php echo nl2br(htmlspecialchars($customer['address'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div style="margin-bottom: 15px;">
                        <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Registration Date</div>
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-calendar" style="margin-right: 10px; color: var(--color-burgundy);"></i>
                            <?php echo date('F j, Y', strtotime($customer['created_at'])); ?>
                        </div>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <a href="customer-appointments.php?customer_id=<?php echo $customer['id']; ?>" style="display: block; text-align: center; padding: 12px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px; margin-bottom: 10px;">
                        <i class="fas fa-calendar-alt"></i> View All Appointments
                    </a>
                    <a href="#" onclick="window.print();" style="display: block; text-align: center; padding: 12px; background-color: #f5f5f5; color: #666; text-decoration: none; border-radius: 5px;">
                        <i class="fas fa-print"></i> Print Customer Info
                    </a>
                </div>
            </div>
            
            <!-- Right Column: Details and Appointments -->
            <div>
                <!-- Customer Details -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.2rem; margin: 0 0 20px; color: var(--color-dark);">Customer Details</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        <?php if (!empty($customer['date_of_birth'])): ?>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Date of Birth</div>
                            <div><?php echo date('F j, Y', strtotime($customer['date_of_birth'])); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($customer['country_of_origin'])): ?>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Country of Origin</div>
                            <div><?php echo htmlspecialchars($customer['country_of_origin']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($customer['immigration_status'])): ?>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Immigration Status</div>
                            <div><?php echo htmlspecialchars($customer['immigration_status']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($customer['visa_type'])): ?>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Visa Type</div>
                            <div><?php echo htmlspecialchars($customer['visa_type']); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($customer['visa_expiry'])): ?>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Visa Expiry Date</div>
                            <div><?php echo date('F j, Y', strtotime($customer['visa_expiry'])); ?></div>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($customer['service_interest'])): ?>
                        <div>
                            <div style="font-weight: 500; margin-bottom: 5px; color: #666;">Service Interest</div>
                            <div><?php echo htmlspecialchars($customer['service_interest']); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Recent Appointments -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.2rem; margin: 0 0 20px; color: var(--color-dark);">Recent Appointments</h3>
                    
                    <?php if (mysqli_num_rows($appointments_result) > 0): ?>
                        <div style="overflow-x: auto;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Date & Time</th>
                                        <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Type</th>
                                        <th style="padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; color: #444;">Status</th>
                                        <th style="padding: 12px 15px; text-align: center; border-bottom: 1px solid #eee; color: #444;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($appointment = mysqli_fetch_assoc($appointments_result)): ?>
                                        <tr>
                                            <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                                <?php echo date('M j, Y g:i A', strtotime($appointment['appointment_datetime'])); ?>
                                            </td>
                                            <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
                                                <?php echo htmlspecialchars($appointment['consultation_type']); ?>
                                            </td>
                                            <td style="padding: 12px 15px; border-bottom: 1px solid #eee;">
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
                                            <td style="padding: 12px 15px; border-bottom: 1px solid #eee; text-align: center;">
                                                <a href="view-appointment.php?id=<?php echo $appointment['id']; ?>" style="display: inline-block; padding: 5px 10px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 4px; font-size: 0.9em;">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div style="margin-top: 20px; text-align: right;">
                            <a href="customer-appointments.php?customer_id=<?php echo $customer['id']; ?>" style="text-decoration: none; color: var(--color-burgundy); font-weight: 500;">
                                View All Appointments <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 30px 20px;">
                            <div style="font-size: 3rem; color: #eee; margin-bottom: 15px;">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <p style="color: #666; margin-bottom: 15px;">No appointments found for this customer.</p>
                            <a href="new-appointment.php?customer_id=<?php echo $customer['id']; ?>" style="display: inline-block; padding: 10px 20px; background-color: var(--color-burgundy); color: white; text-decoration: none; border-radius: 5px;">
                                <i class="fas fa-plus"></i> Schedule New Appointment
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Customer Notes -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); padding: 30px;">
                    <h3 style="font-size: 1.2rem; margin: 0 0 20px; color: var(--color-dark);">Customer Notes</h3>
                    
                    <!-- Add Note Form -->
                    <form action="" method="POST" style="margin-bottom: 30px;">
                        <div style="margin-bottom: 15px;">
                            <textarea name="note_content" rows="4" placeholder="Add a note about this customer..." style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; resize: vertical;"></textarea>
                        </div>
                        
                        <?php if (!empty($note_message)): ?>
                            <div style="margin-bottom: 15px; padding: 10px; border-radius: 5px; background-color: #e8f5e9; color: #2e7d32;">
                                <?php echo $note_message; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div style="text-align: right;">
                            <button type="submit" name="add_note" style="padding: 10px 20px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer;">
                                <i class="fas fa-plus"></i> Add Note
                            </button>
                        </div>
                    </form>
                    
                    <!-- Notes List -->
                    <?php if (mysqli_num_rows($notes_result) > 0): ?>
                        <div style="border-top: 1px solid #eee; padding-top: 20px;">
                            <?php while ($note = mysqli_fetch_assoc($notes_result)): ?>
                                <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f5f5f5;">
                                    <div style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center;">
                                        <div style="font-weight: 500; color: var(--color-dark);">
                                            <i class="fas fa-sticky-note" style="margin-right: 5px; color: var(--color-burgundy);"></i> 
                                            Note from <?php echo date('F j, Y', strtotime($note['created_at'])); ?>
                                        </div>
                                        <div style="font-size: 0.85em; color: #666;">
                                            <?php echo date('g:i A', strtotime($note['created_at'])); ?>
                                        </div>
                                    </div>
                                    <div style="line-height: 1.6; color: #444;">
                                        <?php echo nl2br(htmlspecialchars($note['content'])); ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 20px; color: #666; background-color: #f9f9f9; border-radius: 5px;">
                            <i class="fas fa-info-circle" style="margin-right: 5px;"></i> 
                            No notes have been added for this customer yet.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include('includes/footer.php'); ?> 