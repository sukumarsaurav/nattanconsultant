<?php
$page_title = "Booking Details | CANEXT Immigration";
include('includes/header.php');

// Check if consultant is logged in
session_start();
if (!isset($_SESSION['consultant_id']) || $_SESSION['consultant_role'] !== 'consultant') {
    header("Location: consultant-login.php");
    exit();
}

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Get booking ID from URL parameter
$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Database connection
$conn = mysqli_connect("localhost", "root", "", "immigration_db");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to execute query
function executeQuery($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

// Get booking details
$sql = "SELECT a.*, CONCAT(c.first_name, ' ', c.last_name) as customer_name, c.email as customer_email, c.phone as customer_phone, c.id as customer_id
        FROM appointments a 
        LEFT JOIN customers c ON a.email = c.email
        WHERE a.id = $booking_id AND a.consultant_id = $consultant_id";
$result = executeQuery($sql);

// Check if booking exists and belongs to this consultant
if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: consultant-bookings.php");
    exit();
}

$booking = mysqli_fetch_assoc($result);

// Get consultation notes
$sql_notes = "SELECT n.*, CONCAT(c.first_name, ' ', c.last_name) as consultant_name 
             FROM consultation_notes n
             JOIN consultants c ON n.consultant_id = c.id
             WHERE n.appointment_id = $booking_id
             ORDER BY n.created_at DESC";
$notes_result = executeQuery($sql_notes);

