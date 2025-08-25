<?php
include 'includes/session.php';

// Set content type to JSON
header('Content-Type: application/json');

if(isset($_POST['id'])) {
    $id = intval($_POST['id']); // Sanitize the ID
    
    // Check if evaluation exists
    $check_sql = "SELECT id FROM evaluations WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if($check_result->num_rows > 0) {
        // Delete the evaluation
        $delete_sql = "DELETE FROM evaluations WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $id);
        
        if($delete_stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Evaluation deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Database error: ' . $conn->error
            ]);
        }
        
        $delete_stmt->close();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Evaluation record not found'
        ]);
    }
    
    $check_stmt->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request - ID not provided'
    ]);
}

$conn->close();
?>