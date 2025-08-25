<?php
include 'includes/session.php';

if(isset($_POST['company_name']) && isset($_POST['product_name']) && isset($_POST['unit']) && isset($_POST['qty'])){
    // Sanitize inputs to prevent SQL injection
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $unit = mysqli_real_escape_string($conn, $_POST['unit']);
    $requested_qty = (float) $_POST['qty']; // Convert to float for numeric comparison

    // Check if the product exists and has enough stock
    $query = "SELECT id, quantity FROM products 
              WHERE company_name = '$company_name' 
              AND product_name = '$product_name' 
              AND unit = '$unit'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $available_qty = $row['quantity'];
        $product_id = $row['id'];
        
        if ($available_qty >= $requested_qty) {
            // Sufficient stock available
            $response = array(
                'status' => 'success',
                'message' => 'Product is available in stock',
                'available' => $available_qty,
                'product_id' => $product_id
            );
        } else {
            // Insufficient stock
            $response = array(
                'status' => 'error',
                'message' => 'Insufficient stock. Only ' . $available_qty . ' ' . $unit . ' available',
                'available' => $available_qty,
                'product_id' => $product_id
            );
        }
    } else {
        // Product not found
        $response = array(
            'status' => 'error',
            'message' => 'Product not found'
        );
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    // Required parameters missing
    $response = array(
        'status' => 'error',
        'message' => 'Missing required parameters'
    );
    
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>