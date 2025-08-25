<?php 
include 'includes/session.php';
include 'includes/header.php';

// Get bill ID from URL
$bill_id = isset($_GET['bill_id']) ? intval($_GET['bill_id']) : 0;

// Fetch bill header
$sql = "SELECT * FROM billing_header WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$bill_header = $stmt->get_result()->fetch_assoc();

// Fetch bill details
$sql = "SELECT * FROM billing_details WHERE bill_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $bill_id);
$stmt->execute();
$bill_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calculate grand total
$grand_total = 0;
foreach($bill_details as $row){
    $grand_total += $row['total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bill <?= $bill_header['bill_no'] ?? '' ?></title>
    <style>
        .bill-container { width: 21cm; margin: 0 auto; padding: 20px; font-family: Arial, sans-serif; }
        .header { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; }
        .bill-info { margin: 15px 0; }
        .bill-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .bill-table th, .bill-table td { border: 1px solid #000; padding: 8px; }
        .text-right { text-align: right; }
        .terms { margin-top: 30px; }
        .print-hide { display: none; }
        @media print {
            .print-hide { display: none; }
            .bill-container { width: 100%; padding: 10px; }
        }
    </style>
</head>
<body>
    <div class="bill-container">
        <div class="header">
            <h2>RGA</h2>
            <p>Company Address<br>City, State, PIN Code<br>
            GSTIN: XXXXXXXX</p>
        </div>

        <div class="bill-info">
            <p>Bill No: <?= $bill_header['bill_no'] ?? '' ?></p>
            <p>Date: <?= $bill_header['sale_date'] ?? date('Y-m-d') ?></p>
        </div>

        <div class="customer-info">
            <p>Customer Name: <?= $bill_header['customer_name'] ?? '' ?></p>
            <p>Address: <?= $bill_header['customer_address'] ?? '' ?></p>
            <p>GSTIN: <?= $bill_header['customer_gst'] ?? '' ?></p>
        </div>

        <table class="bill-table">
            <thead>
                <tr>
                    <th>Inventory</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($bill_details as $row): ?>
                <tr>
                    <td><?= $row['inventory_selection'] ?? '' ?></td>
                    <td><?= $row['company_name'] ?? '' ?></td>
                    <td><?= $row['product_name'] ?? '' ?></td>
                    <td><?= $row['unit'] ?? '' ?></td>
                    <td class="text-right"><?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td class="text-right"><?= number_format($row['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="text-right"><strong>Grand Total:</strong></td>
                    <td class="text-right"><?= number_format($grand_total, 2) ?></td>
                </tr>
            </tfoot>
        </div>

        <div class="terms">
            <p><strong>Terms & Conditions:</strong></p>
            <p>1. Goods once sold cannot be returned</p>
            <p>2. Payment due within 30 days</p>
        </div>

        <div class="print-hide" style="margin-top: 30px;">
            <button onclick="window.print()" class="btn btn-primary">Print Bill</button>
            <a href="export_bill.php?bill_id=<?= $bill_id ?>&type=excel" class="btn btn-success">Export to Excel</a>
            <a href="export_bill.php?bill_id=<?= $bill_id ?>&type=doc" class="btn btn-info">Export to Word</a>
        </div>
    </div>
</body>
</html>