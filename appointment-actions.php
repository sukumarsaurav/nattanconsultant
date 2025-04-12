<?php
// Include database configuration
require_once 'includes/config.php';

// Check if consultant is logged in
requireConsultantAuth();

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Helper function to get the correct consultant column name
function getConsultantColumnName() {
    // Check if consultant_id column exists in the appointments table
    $column_check_query = "SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.COLUMNS 
                           WHERE TABLE_NAME = 'appointments' 
                           AND COLUMN_NAME = 'consultant_id' 
                           AND TABLE_SCHEMA = DATABASE()";
    $column_check_result = executeQuery($column_check_query);
    $column_check_row = mysqli_fetch_assoc($column_check_result);
    
    // Return the correct column name based on database structure
    return ($column_check_row['count'] > 0) ? 'consultant_id' : 'user_id';
}

// Get action type
$action = isset($_POST['action']) ? sanitizeInput($_POST['action']) : '';

// Handle different action types
switch ($action) {
    case 'update_status':
        updateAppointmentStatus();
        break;
    case 'get_appointments':
        getAppointmentsForMonth();
        break;
    case 'delete_appointment':
        deleteAppointment();
        break;
    case 'reschedule_appointment':
        rescheduleAppointment();
        break;
    case 'add_appointment_note':
        addAppointmentNote();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}

// Function to update appointment status
function updateAppointmentStatus() {
    global $consultant_id;
    
    // Get parameters
    $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $status = isset($_POST['status']) ? sanitizeInput($_POST['status']) : '';
    
    // Validate inputs
    if ($appointment_id <= 0 || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment ID or status']);
        exit();
    }
    
    // Validate status value
    $valid_statuses = ['pending', 'confirmed', 'completed', 'cancelled', 'no-show'];
    if (!in_array(strtolower($status), $valid_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Invalid status value']);
        exit();
    }
    
    // Get the correct consultant column name
    $consultant_column = getConsultantColumnName();
    
    // Verify appointment belongs to this consultant
    $check_query = "SELECT id FROM appointments WHERE id = ? AND $consultant_column = ?";
    $result = executeQuery($check_query, [$appointment_id, $consultant_id]);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or unauthorized']);
        exit();
    }
    
    // Update appointment status using prepared statement
    $update_data = [
        'status' => $status,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $update_success = updateData('appointments', $update_data, "id = ? AND $consultant_column = ?", [$appointment_id, $consultant_id]);
    
    if ($update_success) {
        // Log status change in appointment history
        $history_data = [
            'appointment_id' => $appointment_id,
            'user_id' => $consultant_id,
            'user_type' => 'consultant',
            'action' => 'status_change',
            'details' => 'Status updated to: ' . $status,
            'created_at' => date('Y-m-d H:i:s')
        ];
        insertData('appointment_history', $history_data);
        
        echo json_encode(['success' => true, 'message' => 'Appointment status updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update appointment status']);
    }
}

// Function to get appointments for a specific month
function getAppointmentsForMonth() {
    global $consultant_id;
    
    // Get parameters
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');
    $month = isset($_POST['month']) ? (int)$_POST['month'] : date('m');
    
    // Validate month and year
    if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
        echo json_encode(['success' => false, 'message' => 'Invalid month or year']);
        exit();
    }
    
    // Get the correct consultant column name
    $consultant_column = getConsultantColumnName();
    
    // Format month for query
    $month_str = str_pad($month, 2, '0', STR_PAD_LEFT);
    
    // Get appointments for this consultant in the specified month using prepared statement
    $query = "SELECT a.*, 
              CONCAT(c.first_name, ' ', c.last_name) as client_name,
              DATE(a.appointment_datetime) as date
              FROM appointments a 
              LEFT JOIN customers c ON a.customer_id = c.id
              WHERE a.$consultant_column = ? 
              AND DATE_FORMAT(a.appointment_datetime, '%Y-%m') = ?
              ORDER BY a.appointment_datetime ASC";
    
    $result = executeQuery($query, [$consultant_id, "$year-$month_str"]);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Database query failed']);
        exit();
    }
    
    // Format appointments data
    $appointments = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = [
            'id' => $row['id'],
            'client_name' => $row['client_name'],
            'date' => $row['date'],
            'datetime' => $row['appointment_datetime'],
            'status' => $row['status'],
            'consultation_type' => $row['consultation_type']
        ];
    }
    
    echo json_encode(['success' => true, 'appointments' => $appointments]);
}

