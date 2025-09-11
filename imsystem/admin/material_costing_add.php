<?php
include 'includes/session.php';
include 'includes/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_costing'])) {
    $project_name = $conn->real_escape_string($_POST['project_name']);
    $module_title = $conn->real_escape_string($_POST['module_title']);
    $designer = $conn->real_escape_string($_POST['designer']);
    $date = $_POST['date'];
    $location = $conn->real_escape_string($_POST['location']);
    $quantity = intval($_POST['quantity']);
    $remarks_finish = $conn->real_escape_string($_POST['remarks_finish']);
    $dimension = $conn->real_escape_string($_POST['dimension']);
    
    // Totals
    $materials_total = floatval($_POST['materials_total']);
    $accessories_total = floatval($_POST['accessories_total']);
    $paint_materials_total = floatval($_POST['paint_materials_total']);
    $overall_material_cost = floatval($_POST['overall_material_cost']);
    $labor_total = floatval($_POST['labor_total']);
    $jobout_total = floatval($_POST['jobout_total']);
    $grand_total = floatval($_POST['grand_total']);
    
    $created_by = $_SESSION['login_id'];
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert main record
        $sql = "INSERT INTO material_costing (
                    project_name, module_title, designer, date, location, quantity, 
                    remarks_finish, dimension, materials_total, accessories_total, 
                    paint_materials_total, overall_material_cost, labor_total, 
                    jobout_total, grand_total, created_by
                ) VALUES (
                    '$project_name', '$module_title', '$designer', '$date', '$location', 
                    $quantity, '$remarks_finish', '$dimension', $materials_total, 
                    $accessories_total, $paint_materials_total, $overall_material_cost, 
                    $labor_total, $jobout_total, $grand_total, $created_by
                )";
        
        if (!$conn->query($sql)) {
            throw new Exception("Error inserting main record: " . $conn->error);
        }
        
        $costing_id = $conn->insert_id;
        
        // Insert materials
        if (!empty($_POST['material_name']) && is_array($_POST['material_name'])) {
            for ($i = 0; $i < count($_POST['material_name']); $i++) {
                if (!empty($_POST['material_name'][$i])) {
                    $material_name = $conn->real_escape_string($_POST['material_name'][$i]);
                    $material_dimension = $conn->real_escape_string($_POST['material_dimension'][$i] ?? '');
                    $material_thick = $conn->real_escape_string($_POST['material_thick'][$i] ?? '');
                    $material_qty = floatval($_POST['material_qty'][$i] ?? 0);
                    $material_uom = $conn->real_escape_string($_POST['material_uom'][$i] ?? '');
                    $material_price = floatval($_POST['material_price'][$i] ?? 0);
                    $material_total = floatval($_POST['material_total'][$i] ?? 0);
                    
                    $sql = "INSERT INTO material_costing_materials 
                            (costing_id, material_name, dimension, thick, qty, uom, unit_price, total) 
                            VALUES ($costing_id, '$material_name', '$material_dimension', '$material_thick', 
                                   $material_qty, '$material_uom', $material_price, $material_total)";
                    
                    if (!$conn->query($sql)) {
                        throw new Exception("Error inserting material: " . $conn->error);
                    }
                }
            }
        }
        
        // Insert accessories
        if (!empty($_POST['accessory_name']) && is_array($_POST['accessory_name'])) {
            for ($i = 0; $i < count($_POST['accessory_name']); $i++) {
                if (!empty($_POST['accessory_name'][$i])) {
                    $accessory_name = $conn->real_escape_string($_POST['accessory_name'][$i]);
                    $accessory_specification = $conn->real_escape_string($_POST['accessory_specification'][$i] ?? '');
                    $accessory_thick = $conn->real_escape_string($_POST['accessory_thick'][$i] ?? '');
                    $accessory_qty = floatval($_POST['accessory_qty'][$i] ?? 0);
                    $accessory_uom = $conn->real_escape_string($_POST['accessory_uom'][$i] ?? '');
                    $accessory_price = floatval($_POST['accessory_price'][$i] ?? 0);
                    $accessory_total = floatval($_POST['accessory_total'][$i] ?? 0);
                    
                    $sql = "INSERT INTO material_costing_accessories 
                            (costing_id, accessory_name, specification, thick, qty, uom, unit_price, total) 
                            VALUES ($costing_id, '$accessory_name', '$accessory_specification', '$accessory_thick', 
                                   $accessory_qty, '$accessory_uom', $accessory_price, $accessory_total)";
                    
                    if (!$conn->query($sql)) {
                        throw new Exception("Error inserting accessory: " . $conn->error);
                    }
                }
            }
        }
        
        // Insert paint materials
        if (!empty($_POST['paint_name']) && is_array($_POST['paint_name'])) {
            for ($i = 0; $i < count($_POST['paint_name']); $i++) {
                if (!empty($_POST['paint_name'][$i])) {
                    $paint_name = $conn->real_escape_string($_POST['paint_name'][$i]);
                    $paint_specification = $conn->real_escape_string($_POST['paint_specification'][$i] ?? '');
                    $paint_thick = $conn->real_escape_string($_POST['paint_thick'][$i] ?? '');
                    $paint_qty = floatval($_POST['paint_qty'][$i] ?? 0);
                    $paint_uom = $conn->real_escape_string($_POST['paint_uom'][$i] ?? '');
                    $paint_price = floatval($_POST['paint_price'][$i] ?? 0);
                    $paint_total = floatval($_POST['paint_total'][$i] ?? 0);
                    
                    $sql = "INSERT INTO material_costing_paint_materials 
                            (costing_id, paint_name, specification, thick, qty, uom, unit_price, total) 
                            VALUES ($costing_id, '$paint_name', '$paint_specification', '$paint_thick', 
                                   $paint_qty, '$paint_uom', $paint_price, $paint_total)";
                    
                    if (!$conn->query($sql)) {
                        throw new Exception("Error inserting paint material: " . $conn->error);
                    }
                }
            }
        }
        
        // Insert labor
        if (!empty($_POST['manpower_name']) && is_array($_POST['manpower_name'])) {
            for ($i = 0; $i < count($_POST['manpower_name']); $i++) {
                if (!empty($_POST['manpower_name'][$i])) {
                    $manpower_name = $conn->real_escape_string($_POST['manpower_name'][$i]);
                    $person_count = intval($_POST['person_count'][$i] ?? 0);
                    $days_per_person = floatval($_POST['days_per_person'][$i] ?? 0);
                    $labor_uom = $conn->real_escape_string($_POST['labor_uom'][$i] ?? '');
                    $labor_price = floatval($_POST['labor_price'][$i] ?? 0);
                    $labor_total = floatval($_POST['labor_total'][$i] ?? 0);
                    
                    $sql = "INSERT INTO material_costing_labor 
                            (costing_id, manpower_name, person_count, days_per_person, uom, unit_price, total) 
                            VALUES ($costing_id, '$manpower_name', $person_count, $days_per_person, 
                                   '$labor_uom', $labor_price, $labor_total)";
                    
                    if (!$conn->query($sql)) {
                        throw new Exception("Error inserting labor: " . $conn->error);
                    }
                }
            }
        }
        
        // Insert job out
        if (!empty($_POST['jobout_name']) && is_array($_POST['jobout_name'])) {
            for ($i = 0; $i < count($_POST['jobout_name']); $i++) {
                if (!empty($_POST['jobout_name'][$i])) {
                    $jobout_name = $conn->real_escape_string($_POST['jobout_name'][$i]);
                    $jobout_specs = $conn->real_escape_string($_POST['jobout_specs'][$i] ?? '');
                    $jobout_pcs_mins = floatval($_POST['jobout_pcs_mins'][$i] ?? 0);
                    $jobout_uom = $conn->real_escape_string($_POST['jobout_uom'][$i] ?? '');
                    $jobout_price = floatval($_POST['jobout_price'][$i] ?? 0);
                    $jobout_total = floatval($_POST['jobout_total'][$i] ?? 0);
                    
                    $sql = "INSERT INTO material_costing_jobout 
                            (costing_id, jobout_name, specs, pcs_mins, uom, unit_price, total) 
                            VALUES ($costing_id, '$jobout_name', '$jobout_specs', $jobout_pcs_mins, 
                                   '$jobout_uom', $jobout_price, $jobout_total)";
                    
                    if (!$conn->query($sql)) {
                        throw new Exception("Error inserting job out: " . $conn->error);
                    }
                }
            }
        }
        
        $conn->commit();
        $_SESSION['success'] = 'Material costing saved successfully!';
        header("Location: material_costing.php");
        exit;
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = [$e->getMessage()];
    }
}

