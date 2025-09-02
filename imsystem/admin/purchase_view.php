<?php
include 'includes/session.php';

if(isset($_POST['id'])){
    $id = $_POST['id'];
    
    // Get current purchase details
    $sql = "SELECT * FROM purchase_master WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows > 0){
        $purchase = $result->fetch_assoc();
        
        // Get purchase history for this product (including specification)
        $history_sql = "SELECT ph.*, DATE_FORMAT(ph.action_time, '%M %d, %Y %h:%i %p') as formatted_time
                       FROM purchase_history ph 
                       WHERE ph.company_name = ? 
                       AND ph.product_name = ? 
                       AND ph.unit = ? 
                       AND ph.specification = ?
                       ORDER BY ph.action_time DESC";
        
        $history_stmt = $conn->prepare($history_sql);
        $history_stmt->bind_param("ssss", 
            $purchase['company_name'], 
            $purchase['product_name'], 
            $purchase['unit'],
            $purchase['specification']
        );
        $history_stmt->execute();
        $history_result = $history_stmt->get_result();
        
        $history = [];
        while($row = $history_result->fetch_assoc()){
            $history[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'product_name' => $purchase['product_name'],
            'company_name' => $purchase['company_name'],
            'unit' => $purchase['unit'],
            'specification' => $purchase['specification'],
            'history' => $history
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Purchase record not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>