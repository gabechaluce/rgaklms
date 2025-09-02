<?php
include 'includes/session.php';

if(isset($_POST['company_name']) && isset($_POST['product_name']) && isset($_POST['unit'])){
    $company_name = $_POST['company_name'];
    $product_name = $_POST['product_name'];
    $unit = $_POST['unit'];
    
    $output = '<option value="">- Select Specification -</option>';
    
    // First, get the product ID
    $sql = "SELECT id FROM products WHERE company_name = '$company_name' AND product_name = '$product_name' AND unit = '$unit' LIMIT 1";
    $query = $conn->query($sql);
    
    if($query->num_rows > 0){
        $row = $query->fetch_assoc();
        $product_id = $row['id'];
        
        // Now get specifications for this product
        $spec_sql = "SELECT spec_name FROM specifications WHERE product_id = '$product_id' ORDER BY spec_name";
        $spec_query = $conn->query($spec_sql);
        
        while($spec_row = $spec_query->fetch_assoc()){
            $output .= '<option value="'.$spec_row['spec_name'].'">'.$spec_row['spec_name'].'</option>';
        }
    }
    
    echo json_encode($output);
}
?>