// Get projects for dropdown
$projects_query = $conn->query("SELECT name FROM project_list ORDER BY name ASC");
$projects = [];
if ($projects_query) {
    while ($row = $projects_query->fetch_assoc()) {
        $projects[] = $row['name'];
    }
}
?>
<head>
    <link rel="icon" type="image/x-icon" href="rga.png">
    <title>Add Material Costing</title>
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Add Material Costing</h1>
    </section>

    <section class="content">
      <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-warning"></i> Error!</h4>
                    <ul>';
            foreach ($_SESSION['error'] as $error) {
                echo "<li>$error</li>";
            }
            echo '</ul></div>';
            unset($_SESSION['error']);
        }
      ?>

      <form method="post" action="">
        <div class="quotation-form">
            <div class="form-header">MATERIAL COSTING FORM</div>
            
            <div class="info-section">
                <table class="form-table">
                    <!-- Header Information -->
                    <tr>
                        <td class="label-col">PROJECT NAME:</td>
                        <td>
                            <select name="project_name" id="project_name" required style="width: 100%; border: none; padding: 3px;">
                                <option value="">Select Project</option>
                                <?php foreach($projects as $project): ?>
                                    <option value="<?php echo htmlspecialchars($project); ?>">
                                        <?php echo htmlspecialchars($project); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td class="label-col">DESIGNER:</td>
                        <td><input type="text" name="designer" id="designer" placeholder="Enter designer name" required></td>
                    </tr>
                    <tr>
                        <td class="label-col">MODULE TITLE:</td>
                        <td><input type="text" name="module_title" id="module_title" placeholder="Enter module title"></td>
                        <td class="label-col">DATE:</td>
                        <td><input type="date" name="date" id="date" value="<?php echo date('Y-m-d'); ?>" required></td>
                    </tr>
                    <tr>
                        <td class="label-col">LOCATION:</td>
                        <td><input type="text" name="location" id="location" placeholder="Enter location"></td>
                        <td class="label-col">QUANTITY:</td>
                        <td><input type="number" name="quantity" id="quantity" value="1" min="1" required></td>
                    </tr>
                    <tr>
                        <td class="label-col">REMARKS/FINISH:</td>
                        <td><input type="text" name="remarks_finish" id="remarks_finish" placeholder="Enter remarks/finish"></td>
                        <td class="label-col">DIMENSION:</td>
                        <td><input type="text" name="dimension" id="dimension" placeholder="Enter dimension"></td>
                    </tr>
                </table>
            </div>
            
            <div class="materials-section">
                <table class="form-table">
                    <!-- Materials Section -->
                    <tr>
                        <td colspan="8" class="section-header">BILLS OF MATERIALS</td>
                    </tr>
                    <tr>
                        <th style="width: 150px;">MATERIALS</th>
                        <th style="width: 100px;">DIMENSION</th>
                        <th style="width: 80px;">THICK</th>
                        <th style="width: 60px;">QTY</th>
                        <th style="width: 60px;">UOM</th>
                        <th style="width: 80px;">UNIT PRICE</th>
                        <th style="width: 80px;">TOTAL</th>
                        <th style="width: 60px;">ACTION</th>
                    </tr>
                    <tbody id="materials_tbody">
                        <tr class="material-row">
                            <td><input type="text" name="material_name[]" placeholder="Material name"></td>
                            <td><input type="text" name="material_dimension[]" placeholder="Dimension"></td>
                            <td><input type="text" name="material_thick[]" placeholder="Thick"></td>
                            <td><input type="number" name="material_qty[]" class="number-input material-qty" min="0" step="0.01"></td>
                            <td><input type="text" name="material_uom[]" placeholder="UOM"></td>
                            <td><input type="number" name="material_price[]" class="number-input material-price" min="0" step="0.01"></td>
                            <td><input type="number" name="material_total[]" class="number-input material-total" readonly></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'materials')">×</button></td>
                        </tr>
                    </tbody>
                    <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" name="materials_total" id="materials_total" class="number-input" readonly></td>
                        <td><button type="button" class="btn-add-row" onclick="addRow('materials')">+</button></td>
                    </tr>
                    
                    <!-- Accessories Section -->
                    <tr>
                        <td colspan="8" class="section-header">ACCESSORIES</td>
                    </tr>
                    <tr>
                        <th>ACCESSORIES</th>
                        <th>SPECIFICATION</th>
                        <th>THICK</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>UNIT PRICE</th>
                        <th>TOTAL</th>
                        <th>ACTION</th>
                    </tr>
                    <tbody id="accessories_tbody">
                        <tr class="accessory-row">
                            <td><input type="text" name="accessory_name[]" placeholder="Accessory name"></td>
                            <td><input type="text" name="accessory_specification[]" placeholder="Specification"></td>
                            <td><input type="text" name="accessory_thick[]" placeholder="Thick (optional)"></td>
                            <td><input type="number" name="accessory_qty[]" class="number-input accessory-qty" min="0" step="0.01"></td>
                            <td><input type="text" name="accessory_uom[]" placeholder="UOM"></td>
                            <td><input type="number" name="accessory_price[]" class="number-input accessory-price" min="0" step="0.01"></td>
                            <td><input type="number" name="accessory_total[]" class="number-input accessory-total" readonly></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'accessories')">×</button></td>
                        </tr>
                    </tbody>
                    <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" name="accessories_total" id="accessories_total" class="number-input" readonly></td>
                        <td><button type="button" class="btn-add-row" onclick="addRow('accessories')">+</button></td>
                    </tr>
                    
                    <!-- Paint Materials Section -->
                    <tr>
                        <td colspan="8" class="section-header">PAINT MATERIALS</td>
                    </tr>
                    <tr>
                        <th>PAINT MATERIALS</th>
                        <th>SPECIFICATION</th>
                        <th>THICK</th>
                        <th>QTY</th>
                        <th>UOM</th>
                        <th>UNIT PRICE</th>
                        <th>TOTAL</th>
                        <th>ACTION</th>
                    </tr>
                    <tbody id="paint_materials_tbody">
                        <tr class="paint-row">
                            <td><input type="text" name="paint_name[]" placeholder="Paint material name"></td>
                            <td><input type="text" name="paint_specification[]" placeholder="Specification"></td>
                            <td><input type="text" name="paint_thick[]" placeholder="Thick (optional)"></td>
                            <td><input type="number" name="paint_qty[]" class="number-input paint-qty" min="0" step="0.01"></td>
                            <td><input type="text" name="paint_uom[]" placeholder="UOM"></td>
                            <td><input type="number" name="paint_price[]" class="number-input paint-price" min="0" step="0.01"></td>
                            <td><input type="number" name="paint_total[]" class="number-input paint-total" readonly></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'paint_materials')">×</button></td>
                        </tr>
                    </tbody>
                    <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" name="paint_materials_total" id="paint_materials_total" class="number-input" readonly></td>
                        <td><button type="button" class="btn-add-row" onclick="addRow('paint_materials')">+</button></td>
                    </tr>
                    
                    <!-- Overall Material Cost -->
                    <tr>
                        <td colspan="6" class="overall-total-row center-text">TOTAL AMOUNT (OVERALL) MATERIAL COST</td>
                        <td colspan="2" class="overall-total-row"><input type="number" name="overall_material_cost" id="overall_material_cost" class="number-input" readonly></td>
                    </tr>
                    
                    <!-- Labor Cost Section -->
                    <tr>
                        <td colspan="8" class="section-header">LABOR COST</td>
                    </tr>
                    <tr>
                        <th>MANPOWER</th>
                        <th>NO. OF PERSON</th>
                        <th>NUMBER OF DAYS PER PERSON</th>
                        <th colspan="2">UOM</th>
                        <th>UNIT PRICE</th>
                        <th>TOTAL</th>
                        <th>ACTION</th>
                    </tr>
                    <tbody id="labor_tbody">
                        <tr class="labor-row">
                            <td><input type="text" name="manpower_name[]" placeholder="Manpower name"></td>
                            <td><input type="number" name="person_count[]" class="number-input person-count" min="0" step="1"></td>
                            <td><input type="number" name="days_per_person[]" class="number-input days-per-person" min="0" step="0.5"></td>
                            <td colspan="2"><input type="text" name="labor_uom[]" placeholder="UOM"></td>
                            <td><input type="number" name="labor_price[]" class="number-input labor-price" min="0" step="0.01"></td>
                            <td><input type="number" name="labor_total[]" class="number-input labor-total" readonly></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'labor')">×</button></td>
                        </tr>
                    </tbody>
                    <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" name="labor_total" id="labor_total" class="number-input" readonly></td>
                        <td><button type="button" class="btn-add-row" onclick="addRow('labor')">+</button></td>
                    </tr>
                    
                    <!-- Job Out Section -->
                    <tr>
                        <td colspan="8" class="section-header">JOB OUT:</td>
                    </tr>
                    <tr>
                        <th>JOB OUT:</th>
                        <th>SPECS</th>
                        <th>NUMBER OF PCS&MINS</th>
                        <th colspan="2">UOM</th>
                        <th>UNIT PRICE</th>
                        <th>TOTAL</th>
                        <th>ACTION</th>
                    </tr>
                    <tbody id="jobout_tbody">
                        <tr class="jobout-row">
                            <td><input type="text" name="jobout_name[]" placeholder="Job out name"></td>
                            <td><input type="text" name="jobout_specs[]" placeholder="Specifications"></td>
                            <td><input type="number" name="jobout_pcs_mins[]" class="number-input jobout-pcs-mins" min="0" step="0.01"></td>
                            <td colspan="2"><input type="text" name="jobout_uom[]" placeholder="UOM (optional)"></td>
                            <td><input type="number" name="jobout_price[]" class="number-input jobout-price" min="0" step="0.01"></td>
                            <td><input type="number" name="jobout_total[]" class="number-input jobout-total" readonly></td>
                            <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'jobout')">×</button></td>
                        </tr>
                    </tbody>
                    <tr>
                        <td colspan="6" class="total-row center-text">TOTAL JOB OUT</td>
                        <td class="total-row"><input type="number" name="jobout_total" id="jobout_total" class="number-input" readonly></td>
                        <td><button type="button" class="btn-add-row" onclick="addRow('jobout')">+</button></td>
                    </tr>
                    
                    <!-- Final Total -->
                    <tr>
                        <td colspan="6" class="overall-total-row center-text">TOTAL AMOUNT (OVERALL W/ LABOR):</td>
                        <td colspan="2" class="overall-total-row"><input type="number" name="grand_total" id="grand_total" class="number-input" readonly style="font-size: 16px; font-weight: bold;"></td>
                    </tr>
                </table>
            </div>
            
            <div class="form-actions">
                <table class="form-table">
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px;">
                            <a href="material_costing.php" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" name="save_costing" class="btn btn-success">
                                <i class="fa fa-save"></i> Save Material Costing
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
      </form>
    </section>   
  </div>
  
  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f1ed !important;
    padding: 20px;
}

