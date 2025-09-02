<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];

    $sql = "DELETE FROM specifications WHERE id = '$id'";
    if($conn->query($sql)){
        $_SESSION['success'] = 'Specification deleted successfully';
    }
    else{
        $_SESSION['error'] = ['Error deleting specification: ' . $conn->error];
    }
}
header('location: specification.php');
?>