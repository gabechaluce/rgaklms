<?php
include 'includes/session.php';

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];

    $sql = "SELECT * FROM units WHERE unit = '$name' AND id != '$id'";
    $query = $conn->query($sql);
    
    if ($query->num_rows > 0) {
        $_SESSION['error'] = array('Unit already exists');
    } else {
        $sql = "UPDATE units SET unit = '$name' WHERE id = '$id'";
        if ($conn->query($sql)) {
            $_SESSION['success'] = 'Unit updated successfully';
        } else {
            $_SESSION['error'] = array($conn->error);
        }
    }
}

header('location: product_unit.php');
?>