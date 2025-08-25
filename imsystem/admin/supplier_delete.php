<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];
    
    $sql = "DELETE FROM party_info WHERE id = '$id'";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Supplier deleted successfully';
    }
    else{
        $_SESSION['error'] = ['Error deleting supplier: ' . $conn->error];
    }
}

header('location: supplier_add.php');
?>