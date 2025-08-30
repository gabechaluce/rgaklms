<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $firstname = $_POST['edit_firstname'];
    $lastname = $_POST['edit_lastname'];
    $businessname = $_POST['edit_businessname'];
    $contact = $_POST['edit_contact'];
    $address = $_POST['edit_address'];
    $city = $_POST['edit_city'];
    $remarks = $_POST['edit_remarks'];
    
    $sql = "UPDATE party_info SET 
            firstname = '$firstname', 
            lastname = '$lastname', 
            businessname = '$businessname', 
            contact = '$contact', 
            address = '$address', 
            city = '$city',
            remarks = '$remarks'
            WHERE id = '$id'";
            
    if($conn->query($sql)){
        $_SESSION['success'] = 'Supplier updated successfully';
    }
    else{
        $_SESSION['error'] = ['Error updating supplier: ' . $conn->error];
    }
}

header('location: supplier_add.php');
?>