// Function to delete an appointment
function deleteAppointment() {
    global $consultant_id;
    
    // Get parameters
    $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    
    // Validate input
    if ($appointment_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment ID']);
        exit();
    }
    
    // Get the correct consultant column name
    $consultant_column = getConsultantColumnName();
    
    // Verify appointment belongs to this consultant
    $check_query = "SELECT id FROM appointments WHERE id = ? AND $consultant_column = ?";
    $result = executeQuery($check_query, [$appointment_id, $consultant_id]);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or unauthorized']);
        exit();
    }
    
    // Update appointment as deleted
    $update_data = [
        'status' => 'cancelled',
        'is_deleted' => 1,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $update_success = updateData('appointments', $update_data, "id = ? AND $consultant_column = ?", [$appointment_id, $consultant_id]);
    
    if ($update_success) {
        // Log deletion in appointment history
        $history_data = [
            'appointment_id' => $appointment_id,
            'user_id' => $consultant_id,
            'user_type' => 'consultant',
            'action' => 'cancelled',
            'details' => 'Appointment cancelled/deleted by consultant',
            'created_at' => date('Y-m-d H:i:s')
        ];
        insertData('appointment_history', $history_data);
        
        echo json_encode(['success' => true, 'message' => 'Appointment deleted successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete appointment']);
    }
}

// Function to reschedule an appointment
function rescheduleAppointment() {
    global $consultant_id;
    
    // Get parameters
    $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $datetime = isset($_POST['datetime']) ? sanitizeInput($_POST['datetime']) : '';
    
    // Validate inputs
    if ($appointment_id <= 0 || empty($datetime)) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment ID or datetime']);
        exit();
    }
    
    // Get the correct consultant column name
    $consultant_column = getConsultantColumnName();
    
    // Verify appointment belongs to this consultant
    $check_query = "SELECT id, appointment_datetime FROM appointments WHERE id = ? AND $consultant_column = ?";
    $result = executeQuery($check_query, [$appointment_id, $consultant_id]);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or unauthorized']);
        exit();
    }
    
    // Get old datetime for logging
    $row = mysqli_fetch_assoc($result);
    $old_datetime = $row['appointment_datetime'];
    
    // Update appointment datetime
    $update_data = [
        'appointment_datetime' => $datetime,
        'updated_at' => date('Y-m-d H:i:s')
    ];
    $update_success = updateData('appointments', $update_data, "id = ? AND $consultant_column = ?", [$appointment_id, $consultant_id]);
    
    if ($update_success) {
        // Log rescheduling in appointment history
        $history_data = [
            'appointment_id' => $appointment_id,
            'user_id' => $consultant_id,
            'user_type' => 'consultant',
            'action' => 'reschedule',
            'details' => "Appointment rescheduled from $old_datetime to $datetime",
            'created_at' => date('Y-m-d H:i:s')
        ];
        insertData('appointment_history', $history_data);
        
        echo json_encode(['success' => true, 'message' => 'Appointment rescheduled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to reschedule appointment']);
    }
}

// Function to add a note to an appointment
function addAppointmentNote() {
    global $consultant_id;
    
    // Get parameters
    $appointment_id = isset($_POST['appointment_id']) ? (int)$_POST['appointment_id'] : 0;
    $note = isset($_POST['note']) ? sanitizeInput($_POST['note']) : '';
    
    // Validate inputs
    if ($appointment_id <= 0 || empty($note)) {
        echo json_encode(['success' => false, 'message' => 'Invalid appointment ID or empty note']);
        exit();
    }
    
    // Get the correct consultant column name
    $consultant_column = getConsultantColumnName();
    
    // Verify appointment belongs to this consultant
    $check_query = "SELECT id FROM appointments WHERE id = ? AND $consultant_column = ?";
    $result = executeQuery($check_query, [$appointment_id, $consultant_id]);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or unauthorized']);
        exit();
    }
    
    // Insert note
    $note_data = [
        'appointment_id' => $appointment_id,
        'user_id' => $consultant_id,
        'user_type' => 'consultant',
        'note' => $note,
        'created_at' => date('Y-m-d H:i:s')
    ];
    
    $insert_success = insertData('appointment_notes', $note_data);
    
    if ($insert_success) {
        echo json_encode([
            'success' => true,
            'message' => 'Note added successfully',
            'note' => [
                'id' => $insert_success,
                'note' => $note,
                'created_at' => date('Y-m-d H:i:s'),
                'user_type' => 'consultant'
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add note']);
    }
}
?> 