<?php
include 'includes/session.php';

header('Content-Type: application/json');

if(isset($_POST['role_type'])) {
    $role_type = $_POST['role_type'];
    
    $response = array();
    
    try {
        if($role_type == 'all') {
            // Get all team members
            $sql = "SELECT id, CONCAT(firstname, ' ', lastname) as name, type FROM users ORDER BY firstname ASC";
            $stmt = $conn->prepare($sql);
        } else {
            // Get team members by specific role type (using numeric type directly)
            $sql = "SELECT id, CONCAT(firstname, ' ', lastname) as name, type FROM users WHERE type = ? ORDER BY firstname ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $role_type);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        while($row = $result->fetch_assoc()) {
            $response[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'type' => $row['type'] // Include type for debugging
            );
        }
        
        // Log for debugging
        error_log("Role type requested: " . $role_type);
        error_log("Results found: " . count($response));
        error_log("SQL Query: " . $sql);
        
        // Add debugging information to help troubleshoot
        if($role_type != 'all' && count($response) == 0) {
            // Check what types actually exist in the database
            $debug_sql = "SELECT DISTINCT type FROM users ORDER BY type";
            $debug_result = $conn->query($debug_sql);
            $existing_types = array();
            while($debug_row = $debug_result->fetch_assoc()) {
                $existing_types[] = $debug_row['type'];
            }
            error_log("Existing types in database: " . implode(', ', $existing_types));
        }
        
        echo json_encode($response);
        
    } catch(Exception $e) {
        error_log("Database error in get_team_members.php: " . $e->getMessage());
        echo json_encode(array('error' => 'Database error: ' . $e->getMessage()));
    }
    
} else {
    echo json_encode(array('error' => 'No role type specified'));
}
?>