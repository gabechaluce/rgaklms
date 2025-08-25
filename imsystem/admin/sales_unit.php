<?php
include 'includes/session.php';

if(isset($_POST['product_name']) && isset($_POST['company_name'])){
    $product_name = $_POST['product_name'];
    $company_name = $_POST['company_name'];
    
    $sql = "SELECT DISTINCT unit FROM purchase_master 
            WHERE product_name = ? AND company_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $product_name, $company_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $options = '<option value="">- Select Unit -</option>';
    while($row = $result->fetch_assoc()){
        $options .= '<option value="'.$row['unit'].'">'.$row['unit'].'</option>';
    }
    
    echo json_encode($options);
}
?>