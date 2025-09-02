<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $product_id = $_POST['product_id'];
    $inventory_selection = $_POST['inventory_selection'];
    $company_name = $_POST['company_name'];
    $unit = $_POST['unit'];
    $spec_name = $_POST['spec_name'];

    $sql = "UPDATE specifications SET 
            product_id = '$product_id', 
            inventory_selection = '$inventory_selection', 
            company_name = '$company_name', 
            unit = '$unit', 
            spec_name = '$spec_name' 
            WHERE id = '$id'";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Specification updated successfully';
    }
    else{
        $_SESSION['error'] = ['Error updating specification: ' . $conn->error];
    }
}
header('location: specification.php');
?>