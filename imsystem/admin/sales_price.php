<?php
include 'includes/session.php';

if(isset($_POST['unit']) && isset($_POST['product_name']) && isset($_POST['company_name'])) {
    $unit = $_POST['unit'];
    $product_name = $_POST['product_name'];
    $company_name = $_POST['company_name'];
    
    // Don't return a price - price should only be set when specification is selected
    // Return 0 instead of fetching from database
    echo json_encode(0);
    
    /*
    // OLD CODE - COMMENTED OUT
    $sql = "SELECT product_selling_price as price FROM stock_master 
            WHERE product_company = ? AND product_name = ? AND product_unit = ? ORDER BY id DESC LIMIT 1";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $company_name, $product_name, $unit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        echo json_encode($row['price']);
    } else {
        echo json_encode(0);
    }
    */
} else {
    echo json_encode(0);
}
?>