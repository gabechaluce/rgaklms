<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $purchase_type = $_POST['purchase_type'];
    $expiry_date = $_POST['expiry_date'];
    
    // Get current record details
    $old_query = "SELECT * FROM purchase_master WHERE id = '$id'";
    $old_result = $conn->query($old_query);
    $old_row = $old_result->fetch_assoc();
    $old_quantity = $old_row['quantity'];
    $old_price = $old_row['price'];
    $company_name = $old_row['company_name'];
    $product_name = $old_row['product_name'];
    $unit = $old_row['unit'];
    $specification = isset($old_row['specification']) ? $old_row['specification'] : '';
    $inventory_selection = $old_row['inventory_selection'];
    $party_name = $old_row['party_name'];
    
    // Calculate quantity difference
    $quantity_diff = $quantity - $old_quantity;
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Update purchase_master
        $sql = "UPDATE purchase_master SET 
                quantity = '$quantity', 
                price = '$price', 
                purchase_type = '$purchase_type', 
                expiry_date = '$expiry_date',
                last_updated = NOW()
                WHERE id = '$id'";
        
        if(!$conn->query($sql)){
            throw new Exception('Error updating purchase: ' . $conn->error);
        }
        
        // Check if specification column exists in stock_master
        $check_stock_cols = "SHOW COLUMNS FROM stock_master LIKE 'specification'";
        $stock_col_exists = $conn->query($check_stock_cols)->num_rows > 0;
        
        // Update stock_master if quantity changed
        if($quantity_diff != 0){
            if($stock_col_exists) {
                $update_stock = "UPDATE stock_master SET 
                               product_qty = product_qty + $quantity_diff,
                               product_selling_price = '$price',
                               last_updated = NOW()
                               WHERE product_company='$company_name' 
                               AND product_name='$product_name' 
                               AND product_unit='$unit'
                               AND specification='$specification'";
            } else {
                $update_stock = "UPDATE stock_master SET 
                               product_qty = product_qty + $quantity_diff,
                               product_selling_price = '$price',
                               last_updated = NOW()
                               WHERE product_company='$company_name' 
                               AND product_name='$product_name' 
                               AND product_unit='$unit'";
            }
            
            if(!$conn->query($update_stock)){
                throw new Exception('Error updating stock: ' . $conn->error);
            }
        } else if($price != $old_price) {
            // Update price even if quantity didn't change
            if($stock_col_exists) {
                $update_price = "UPDATE stock_master SET 
                               product_selling_price = '$price',
                               last_updated = NOW()
                               WHERE product_company='$company_name' 
                               AND product_name='$product_name' 
                               AND product_unit='$unit'
                               AND specification='$specification'";
            } else {
                $update_price = "UPDATE stock_master SET 
                               product_selling_price = '$price',
                               last_updated = NOW()
                               WHERE product_company='$company_name' 
                               AND product_name='$product_name' 
                               AND product_unit='$unit'";
            }
            
            if(!$conn->query($update_price)){
                throw new Exception('Error updating stock price: ' . $conn->error);
            }
        }
        
        // Add history record for the edit (check if history table exists and has specification column)
        $check_history_table = "SHOW TABLES LIKE 'purchase_history'";
        if($conn->query($check_history_table)->num_rows > 0) {
            $check_history_cols = "SHOW COLUMNS FROM purchase_history LIKE 'specification'";
            $history_col_exists = $conn->query($check_history_cols)->num_rows > 0;
            
            if($history_col_exists) {
                $history_sql = "INSERT INTO purchase_history 
                              (purchase_id, inventory_selection, company_name, product_name, 
                               unit, specification, quantity, price, party_name, purchase_type, purchase_date, action_type) 
                              VALUES ('$id', '$inventory_selection', '$company_name', '$product_name', 
                                      '$unit', '$specification', '$quantity', '$price', '$party_name', '$purchase_type', NOW(), 'edit')";
            } else {
                $history_sql = "INSERT INTO purchase_history 
                              (purchase_id, inventory_selection, company_name, product_name, 
                               unit, quantity, price, party_name, purchase_type, purchase_date, action_type) 
                              VALUES ('$id', '$inventory_selection', '$company_name', '$product_name', 
                                      '$unit', '$quantity', '$price', '$party_name', '$purchase_type', NOW(), 'edit')";
            }
            
            if(!$conn->query($history_sql)){
                throw new Exception('Error inserting history record: ' . $conn->error);
            }
        }
        
        // Commit transaction
        $conn->commit();
        
        $_SESSION['success'] = 'Purchase updated successfully';
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