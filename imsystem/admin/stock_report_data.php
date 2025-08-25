<?php
include 'includes/session.php';

// Get filter parameters
$inventoryType = isset($_POST['inventoryType']) ? $_POST['inventoryType'] : 'all';
$reportType = isset($_POST['reportType']) ? $_POST['reportType'] : 'all';

// Sanitize inputs
$inventoryType = $conn->real_escape_string($inventoryType);

// Build the query based on filters
$where = [];

// Filter by inventory type
if ($inventoryType != 'all') {
    $where[] = "inventory_selection = '$inventoryType'";
}

// Filter by report type
if ($reportType == 'low') {
    $where[] = "product_qty > 0 AND product_qty <= 5";
} elseif ($reportType == 'out') {
    $where[] = "product_qty <= 0";
} elseif ($reportType == 'low_and_out') {
    $where[] = "product_qty <= 5";  // Include both low and out of stock
}

// Combine where clauses
$whereClause = "";
if (!empty($where)) {
    $whereClause = "WHERE " . implode(" AND ", $where);
}

// Final query
$sql = "SELECT * FROM stock_master $whereClause ORDER BY product_company, product_name";
$query = $conn->query($sql);

$data = [];

// Fetch all rows
while ($row = $query->fetch_assoc()) {
    // Use null coalescing operator to handle missing keys
    $quantity = intval($row['product_qty'] ?? 0);
    
    $status = '';
    if ($quantity <= 0) {
        $status = '<span class="label label-danger">Out of Stock</span>';
    } elseif ($quantity <= 5) {
        $status = '<span class="label label-warning">Low Stock</span>';
    } else {
        $status = '<span class="label label-success">In Stock</span>';
    }
    
    $data[] = [
        'category' => $row['product_company'] ?? 'N/A',
        'inventory_type' => $row['inventory_selection'] ?? 'N/A',
        'product' => $row['product_name'] ?? 'N/A',
        'unit' => $row['product_unit'] ?? 'N/A',
        'price' => isset($row['product_selling_price']) ? 
                  number_format($row['product_selling_price'], 2) : '0.00',
        'quantity' => $quantity,
        'status' => $status
    ];
}

// Prepare response for DataTables
$response = [
    'data' => $data
];

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>