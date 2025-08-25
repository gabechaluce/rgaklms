<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $purchase_type = $_POST['purchase_type'];
    $expiry_date = $_POST['expiry_date'];
    
    // Get current quantity
    $old_query = "SELECT * FROM purchase_master WHERE id = '$id'";
    $old_result = $conn->query($old_query);
    $old_row = $old_result->fetch_assoc();
    $old_quantity = $old_row['quantity'];
    $company_name = $old_row['company_name'];
    $product_name = $old_row['product_name'];
    $unit = $old_row['unit'];
    $packing_size = $old_row['packing_size'];
    
    // Calculate quantity difference
    $quantity_diff = $quantity - $old_quantity;
    
    // Update purchase_master
    $sql = "UPDATE purchase_master SET quantity = '$quantity', price = '$price', purchase_type = '$purchase_type', expiry_date = '$expiry_date' WHERE id = '$id'";
    
    if($conn->query($sql)){
        // Update stock_master
        if($quantity_diff != 0){
            $update_stock = "UPDATE stock_master SET product_qty = product_qty + $quantity_diff WHERE product_company='$company_name' AND product_name='$product_name' AND product_unit='$unit' AND packing_size='$packing_size'";
            $conn->query($update_stock);
        }
        
        $_SESSION['success'] = 'Purchase updated successfully';
    }
    else{
        $_SESSION['error'] = array('Error updating purchase: ' . $conn->error);
    }
}

header('location: purchase_master.php');
?>