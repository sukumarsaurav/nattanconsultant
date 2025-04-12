<?php
// Start output buffering to capture any errors
ob_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly

// Include database connection and functions
require_once 'includes/config.php';

// Set the content type to JSON
header('Content-Type: application/json');

// Log actual file inclusion for debugging
error_log("Config file included from: " . __FILE__);

// Make sure we have a valid database connection
$conn = getDbConnection();
if (!$conn) {
    http_response_code(500);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['error' => 'Failed to connect to database']);
    exit;
}

// Test query to verify connection
try {
    $test_query = mysqli_query($conn, "SELECT 1");
    if (!$test_query) {
        throw new Exception("Database connection test failed: " . mysqli_error($conn));
    }
} catch (Exception $e) {
    http_response_code(500);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

// Check if consultant is logged in
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['consultant_id'])) {
    http_response_code(401);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

// Get consultant ID from session
$consultant_id = $_SESSION['consultant_id'];

// Verify consultant exists in database
$consultant_check = "SELECT id FROM consultants WHERE id = ?";
$result = executeQuery($consultant_check, [$consultant_id]);

if ($result === false) {
    // Database query error
    http_response_code(500);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['error' => 'Database error checking consultant: ' . mysqli_error($conn)]);
    exit;
}

if (mysqli_num_rows($result) === 0) {
    http_response_code(401);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['error' => 'Invalid consultant account']);
    exit;
}

// Debug: Log all POST data
error_log("POST data: " . print_r($_POST, true));

// Verify data is received
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['time_slots_ajax']) || !isset($_POST['selected_day'])) {
    http_response_code(400);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['error' => 'Invalid request. Missing required fields.']);
    exit;
}