.quotation-form {
    background: white;
    border: 2px solid #000;
    max-width: 1200px;
    margin: 0 auto;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.form-header {
    background-color: #fff;
    text-align: center;
    border-bottom: 2px solid #000;
    padding: 10px;
    font-weight: bold;
    font-size: 18px;
}

.form-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}

.form-table td, .form-table th {
    border: 1px solid #000;
    padding: 5px;
    font-size: 12px;
    vertical-align: middle;
}

.form-table input, .form-table select {
    width: 100%;
    border: none;
    padding: 3px;
    font-size: 11px;
    box-sizing: border-box;
}

.form-table input:focus, .form-table select:focus {
    outline: 1px solid #3c8dbc;
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
    font-size: 14px;
}

.overall-total-row input {
    color: #000000;
    font-weight: bold;
}

.grand-total-row {
    background-color: #d3d3d3;
    font-weight: bold;
}

.grand-total-row input {
    color: #ff0000;
    font-weight: bold;
}

.label-col {
    background-color: #f9f9f9;
    font-weight: bold;
    text-align: right;
    padding-right: 10px;
    width: 120px;
}

.btn-add-row {
    background-color: #5cb85c;
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 11px;
    margin: 2px;
}

.btn-remove-row {
    background-color: #d9534f;
    color: white;
    border: none;
    padding: 4px 8px;
    border-radius: 3px;
    cursor: pointer;
    font-size: 11px;
    margin: 2px;
}

