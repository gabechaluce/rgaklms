<?php
include 'includes/session.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid costing ID.");
}

$id = intval($_GET['id']);

// Fetch main costing record
$qry = $conn->query("SELECT * FROM material_costing WHERE id = $id");
if (!$qry || $qry->num_rows == 0) {
    die("Material costing record not found.");
}

$costing = $qry->fetch_assoc();

// Check if user has permission to view this record
if ($_SESSION['login_type'] != 1 && $costing['created_by'] != $_SESSION['login_id']) {
    die("Access denied. You don't have permission to view this record.");
}

// Fetch all related data
$materials_qry = $conn->query("SELECT * FROM material_costing_materials WHERE costing_id = $id ORDER BY id ASC");
$materials = [];
if ($materials_qry) {
    while ($row = $materials_qry->fetch_assoc()) {
        $materials[] = $row;
    }
}

$accessories_qry = $conn->query("SELECT * FROM material_costing_accessories WHERE costing_id = $id ORDER BY id ASC");
$accessories = [];
if ($accessories_qry) {
    while ($row = $accessories_qry->fetch_assoc()) {
        $accessories[] = $row;
    }
}

$paint_qry = $conn->query("SELECT * FROM material_costing_paint_materials WHERE costing_id = $id ORDER BY id ASC");
$paint_materials = [];
if ($paint_qry) {
    while ($row = $paint_qry->fetch_assoc()) {
        $paint_materials[] = $row;
    }
}

$labor_qry = $conn->query("SELECT * FROM material_costing_labor WHERE costing_id = $id ORDER BY id ASC");
$labor = [];
if ($labor_qry) {
    while ($row = $labor_qry->fetch_assoc()) {
        $labor[] = $row;
    }
}

