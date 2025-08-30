<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $businessname = $_POST['businessname'];
    $contact = $_POST['contact'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $remarks = $_POST['remarks'];
    
    $sql = "SELECT * FROM party_info WHERE businessname = '$businessname'";
    $query = $conn->query($sql);
    
    if($query->num_rows > 0){
        $_SESSION['error'] = ['Supplier already exists'];
    }
    else{
        $sql = "INSERT INTO party_info (firstname, lastname, businessname, contact, address, city, remarks) VALUES ('$firstname', '$lastname', '$businessname', '$contact', '$address', '$city', '$remarks')";
        if($conn->query($sql)){
            $_SESSION['success'] = 'Supplier added successfully';
        }
        else{
            $_SESSION['error'] = ['Error adding supplier: ' . $conn->error];
        }
    }
}
header('location: supplier_add.php');
?>