.btn-add-row:hover, .btn-remove-row:hover {
    opacity: 0.8;
}

.number-input {
    text-align: right;
}

.center-text {
    text-align: center;
}

.form-actions {
    border-top: 2px solid #000;
    background-color: #f9f9f9;
}

.btn {
    border-radius: 5px;
    padding: 10px 20px;
    margin: 5px;
    font-size: 14px;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    cursor: pointer;
    border: none;
}

.btn-default {
    background-color: #777;
    color: white;
}

.btn-success {
    background-color: #5cb85c;
    color: white;
}

.btn:hover {
    opacity: 0.8;
    transform: translateY(-1px);
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize calculations on page load
    calculateAllTotals();
    
    // Bind calculation events
    bindCalculationEvents();
});

function bindCalculationEvents() {
    // Materials calculations
    $(document).on('input', '.material-qty, .material-price', function() {
        calculateRowTotal($(this).closest('tr'), 'material');
        calculateSectionTotal('materials');
        calculateOverallMaterialCost();
        calculateGrandTotal();
    });
    
    // Accessories calculations
    $(document).on('input', '.accessory-qty, .accessory-price', function() {
        calculateRowTotal($(this).closest('tr'), 'accessory');
        calculateSectionTotal('accessories');
        calculateOverallMaterialCost();
        calculateGrandTotal();
    });
    
    // Paint materials calculations
    $(document).on('input', '.paint-qty, .paint-price', function() {
        calculateRowTotal($(this).closest('tr'), 'paint');
        calculateSectionTotal('paint_materials');
        calculateOverallMaterialCost();
        calculateGrandTotal();
    });
    
    // Labor calculations
    $(document).on('input', '.person-count, .days-per-person, .labor-price', function() {
        calculateLaborRowTotal($(this).closest('tr'));
        calculateSectionTotal('labor');
        calculateGrandTotal();
    });
    
    // Job out calculations
    $(document).on('input', '.jobout-pcs-mins, .jobout-price', function() {
        calculateRowTotal($(this).closest('tr'), 'jobout');
        calculateSectionTotal('jobout');
        calculateGrandTotal();
    });
}

