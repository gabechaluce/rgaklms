<?php
include 'includes/session.php';

if(isset($_POST['sales_data'])) {
    $data = json_decode($_POST['sales_data'], true);
    
    if($data) {
        $conn->begin_transaction();
        
        try {
            // Insert billing header
            $project_name = $conn->real_escape_string($data['project_name']);
            $module_title = $conn->real_escape_string($data['module_title']);
            $location = $conn->real_escape_string($data['location']);
            $remarks = $conn->real_escape_string($data['remarks']);
            $designer = $conn->real_escape_string($data['designer']);
            $dimension = $conn->real_escape_string($data['dimension']);

            $customer_name = $conn->real_escape_string($data['customer_name']);
            $bill_type = $conn->real_escape_string($data['bill_type']);
           
            $sale_date = $conn->real_escape_string($data['sale_date']);
            
            $sql = "INSERT INTO billing_header (project_name, module_title, location, remarks, designer, dimension, full_name, bill_type, date) 
                    VALUES ('$project_name', '$module_title', '$location', '$remarks', '$designer', '$dimension', '$customer_name', '$bill_type', '$sale_date')";
            $conn->query($sql);
            $bill_id = $conn->insert_id;
            
            if(!$bill_id) throw new Exception("Failed to create billing header");

            $grandTotal = 0;
            $receiptItems = [];
            
            foreach($data['cart'] as $item) {
                $inventory_selection = $conn->real_escape_string($item['inventory_selection']);
                $company_name = $conn->real_escape_string($item['company_name']);
                $product_name = $conn->real_escape_string($item['product_name']);
                $unit = $conn->real_escape_string($item['unit']);
                    $specification_id = $item['specification'];
    $specification_text = $item['specification_text'];
                $price = floatval($item['price']);
                $quantity = intval($item['quantity']);
                $total = floatval($item['total']);
                
                $grandTotal += $total;
                $receiptItems[] = [
                    'unit' => $unit,
                    'product_name' => $product_name,
                    'price' => $price,
                    'total' => $total
                ];

                // Insert billing detail
                $detail_sql = "INSERT INTO billing_details 
                              (inventory_selection, bill_id, product_company, product_name, product_unit, price, qty, total) 
                              VALUES ('$inventory_selection', $bill_id, '$company_name', '$product_name', '$unit', $price, $quantity, $total)";
                $conn->query($detail_sql);

                // Only update stock for physical products
                if(!in_array($inventory_selection, ['Labor Cost', 'Job Out'])) {
                    // Check stock
                    $check_sql = "SELECT quantity FROM purchase_master 
                                  WHERE inventory_selection = '$inventory_selection' 
                                  AND company_name = '$company_name' 
                                  AND product_name = '$product_name' 
                                  AND unit = '$unit'";
                    
                    $check_query = $conn->query($check_sql);
                    
                    if($check_query->num_rows > 0) {
                        $check_row = $check_query->fetch_assoc();
                        $current_stock = intval($check_row['quantity']);
                        
                        if($current_stock < $quantity) {
                            throw new Exception("Insufficient stock for $product_name ($unit). Only $current_stock available.");
                        }
                        
                        // Update purchase master
                        $update_purchase_sql = "UPDATE purchase_master 
                                              SET quantity = quantity - $quantity 
                                              WHERE inventory_selection = '$inventory_selection'
                                              AND company_name = '$company_name' 
                                              AND product_name = '$product_name' 
                                              AND unit = '$unit'";
                        $conn->query($update_purchase_sql);

                        // Update stock master
                        $update_stock_sql = "UPDATE stock_master 
                                          SET product_qty = product_qty - $quantity 
                                          WHERE inventory_selection = '$inventory_selection'
                                          AND product_company = '$company_name' 
                                          AND product_name = '$product_name' 
                                          AND product_unit = '$unit'";
                        $conn->query($update_stock_sql);
                    } else {
                        throw new Exception("Product not found: $product_name");
                    }
                }
            }
            
            // Commit transaction
            $conn->commit();
            
            // Generate the receipt HTML
            $receiptHTML = generateReceiptHTML($project_name, $customer_name, $location,  $sale_date, $receiptItems, $grandTotal);
            
            // Return success with receipt data
            echo json_encode([
                'success' => true, 
                'bill_id' => $bill_id,

                'customer_name' => $customer_name,
                'project_name' => $project_name,
                'grand_total' => number_format($grandTotal, 2),
                'date' => date('F d, Y', strtotime($sale_date)),
                'receipt_html' => $receiptHTML
            ]);
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid data format']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No data received']);
}

function generateReceiptHTML($project_name, $customer_name, $location,  $sale_date, $items, $grandTotal) {
    ob_start();
    ?>
    <div class="receipt-content">
        <h2 class="text-center">MATERIALS REQUISITION FORM</h2>
        
        <table class="receipt-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="10%">UOM</th>
                    <th width="50%">PRODUCT NAME</th>
                    <th width="15%">UNIT PRICE</th>
                    <th width="20%">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($item['unit']) ?></td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td class="text-right">₱<?= number_format($item['price'], 2) ?></td>
                    <td class="text-right">₱<?= number_format($item['total'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="4" class="text-right"><strong>TOTAL</strong></td>
                    <td class="text-right"><strong>₱<?= number_format($grandTotal, 2) ?></strong></td>
                </tr>
            </tbody>
        </table>
        
        <div class="receipt-footer">
            <p><strong>PROJECT:</strong> <?= htmlspecialchars($project_name) ?></p>
            <p><strong>DATE:</strong> <?= date('F d, Y', strtotime($sale_date)) ?></p>
            <div class="signature-lines">
                <div class="signature-box">
                    <p><strong>ACCOUNT NAME:</strong></p>
                    <p><strong>REQUESTED BY:</strong></p>
                    <p><?= htmlspecialchars($customer_name) ?></p>
                </div>
                <div class="signature-box">
                    <p><strong>LOCATION:</strong></p>
                    <p><?= htmlspecialchars($location) ?></p>
                    <p><strong>APPROVED BY:</strong></p>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>