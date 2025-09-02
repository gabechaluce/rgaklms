<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $product_id = $_POST['product_id'];
    $inventory_selection = $_POST['inventory_selection'];
    $company_name = $_POST['company_name'];
    $unit = $_POST['unit'];
    $spec_name = $_POST['spec_name'];

    $sql = "INSERT INTO specifications (product_id, inventory_selection, company_name, unit, spec_name) 
            VALUES ('$product_id', '$inventory_selection', '$company_name', '$unit', '$spec_name')";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Specification added successfully';
    }
    else{
        $_SESSION['error'] = ['Error adding specification: ' . $conn->error];
    }
}
header('location: specification.php');
?>