<?php
include 'includes/session.php';

if(isset($_POST['product_name']) && isset($_POST['company_name'])) {
    $product_name = $_POST['product_name'];
    $company_name = $_POST['company_name'];
    
    // First, get the product ID from the product name
    $sql = "SELECT id FROM products WHERE product_name = '$product_name' AND company_name = '$company_name'";
    $query = $conn->query($sql);
    
    $output = '<option value="">- Select Specification -</option>';
    
    if($query->num_rows > 0) {
        $row = $query->fetch_assoc();
        $product_id = $row['id'];
        
        // Now get specifications for this product
        $sql = "SELECT * FROM specifications WHERE product_id = '$product_id' ORDER BY spec_name";
        $query = $conn->query($sql);
        
        if($query->num_rows > 0) {
            while($row = $query->fetch_assoc()) {
                $output .= '<option value="'.$row['spec_name'].'">'.$row['spec_name'].'</option>';
            }
        } else {
            // If no specifications found, create a default option
            $output .= '<option value="Standard">Standard</option>';
        }
    } else {
        $output .= '<option value="Standard">Standard</option>';
    }
    
    echo json_encode($output);
} else {
    echo json_encode('<option value="">Invalid request</option>');
}
?>