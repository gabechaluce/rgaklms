<?php
include 'includes/session.php';

if (isset($_POST['add'])) {
    $name = $_POST['name'];

    $sql = "SELECT * FROM units WHERE unit = '$name'";
    $query = $conn->query($sql);
    
    if ($query->num_rows > 0) {
        $_SESSION['error'] = array('Unit already exists');
    } else {
        $sql = "INSERT INTO units (unit) VALUES ('$name')";
        if ($conn->query($sql)) {
            $_SESSION['success'] = 'Unit added successfully';
        } else {
            $_SESSION['error'] = array($conn->error);
        }
    }
}

header('location: product_unit.php');
?>