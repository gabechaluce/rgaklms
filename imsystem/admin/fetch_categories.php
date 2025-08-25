<?php
include 'includes/session.php';

if(isset($_POST['inventory_selection'])){
    $inventory_selection = $_POST['inventory_selection'];

    // Modify the SQL query to filter categories based on the selected inventory
    $sql = "SELECT * FROM company_name WHERE inventory_selection = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $inventory_selection);
    $stmt->execute();
    $result = $stmt->get_result();

    $categories = [];
    while($row = $result->fetch_assoc()){
        $categories[] = $row;
    }

    echo json_encode($categories);
}
?>
