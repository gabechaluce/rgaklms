<?php
include 'includes/session.php';

$bill_id = intval($_GET['bill_id']);
$type = $_GET['type'] ?? 'excel';

// Fetch bill data (same as generate_bill.php)
// ...

if($type == 'excel'){
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="Bill_'.$bill_header['bill_no'].'.xls"');
    
    echo '<table border="1">';
    echo '<tr><th colspan="6">Bill No: '.$bill_header['bill_no'].'</th></tr>';
    echo '<tr><th colspan="6">Date: '.$bill_header['sale_date'].'</th></tr>';
    echo '<tr>
            <th>Inventory</th>
            <th>Category</th>
            <th>Product</th>
            <th>Unit</th>
            <th>Price</th>
            <th>Total</th>
          </tr>';
    
    foreach($bill_details as $row){
        echo '<tr>
                <td>'.$row['inventory_selection'].'</td>
                <td>'.$row['company_name'].'</td>
                <td>'.$row['product_name'].'</td>
                <td>'.$row['unit'].'</td>
                <td>'.$row['price'].'</td>
                <td>'.$row['total'].'</td>
              </tr>';
    }
    
    echo '<tr><td colspan="5">Grand Total</td><td>'.$grand_total.'</td></tr>';
    echo '</table>';
}
elseif($type == 'doc'){
    header('Content-Type: application/vnd.ms-word');
    header('Content-Disposition: attachment; filename="Bill_'.$bill_header['bill_no'].'.doc"');
    
    echo '<html>';
    echo '<h2>Bill No: '.$bill_header['bill_no'].'</h2>';
    echo '<p>Date: '.$bill_header['sale_date'].'</p>';
    // Add other bill content similar to Excel export
    echo '</html>';
}