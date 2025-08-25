<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $inventory_selection = $_POST['edit_inventory_selection'];
    $name = $_POST['name'];
    
    $sql = "UPDATE company_name SET inventory_selection = '$inventory_selection', company_name = '$name' WHERE id = '$id'";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Category updated successfully';
    }
    else{
        $_SESSION['error'] = ['Error updating category: ' . $conn->error];
    }
}

header('location: product_category.php');
?>