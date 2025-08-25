<?php
include 'includes/session.php';

if(isset($_POST['company_name']) && isset($_POST['product_name'])){
    $company_name = $_POST['company_name'];
    $product_name = $_POST['product_name'];
    
    $output = '<option value="">- Select Unit -</option>';
    
    $sql = "SELECT DISTINCT unit FROM products WHERE company_name = '$company_name' AND product_name = '$product_name' ORDER BY unit";
    $query = $conn->query($sql);
    
    while($row = $query->fetch_assoc()){
        $output .= '<option value="'.$row['unit'].'">'.$row['unit'].'</option>';
    }
    
    echo json_encode($output);
}
?>