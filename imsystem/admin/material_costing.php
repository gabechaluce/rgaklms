<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<head><link rel="icon" type="image/x-icon" href="rga.png"></head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>Material Costing Form</h1>
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

        if (isset($_SESSION['success'])) {
            echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              " . $_SESSION['success'] . "
            </div>
          ";
            unset($_SESSION['success']);
        }
      ?>

      <div class="row">
        <div class="col-xs-12">
          <div class="box floating-box">
            <div class="box-header with-border">
              <h3 class="box-title">Material Costing Form</h3>
              <div class="pull-right">
                <button id="printBtn" class="btn btn-info btn-sm">
                  <i class="fa fa-print"></i> Print
                </button>
                <button id="downloadPdfBtn" class="btn btn-danger btn-sm">
                  <i class="fa fa-file-pdf-o"></i> Download PDF
                </button>
                <button id="downloadWordBtn" class="btn btn-primary btn-sm">
                  <i class="fa fa-file-word-o"></i> Download Word
                </button>
              </div>
            </div>
            <div class="box-body">
              <div id="printableArea">
                <div class="quotation-form">
                  <div class="form-header">MATERIAL COSTING FORM</div>
                  
                  <div class="info-section">
                    <table class="form-table">
                      <!-- Header Information -->
                      <tr>
                        <td class="label-col">PROJECT NAME:</td>
                        <td><input type="text" id="project_name" placeholder="Enter project name"></td>
                        <td class="label-col">DESIGNER:</td>
                        <td><input type="text" id="designer" placeholder="Enter designer name"></td>
                      </tr>
                      <tr>
                        <td class="label-col">MODULE TITLE:</td>
                        <td><input type="text" id="module_title" placeholder="Enter module title"></td>
                        <td class="label-col">DATE:</td>
                        <td><input type="date" id="date" value="<?php echo date('Y-m-d'); ?>"></td>
                      </tr>
<tr>
  <td class="label-col">LOCATION:</td>
  <td><input type="text" id="location" placeholder="Enter location"></td>
  <td class="label-col">QUANTITY:</td>
  <td><input type="text" id="quantity" value="1" placeholder="Enter quantity"></td>
</tr>
                      <tr>
                        <td class="label-col">REMARKS/FINISH:</td>
                        <td><input type="text" id="remarks" placeholder="Enter remarks/finish"></td>
                        <td class="label-col">DIMENSION:</td>
                        <td><input type="text" id="dimension" placeholder="Enter dimension"></td>
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
                        <th style="width: 60px;" class="no-print">ACTION</th>
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
                          <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'materials')">×</button></td>
                        </tr>
                      </tbody>
                      <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" id="materials_total" class="number-input" readonly></td>
                        <td class="no-print"><button type="button" class="btn-add-row" onclick="addRow('materials')">+</button></td>
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
                        <th class="no-print">ACTION</th>
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
                          <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'accessories')">×</button></td>
                        </tr>
                      </tbody>
                      <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" id="accessories_total" class="number-input" readonly></td>
                        <td class="no-print"><button type="button" class="btn-add-row" onclick="addRow('accessories')">+</button></td>
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
                        <th class="no-print">ACTION</th>
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
                          <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'paint_materials')">×</button></td>
                        </tr>
                      </tbody>
                      <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" id="paint_materials_total" class="number-input" readonly></td>
                        <td class="no-print"><button type="button" class="btn-add-row" onclick="addRow('paint_materials')">+</button></td>
                      </tr>
                      
                      <!-- Overall Material Cost -->
                      <tr>
                        <td colspan="6" class="overall-total-row center-text">TOTAL AMOUNT (OVERALL) MATERIAL COST</td>
                        <td colspan="2" class="overall-total-row"><input type="number" id="overall_material_cost" class="number-input" readonly></td>
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
                        <th class="no-print">ACTION</th>
                      </tr>
                      <tbody id="labor_tbody">
                        <tr class="labor-row">
                          <td><input type="text" name="manpower_name[]" placeholder="Manpower name"></td>
                          <td><input type="number" name="person_count[]" class="number-input person-count" min="0" step="1"></td>
                          <td><input type="number" name="days_per_person[]" class="number-input days-per-person" min="0" step="0.5"></td>
                          <td colspan="2"><input type="text" name="labor_uom[]" placeholder="UOM"></td>
                          <td><input type="number" name="labor_price[]" class="number-input labor-price" min="0" step="0.01"></td>
                          <td><input type="number" name="labor_total[]" class="number-input labor-total" readonly></td>
                          <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'labor')">×</button></td>
                        </tr>
                      </tbody>
                      <tr>
                        <td colspan="6" class="total-row center-text">TOTAL:</td>
                        <td class="total-row"><input type="number" id="labor_total" class="number-input" readonly></td>
                        <td class="no-print"><button type="button" class="btn-add-row" onclick="addRow('labor')">+</button></td>
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
                        <th class="no-print">ACTION</th>
                      </tr>
                      <tbody id="jobout_tbody">
                        <tr class="jobout-row">
                          <td><input type="text" name="jobout_name[]" placeholder="Job out name"></td>
                          <td><input type="text" name="jobout_specs[]" placeholder="Specifications"></td>
                          <td><input type="number" name="jobout_pcs_mins[]" class="number-input jobout-pcs-mins" min="0" step="0.01"></td>
                          <td colspan="2"><input type="text" name="jobout_uom[]" placeholder="UOM (optional)"></td>
                          <td><input type="number" name="jobout_price[]" class="number-input jobout-price" min="0" step="0.01"></td>
                          <td><input type="number" name="jobout_total[]" class="number-input jobout-total" readonly></td>
                          <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'jobout')">×</button></td>
                        </tr>
                      </tbody>
                      <tr>
                        <td colspan="6" class="total-row center-text">TOTAL JOB OUT</td>
                        <td class="total-row"><input type="number" id="jobout_total" class="number-input" readonly></td>
                        <td class="no-print"><button type="button" class="btn-add-row" onclick="addRow('jobout')">+</button></td>
                      </tr>
                      
                      <!-- Final Total -->
                      <tr>
                        <td colspan="6" class="overall-total-row center-text">TOTAL AMOUNT (OVERALL W/ LABOR):</td>
                        <td colspan="2" class="overall-total-row"><input type="number" id="grand_total" class="number-input" readonly style="font-size: 16px; font-weight: bold;"></td>
                      </tr>
                      
         
