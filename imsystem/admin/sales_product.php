<?php
include 'includes/session.php';

if(isset($_POST['company_name'])) {
    $company_name = $_POST['company_name'];
    
    $sql = "SELECT DISTINCT product_name FROM purchase_master WHERE company_name = '$company_name' ORDER BY product_name";
    $query = $conn->query($sql);
    
    $output = '<option value="">- Select Product -</option>';
    while($row = $query->fetch_assoc()) {
        $output .= '<option value="'.$row['product_name'].'">'.$row['product_name'].'</option>';
    }
    
    echo json_encode($output);
}
?>