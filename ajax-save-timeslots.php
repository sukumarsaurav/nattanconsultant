<?php
// Include database connection and functions
require_once('../includes/db.php');
require_once('../includes/functions.php');

// Check if consultant is logged in
session_start();
if (!isset($_SESSION['consultant_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Verify consultant exists in database
$consultant_check = "SELECT id FROM consultants WHERE id = ?";
$result = executeQuery($consultant_check, [$consultant_id]);

if (mysqli_num_rows($result) === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid consultant account']);
    exit;
}

// Verify data is received
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['time_slots_ajax']) || !isset($_POST['selected_day'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

try {
    // Parse the time slots data
    $time_slots_data = json_decode($_POST['time_slots_ajax'], true);
    $selected_day = strtolower(trim($_POST['selected_day']));
    
    if (!is_array($time_slots_data) || empty($selected_day)) {
        throw new Exception('Invalid data received');
    }
    
    // Process each time slot change
    foreach ($time_slots_data as $slot) {
        $start_time = $slot['start'];
        $end_time = $slot['end'];
        $type = $slot['type'];
        $is_checked = $slot['checked'];
        
        // If checked, insert/ensure the slot exists
        if ($is_checked) {
            // Check if slot already exists
            $check_query = "SELECT id FROM availability_schedule 
                           WHERE admin_user_id = ? 
                           AND day_of_week = ? 
                           AND start_time = ? 
                           AND end_time = ? 
                           AND consultation_type = ?";
            
            $check_result = executeQuery($check_query, [
                $consultant_id,
                $selected_day,
                $start_time,
                $end_time,
                $type
            ]);
            
            // If slot doesn't exist, insert it
            if (mysqli_num_rows($check_result) == 0) {
                $insert_query = "INSERT INTO availability_schedule 
                               (admin_user_id, day_of_week, start_time, end_time, is_available, consultation_type) 
                               VALUES (?, ?, ?, ?, 1, ?)";
                
                executeQuery($insert_query, [
                    $consultant_id,
                    $selected_day,
                    $start_time,
                    $end_time,
                    $type
                ]);
            }
        } 
        // If unchecked, remove the slot if it exists
        else {
            $delete_query = "DELETE FROM availability_schedule 
                            WHERE admin_user_id = ? 
                            AND day_of_week = ? 
                            AND start_time = ? 
                            AND end_time = ? 
                            AND consultation_type = ?";
            
            executeQuery($delete_query, [
                $consultant_id,
                $selected_day,
                $start_time,
                $end_time,
                $type
            ]);
        }
    }
    
    // Return success response
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Time slots updated successfully']);
    
} catch (Exception $e) {
    // Return error response
    http_response_code(500);
    echo json_encode(['error' => 'Error updating time slots: ' . $e->getMessage()]);
}
?> 