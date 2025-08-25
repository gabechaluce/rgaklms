<?php
include 'includes/session.php';

if(isset($_GET['id'])){
    $id = $_GET['id'];
    
    $sql = "DELETE FROM products WHERE id = '$id'";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Product deleted successfully';
    }
    else{
        $_SESSION['error'] = ['Error deleting product: ' . $conn->error];
    }
}

header('location: product_add.php');
?>