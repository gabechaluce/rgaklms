<?php
include 'include/session.php';

if(isset($_POST['inventory'])){
    $inventory = $_POST['inventory'];
    
    $sql = "SELECT DISTINCT company_name FROM products WHERE inventory_selection = '$inventory'";
    $query = $conn->query($sql);
    
    $options = '<option value="">- Select Category -</option>';
    while($row = $query->fetch_assoc()){
        $options .= "<option value='".$row['company_name']."'>".$row['company_name']."</option>";
    }
    
    echo $options;
}
?>