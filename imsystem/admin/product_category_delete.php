<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];
    
    $sql = "DELETE FROM company_name WHERE id = '$id'";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Category deleted successfully';
    }
    else{
        $_SESSION['error'] = ['Error deleting category: ' . $conn->error];
    }
}

header('location: product_category.php');
?>