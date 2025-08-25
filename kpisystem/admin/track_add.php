<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $date = $_POST['date'];
    $time = $_POST['time'];
    $destination = $_POST['destination'];
    $address = $_POST['address'];
    $purpose = $_POST['purpose'];
    $vehicle = $_POST['vehicle'];
    $driver = $_POST['driver'];
    $dept = $_POST['dept'];

    // Validate inputs (add more validation as needed)
    $errors = [];
    if(empty($date)) $errors[] = "Date is required";
    if(empty($time)) $errors[] = "Time is required";
    if(empty($destination)) $errors[] = "Destination is required";

    if(empty($errors)){
        $sql = "INSERT INTO track (date, time, destination, address, purpose, vehicle, driver, dept) 
                VALUES ('$date', '$time', '$destination', '$address', '$purpose', '$vehicle', '$driver', '$dept')";
        
        if($conn->query($sql)){
            $_SESSION['success'] = "Track added successfully";
        } else {
            $_SESSION['error'][] = $conn->error;
        }
    } else {
        $_SESSION['error'] = $errors;
    }
}

header('location: track_list.php');
?>