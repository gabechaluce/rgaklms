<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];
    
    // Get purchase details before deletion
    $get_purchase = "SELECT * FROM purchase_master WHERE id = '$id'";
    $purchase_result = $conn->query($get_purchase);
    $purchase_row = $purchase_result->fetch_assoc();
    
    $quantity = $purchase_row['quantity'];
    $company_name = $purchase_row['company_name'];
    $product_name = $purchase_row['product_name'];
    $unit = $purchase_row['unit'];
    $specification = $purchase_row['specification'];
    $inventory_selection = $purchase_row['inventory_selection'];
    $party_name = $purchase_row['party_name'];
    $purchase_type = $purchase_row['purchase_type'];
    $price = $purchase_row['price'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert history record for deletion
        $history_sql = "INSERT INTO purchase_history 
                      (purchase_id, inventory_selection, company_name, product_name, 
                       unit, specification, quantity, price, party_name, purchase_type, purchase_date, action_type) 
                      VALUES ('$id', '$inventory_selection', '$company_name', '$product_name', 
                              '$unit', '$specification', '$quantity', '$price', '$party_name', '$purchase_type', NOW(), 'delete')";
        
        if(!$conn->query($history_sql)){
            throw new Exception('Error inserting history record: ' . $conn->error);
        }
        
        // Delete from purchase_master
        $delete_purchase = "DELETE FROM purchase_master WHERE id = '$id'";
        if(!$conn->query($delete_purchase)){
            throw new Exception('Error deleting purchase: ' . $conn->error);
        }
        
        // Update stock_master - subtract the deleted quantity
        $update_stock = "UPDATE stock_master SET 
                       product_qty = product_qty - $quantity,
                       last_updated = NOW()
                       WHERE product_company='$company_name' 
                       AND product_name='$product_name' 
                       AND product_unit='$unit'
                       AND specification='$specification'";
        
        if(!$conn->query($update_stock)){
            throw new Exception('Error updating stock: ' . $conn->error);
        }
        
        // Check if stock quantity is now 0 or negative, and handle accordingly
        $check_stock = "SELECT product_qty FROM stock_master 
                       WHERE product_company='$company_name' 
                       AND product_name='$product_name' 
                       AND product_unit='$unit'
                       AND specification='$specification'";
        
        $stock_result = $conn->query($check_stock);
        if($stock_result && $stock_result->num_rows > 0){
            $stock_row = $stock_result->fetch_assoc();
            if($stock_row['product_qty'] <= 0){
                // Optionally delete the stock record if quantity is 0 or negative
                // Or you can choose to keep it and just set quantity to 0
                $cleanup_stock = "UPDATE stock_master SET product_qty = 0 
                                WHERE product_company='$company_name' 
                                AND product_name='$product_name' 
                                AND product_unit='$unit'
                                AND specification='$specification'";
                $conn->query($cleanup_stock);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = 'Purchase deleted successfully';
    }
    catch(Exception $e){
        // Rollback transaction
        $conn->rollback();
        
        // Log error
        error_log($e->getMessage());
        
        $_SESSION['error'] = array($e->getMessage());
    }
}

header('location: purchase_master.php');
exit();
?>