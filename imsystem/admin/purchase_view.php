<?php
include 'includes/session.php';

if(isset($_POST['id'])) {
    $id = $_POST['id'];
    
    try {
        // Get current purchase details
        $sql = "SELECT * FROM purchase_master WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $current = $stmt->get_result()->fetch_assoc();
        
        if($current) {
            // Get all history for this product
            $history_sql = "SELECT * FROM purchase_history 
                          WHERE product_name = ? 
                          AND company_name = ?
                          AND unit = ?
                          ORDER BY action_time DESC";
            $history_stmt = $conn->prepare($history_sql);
            $history_stmt->bind_param("sss", 
                $current['product_name'],
                $current['company_name'],
                $current['unit']
            );
            $history_stmt->execute();
            $history = $history_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            echo json_encode([
                'success' => true,
                'product_name' => $current['product_name'],
                'company_name' => $current['company_name'],
                'unit' => $current['unit'],
                'history' => $history
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Purchase not found']);
        }
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}