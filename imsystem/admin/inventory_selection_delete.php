<?php
include 'includes/session.php';

if (isset($_POST['delete'])) {
    $id = $conn->real_escape_string($_POST['id']);

    $sql = "DELETE FROM inventory_selection WHERE id = '$id'";
    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Selection deleted successfully';
    } else {
        $_SESSION['error'] = $conn->error;
    }
}

header('location: inventory_selection.php');
?>