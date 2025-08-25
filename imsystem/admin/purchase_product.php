<?php
include 'includes/session.php';

if(isset($_POST['company_name'])){
    $company_name = $_POST['company_name'];
    
    $output = '<option value="">- Select Product -</option>';
    
    $sql = "SELECT DISTINCT product_name FROM products WHERE company_name = '$company_name' ORDER BY product_name";
    $query = $conn->query($sql);
    
    while($row = $query->fetch_assoc()){
        $output .= '<option value="'.$row['product_name'].'">'.$row['product_name'].'</option>';
    }
    
    echo json_encode($output);
}
?>