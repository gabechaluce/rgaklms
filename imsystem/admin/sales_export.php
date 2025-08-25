<?php
include 'includes/session.php';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=sales_report.csv');

$output = fopen('php://output', 'w');
fputs($output, "\xEF\xBB\xBF"); // UTF-8 BOM

// Get all filter parameters
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$project_name = isset($_GET['project_name']) ? $_GET['project_name'] : '';
$customer = isset($_GET['customer']) ? $_GET['customer'] : '';
$bill_type = isset($_GET['bill_type']) ? $_GET['bill_type'] : '';
$inventory = isset($_GET['inventory']) ? $_GET['inventory'] : '';

// Build the same query as sales_report.php
$sql = "SELECT bh.id, bh.full_name, bh.bill_type, bh.date AS sale_date, bh.bill_no, bh.project_name,
               SUM(bd.total) AS total_amount,
               GROUP_CONCAT(DISTINCT bd.inventory_selection SEPARATOR ', ') AS inventory_selection 
        FROM billing_header bh
        LEFT JOIN billing_details bd ON bh.id = bd.bill_id
        WHERE 1";

if(!empty($date_from)){
    $sql .= " AND bh.date >= '".$date_from."'";
}
if(!empty($date_to)){
    $sql .= " AND bh.date <= '".$date_to."'";
}
if(!empty($project_name)){
    $sql .= " AND bh.project_name LIKE '%".$project_name."%'";
}
if(!empty($customer)){
    $sql .= " AND bh.full_name LIKE '%".$customer."%'";
}
if(!empty($bill_type)){
    $sql .= " AND bh.bill_type = '".$bill_type."'";
}
if(!empty($inventory)){
    $sql .= " AND bd.inventory_selection = '".$inventory."'";
}

$sql .= " GROUP BY bh.id ORDER BY bh.date DESC";

$query = $conn->query($sql);

// Header
fputcsv($output, [
    'Bill No', 
    'Date', 
    'Project Name',
    'Customer', 
    'Bill Type', 
    'Inventory', 
    'Total Amount'
], ',');

// Data
while($row = $query->fetch_assoc()){
    fputcsv($output, [
        $row['bill_no'],
        $row['sale_date'],
        $row['project_name'],
        $row['full_name'],
        $row['bill_type'],
        $row['inventory_selection'],
        number_format($row['total_amount'], 2)
    ], ',');
}

fclose($output);
exit;
?>