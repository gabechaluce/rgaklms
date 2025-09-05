<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];
    
    // Get purchase details before deletion
    $get_purchase = "SELECT * FROM purchase_master WHERE id = '$id'";
    $purchase_result = $conn->query($get_purchase);
    
    if(!$purchase_result || $purchase_result->num_rows === 0) {
        $_SESSION['error'] = array('Purchase record not found');
        header('location: purchase_master.php');
        exit();
    }
    
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
    $purchase_date = $purchase_row['purchase_date'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // STEP 1: Delete existing history records for this purchase_id to avoid FK constraint
        $delete_existing_history = "DELETE FROM purchase_history WHERE purchase_id = '$id'";
        if(!$conn->query($delete_existing_history)){
            throw new Exception('Error deleting existing history records: ' . $conn->error);
        }
        
        // STEP 2: Update stock_master - subtract the deleted quantity
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
        
        // Check if stock quantity is now 0 or negative
        $check_stock = "SELECT product_qty FROM stock_master 
                       WHERE product_company='$company_name' 
                       AND product_name='$product_name' 
                       AND product_unit='$unit'
                       AND specification='$specification'";
        
        $stock_result = $conn->query($check_stock);
        if($stock_result && $stock_result->num_rows > 0){
            $stock_row = $stock_result->fetch_assoc();
            if($stock_row['product_qty'] < 0){
                // Set quantity to 0 if it goes negative
                $cleanup_stock = "UPDATE stock_master SET product_qty = 0 
                                WHERE product_company='$company_name' 
                                AND product_name='$product_name' 
                                AND product_unit='$unit'
                                AND specification='$specification'";
                if(!$conn->query($cleanup_stock)){
                    throw new Exception('Error cleaning up stock: ' . $conn->error);
                }
            }
        }
        
        // STEP 3: Delete from purchase_master
        $delete_purchase = "DELETE FROM purchase_master WHERE id = '$id'";
        if(!$conn->query($delete_purchase)){
            throw new Exception('Error deleting purchase: ' . $conn->error);
        }
        
        // STEP 4: Insert deletion history record (after successful deletion)
        $history_sql = "INSERT INTO purchase_history 
                      (purchase_id, inventory_selection, company_name, product_name, 
                       unit, specification, quantity, price, party_name, purchase_type, purchase_date, action_type) 
                      VALUES ('$id', '$inventory_selection', '$company_name', '$product_name', 
                              '$unit', '$specification', '$quantity', '$price', '$party_name', '$purchase_type', '$purchase_date', 'delete')";
        
        if(!$conn->query($history_sql)){
            throw new Exception('Error inserting history record: ' . $conn->error);
        }
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = 'Purchase deleted successfully';
    }
    catch(Exception $e){
        // Rollback transaction
        $conn->rollback();
        
        // Log error
        error_log('Purchase deletion error: ' . $e->getMessage());
        
        $_SESSION['error'] = array($e->getMessage());
    }
}

header('location: purchase_master.php');
exit();
?>