$jobout_qry = $conn->query("SELECT * FROM material_costing_jobout WHERE costing_id = $id ORDER BY id ASC");
$jobout = [];
if ($jobout_qry) {
    while ($row = $jobout_qry->fetch_assoc()) {
        $jobout[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Costing - <?php echo htmlspecialchars($costing['project_name']); ?></title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-after: always; }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
            background: white;
        }

        .print-container {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            border: 2px solid #000;
        }

        .form-header {
            background-color: #fff;
            text-align: center;
            border-bottom: 2px solid #000;
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
            margin: 0;
        }

        .info-section {
            padding: 0;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .form-table td, .form-table th {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: middle;
        }

        .label-col {
            background-color: #f9f9f9;
            font-weight: bold;
            text-align: right;
            padding-right: 10px;
            width: 15%;
        }

        .value-col {
            text-align: left;
            width: 35%;
        }

        .section-header {
            background-color: #d3d3d3;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            color: #ff0000;
        }

        .total-row {
            background-color: #ffff99;
            font-weight: bold;
        }

        .overall-total-row {
            background-color: #d3d3d3;
            font-weight: bold;
            font-size: 12px;
        }

        .center-text {
            text-align: center;
        }

        .right-text {
            text-align: right;
        }

        .print-actions {
            text-align: center;
            margin: 20px 0;
            padding: 10px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }

        /* Ensure table fits on page */
        table {
            font-size: 10px;
        }

        .narrow-col {
            width: 8%;
        }

        .wide-col {
            width: 20%;
        }

        .medium-col {
            width: 12%;
        }
    </style>
</head>
<body>
    <div class="print-actions no-print">
        <button class="btn" onclick="window.print()">
            <i class="fa fa-print"></i> Print Document
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <i class="fa fa-times"></i> Close
        </button>
    </div>

    <div class="print-container">
        <div class="form-header">MATERIAL COSTING FORM</div>
        
        <div class="info-section">
            <table class="form-table">
                <!-- Header Information -->
                <tr>
                    <td class="label-col">PROJECT NAME:</td>
                    <td class="value-col"><?php echo htmlspecialchars($costing['project_name']); ?></td>
                    <td class="label-col">DESIGNER:</td>
                    <td class="value-col"><?php echo htmlspecialchars($costing['designer'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td class="label-col">MODULE TITLE:</td>
                    <td class="value-col"><?php echo htmlspecialchars($costing['module_title'] ?? ''); ?></td>
                    <td class="label-col">DATE:</td>
                    <td class="value-col"><?php echo date('M d, Y', strtotime($costing['date'])); ?></td>
                </tr>
                <tr>
                    <td class="label-col">LOCATION:</td>
                    <td class="value-col"><?php echo htmlspecialchars($costing['location'] ?? ''); ?></td>
                    <td class="label-col">QUANTITY:</td>
                    <td class="value-col"><?php echo $costing['quantity']; ?></td>
                </tr>
                <tr>
                    <td class="label-col">REMARKS/FINISH:</td>
                    <td class="value-col"><?php echo htmlspecialchars($costing['remarks_finish'] ?? ''); ?></td>
                    <td class="label-col">DIMENSION:</td>
                    <td class="value-col"><?php echo htmlspecialchars($costing['dimension'] ?? ''); ?></td>
                </tr>
            </table>
        </div>
        
        <div class="materials-section">
            <table class="form-table">
                <!-- Materials Section -->
                <?php if (!empty($materials)): ?>
                <tr>
                    <td colspan="8" class="section-header">BILLS OF MATERIALS</td>
                </tr>
                <tr>
                    <th class="wide-col">MATERIALS</th>
                    <th class="medium-col">DIMENSION</th>
                    <th class="narrow-col">THICK</th>
                    <th class="narrow-col">QTY</th>
                    <th class="narrow-col">UOM</th>
                    <th class="medium-col">UNIT PRICE</th>
                    <th class="medium-col">TOTAL</th>
                </tr>
                <?php foreach ($materials as $material): ?>
                <tr>
                    <td><?php echo htmlspecialchars($material['material_name']); ?></td>
                    <td><?php echo htmlspecialchars($material['dimension'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($material['thick'] ?? ''); ?></td>
                    <td class="center-text"><?php echo number_format($material['qty'], 2); ?></td>
                    <td><?php echo htmlspecialchars($material['uom'] ?? ''); ?></td>
                    <td class="right-text">₱<?php echo number_format($material['unit_price'], 2); ?></td>
                    <td class="right-text">₱<?php echo number_format($material['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" class="total-row center-text">TOTAL:</td>
                    <td class="total-row right-text">₱<?php echo number_format($costing['materials_total'], 2); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Accessories Section -->
                <?php if (!empty($accessories)): ?>
                <tr>
                    <td colspan="8" class="section-header">ACCESSORIES</td>
                </tr>
                <tr>
                    <th class="wide-col">ACCESSORIES</th>
                    <th class="medium-col">SPECIFICATION</th>
                    <th class="narrow-col">THICK</th>
                    <th class="narrow-col">QTY</th>
                    <th class="narrow-col">UOM</th>
                    <th class="medium-col">UNIT PRICE</th>
                    <th class="medium-col">TOTAL</th>
                </tr>
                <?php foreach ($accessories as $accessory): ?>
                <tr>
                    <td><?php echo htmlspecialchars($accessory['accessory_name']); ?></td>
                    <td><?php echo htmlspecialchars($accessory['specification'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($accessory['thick'] ?? ''); ?></td>
                    <td class="center-text"><?php echo number_format($accessory['qty'], 2); ?></td>
                    <td><?php echo htmlspecialchars($accessory['uom'] ?? ''); ?></td>
                    <td class="right-text">₱<?php echo number_format($accessory['unit_price'], 2); ?></td>
                    <td class="right-text">₱<?php echo number_format($accessory['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" class="total-row center-text">TOTAL:</td>
                    <td class="total-row right-text">₱<?php echo number_format($costing['accessories_total'], 2); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Paint Materials Section -->
                <?php if (!empty($paint_materials)): ?>
                <tr>
                    <td colspan="8" class="section-header">PAINT MATERIALS</td>
                </tr>
                <tr>
                    <th class="wide-col">PAINT MATERIALS</th>
                    <th class="medium-col">SPECIFICATION</th>
                    <th class="narrow-col">THICK</th>
                    <th class="narrow-col">QTY</th>
                    <th class="narrow-col">UOM</th>
                    <th class="medium-col">UNIT PRICE</th>
                    <th class="medium-col">TOTAL</th>
                </tr>
                <?php foreach ($paint_materials as $paint): ?>
                <tr>
                    <td><?php echo htmlspecialchars($paint['paint_name']); ?></td>
                    <td><?php echo htmlspecialchars($paint['specification'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($paint['thick'] ?? ''); ?></td>
                    <td class="center-text"><?php echo number_format($paint['qty'], 2); ?></td>
                    <td><?php echo htmlspecialchars($paint['uom'] ?? ''); ?></td>
                    <td class="right-text">₱<?php echo number_format($paint['unit_price'], 2); ?></td>
                    <td class="right-text">₱<?php echo number_format($paint['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6" class="total-row center-text">TOTAL:</td>
                    <td class="total-row right-text">₱<?php echo number_format($costing['paint_materials_total'], 2); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Overall Material Cost -->
                <tr>
                    <td colspan="6" class="overall-total-row center-text">TOTAL AMOUNT (OVERALL) MATERIAL COST</td>
                    <td class="overall-total-row right-text">₱<?php echo number_format($costing['overall_material_cost'], 2); ?></td>
                </tr>
                
                <!-- Labor Cost Section -->
                <?php if (!empty($labor)): ?>
                <tr>
                    <td colspan="8" class="section-header">LABOR COST</td>
                </tr>
                <tr>
                    <th class="wide-col">MANPOWER</th>
                    <th class="narrow-col">NO. OF PERSON</th>
                    <th class="medium-col">NUMBER OF DAYS PER PERSON</th>
                    <th class="narrow-col">UOM</th>
                    <th class="medium-col">UNIT PRICE</th>
                    <th class="medium-col">TOTAL</th>
                </tr>
                <?php foreach ($labor as $lab): ?>
                <tr>
                    <td><?php echo htmlspecialchars($lab['manpower_name']); ?></td>
                    <td class="center-text"><?php echo $lab['person_count']; ?></td>
                    <td class="center-text"><?php echo number_format($lab['days_per_person'], 1); ?></td>
                    <td><?php echo htmlspecialchars($lab['uom'] ?? ''); ?></td>
                    <td class="right-text">₱<?php echo number_format($lab['unit_price'], 2); ?></td>
                    <td class="right-text">₱<?php echo number_format($lab['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" class="total-row center-text">TOTAL:</td>
                    <td class="total-row right-text">₱<?php echo number_format($costing['labor_total'], 2); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Job Out Section -->
                <?php if (!empty($jobout)): ?>
                <tr>
                    <td colspan="8" class="section-header">JOB OUT:</td>
                </tr>
                <tr>
                    <th class="wide-col">JOB OUT:</th>
                    <th class="medium-col">SPECS</th>
                    <th class="medium-col">NUMBER OF PCS&MINS</th>
                    <th class="narrow-col">UOM</th>
                    <th class="medium-col">UNIT PRICE</th>
                    <th class="medium-col">TOTAL</th>
                </tr>
                <?php foreach ($jobout as $job): ?>
                <tr>
                    <td><?php echo htmlspecialchars($job['jobout_name']); ?></td>
                    <td><?php echo htmlspecialchars($job['specs'] ?? ''); ?></td>
                    <td class="center-text"><?php echo number_format($job['pcs_mins'], 2); ?></td>
                    <td><?php echo htmlspecialchars($job['uom'] ?? ''); ?></td>
                    <td class="right-text">₱<?php echo number_format($job['unit_price'], 2); ?></td>
                    <td class="right-text">₱<?php echo number_format($job['total'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" class="total-row center-text">TOTAL JOB OUT</td>
                    <td class="total-row right-text">₱<?php echo number_format($costing['jobout_total'], 2); ?></td>
                </tr>
                <?php endif; ?>
                
                <!-- Final Total -->
                <tr>
                    <td colspan="6" class="overall-total-row center-text" style="font-size: 14px; font-weight: bold;">TOTAL AMOUNT (OVERALL W/ LABOR):</td>
                    <td class="overall-total-row right-text" style="font-size: 16px; font-weight: bold; color: #000;">₱<?php echo number_format($costing['grand_total'], 2); ?></td>
                </tr>
            </table>
        </div>

        <div style="padding: 20px; border-top: 1px solid #000; margin-top: 20px;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; padding: 10px; vertical-align: top;">
                        <strong>Prepared by:</strong><br><br>
                        ________________________<br>
                        Signature & Date
                    </td>
                    <td style="border: none; padding: 10px; vertical-align: top;">
                        <strong>Reviewed by:</strong><br><br>
                        ________________________<br>
                        Signature & Date
                    </td>
                    <td style="border: none; padding: 10px; vertical-align: top;">
                        <strong>Approved by:</strong><br><br>
                        ________________________<br>
                        Signature & Date
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="print-actions no-print" style="margin-top: 20px;">
        <button class="btn" onclick="window.print()">
            <i class="fa fa-print"></i> Print Document
        </button>
        <button class="btn btn-secondary" onclick="window.close()">
            <i class="fa fa-times"></i> Close
        </button>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() { window.print(); };
        
        // Print function
        function printDocument() {
            window.print();
        }
        
        // Handle print completion
        window.onafterprint = function() {
            console.log('Print dialog closed');
        };
    </script>
</body>
</html>