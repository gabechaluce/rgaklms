<?php
include 'includes/session.php';

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json');

try {
    if(isset($_POST['company_name']) && isset($_POST['inventory_selection']) && 
       !empty($_POST['company_name']) && !empty($_POST['inventory_selection'])){
        
        $company_name = $_POST['company_name'];
        $inventory_selection = $_POST['inventory_selection'];

        // Use prepared statement to prevent SQL injection
        $sql = "SELECT * FROM products WHERE company_name = ? AND inventory_selection = ? ORDER BY product_name";
        $stmt = $conn->prepare($sql);
        
        if(!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ss", $company_name, $inventory_selection);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = '<option value="">- Select Product -</option>';
        
        if($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()){
                $output .= '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['product_name']) . '</option>';
            }
        } else {
            $output = '<option value="">No products found</option>';
        }
        
        $stmt->close();
        echo json_encode($output);
        
    } else {
        echo json_encode('<option value="">- Select Product -</option>');
    }
    
} catch (Exception $e) {
    // Log error and return user-friendly message
    error_log("Error in get_products_by_category.php: " . $e->getMessage());
    echo json_encode('<option value="">Error loading products</option>');
}
?>