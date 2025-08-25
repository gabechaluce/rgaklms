<?php
// add_to_cart.php - For adding products to cart session
include 'includes/session.php';

if(isset($_POST['company_name']) && isset($_POST['product_name']) && isset($_POST['unit']) && isset($_POST['packing_size']) && isset($_POST['price']) && isset($_POST['qty'])){
    $company_name = $_POST['company_name'];
    $product_name = $_POST['product_name'];
    $unit = $_POST['unit'];
    $packing_size = $_POST['packing_size'];
    $price = $_POST['price'];
    $qty = $_POST['qty'];
    $total = $_POST['total'];
    
    // Check if product exists in stock with sufficient quantity
    $sql = "SELECT product_qty FROM stock_master WHERE product_company = '$company_name' AND product_name = '$product_name' AND product_unit = '$unit' AND packing_size = '$packing_size'";
    $res = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($res);
    
    if($row){
        $available_qty = $row['product_qty'];
        
        if($available_qty < $qty){
            echo "Only $available_qty items available in stock.";
            exit;
        }
    } else {
        echo "Product not found in stock.";
        exit;
    }
    
    // Initialize cart if not exists
    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();
    }
    
    // Add product to cart
    $product_array = array(
        'company_name' => $company_name,
        'product_name' => $product_name,
        'unit' => $unit,
        'packing_size' => $packing_size,
        'price' => $price,
        'qty' => $qty,
        'total' => $total
    );
    
    // Generate unique session ID for this cart item
    $session_id = md5($company_name.$product_name.$unit.$packing_size.time());
    $_SESSION['cart'][$session_id] = $product_array;
    
    echo "";
}
?>