function calculateRowTotal(row, type) {
    let qty, price, total;
    
    if (type === 'material') {
        qty = parseFloat(row.find('.material-qty').val()) || 0;
        price = parseFloat(row.find('.material-price').val()) || 0;
        total = qty * price;
        row.find('.material-total').val(total.toFixed(2));
    } else if (type === 'accessory') {
        qty = parseFloat(row.find('.accessory-qty').val()) || 0;
        price = parseFloat(row.find('.accessory-price').val()) || 0;
        total = qty * price;
        row.find('.accessory-total').val(total.toFixed(2));
    } else if (type === 'paint') {
        qty = parseFloat(row.find('.paint-qty').val()) || 0;
        price = parseFloat(row.find('.paint-price').val()) || 0;
        total = qty * price;
        row.find('.paint-total').val(total.toFixed(2));
    } else if (type === 'jobout') {
        qty = parseFloat(row.find('.jobout-pcs-mins').val()) || 0;
        price = parseFloat(row.find('.jobout-price').val()) || 0;
        total = qty * price;
        row.find('.jobout-total').val(total.toFixed(2));
    }
}

function calculateLaborRowTotal(row) {
    const personCount = parseFloat(row.find('.person-count').val()) || 0;
    const daysPerPerson = parseFloat(row.find('.days-per-person').val()) || 0;
    const price = parseFloat(row.find('.labor-price').val()) || 0;
    const total = personCount * daysPerPerson * price;
    row.find('.labor-total').val(total.toFixed(2));
}

