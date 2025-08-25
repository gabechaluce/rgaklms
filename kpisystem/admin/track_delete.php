<?php
include 'includes/session.php';

if(isset($_POST['delete'])){
    $id = $_POST['id'];

    $sql = "DELETE FROM track WHERE id = '$id'";
    
    if($conn->query($sql)){
        $_SESSION['success'] = "Track deleted successfully";
    } else {
        $_SESSION['error'][] = $conn->error;
    }
}

header('location: track_list.php');
?>