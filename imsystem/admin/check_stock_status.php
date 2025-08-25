<?php
include 'includes/session.php';
if(isset($_POST['company_name']) && isset($_POST['product_name']) && isset($_POST['unit'])) {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $unit = $conn->real_escape_string($_POST['unit']);
    
    // Query to get current stock
    $sql = "SELECT product_qty FROM stock_master
            WHERE product_company = '$company_name' 
            AND product_name = '$product_name' 
            AND product_unit = '$unit'";
    
    $query = $conn->query($sql);
    
    if($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $available_stock = $row['product_qty'];
        
        // Get requested quantity
        $requested_quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        
        // Return JSON response
        echo json_encode([
            'success' => true,
            'available' => $available_stock,
            'requested' => $requested_quantity
        ]);
    } else {
        // Product not found
        echo json_encode([
            'success' => false,
            'message' => 'Product not found in inventory'
        ]);
    }
} else {
    // Invalid request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request parameters'
    ]);
}
?>