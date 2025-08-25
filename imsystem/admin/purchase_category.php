<?php
include 'includes/session.php';

if(isset($_POST['inventory_selection'])){
  $inventory = $_POST['inventory_selection'];
  
  $output = '<option value="">- Select Category -</option>';
  $sql = "SELECT DISTINCT company_name FROM products WHERE inventory_selection = '$inventory'";
  $query = $conn->query($sql);
  
  while($row = $query->fetch_assoc()){
    $output .= '<option value="'.$row['company_name'].'">'.$row['company_name'].'</option>';
  }
  
  echo json_encode($output);
}
?>