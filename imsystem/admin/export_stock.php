<?php
include 'includes/session.php';

// Get export parameters
$exportType = isset($_GET['type']) ? $_GET['type'] : 'excel';
$inventoryType = isset($_GET['inventoryType']) ? $_GET['inventoryType'] : 'all';
$reportType = isset($_GET['reportType']) ? $_GET['reportType'] : 'all';

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
}

// Combine where clauses
$whereClause = "";
if (!empty($where)) {
    $whereClause = "WHERE " . implode(" AND ", $where);
}

// Final query
$sql = "SELECT * FROM stock_master $whereClause ORDER BY product_company, product_name";
$query = $conn->query($sql);

// Create document title based on filters
$title = "Stock Report";
if ($inventoryType != 'all') {
    $title .= " - " . $inventoryType;
}
if ($reportType == 'low') {
    $title .= " - Low Stock";
} elseif ($reportType == 'out') {
    $title .= " - Out of Stock";
}

// Generate date string for filename
$dateStr = date('Y-m-d_H-i-s');

// Set appropriate headers based on export type
if ($exportType == 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $title . '_' . $dateStr . '.xls"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Start Excel XML
    echo '<?xml version="1.0" encoding="UTF-8"?>';
    echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';
    echo '<Worksheet ss:Name="Sheet1">';
    echo '<Table>';
    
    // Header row
    echo '<Row>';
    echo '<Cell><Data ss:Type="String">Category</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Inventory Type</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Product</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Unit</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Price</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Available Quantity</Data></Cell>';
    echo '<Cell><Data ss:Type="String">Stock Status</Data></Cell>';
    echo '</Row>';
    
    // Data rows
    while ($row = $query->fetch_assoc()) {
        $quantity = intval($row['product_qty']);
        $status = '';
        
        if ($quantity <= 0) {
            $status = 'Out of Stock';
        } elseif ($quantity <= 5) {
            $status = 'Low Stock';
        } else {
            $status = 'In Stock';
        }
        
        echo '<Row>';
        echo '<Cell><Data ss:Type="String">' . $row['product_company'] . '</Data></Cell>';
        echo '<Cell><Data ss:Type="String">' . $row['inventory_selection'] . '</Data></Cell>';
        echo '<Cell><Data ss:Type="String">' . $row['product_name'] . '</Data></Cell>';
        echo '<Cell><Data ss:Type="String">' . $row['product_unit'] . '</Data></Cell>';
        echo '<Cell><Data ss:Type="Number">' . $row['product_selling_price'] . '</Data></Cell>';
        echo '<Cell><Data ss:Type="Number">' . $quantity . '</Data></Cell>';
        echo '<Cell><Data ss:Type="String">' . $status . '</Data></Cell>';
        echo '</Row>';
    }
    
    // End Excel XML
    echo '</Table>';
    echo '</Worksheet>';
    echo '</Workbook>';
} else {
    // Word export (HTML format that can be opened in Word)
    header('Content-Type: application/msword');
    header('Content-Disposition: attachment; filename="' . $title . '_' . $dateStr . '.doc"');
    header('Pragma: no-cache');
    header('Expires: 0');
    
    // Start Word document
    echo '<!DOCTYPE html>';
    echo '<html>';
    echo '<head>';
    echo '<meta charset="utf-8">';
    echo '<title>' . $title . '</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; }';
    echo 'table { width: 100%; border-collapse: collapse; }';
    echo 'th, td { border: 1px solid #000; padding: 6px; }';
    echo 'th { background-color: #f0f0f0; }';
    echo '.out-stock { color: red; }';
    echo '.low-stock { color: orange; }';
    echo '.in-stock { color: green; }';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<h1>' . $title . '</h1>';
    echo '<p>Generated on: ' . date('F j, Y, g:i a') . '</p>';
    
    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Category</th>';
    echo '<th>Inventory Type</th>';
    echo '<th>Product</th>';
    echo '<th>Unit</th>';
    echo '<th>Price</th>';
    echo '<th>Available Quantity</th>';
    echo '<th>Stock Status</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    // Data rows
    while ($row = $query->fetch_assoc()) {
        $quantity = intval($row['product_qty']);
        $status = '';
        $class = '';
        
        if ($quantity <= 0) {
            $status = 'Out of Stock';
            $class = 'out-stock';
        } elseif ($quantity <= 5) {
            $status = 'Low Stock';
            $class = 'low-stock';
        } else {
            $status = 'In Stock';
            $class = 'in-stock';
        }
        
        echo '<tr>';
        echo '<td>' . $row['product_company'] . '</td>';
        echo '<td>' . $row['inventory_selection'] . '</td>';
        echo '<td>' . $row['product_name'] . '</td>';
        echo '<td>' . $row['product_unit'] . '</td>';
        echo '<td>' . number_format($row['product_selling_price'], 2) . '</td>';
        echo '<td>' . $quantity . '</td>';
        echo '<td class="' . $class . '">' . $status . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</body>';
    echo '</html>';
}
?>