try {
    // Parse the time slots data
    $time_slots_data = json_decode($_POST['time_slots_ajax'], true);
    $selected_day = strtolower(trim($_POST['selected_day']));
    
    // Validate day_of_week value (must be one of the enum values)
    $valid_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    if (!in_array($selected_day, $valid_days)) {
        throw new Exception("Invalid day_of_week value: " . $selected_day . ". Must be one of: " . implode(', ', $valid_days));
    }
    
    // Debug: Log time slots data
    error_log("Time slots data: " . print_r($time_slots_data, true));
    error_log("Selected day: " . $selected_day);
    
    if (!is_array($time_slots_data) || empty($selected_day)) {
        throw new Exception('Invalid data received. time_slots_data: ' . (is_array($time_slots_data) ? 'is array' : 'not array') . ', selected_day: ' . $selected_day);
    }
    
    // Log the data for debugging
    error_log('Processing ' . count($time_slots_data) . ' time slot changes for day: ' . $selected_day);
    
    // Verify the availability_schedule table structure
    $table_check = mysqli_query($conn, "SHOW COLUMNS FROM availability_schedule");
    if ($table_check) {
        $columns = [];
        while ($col = mysqli_fetch_assoc($table_check)) {
            $columns[] = $col['Field'];
        }
        error_log("Available columns in availability_schedule: " . implode(", ", $columns));
        
        // Check if the table has been updated to use consultant_id
        if (!in_array('consultant_id', $columns)) {
            throw new Exception('Database schema update required: availability_schedule table needs a consultant_id column');
        }
    } else {
        error_log("Failed to check table structure: " . mysqli_error($conn));
    }
    
    // Process each time slot change
    foreach ($time_slots_data as $slot) {
        // Validate slot data
        if (!isset($slot['start']) || !isset($slot['end']) || !isset($slot['type']) || !isset($slot['checked'])) {
            throw new Exception('Incomplete slot data: ' . json_encode($slot));
        }
        
        $start_time = $slot['start'];
        $end_time = $slot['end'];
        $type = $slot['type']; // Capture type but don't use in query if column doesn't exist
        $is_checked = $slot['checked'];
        
        // Validate time format (should be HH:MM)
        if (!preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $start_time) || 
            !preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $end_time)) {
            throw new Exception("Invalid time format. Start: $start_time, End: $end_time");
        }
        
        // Determine which availability column to check based on consultation type
        $availability_column = null;
        if (strpos($type, 'Video') !== false) {
            $availability_column = 'video_available';
        } elseif (strpos($type, 'Phone') !== false) {
            $availability_column = 'phone_available';
        } elseif (strpos($type, 'In-Person') !== false) {
            $availability_column = 'in_person_available';
        }
        
        // Check if the consultation type is available for this day
        if ($availability_column && $is_checked) {
            // Check if day-specific setting exists
            $availability_check = mysqli_prepare($conn, 
                "SELECT $availability_column FROM day_consultation_availability 
                WHERE consultant_id = ? AND day_of_week = ?"
            );
            
            if (!$availability_check) {
                throw new Exception("Prepare availability check failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($availability_check, "is", $consultant_id, $selected_day);
            mysqli_stmt_execute($availability_check);
            mysqli_stmt_store_result($availability_check);
            
            // Default to allowing the time slot if no day-specific setting exists
            $type_available = 1;
            
            if (mysqli_stmt_num_rows($availability_check) > 0) {
                mysqli_stmt_bind_result($availability_check, $type_available);
                mysqli_stmt_fetch($availability_check);
            }
            
            mysqli_stmt_close($availability_check);
            
            // If consultation type is not available for this day, skip this slot
            if (!$type_available) {
                error_log("Skipping slot $start_time-$end_time of type $type because $availability_column is disabled for $selected_day");
                continue;
            }
        }
        
        // Log the operation
        error_log("Processing slot: $start_time-$end_time, Type: $type, Checked: " . ($is_checked ? 'Yes' : 'No'));
        
        // If checked, insert/ensure the slot exists
        if ($is_checked) {
            // Use direct mysqli prepared statement for better control
            // Check if slot already exists
            $check_stmt = mysqli_prepare($conn, 
                "SELECT id FROM availability_schedule 
                WHERE consultant_id = ? 
                AND day_of_week = ? 
                AND start_time = ? 
                AND end_time = ?"
            );
            
            if (!$check_stmt) {
                throw new Exception("Prepare check statement failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($check_stmt, "isss", $consultant_id, $selected_day, $start_time, $end_time);
            mysqli_stmt_execute($check_stmt);
            mysqli_stmt_store_result($check_stmt);
            $exists = mysqli_stmt_num_rows($check_stmt) > 0;
            mysqli_stmt_close($check_stmt);
            
            // If slot doesn't exist, insert it
            if (!$exists) {
                $insert_stmt = mysqli_prepare($conn, 
                    "INSERT INTO availability_schedule 
                     (consultant_id, day_of_week, start_time, end_time, is_available) 
                     VALUES (?, ?, ?, ?, 1)"
                );
                
                if (!$insert_stmt) {
                    throw new Exception("Prepare insert statement failed: " . mysqli_error($conn));
                }
                
                mysqli_stmt_bind_param($insert_stmt, "isss", $consultant_id, $selected_day, $start_time, $end_time);
                $success = mysqli_stmt_execute($insert_stmt);
                
                if (!$success) {
                    error_log("SQL Error in insert: " . mysqli_error($conn));
                    error_log("Insert Query: INSERT INTO availability_schedule (consultant_id, day_of_week, start_time, end_time, is_available) VALUES (?, ?, ?, ?, 1)");
                    error_log("Parameters: consultant_id=" . $consultant_id . ", day=" . $selected_day . 
                              ", start=" . $start_time . ", end=" . $end_time);
                    throw new Exception('Error inserting slot: ' . mysqli_error($conn));
                }
                
                mysqli_stmt_close($insert_stmt);
                error_log("Inserted new slot: $start_time-$end_time for $selected_day");
            } else {
                error_log("Slot already exists: $start_time-$end_time for $selected_day");
            }
        } 
        // If unchecked, remove the slot if it exists
        else {
            $delete_stmt = mysqli_prepare($conn, 
                "DELETE FROM availability_schedule 
                WHERE consultant_id = ? 
                AND day_of_week = ? 
                AND start_time = ? 
                AND end_time = ?"
            );
            
            if (!$delete_stmt) {
                throw new Exception("Prepare delete statement failed: " . mysqli_error($conn));
            }
            
            mysqli_stmt_bind_param($delete_stmt, "isss", $consultant_id, $selected_day, $start_time, $end_time);
            $success = mysqli_stmt_execute($delete_stmt);
            
            if (!$success) {
                error_log("SQL Error in delete: " . mysqli_error($conn));
                throw new Exception('Error deleting slot: ' . mysqli_error($conn));
            }
            
            mysqli_stmt_close($delete_stmt);
            error_log("Deleted slot: $start_time-$end_time for $selected_day");
        }
    }
    
    // Return success response
    http_response_code(200);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['success' => true, 'message' => 'Time slots updated successfully']);
    
} catch (Exception $e) {
    // Return error response with detailed message
    error_log('Error in ajax-save-timeslots.php: ' . $e->getMessage());
    http_response_code(500);
    // Clean the output buffer
    ob_end_clean();
    echo json_encode(['error' => 'Error updating time slots: ' . $e->getMessage()]);
}
?> 