<!-- Signature Section -->
<tr class="signature-section">
  <td colspan="4" class="signature-cell">
    Prepared By:<br>
    <input type="text" id="prepared_by" placeholder="Enter name" style="border: none; border-bottom: 1px solid #000; background: transparent; text-align: center; margin-top: 10px; width: 200px;">
    <br><br>
   
  </td>
  <td colspan="4" class="signature-cell">
    Approved By:<br>
    <input type="text" id="approved_by" placeholder="Enter name" style="border: none; border-bottom: 1px solid #000; background: transparent; text-align: center; margin-top: 10px; width: 200px;">
    <br><br>
    
  </td>
</tr>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>   
  </div>
  
  <?php include 'includes/footer.php'; ?>
</div>

<?php include 'includes/scripts.php'; ?>

<style>
/* Floating Box for the entire table and header */
.floating-box {
  border-radius: 15px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.quotation-form {
    background: white;
    border: 2px solid #000;
    max-width: 100%;
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

.signature-section {
    border-top: 2px solid #000;
    height: 80px;
}

/* Add this CSS to your existing style section */

/* Signature input fields styling */
.signature-cell input[type="text"] {
    border: none;
    border-bottom: 1px solid #000;
    background: transparent;
    text-align: center;
    margin-top: 10px;
    width: 200px;
    font-size: 12px;
    padding: 2px;
    outline: none;
}

.signature-cell input[type="text"]:focus {
    border-bottom: 2px solid #3c8dbc;
}

/* Print styles for signature fields */
@media print {
    .signature-cell input[type="text"] {
        border: none;
        border-bottom: 1px solid #000;
        background: transparent;
        color: #000 !important;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }
    
    .signature-cell input[type="text"]:after {
        content: attr(value);
    }
}
.number-input {
    text-align: right;
}

.center-text {
    text-align: center;
}

.jobout-labor-header {
    background-color: #ffd700;
    font-weight: bold;
    text-align: center;
    padding: 8px;
}

.info-row {
    margin: 0;
    padding: 0;
}

.info-row td {
    padding: 2px 5px;
}

@media print {
    .no-print, .btn-add-row, .btn-remove-row, .box-header, .content-header {
        display: none !important;
    }
    
    body {
        background: white;
        padding: 0;
        margin: 0;
    }
    
    .quotation-form {
        box-shadow: none;
        border: 2px solid #000;
    }
    
    .content-wrapper {
        margin: 0;
        padding: 0;
    }
    
    .box {
        border: none;
        box-shadow: none;
    }
    
    .box-body {
        padding: 0;
    }
    
    #printableArea {
        margin: 0;
        padding: 20px;
    }
}
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize calculations on page load
    calculateAllTotals();
    
    // Bind calculation events
    bindCalculationEvents();
    
// Add this to your existing JavaScript section
function printWithoutHeaders() {
    // First, ensure all input values are properly set as attributes before printing
    document.querySelectorAll('#printableArea input').forEach(function(input) {
        if (input.type === 'text' || input.type === 'number' || input.type === 'date') {
            input.setAttribute('value', input.value);
        }
    });
    
    // Special handling for signature fields
    const preparedBy = document.getElementById('prepared_by');
    const approvedBy = document.getElementById('approved_by');
    if (preparedBy) preparedBy.setAttribute('value', preparedBy.value);
    if (approvedBy) approvedBy.setAttribute('value', approvedBy.value);
    
    // Create a new window for printing
    var printWindow = window.open('', '_blank');
    var printContent = document.getElementById('printableArea').innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Material Costing Form</title>
            <style>
                @page { 
                    margin: 0.5in; 
                    size: auto;
                    @top-left { content: ""; }
                    @top-center { content: ""; }
                    @top-right { content: ""; }
                    @bottom-left { content: ""; }
                    @bottom-center { content: ""; }
                    @bottom-right { content: ""; }
                }
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .form-table { width: 100%; border-collapse: collapse; margin: 0; }
                .form-table td, .form-table th { border: 1px solid #000; padding: 5px; font-size: 12px; vertical-align: middle; }
                .form-table input { 
                    width: 100%; 
                    border: none; 
                    padding: 3px; 
                    font-size: 11px; 
                    background: transparent;
                    color: #000 !important;
                }
                .section-header { background-color: #d3d3d3; font-weight: bold; text-align: center; padding: 8px; color: #ff0000; }
                .total-row { background-color: #ffff99; font-weight: bold; }
                .overall-total-row { background-color: #d3d3d3; font-weight: bold; font-size: 14px; }
                .label-col { background-color: #f9f9f9; font-weight: bold; text-align: right; padding-right: 10px; }
                .form-header { background-color: #fff; text-align: center; border-bottom: 2px solid #000; padding: 10px; font-weight: bold; font-size: 18px; }
                .quotation-form { background: white; border: 2px solid #000; }
                .signature-section { border-top: 2px solid #000; height: 80px; }
                .signature-cell { text-align: center; vertical-align: bottom; font-weight: bold; padding-bottom: 20px; }
                .number-input { text-align: right; }
                .center-text { text-align: center; }
                .no-print { display: none; }
            </style>
        </head>
        <body>
            ${printContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Add a small delay to ensure the content is fully loaded before printing
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 100);
}
// Update the print button click handler
$('#printBtn').click(function() {
    printWithoutHeaders();
});
    // PDF Download functionality
    $('#downloadPdfBtn').click(function() {
        const element = document.getElementById('printableArea');
        const projectName = $('#project_name').val() || 'Material_Costing_Form';
        
        html2canvas(element, {
            scale: 2,
            useCORS: true,
            allowTaint: true
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('l', 'mm', 'a4');
            
            const imgWidth = 297; // A4 landscape width
            const pageHeight = 210; // A4 landscape height
            const imgHeight = (canvas.height * imgWidth) / canvas.width;
            let heightLeft = imgHeight;
            
            let position = 0;
            
            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
            
            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }
            
            pdf.save(projectName + '_Material_Costing.pdf');
        });
    });
    
    // Word Download functionality
    $('#downloadWordBtn').click(function() {
        const projectName = $('#project_name').val() || 'Material_Costing_Form';
        const content = document.getElementById('printableArea').innerHTML;
        
        // Create a complete HTML document for Word
        const htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Material Costing Form</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .form-table { width: 100%; border-collapse: collapse; margin: 0; }
                    .form-table td, .form-table th { border: 1px solid #000; padding: 5px; font-size: 12px; vertical-align: middle; }
                    .form-table input { width: 100%; border: none; padding: 3px; font-size: 11px; }
                    .section-header { background-color: #d3d3d3; font-weight: bold; text-align: center; padding: 8px; color: #ff0000; }
                    .total-row { background-color: #ffff99; font-weight: bold; }
                    .overall-total-row { background-color: #d3d3d3; font-weight: bold; font-size: 14px; }
                    .label-col { background-color: #f9f9f9; font-weight: bold; text-align: right; padding-right: 10px; }
                    .form-header { background-color: #fff; text-align: center; border-bottom: 2px solid #000; padding: 10px; font-weight: bold; font-size: 18px; }
                    .quotation-form { background: white; border: 2px solid #000; }
                    .signature-section { border-top: 2px solid #000; height: 80px; }
                    .signature-cell { text-align: center; vertical-align: bottom; font-weight: bold; padding-bottom: 20px; }
                    .number-input { text-align: right; }
                    .center-text { text-align: center; }
                </style>
            </head>
            <body>
                ${content}
            </body>
            </html>
        `;
        
        const blob = new Blob([htmlContent], {
            type: 'application/msword'
        });
        
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = projectName + '_Material_Costing.doc';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });
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
                <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'materials')">×</button></td>
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
                <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'accessories')">×</button></td>
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
                <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'paint_materials')">×</button></td>
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
                <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'labor')">×</button></td>
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
                <td class="no-print"><button type="button" class="btn-remove-row" onclick="removeRow(this, 'jobout')">×</button></td>
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
</script>

</body>
</html>