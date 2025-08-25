<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $inventory_selection = $_POST['edit_inventory_selection'];
    $company_name = $_POST['edit_company_name'];
    $product_name = $_POST['edit_product_name'];
    $unit = $_POST['edit_unit'];
    
    $sql = "UPDATE products SET 
            inventory_selection = ?,
            company_name = ?,
            product_name = ?,
            unit = ?
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", 
        $inventory_selection,
        $company_name,
        $product_name,
        $unit,
        $id
    );
    
    if($stmt->execute()){
        $_SESSION['success'] = 'Product updated successfully';
    }
    else{
        $_SESSION['error'] = 'Error updating product: ' . $stmt->error;
    }
    $stmt->close();
}

header('location: product_add.php');
?>