// Process form submission for updating status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $new_status = mysqli_real_escape_string($conn, $_POST['status']);
        $update_sql = "UPDATE appointments SET status = '$new_status' WHERE id = $booking_id AND consultant_id = $consultant_id";
        
        if (executeQuery($update_sql)) {
            // Update booking data after successful change
            $booking['status'] = $new_status;
            $success_message = "Booking status updated successfully!";
        } else {
            $error_message = "Failed to update booking status. Please try again.";
        }
    } elseif ($_POST['action'] === 'add_note') {
        $note_content = mysqli_real_escape_string($conn, $_POST['note_content']);
        
        if (!empty($note_content)) {
            $insert_note_sql = "INSERT INTO consultation_notes (appointment_id, consultant_id, notes, created_at) 
                               VALUES ($booking_id, $consultant_id, '$note_content', NOW())";
            
            if (executeQuery($insert_note_sql)) {
                // Refresh notes after adding
                $notes_result = executeQuery($sql_notes);
                $success_message = "Note added successfully!";
            } else {
                $error_message = "Failed to add note. Please try again.";
            }
        } else {
            $error_message = "Note content cannot be empty.";
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header" style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('images/consultation-header.jpg'); background-size: cover; background-position: center; padding: 60px 0; color: var(--color-light); text-align: center;background-color: var(--color-burgundy);">
    <div class="container">
        <h1 data-aos="fade-up">Booking Details</h1>
        <p data-aos="fade-up" data-aos-delay="100" style="max-width: 600px; margin: 20px auto 0;">View and manage consultation details</p>
    </div>
</section>

<section class="section" style="padding: 60px 0;">
    <div class="container">
        <!-- Back to Bookings Button -->
        <div style="margin-bottom: 20px;">
            <a href="consultant-bookings.php" style="display: inline-flex; align-items: center; gap: 8px; color: var(--color-burgundy); text-decoration: none; font-weight: 500;">
                <i class="fas fa-arrow-left"></i> Back to All Bookings
            </a>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <!-- Left Column: Booking Details -->
            <div>
                <!-- Booking Information Card -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); margin-bottom: 30px; overflow: hidden;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #f0f0f0;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">Booking Information</h2>
                        
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
                        <span style="display: inline-block; padding: 8px 15px; background-color: <?php echo $status_color; ?>; color: white; border-radius: 20px; font-size: 0.9rem; font-weight: 500;">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                    
                    <div style="padding: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                            <div>
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Booking ID</div>
                                    <div style="font-weight: 600; color: var(--color-dark);">#<?php echo str_pad($booking['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Consultation Type</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo $booking['consultation_type']; ?></div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Appointment Date</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo date('F j, Y', strtotime($booking['appointment_datetime'])); ?></div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Appointment Time</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo date('g:i A', strtotime($booking['appointment_datetime'])); ?></div>
                                </div>
                            </div>
                            
                            <div>
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Booking Date</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo date('F j, Y', strtotime($booking['created_at'])); ?></div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Payment Status</div>
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
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Payment Amount</div>
                                    <div style="font-weight: 600; color: var(--color-dark);">$<?php echo number_format($booking['payment_amount'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Client Information Card -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); margin-bottom: 30px; overflow: hidden;">
                    <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">Client Information</h2>
                    </div>
                    
                    <div style="padding: 20px;">
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                            <div>
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Name</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo $booking['first_name'] . ' ' . $booking['last_name']; ?></div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Email</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo $booking['email']; ?></div>
                                </div>
                            </div>
                            
                            <div>
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Phone</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo $booking['phone']; ?></div>
                                </div>
                                
                                <div style="margin-bottom: 20px;">
                                    <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Country</div>
                                    <div style="font-weight: 600; color: var(--color-dark);"><?php echo $booking['country'] ?? 'Not specified'; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information Card -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); margin-bottom: 30px; overflow: hidden;">
                    <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">Additional Information</h2>
                    </div>
                    
                    <div style="padding: 20px;">
                        <div style="margin-bottom: 20px;">
                            <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Immigration Purpose</div>
                            <div style="font-weight: 600; color: var(--color-dark);"><?php echo $booking['immigration_purpose'] ?? 'Not specified'; ?></div>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Special Requests</div>
                            <div style="font-weight: 600; color: var(--color-dark);"><?php echo !empty($booking['special_requests']) ? $booking['special_requests'] : 'None'; ?></div>
                        </div>
                    </div>
                </div>
                
                <!-- Consultation Notes Card -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); overflow: hidden;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 20px; border-bottom: 1px solid #f0f0f0;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">Consultation Notes</h2>
                    </div>
                    
                    <div style="padding: 20px;">
                        <form method="post" action="" style="margin-bottom: 30px;">
                            <input type="hidden" name="action" value="add_note">
                            
                            <div style="margin-bottom: 15px;">
                                <label for="note_content" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--color-dark);">Add New Note</label>
                                <textarea id="note_content" name="note_content" rows="4" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit; resize: vertical;" placeholder="Enter your notes about this consultation..."></textarea>
                            </div>
                            
                            <button type="submit" style="padding: 10px 20px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                                <i class="fas fa-plus"></i> Add Note
                            </button>
                        </form>
                        
                        <div style="border-top: 1px solid #f0f0f0; padding-top: 20px;">
                            <?php if ($notes_result && mysqli_num_rows($notes_result) > 0): ?>
                                <h3 style="margin-top: 0; margin-bottom: 15px; color: var(--color-dark); font-size: 1.1rem;">Previous Notes</h3>
                                
                                <div style="display: flex; flex-direction: column; gap: 15px;">
                                    <?php while ($note = mysqli_fetch_assoc($notes_result)): ?>
                                        <div style="background-color: #f9f9f9; border-radius: 8px; padding: 15px;">
                                            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                                                <div style="font-weight: 500; color: var(--color-burgundy);"><?php echo $note['consultant_name']; ?></div>
                                                <div style="font-size: 0.8rem; color: #666;"><?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?></div>
                                            </div>
                                            <div style="color: var(--color-dark); line-height: 1.5;"><?php echo nl2br(htmlspecialchars($note['notes'])); ?></div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            <?php else: ?>
                                <div style="text-align: center; padding: 20px; color: #666;">
                                    <p>No consultation notes have been added yet.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column: Actions -->
            <div>
                <!-- Action Card: Update Status -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); margin-bottom: 30px; overflow: hidden;">
                    <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">Update Status</h2>
                    </div>
                    
                    <div style="padding: 20px;">
                        <form method="post" action="">
                            <input type="hidden" name="action" value="update_status">
                            
                            <div style="margin-bottom: 20px;">
                                <label for="status" style="display: block; margin-bottom: 8px; font-weight: 500; color: var(--color-dark);">Booking Status</label>
                                <select id="status" name="status" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;">
                                    <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo $booking['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    <option value="no-show" <?php echo $booking['status'] === 'no-show' ? 'selected' : ''; ?>>No-Show</option>
                                </select>
                            </div>
                            
                            <button type="submit" style="width: 100%; padding: 12px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                                Update Status
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Quick Actions Card -->
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); margin-bottom: 30px; overflow: hidden;">
                    <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">Quick Actions</h2>
                    </div>
                    
                    <div style="padding: 20px;">
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <a href="mailto:<?php echo $booking['email']; ?>" style="display: flex; align-items: center; gap: 10px; padding: 12px; background-color: #e3f2fd; color: #2196f3; border-radius: 5px; text-decoration: none; font-weight: 500;">
                                <i class="fas fa-envelope"></i> Send Email to Client
                            </a>
                            
                            <?php if (!empty($booking['phone'])): ?>
                            <a href="tel:<?php echo $booking['phone']; ?>" style="display: flex; align-items: center; gap: 10px; padding: 12px; background-color: #e8f5e9; color: #4caf50; border-radius: 5px; text-decoration: none; font-weight: 500;">
                                <i class="fas fa-phone"></i> Call Client
                            </a>
                            <?php endif; ?>
                            
                            <a href="#" onclick="printBookingDetails(); return false;" style="display: flex; align-items: center; gap: 10px; padding: 12px; background-color: #f5f5f5; color: #666; border-radius: 5px; text-decoration: none; font-weight: 500;">
                                <i class="fas fa-print"></i> Print Booking Details
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Meeting Links Card (if applicable) -->
                <?php if ($booking['consultation_type'] === 'Video Consultation'): ?>
                <div style="background-color: white; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05); overflow: hidden;">
                    <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
                        <h2 style="margin: 0; color: var(--color-burgundy); font-size: 1.3rem;">Video Meeting</h2>
                    </div>
                    
                    <div style="padding: 20px;">
                        <?php if (!empty($booking['meeting_link'])): ?>
                            <div style="margin-bottom: 20px;">
                                <div style="font-weight: 500; color: #666; margin-bottom: 5px; font-size: 0.9rem;">Meeting Link</div>
                                <div style="word-break: break-all;">
                                    <a href="<?php echo $booking['meeting_link']; ?>" target="_blank" style="color: var(--color-burgundy); font-weight: 500;"><?php echo $booking['meeting_link']; ?></a>
                                </div>
                            </div>
                            
                            <a href="<?php echo $booking['meeting_link']; ?>" target="_blank" style="display: block; text-align: center; padding: 12px; background-color: var(--color-burgundy); color: white; border-radius: 5px; text-decoration: none; font-weight: 500;">
                                <i class="fas fa-video"></i> Join Meeting
                            </a>
                        <?php else: ?>
                            <div style="text-align: center; padding: 20px; color: #666;">
                                <p>No meeting link has been set for this consultation.</p>
                                <button onclick="addMeetingLink()" style="margin-top: 10px; padding: 10px 20px; background-color: var(--color-burgundy); color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 500;">
                                    <i class="fas fa-plus"></i> Add Meeting Link
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
// Print booking details
function printBookingDetails() {
    window.print();
}

// Add meeting link modal functionality
function addMeetingLink() {
    // In a real implementation, this would show a modal to add a meeting link
    const meetingLink = prompt("Enter the meeting link for this consultation:");
    
    if (meetingLink) {
        // This would normally submit to a server endpoint to update the meeting link
        // For now, we'll just reload the page
        alert("In a real implementation, this would save the meeting link: " + meetingLink);
        location.reload();
    }
}
</script>

<?php include('includes/footer.php'); ?> 