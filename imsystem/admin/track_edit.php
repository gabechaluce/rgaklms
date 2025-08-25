<?php
include 'includes/session.php';

if(isset($_POST['edit'])){
    $id = $_POST['id'];
    $date = $_POST['edit_date'];
    $time = $_POST['edit_time'];
    $destination = $_POST['edit_destination'];
    $address = $_POST['edit_address'];
    $purpose = $_POST['edit_purpose'];
    $vehicle = $_POST['edit_vehicle'];
    $driver = $_POST['edit_driver'];
    $dept = $_POST['edit_dept'];

    // Validate inputs (add more validation as needed)
    $errors = [];
    if(empty($date)) $errors[] = "Date is required";
    if(empty($time)) $errors[] = "Time is required";
    if(empty($destination)) $errors[] = "Destination is required";

    if(empty($errors)){
        $sql = "UPDATE track 
                SET date = '$date', 
                    time = '$time', 
                    destination = '$destination', 
                    address = '$address', 
                    purpose = '$purpose', 
                    vehicle = '$vehicle', 
                    driver = '$driver', 
                    dept = '$dept' 
                WHERE id = '$id'";
        
        if($conn->query($sql)){
            $_SESSION['success'] = "Track updated successfully";
        } else {
            $_SESSION['error'][] = $conn->error;
        }
    } else {
        $_SESSION['error'] = $errors;
    }
}

header('location: track_list.php');
?>