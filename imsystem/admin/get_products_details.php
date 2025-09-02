<?php
include 'includes/session.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

try {
    if(isset($_POST['product_id']) && !empty($_POST['product_id'])){
        $product_id = $_POST['product_id'];

        // Use prepared statement to prevent SQL injection
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $conn->prepare($sql);
        
        if(!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode($row);
        } else {
            echo json_encode(['error' => 'Product not found']);
        }
        
        $stmt->close();
        
    } else {
        echo json_encode(['error' => 'Product ID not provided']);
    }
    
} catch (Exception $e) {
    // Log error and return user-friendly message
    error_log("Error in get_product_details.php: " . $e->getMessage());
    echo json_encode(['error' => 'Error loading product details']);
}
?>