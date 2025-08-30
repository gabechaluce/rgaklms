<?php
include 'includes/session.php';

// Check if project_id is provided
if (!isset($_POST['project_id']) || empty($_POST['project_id'])) {
    echo json_encode(['success' => false, 'message' => 'Project ID is required']);
    exit;
}

$project_id = intval($_POST['project_id']);

try {
    // Get project details with designer information
    $sql = "SELECT 
                p.id,
                p.name,
                p.full_name,
                p.description,
                p.location,
                p.dimension,
                p.project_cost,
                p.status,
                p.start_date,
                p.end_date,
                p.designer_ids,
                p.manager_id,
                GROUP_CONCAT(DISTINCT CONCAT(u_designer.firstname, ' ', u_designer.lastname) SEPARATOR ', ') as designer_names,
                GROUP_CONCAT(DISTINCT CONCAT(u_manager.firstname, ' ', u_manager.lastname) SEPARATOR ', ') as manager_names
            FROM project_list p
            LEFT JOIN users u_designer ON FIND_IN_SET(u_designer.id, p.designer_ids) AND u_designer.type = 4
            LEFT JOIN users u_manager ON FIND_IN_SET(u_manager.id, p.manager_id) AND u_manager.type = 14
            WHERE p.id = ?
            GROUP BY p.id";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $response = [
            'success' => true,
            'project_name' => $row['name'],
            'customer_name' => $row['full_name'],
            'location' => $row['location'],
            'description' => $row['description'],
            'designer' => $row['designer_names'] ?: 'Not Assigned',
            'manager' => $row['manager_names'] ?: 'Not Assigned',
            'dimension' => $row['dimension'] ?: '',
            'project_cost' => $row['project_cost'] ?: '0.00',
            'status' => $row['status'],
            'start_date' => $row['start_date'],
            'end_date' => $row['end_date']
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'Project not found'
        ];
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>