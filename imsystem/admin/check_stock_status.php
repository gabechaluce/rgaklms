<?php
include 'includes/session.php';

if(isset($_POST['company_name']) && isset($_POST['product_name']) && isset($_POST['unit'])) {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $unit = $conn->real_escape_string($_POST['unit']);
    $specification = isset($_POST['specification']) ? $conn->real_escape_string($_POST['specification']) : '';
    
$sql = "SELECT product_qty, specification FROM stock_master 
        WHERE product_company = '$company_name' 
        AND product_name = '$product_name' 
        AND product_unit = '$unit'";

// Handle specification matching more flexibly
if(!empty($specification) && $specification != '- Select Specification -') {
    $sql .= " AND (specification = '$specification' OR specification = 'Standard' 
              OR specification IS NULL OR specification = '' 
              OR specification LIKE '%$specification%')";
} else {
    // If no specification provided, check for any stock with this product
    $sql .= " AND (specification IS NULL OR specification = '' 
              OR specification = 'Standard' OR specification LIKE '%Standard%')";
}
    
    $query = $conn->query($sql);
    
    if($query && $query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $available_stock = (int)$row['product_qty'];
        
        // Get requested quantity
        $requested_quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
        
        // Return JSON response
        echo json_encode([
            'success' => true,
            'available' => $available_stock,
            'requested' => $requested_quantity,
            'message' => 'Stock found'
        ]);
    } else {
        // Product not found
        echo json_encode([
            'success' => false,
            'available' => 0,
            'message' => 'Product not found in inventory with the selected specification. Please add this product to stock first.'
        ]);
    }
} else {
    // Invalid request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request parameters. Required: company_name, product_name, unit'
    ]);
}
?>