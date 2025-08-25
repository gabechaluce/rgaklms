<?php
include 'includes/session.php';

if (isset($_POST['id'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $sql = "SELECT id, inventory_selection AS name FROM inventory_selection WHERE id = '$id'";
    $query = $conn->query($sql);
    $row = $query->fetch_assoc();
    echo json_encode($row);
}
?>