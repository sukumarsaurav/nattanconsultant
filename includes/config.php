<?php
/**
 * Database Configuration and Connection
 * 
 * This file manages database connections and provides 
 * security functions to prevent SQL injection.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration
define('DB_HOST', '193.203.184.121');
define('DB_USERNAME', 'u911550082_nattan');
define('DB_PASSWORD', 'Milk@sdk14'); // In production, consider using environment variables
define('DB_NAME', 'u911550082_nattan');

/**
 * Get database connection
 * @return mysqli Database connection object
 */
function getDbConnection() {
    static $conn = null;
    
    // If connection already exists, return it
    if ($conn !== null) {
        return $conn;
    }
    
    // Create new connection
    $conn = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    
    // Set charset to ensure proper encoding
    mysqli_set_charset($conn, "utf8mb4");
    
    return $conn;
}

/**
 * Sanitize input to prevent SQL injection
 * @param string $input The input to sanitize
 * @return string Sanitized input
 */
function sanitizeInput($input) {
    $conn = getDbConnection();
    // Check if input is null or not before trimming
    return mysqli_real_escape_string($conn, $input === null ? '' : trim($input));
}

/**
 * Execute a query safely
 * @param string $sql SQL query to execute
 * @param array $params Array of parameters to bind (optional)
 * @return mixed Query result
 */
function executeQuery($sql, $params = []) {
    $conn = getDbConnection();
    
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

/**
 * Get a single row from query result
 * @param string $sql SQL query
 * @param array $params Parameters to bind (optional)
 * @return array|null Row as associative array or null if no result
 */
function getRow($sql, $params = []) {
    $result = executeQuery($sql, $params);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Get multiple rows from query result
 * @param string $sql SQL query
 * @param array $params Parameters to bind (optional)
 * @return array Array of rows
 */
function getRows($sql, $params = []) {
    $result = executeQuery($sql, $params);
    $rows = [];
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    
    return $rows;
}

/**
 * Get count of rows from a query
 * @param string $sql SQL query
 * @param array $params Parameters to bind (optional)
 * @return int Number of rows
 */
function getCount($sql, $params = []) {
    $result = executeQuery($sql, $params);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if ($row) {
            return (int) reset($row); // First column
        }
    }
    
    return 0;
}

/**
 * Insert data into a table
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|false The ID of the inserted row or false on failure
 */
function insertData($table, $data) {
    $conn = getDbConnection();
    
    // Build the query
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    
    // Prepare and execute
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // Build types string and values array
        $types = '';
        $values = [];
        
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $values[] = $value;
        }
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        // Execute
        if (mysqli_stmt_execute($stmt)) {
            $insert_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            return $insert_id;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return false;
}

/**
 * Update data in a table
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @param string $where WHERE clause
 * @param array $whereParams Parameters for WHERE clause
 * @return bool Success or failure
 */
function updateData($table, $data, $where, $whereParams = []) {
    $conn = getDbConnection();
    
    // Build SET part of query
    $set = [];
    foreach ($data as $column => $value) {
        $set[] = "$column = ?";
    }
    $setClause = implode(', ', $set);
    
    $sql = "UPDATE $table SET $setClause WHERE $where";
    
    // Prepare and execute
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // Build types string and values array
        $types = '';
        $values = array_values($data);
        
        foreach ($values as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } elseif (is_string($value)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
        }
        
        // Add where params
        foreach ($whereParams as $param) {
            if (is_int($param)) {
                $types .= 'i';
            } elseif (is_float($param)) {
                $types .= 'd';
            } elseif (is_string($param)) {
                $types .= 's';
            } else {
                $types .= 'b';
            }
            $values[] = $param;
        }
        
        // Bind parameters
        mysqli_stmt_bind_param($stmt, $types, ...$values);
        
        // Execute
        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            return $affected_rows > 0;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return false;
}

/**
 * Delete data from a table
 * @param string $table Table name
 * @param string $where WHERE clause
 * @param array $params Parameters for WHERE clause
 * @return bool Success or failure
 */
function deleteData($table, $where, $params = []) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM $table WHERE $where";
    
    // Prepare and execute
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // Build types string
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
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }
        
        // Execute
        if (mysqli_stmt_execute($stmt)) {
            $affected_rows = mysqli_stmt_affected_rows($stmt);
            mysqli_stmt_close($stmt);
            return $affected_rows > 0;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    return false;
}

// Check if user is authenticated as a consultant
function isConsultantAuthenticated() {
    return isset($_SESSION['consultant_id']) && !empty($_SESSION['consultant_id']) && 
           isset($_SESSION['consultant_role']) && $_SESSION['consultant_role'] === 'consultant';
}

// Redirect if not authenticated
function requireConsultantAuth() {
    if (!isConsultantAuthenticated()) {
        header("Location: consultant-login.php");
        exit();
    }
} 