function calculateSectionTotal(section) {
    let total = 0;
    let selector;
    
    if (section === 'materials') {
        selector = '.material-total';
    } else if (section === 'accessories') {
        selector = '.accessory-total';
    } else if (section === 'paint_materials') {
        selector = '.paint-total';
    } else if (section === 'labor') {
        selector = '.labor-total';
    } else if (section === 'jobout') {
        selector = '.jobout-total';
    }
    
    $(selector).each(function() {
        total += parseFloat($(this).val()) || 0;
    });
    
    $('#' + section + '_total').val(total.toFixed(2));
}

function calculateOverallMaterialCost() {
    const materialsTotal = parseFloat($('#materials_total').val()) || 0;
    const accessoriesTotal = parseFloat($('#accessories_total').val()) || 0;
    const paintTotal = parseFloat($('#paint_materials_total').val()) || 0;
    const overallMaterialCost = materialsTotal + accessoriesTotal + paintTotal;
    $('#overall_material_cost').val(overallMaterialCost.toFixed(2));
}

function calculateGrandTotal() {
    const overallMaterialCost = parseFloat($('#overall_material_cost').val()) || 0;
    const laborTotal = parseFloat($('#labor_total').val()) || 0;
    const joboutTotal = parseFloat($('#jobout_total').val()) || 0;
    const grandTotal = overallMaterialCost + laborTotal + joboutTotal;
    $('#grand_total').val(grandTotal.toFixed(2));
}

