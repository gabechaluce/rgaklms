<?php
include 'includes/session.php';

if(isset($_POST['inventory_selection'])) {
    $inventory_selection = $_POST['inventory_selection'];
    
    $sql = "SELECT DISTINCT company_name FROM purchase_master WHERE inventory_selection = '$inventory_selection' ORDER BY product_name";
    $query = $conn->query($sql);
    
    $output = '<option value="">- Select Category -</option>';
    while($row = $query->fetch_assoc()) {
        $output .= '<option value="'.$row['company_name'].'">'.$row['company_name'].'</option>';
    }
    
    echo json_encode($output);
}
?>