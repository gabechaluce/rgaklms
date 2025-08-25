<?php
include 'includes/session.php';

if(isset($_POST['product_company']) && isset($_POST['product_name']) && isset($_POST['unit']) && isset($_POST['packing_size']) && isset($_POST['quantity'])) {
    $product_company = $_POST['product_company'];
    $product_name = $_POST['product_name'];
    $unit = $_POST['unit'];
    $packing_size = $_POST['packing_size'];
    $quantity = intval($_POST['quantity']);
    
    // Query to get current stock
    $sql = "SELECT quantity FROM product_details 
            WHERE company_name = '$product_company' 
            AND product_name = '$product_name' 
            AND unit = '$unit' 
            AND packing_size = '$packing_size'";
    
    $query = $conn->query($sql);
    
    if($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $available_stock = intval($row['quantity']);
        
        if($available_stock >= $quantity) {
            // Enough stock available
            echo json_encode([
                'success' => true,
                'message' => 'Stock available',
                'available' => $available_stock
            ]);
        } else {
            // Not enough stock
            echo json_encode([
                'success' => false,
                'message' => "Insufficient stock! Only $available_stock units available.",
                'available' => $available_stock
            ]);
        }
    } else {
        // Product not found
        echo json_encode([
            'success' => false,
            'message' => 'Product not found in inventory!',
            'available' => 0
        ]);
    }
} else {
    // Missing parameters
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
}
?>