<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];
    
    // Get purchase details before deleting
    $query = "SELECT * FROM purchase_master WHERE id = '$id'";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();
    
    $company_name = $row['company_name'];
    $product_name = $row['product_name'];
    $unit = $row['unit'];
    $packing_size = $row['packing_size'];
    $quantity = $row['quantity'];
    
    // Delete the purchase
    $sql = "DELETE FROM purchase_master WHERE id = '$id'";
    
    if($conn->query($sql)){
        // Update stock_master to reduce quantity
        $update_stock = "UPDATE stock_master SET product_qty = product_qty - $quantity WHERE product_company='$company_name' AND product_name='$product_name' AND product_unit='$unit' AND packing_size='$packing_size'";
        $conn->query($update_stock);
        
        $_SESSION['success'] = 'Purchase deleted successfully';
    }
    else{
        $_SESSION['error'] = array('Error deleting purchase: ' . $conn->error);
    }
}

header('location: purchase_master.php');
?>