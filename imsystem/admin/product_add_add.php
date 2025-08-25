<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $inventory_selection = $_POST['inventory_selection'];
    $company_name = $_POST['company_name'];
    $product_name = $_POST['product_name'];
    $unit = $_POST['unit'];
    
    $sql = "SELECT * FROM products WHERE inventory_selection = '$inventory_selection' AND product_name = '$product_name' AND company_name = '$company_name'";
    $query = $conn->query($sql);
    
    if($query->num_rows > 0){
        $_SESSION['error'] = ['Product already exists in this category'];
    }
    else{
        $sql = "INSERT INTO products (inventory_selection, company_name, product_name, unit) VALUES ('$inventory_selection', '$company_name', '$product_name', '$unit')";
        if($conn->query($sql)){
            $_SESSION['success'] = 'Product added successfully';
        }
        else{
            $_SESSION['error'] = ['Error adding product: ' . $conn->error];
        }
    }
}
header('location: product_add.php');
?>