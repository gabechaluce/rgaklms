<?php
include 'includes/session.php';

if(isset($_POST['unit']) && isset($_POST['product_name']) && isset($_POST['company_name'])) {
    $unit = $_POST['unit'];
    $product_name = $_POST['product_name'];
    $company_name = $_POST['company_name'];
    
    $sql = "SELECT DISTINCT packing_size FROM purchase_master WHERE company_name = '$company_name' AND product_name = '$product_name' AND unit = '$unit' ORDER BY packing_size";
    $query = $conn->query($sql);
    
    $output = '<option value="">- Select Packing Size -</option>';
    while($row = $query->fetch_assoc()) {
        $output .= '<option value="'.$row['packing_size'].'">'.$row['packing_size'].'</option>';
    }
    
    echo json_encode($output);
}
?>