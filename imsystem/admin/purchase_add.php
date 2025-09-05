<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    // Get form inputs
    $inventory_selection = $_POST['inventory_selection'];
    $company_name = $_POST['company_name'];
    $product_name = $_POST['product_name'];
    $unit = $_POST['unit'];
    $specification = $_POST['specification'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $party_name = $_POST['party_name'];
    $purchase_type = $_POST['purchase_type'];
    $purchase_date = date('Y-m-d');
    
    // Validate inputs
    $errors = array();
 
    if(empty($inventory_selection)){
        $errors[] = 'Inventory Type is required';
    }

    if(empty($company_name)){
        $errors[] = 'Category is required';
    }
    
    if(empty($product_name)){
        $errors[] = 'Product is required';
    }
    
    if(empty($unit)){
        $errors[] = 'Unit is required';
    }
    
    if(empty($specification)){
        $errors[] = 'Specification is required';
    }
    
    if(empty($quantity) || $quantity <= 0){
        $errors[] = 'Quantity must be greater than zero';
    }
    
    if(empty($price) || $price <= 0){
        $errors[] = 'Price must be greater than zero';
    }
    
    if(empty($party_name)){
        $errors[] = 'Party name is required';
    }
    
    if(empty($purchase_type)){
        $errors[] = 'Purchase type is required';
    }
    
    if(count($errors) == 0){
        // Begin transaction for data integrity
        $conn->begin_transaction();
        
        try {
            // Check if an identical product exists in purchase_master
            $existing_check = "SELECT id, quantity, price FROM purchase_master 
                             WHERE product_name = ? 
                             AND company_name = ? 
                             AND unit = ?
                             AND specification = ?";
            
            $stmt_check_existing = $conn->prepare($existing_check);
            $stmt_check_existing->bind_param("ssss", 
                $product_name, $company_name, $unit, $specification);
            
            $stmt_check_existing->execute();
            $existing_result = $stmt_check_existing->get_result();
            
            if($existing_result->num_rows > 0){
                // Product exists, update the existing record
                $existing_row = $existing_result->fetch_assoc();
                $new_quantity = $existing_row['quantity'] + $quantity;
                $purchase_id = $existing_row['id'];
                
                $stmt_update_purchase = $conn->prepare("UPDATE purchase_master 
                SET quantity = ?, price = ?, purchase_date = ?, last_updated = NOW() 
                WHERE id = ?");
                
                $stmt_update_purchase->bind_param("ddsi", 
                    $new_quantity, $price, $purchase_date, $existing_row['id']);
                
                if(!$stmt_update_purchase->execute()){
                    throw new Exception("Error updating purchase: " . $stmt_update_purchase->error);
                }

                // Insert history record for update
                $history_sql = "INSERT INTO purchase_history 
                              (purchase_id, inventory_selection, company_name, product_name, 
                               unit, specification, quantity, price, party_name, purchase_type, purchase_date, action_type) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'update')";
                $history_stmt = $conn->prepare($history_sql);
                $history_stmt->bind_param("isssssdssss", 
                    $purchase_id, $inventory_selection, $company_name, $product_name, 
                    $unit, $specification, $quantity, $price, $party_name, $purchase_type, $purchase_date);
                $history_stmt->execute();
            }
            else{
                // Insert new purchase record
                $stmt_purchase = $conn->prepare("INSERT INTO purchase_master 
                (inventory_selection, company_name, product_name, unit, specification, quantity, price, 
                 party_name, purchase_type, purchase_date, last_updated) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            
                $stmt_purchase->bind_param("sssssdssss", 
                    $inventory_selection, $company_name, $product_name, $unit,
                    $specification, $quantity, $price, $party_name, $purchase_type, $purchase_date);

                if(!$stmt_purchase->execute()){
                    throw new Exception("Error inserting purchase: " . $stmt_purchase->error);
                }

                $purchase_id = $conn->insert_id;

                // Insert history record for create
                $history_sql = "INSERT INTO purchase_history 
                              (purchase_id, inventory_selection, company_name, product_name, 
                               unit, specification, quantity, price, party_name, purchase_type, purchase_date, action_type) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'create')";
                $history_stmt = $conn->prepare($history_sql);
                $history_stmt->bind_param("isssssdssss", 
                    $purchase_id, $inventory_selection, $company_name, $product_name, 
                    $unit, $specification, $quantity, $price, $party_name, $purchase_type, $purchase_date);
                $history_stmt->execute();
            }
            
            // Check if stock entry exists with the exact specification
            $stmt_check = $conn->prepare("SELECT * FROM stock_master 
            WHERE inventory_selection = ?
            AND product_company = ? 
            AND product_name = ? 
            AND product_unit = ?
            AND specification = ?");
        
            $stmt_check->bind_param("sssss", 
                $inventory_selection, $company_name, $product_name, $unit, $specification);
            
            $stmt_check->execute();
            $check_result = $stmt_check->get_result();
            
            if($check_result->num_rows == 0){
                // Insert new stock entry with the exact specification
                $stmt_stock = $conn->prepare("INSERT INTO stock_master 
                (inventory_selection, product_company, product_name, product_unit, specification, product_qty, product_selling_price, last_updated) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            
                $stmt_stock->bind_param("ssssssd", 
                    $inventory_selection, $company_name, $product_name, $unit,
                    $specification, $quantity, $price);
                
                if(!$stmt_stock->execute()){
                    throw new Exception("Error inserting stock: " . $stmt_stock->error);
                }
            }
            else{
                // Update existing stock - including price update
                $stmt_update = $conn->prepare("UPDATE stock_master 
                SET product_qty = product_qty + ?, 
                    product_selling_price = ?,
                    last_updated = NOW() 
                WHERE inventory_selection = ?
                AND product_company = ? 
                AND product_name = ? 
                AND product_unit = ?
                AND specification = ?");
            
                $stmt_update->bind_param("ddsssss", 
                    $quantity, $price, $inventory_selection, $company_name, 
                    $product_name, $unit, $specification);
                
                if(!$stmt_update->execute()){
                    throw new Exception("Error updating stock: " . $stmt_update->error);
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            $_SESSION['success'] = 'Purchase added successfully';
        }
        catch(Exception $e){
            // Rollback transaction
            $conn->rollback();
            
            // Log the error
            error_log($e->getMessage());
            
            // Set error message
            $_SESSION['error'] = ["Failed to update stock: " . $e->getMessage()];
        }
    }
    else{
        $_SESSION['error'] = $errors;
    }
}

header('location: purchase_master.php');
exit();
?>