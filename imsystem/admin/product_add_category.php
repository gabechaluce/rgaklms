<?php
include 'includes/session.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

try {
    if(isset($_POST['inventory_selection']) && !empty($_POST['inventory_selection'])){
        $inventory_selection = $_POST['inventory_selection'];
        
        // Use prepared statement to prevent SQL injection
        $sql = "SELECT DISTINCT company_name FROM products WHERE inventory_selection = ? ORDER BY company_name";
        $stmt = $conn->prepare($sql);
        
        if(!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $inventory_selection);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $output = '<option value="">- Select Category -</option>';
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $output .= '<option value="' . htmlspecialchars($row['company_name']) . '">' . htmlspecialchars($row['company_name']) . '</option>';
            }
        } else {
            $output = '<option value="">No categories found</option>';
        }
        
        $stmt->close();
        echo json_encode($output);
        
    } else {
        echo json_encode('<option value="">- Select Category -</option>');
    }
    
} catch (Exception $e) {
    // Log error and return user-friendly message
    error_log("Error in product_add_category.php: " . $e->getMessage());
    echo json_encode('<option value="">Error loading categories</option>');
}
?>