function calculateAllTotals() {
    // Calculate all row totals
    $('.material-row').each(function() {
        calculateRowTotal($(this), 'material');
    });
    $('.accessory-row').each(function() {
        calculateRowTotal($(this), 'accessory');
    });
    $('.paint-row').each(function() {
        calculateRowTotal($(this), 'paint');
    });
    $('.labor-row').each(function() {
        calculateLaborRowTotal($(this));
    });
    $('.jobout-row').each(function() {
        calculateRowTotal($(this), 'jobout');
    });
    
    // Calculate section totals
    calculateSectionTotal('materials');
    calculateSectionTotal('accessories');
    calculateSectionTotal('paint_materials');
    calculateSectionTotal('labor');
    calculateSectionTotal('jobout');
    
    // Calculate overall totals
    calculateOverallMaterialCost();
    calculateGrandTotal();
}

function addRow(section) {
    let newRow;
    
    if (section === 'materials') {
        newRow = `
            <tr class="material-row">
                <td><input type="text" name="material_name[]" placeholder="Material name"></td>
                <td><input type="text" name="material_dimension[]" placeholder="Dimension"></td>
                <td><input type="text" name="material_thick[]" placeholder="Thick"></td>
                <td><input type="number" name="material_qty[]" class="number-input material-qty" min="0" step="0.01"></td>
                <td><input type="text" name="material_uom[]" placeholder="UOM"></td>
                <td><input type="number" name="material_price[]" class="number-input material-price" min="0" step="0.01"></td>
                <td><input type="number" name="material_total[]" class="number-input material-total" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'materials')">×</button></td>
            </tr>
        `;
        $('#materials_tbody').append(newRow);
    } else if (section === 'accessories') {
        newRow = `
            <tr class="accessory-row">
                <td><input type="text" name="accessory_name[]" placeholder="Accessory name"></td>
                <td><input type="text" name="accessory_specification[]" placeholder="Specification"></td>
                <td><input type="text" name="accessory_thick[]" placeholder="Thick (optional)"></td>
                <td><input type="number" name="accessory_qty[]" class="number-input accessory-qty" min="0" step="0.01"></td>
                <td><input type="text" name="accessory_uom[]" placeholder="UOM"></td>
                <td><input type="number" name="accessory_price[]" class="number-input accessory-price" min="0" step="0.01"></td>
                <td><input type="number" name="accessory_total[]" class="number-input accessory-total" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'accessories')">×</button></td>
            </tr>
        `;
        $('#accessories_tbody').append(newRow);
    } else if (section === 'paint_materials') {
        newRow = `
            <tr class="paint-row">
                <td><input type="text" name="paint_name[]" placeholder="Paint material name"></td>
                <td><input type="text" name="paint_specification[]" placeholder="Specification"></td>
                <td><input type="text" name="paint_thick[]" placeholder="Thick (optional)"></td>
                <td><input type="number" name="paint_qty[]" class="number-input paint-qty" min="0" step="0.01"></td>
                <td><input type="text" name="paint_uom[]" placeholder="UOM"></td>
                <td><input type="number" name="paint_price[]" class="number-input paint-price" min="0" step="0.01"></td>
                <td><input type="number" name="paint_total[]" class="number-input paint-total" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'paint_materials')">×</button></td>
            </tr>
        `;
        $('#paint_materials_tbody').append(newRow);
    } else if (section === 'labor') {
        newRow = `
            <tr class="labor-row">
                <td><input type="text" name="manpower_name[]" placeholder="Manpower name"></td>
                <td><input type="number" name="person_count[]" class="number-input person-count" min="0" step="1"></td>
                <td><input type="number" name="days_per_person[]" class="number-input days-per-person" min="0" step="0.5"></td>
                <td colspan="2"><input type="text" name="labor_uom[]" placeholder="UOM"></td>
                <td><input type="number" name="labor_price[]" class="number-input labor-price" min="0" step="0.01"></td>
                <td><input type="number" name="labor_total[]" class="number-input labor-total" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'labor')">×</button></td>
            </tr>
        `;
        $('#labor_tbody').append(newRow);
    } else if (section === 'jobout') {
        newRow = `
            <tr class="jobout-row">
                <td><input type="text" name="jobout_name[]" placeholder="Job out name"></td>
                <td><input type="text" name="jobout_specs[]" placeholder="Specifications"></td>
                <td><input type="number" name="jobout_pcs_mins[]" class="number-input jobout-pcs-mins" min="0" step="0.01"></td>
                <td colspan="2"><input type="text" name="jobout_uom[]" placeholder="UOM (optional)"></td>
                <td><input type="number" name="jobout_price[]" class="number-input jobout-price" min="0" step="0.01"></td>
                <td><input type="number" name="jobout_total[]" class="number-input jobout-total" readonly></td>
                <td><button type="button" class="btn-remove-row" onclick="removeRow(this, 'jobout')">×</button></td>
            </tr>
        `;
        $('#jobout_tbody').append(newRow);
    }
}

function removeRow(button, section) {
    // Don't remove if it's the last row in the section
    const tbody = $(button).closest('tbody');
    if (tbody.find('tr').length > 1) {
        $(button).closest('tr').remove();
        calculateSectionTotal(section);
        if (section === 'materials' || section === 'accessories' || section === 'paint_materials') {
            calculateOverallMaterialCost();
        }
        calculateGrandTotal();
    } else {
        alert('Cannot remove the last row. At least one row is required.');
    }
}

// Form validation
$('form').on('submit', function(e) {
    let isValid = true;
    let errorMsg = '';
    
    // Check if project name is selected
    if (!$('#project_name').val()) {
        isValid = false;
        errorMsg += '- Project name is required\n';
    }
    
    // Check if designer is filled
    if (!$('#designer').val().trim()) {
        isValid = false;
        errorMsg += '- Designer name is required\n';
    }
    
    // Check if date is filled
    if (!$('#date').val()) {
        isValid = false;
        errorMsg += '- Date is required\n';
    }
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fix the following errors:\n' + errorMsg);
        return false;
    }
    
    // Show loading state
    $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
});
</script>
</body>
</html>