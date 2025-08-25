<?php
include 'includes/session.php';

header("Content-Type: application/vnd.ms-word; charset=UTF-8");
header("Content-Disposition: attachment; filename=sales_report.doc");

// Get filter parameters from GET
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$project_name = isset($_GET['project_name']) ? $_GET['project_name'] : '';
$customer = isset($_GET['customer']) ? $_GET['customer'] : '';
$bill_type = isset($_GET['bill_type']) ? $_GET['bill_type'] : '';
$inventory = isset($_GET['inventory']) ? $_GET['inventory'] : '';

// Build SQL query matching sales_report.php
$sql = "SELECT bh.id, bh.full_name, bh.bill_type, bh.date AS sale_date, bh.bill_no, bh.project_name,
               SUM(bd.total) AS total_amount,
               GROUP_CONCAT(DISTINCT bd.inventory_selection SEPARATOR ', ') AS inventory_selection 
        FROM billing_header bh
        LEFT JOIN billing_details bd ON bh.id = bd.bill_id
        WHERE 1";

if(!empty($date_from)){
    $sql .= " AND bh.date >= '".$conn->real_escape_string($date_from)."'";
}
if(!empty($date_to)){
    $sql .= " AND bh.date <= '".$conn->real_escape_string($date_to)."'";
}
if(!empty($project_name)){
    $sql .= " AND bh.project_name = '".$conn->real_escape_string($project_name)."'";
}
if(!empty($customer)){
    $sql .= " AND bh.full_name LIKE '%".$conn->real_escape_string($customer)."%'";
}
if(!empty($bill_type)){
    $sql .= " AND bh.bill_type = '".$conn->real_escape_string($bill_type)."'";
}
if(!empty($inventory)){
    $sql .= " AND bd.inventory_selection = '".$conn->real_escape_string($inventory)."'";
}

$sql .= " GROUP BY bh.id ORDER BY bh.date DESC";
$query = $conn->query($sql);

// Check if query executed successfully
if(!$query) {
    die("Query failed: " . $conn->error);
}

echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>";
echo "<head>";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">";
echo "<!--[if gte mso 9]><xml><w:WordDocument><w:View>Print</w:View><w:Zoom>100</w:Zoom><w:DoNotOptimizeForBrowser/></w:WordDocument></xml><![endif]-->";
echo "<style>";
echo "table { border-collapse: collapse; width: 100%; }";
echo "th, td { border: 1px solid black; padding: 5px; text-align: left; }";
echo "th { background-color: #f2f2f2; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>Sales Report</h1>";

// Display filter criteria
echo "<p><strong>Filters Applied:</strong><br>";
if(!empty($date_from) || !empty($date_to)) {
    echo "Date Range: ".htmlspecialchars($date_from)." to ".htmlspecialchars($date_to)."<br>";
}
if(!empty($project_name)) {
    echo "Project: ".htmlspecialchars($project_name)."<br>";
}
if(!empty($customer)) {
    echo "Customer: ".htmlspecialchars($customer)."<br>";
}
if(!empty($bill_type)) {
    echo "Bill Type: ".htmlspecialchars($bill_type)."<br>";
}
if(!empty($inventory)) {
    echo "Inventory: ".htmlspecialchars($inventory)."<br>";
}
echo "</p>";

echo "<table>";
echo "<tr>
        <th>Bill No</th>
        <th>Date</th>
        <th>Project Name</th>
        <th>Customer</th>
        <th>Bill Type</th>
        <th>Inventory</th>
        <th>Total Amount</th>
      </tr>";

if($query->num_rows > 0) {
    while($row = $query->fetch_assoc()){
        echo "<tr>
                <td>".htmlspecialchars($row['bill_no'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlspecialchars(date('M d, Y', strtotime($row['sale_date'])), ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlspecialchars($row['project_name'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlspecialchars($row['bill_type'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".htmlspecialchars($row['inventory_selection'], ENT_QUOTES, 'UTF-8')."</td>
                <td>".number_format($row['total_amount'], 2)."</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='7' style='text-align:center;'>No records found</td></tr>";
}

echo "</table>";
echo "</body></html>";
exit;
?>