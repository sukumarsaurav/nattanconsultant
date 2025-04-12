<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection and functions
require_once 'includes/config.php';

// Only allow this script to be accessed by administrators or for development
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_admin = isset($_SESSION['admin_id']) || isset($_SESSION['consultant_id']);

if (!$is_admin && $_SERVER['REMOTE_ADDR'] !== '127.0.0.1') {
    http_response_code(403);
    echo "<h1>Access Denied</h1>";
    exit;
}

// Get connection
$conn = getDbConnection();
if (!$conn) {
    echo "<h1>Database Connection Error</h1>";
    exit;
}

// Define custom header CSS
$css = '
<style>
    body {
        font-family: Arial, sans-serif;
        line-height: 1.6;
        margin: 20px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    h1, h2 {
        color: #333;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: #f2f2f2;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .success {
        color: green;
    }
    .error {
        color: red;
    }
    .info {
        color: blue;
    }
    pre {
        background-color: #f5f5f5;
        padding: 10px;
        overflow-x: auto;
    }
</style>
';

// Define page header
echo "<!DOCTYPE html>
<html>
<head>
    <title>Availability Debugging Tool</title>
    $css
</head>
<body>
    <h1>Availability Debugging Tool</h1>";

// Function to print a table from a query result
function print_table($conn, $query, $params = [], $title = '') {
    echo "<h2>$title</h2>";
    
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        echo "<p class='error'>Query Error: " . mysqli_error($conn) . "</p>";
        return;
    }
    
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        echo "<p class='error'>Result Error: " . mysqli_error($conn) . "</p>";
        mysqli_stmt_close($stmt);
        return;
    }
    
    if (mysqli_num_rows($result) == 0) {
        echo "<p>No records found.</p>";
        mysqli_stmt_close($stmt);
        return;
    }
    
    // Get field information
    $fields = mysqli_fetch_fields($result);
    
    // Start table
    echo "<table>";
    
    // Header row
    echo "<tr>";
    foreach ($fields as $field) {
        echo "<th>" . htmlspecialchars($field->name) . "</th>";
    }
    echo "</tr>";
    
    // Data rows
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
        }
        echo "</tr>";
    }
    
    echo "</table>";
    
    mysqli_stmt_close($stmt);
}

// Print day_consultation_availability table
print_table(
    $conn,
    "SELECT * FROM day_consultation_availability ORDER BY consultant_id, day_of_week",
    [],
    "Day Consultation Availability Settings"
);

// Check for consultant ID parameter
$consultant_id = isset($_GET['consultant_id']) ? intval($_GET['consultant_id']) : null;

if ($consultant_id) {
    // Print consultant information
    print_table(
        $conn,
        "SELECT id, first_name, last_name, email, video_consultation_available, 
                phone_consultation_available, in_person_consultation_available
         FROM consultants
         WHERE id = ?",
        [$consultant_id],
        "Consultant Information"
    );
    
    // Print day_consultation_availability for this consultant
    print_table(
        $conn,
        "SELECT * FROM day_consultation_availability 
         WHERE consultant_id = ? 
         ORDER BY day_of_week",
        [$consultant_id],
        "Day Consultation Availability for This Consultant"
    );
    
    // Print availability_schedule for this consultant
    print_table(
        $conn,
        "SELECT * FROM availability_schedule 
         WHERE consultant_id = ? 
         ORDER BY day_of_week, start_time",
        [$consultant_id],
        "Scheduled Time Slots for This Consultant"
    );
} else {
    // List all consultants
    echo "<h2>Select a Consultant</h2>";
    echo "<ul>";
    
    $consultants_query = "SELECT id, first_name, last_name FROM consultants ORDER BY first_name, last_name";
    $consultants_result = mysqli_query($conn, $consultants_query);
    
    if ($consultants_result) {
        while ($consultant = mysqli_fetch_assoc($consultants_result)) {
            echo "<li><a href='?consultant_id=" . $consultant['id'] . "'>" . 
                  htmlspecialchars($consultant['first_name'] . ' ' . $consultant['last_name']) . 
                  "</a></li>";
        }
    }
    
    echo "</ul>";
}

// Print table structure information
echo "<h2>Table Structure Information</h2>";

// Check day_consultation_availability table structure
$table_check = mysqli_query($conn, "SHOW COLUMNS FROM day_consultation_availability");
echo "<h3>day_consultation_availability columns:</h3>";
echo "<pre>";
while ($col = mysqli_fetch_assoc($table_check)) {
    print_r($col);
}
echo "</pre>";

// Check availability_schedule table structure
$table_check = mysqli_query($conn, "SHOW COLUMNS FROM availability_schedule");
echo "<h3>availability_schedule columns:</h3>";
echo "<pre>";
while ($col = mysqli_fetch_assoc($table_check)) {
    print_r($col);
}
echo "</pre>";

// Show constraints
echo "<h3>Foreign Key Constraints:</h3>";
$constraints_query = "
SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
  AND REFERENCED_TABLE_SCHEMA IS NOT NULL
  AND (TABLE_NAME = 'day_consultation_availability' OR TABLE_NAME = 'availability_schedule'
       OR REFERENCED_TABLE_NAME = 'day_consultation_availability' OR REFERENCED_TABLE_NAME = 'availability_schedule')
";

$constraints_result = mysqli_query($conn, $constraints_query);
echo "<pre>";
if ($constraints_result) {
    while ($constraint = mysqli_fetch_assoc($constraints_result)) {
        print_r($constraint);
    }
} else {
    echo "Error fetching constraints: " . mysqli_error($conn);
}
echo "</pre>";

// Footer
echo "</body></html>";
?> 