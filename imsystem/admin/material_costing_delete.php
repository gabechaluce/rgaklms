<?php
include 'includes/session.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid costing ID.']);
    exit;
}

// Check if user is admin
if ($_SESSION['login_type'] != 1) {
    echo json_encode(['success' => false, 'message' => 'Access denied. Only administrators can delete records.']);
    exit;
}

$id = intval($_POST['id']);

// Verify record exists
$check_qry = $conn->query("SELECT id FROM material_costing WHERE id = $id");
if (!$check_qry || $check_qry->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Record not found.']);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Delete related records (foreign keys will handle this automatically due to CASCADE)
    $delete_materials = $conn->query("DELETE FROM material_costing_materials WHERE costing_id = $id");
    $delete_accessories = $conn->query("DELETE FROM material_costing_accessories WHERE costing_id = $id");
    $delete_paint = $conn->query("DELETE FROM material_costing_paint_materials WHERE costing_id = $id");
    $delete_labor = $conn->query("DELETE FROM material_costing_labor WHERE costing_id = $id");
    $delete_jobout = $conn->query("DELETE FROM material_costing_jobout WHERE costing_id = $id");
    
    // Delete main record
    $delete_main = $conn->query("DELETE FROM material_costing WHERE id = $id");
    
    if (!$delete_main) {
        throw new Exception("Failed to delete main record: " . $conn->error);
    }
    
    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Material costing record deleted successfully.']);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>