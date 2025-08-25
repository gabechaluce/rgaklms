<?php
include 'includes/session.php';

if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    
    $sql = "DELETE FROM units WHERE id = '$id'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Unit deleted successfully';
    } else {
        $_SESSION['error'] = array($conn->error);
    }
}

header('location: product_unit.php');
?>