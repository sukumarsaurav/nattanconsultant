<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

// Store results
$results = [
    'success' => false,
    'tests' => [],
    'error' => null
];

try {
    // Test 1: Check if includes/config.php exists
    $configPath = __DIR__ . '/includes/config.php';
    $results['tests']['config_exists'] = file_exists($configPath);
    if (!$results['tests']['config_exists']) {
        throw new Exception("Configuration file not found at: " . $configPath);
    }
    
    // Test 2: Include configuration file
    require_once 'includes/config.php';
    $results['tests']['config_included'] = true;
    
    // Test 3: Test database connection
    if (!function_exists('getDbConnection')) {
        throw new Exception("getDbConnection function not found. Check includes/config.php");
    }
    
    $conn = getDbConnection();
    $results['tests']['db_connection'] = ($conn !== false);
    
    if (!$results['tests']['db_connection']) {
        throw new Exception("Failed to connect to database");
    }
    
    // Test 4: Check availability_schedule table exists
    $table_check = mysqli_query($conn, "SHOW TABLES LIKE 'availability_schedule'");
    $results['tests']['table_exists'] = (mysqli_num_rows($table_check) > 0);
    
    if (!$results['tests']['table_exists']) {
        throw new Exception("availability_schedule table does not exist");
    }
    
    // Test 5: Check table structure
    $columns_check = mysqli_query($conn, "SHOW COLUMNS FROM availability_schedule");
    $columns = [];
    while ($col = mysqli_fetch_assoc($columns_check)) {
        $columns[] = $col['Field'];
    }
    $results['tests']['table_columns'] = $columns;
    
    // Test 6: Check day_of_week column type
    $column_type_query = mysqli_query($conn, "SHOW COLUMNS FROM availability_schedule WHERE Field = 'day_of_week'");
    $column_info = mysqli_fetch_assoc($column_type_query);
    $results['tests']['day_of_week_type'] = $column_info['Type'];
    
    // Test 7: Check session functionality
    session_start();
    $results['tests']['session_started'] = true;
    $results['tests']['session_data'] = $_SESSION;
    
    // Test 8: Check current consultant_id
    $results['tests']['has_consultant_id'] = isset($_SESSION['consultant_id']);
    if ($results['tests']['has_consultant_id']) {
        $consultant_id = $_SESSION['consultant_id'];
        
        // Test 9: Check if consultant exists in database
        $consultant_check = "SELECT id FROM consultants WHERE id = ?";
        $stmt = mysqli_prepare($conn, $consultant_check);
        mysqli_stmt_bind_param($stmt, 'i', $consultant_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $results['tests']['consultant_exists'] = (mysqli_stmt_num_rows($stmt) > 0);
        mysqli_stmt_close($stmt);
        
        // Test 10: Try a simple insert/delete test
        if ($results['tests']['consultant_exists']) {
            // Try inserting a test record
            $test_day = 'monday';
            $test_start = '12:00';
            $test_end = '12:30';
            
            // First, delete any existing test record to avoid duplicates
            $delete_test = "DELETE FROM availability_schedule WHERE consultant_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?";
            $stmt = mysqli_prepare($conn, $delete_test);
            mysqli_stmt_bind_param($stmt, 'isss', $consultant_id, $test_day, $test_start, $test_end);
            $results['tests']['delete_test'] = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            // Now insert test record
            $insert_test = "INSERT INTO availability_schedule (consultant_id, day_of_week, start_time, end_time, is_available) VALUES (?, ?, ?, ?, 1)";
            $stmt = mysqli_prepare($conn, $insert_test);
            mysqli_stmt_bind_param($stmt, 'isss', $consultant_id, $test_day, $test_start, $test_end);
            $results['tests']['insert_test'] = mysqli_stmt_execute($stmt);
            
            if (!$results['tests']['insert_test']) {
                $results['tests']['insert_error'] = mysqli_error($conn);
            }
            
            mysqli_stmt_close($stmt);
            
            // Clean up test record
            $cleanup_test = "DELETE FROM availability_schedule WHERE consultant_id = ? AND day_of_week = ? AND start_time = ? AND end_time = ?";
            $stmt = mysqli_prepare($conn, $cleanup_test);
            mysqli_stmt_bind_param($stmt, 'isss', $consultant_id, $test_day, $test_start, $test_end);
            $results['tests']['cleanup_test'] = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }
    
    // All tests passed
    $results['success'] = true;
    
} catch (Exception $e) {
    $results['success'] = false;
    $results['error'] = $e->getMessage();
}

// Output results
echo json_encode($results, JSON_PRETTY_PRINT);
?> 