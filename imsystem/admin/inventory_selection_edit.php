<?php
include 'includes/session.php';

if (isset($_POST['edit'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);

    if (empty($name)) {
        $_SESSION['error'] = 'Please enter selection name';
        header('location: inventory_selection.php');
        exit();
    }

    // Corrected column name
    $sql = "UPDATE inventory_selection SET inventory_selection = '$name' WHERE id = '$id'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Selection updated successfully';
    } else {
        $_SESSION['error'] = $conn->error;
    }
}

header('location: inventory_selection.php');
?>