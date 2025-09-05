<?php
include 'includes/session.php';

if(isset($_POST['company_name']) && isset($_POST['product_name']) && isset($_POST['unit']) && isset($_POST['specification'])) {
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $unit = $conn->real_escape_string($_POST['unit']);
    $specification = $conn->real_escape_string($_POST['specification']);
    
    // Try to get price with exact specification match
    $sql = "SELECT product_selling_price as price FROM stock_master 
            WHERE product_company = ? 
            AND product_name = ? 
            AND product_unit = ?
            AND specification = ?
            ORDER BY id DESC 
            LIMIT 1";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $company_name, $product_name, $unit, $specification);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'success' => true,
            'price' => $row['price'],
            'message' => 'Price found with specification'
        ]);
    } else {
        // If no price found with exact specification, try without specification
        $fallback_sql = "SELECT product_selling_price as price FROM stock_master 
                        WHERE product_company = ? 
                        AND product_name = ? 
                        AND product_unit = ?
                        AND (specification IS NULL OR specification = '' OR specification = 'N/A')
                        ORDER BY id DESC 
                        LIMIT 1";
        
        $fallback_stmt = $conn->prepare($fallback_sql);
        $fallback_stmt->bind_param("sss", $company_name, $product_name, $unit);
        $fallback_stmt->execute();
        $fallback_result = $fallback_stmt->get_result();
        
        if($fallback_result && $fallback_result->num_rows > 0) {
            $row = $fallback_result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'price' => $row['price'],
                'message' => 'Price found (general)'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'price' => 0,
                'message' => 'No price found'
            ]);
        }
    }
} else {
    echo json_encode([
        'success' => false,
        'price' => 0,
        'message' => 'Invalid parameters'
    ]);
}
?>