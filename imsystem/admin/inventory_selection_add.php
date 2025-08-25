<?php
include 'includes/session.php';

if (isset($_POST['add'])) {
    $name = $conn->real_escape_string($_POST['name']);

    if (empty($name)) {
        $_SESSION['error'] = 'Please enter selection name';
        header('location: inventory_selection.php');
        exit();
    }

    // Corrected column name to match your database
    $sql = "INSERT INTO inventory_selection (inventory_selection) VALUES ('$name')";
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Selection added successfully';
    } else {
        $_SESSION['error'] = $conn->error;
    }
}

header('location: inventory_selection.php');
?>