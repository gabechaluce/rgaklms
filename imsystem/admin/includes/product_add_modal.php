<!-- Add New Product Modal -->
<div class="modal fade" id="addnew">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Add New Product</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="product_add_add.php">
        <div class="form-group">
    <label for="inventory_selection" class="col-sm-3 control-label">Inventory For</label>
    <div class="col-sm-9">
        <select class="form-control" name="inventory_selection" id = 'inventory_selection' required>
            <option value="" disabled selected>-Select Inventory-</option>
            <?php
            $sql = "SELECT * FROM inventory_selection";
            $query = $conn->query($sql);
            while ($row = $query->fetch_assoc()) {
                echo "<option value='".htmlspecialchars($row['inventory_selection'])."'>"
                    .htmlspecialchars($row['inventory_selection'])."</option>";
            }
            ?>
        </select>
    </div>
</div>
          <div class="form-group">
            <label for="company_name" class="col-sm-3 control-label">Category</label>
            <div class="col-sm-9">
              <select class="form-control" name="company_name" id = 'company_name' required>
              <option value="" disabled selected>-Select Category-</option>
                <?php
                $sql = "SELECT * FROM company_name";
                $query = $conn->query($sql);
                while ($row = $query->fetch_assoc()) {
                  echo "<option value='".$row['company_name']."'>".$row['company_name']."</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="product_name" class="col-sm-3 control-label">Product Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="product_name" required>
            </div>
          </div>
          <div class="form-group">
            <label for="unit" class="col-sm-3 control-label">Unit</label>
            <div class="col-sm-9">
              <select class="form-control" name="unit" required>
              <option value="" disabled selected>-Select Unit-</option>
                <?php
                $sql = "SELECT * FROM units";
                $query = $conn->query($sql);
                while ($row = $query->fetch_assoc()) {
                  echo "<option value='".$row['unit']."'>".$row['unit']."</option>";
                }
                ?>
              </select>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        <button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Edit Product</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="product_add_edit.php">
        <div class="form-group">
    <label for="inventory_selection" class="col-sm-3 control-label">Inventory For</label>
    <div class="col-sm-9">
    <select class="form-control" name="edit_inventory_selection" id="edit_inventory_selection" required>
    <option value="" disabled>Select Inventory</option>
    <?php
    $sql = "SELECT * FROM inventory_selection";
    $query = $conn->query($sql);
    while ($row = $query->fetch_assoc()) {
        echo "<option value='".htmlspecialchars($row['inventory_selection'])."'>"
            .htmlspecialchars($row['inventory_selection'])."</option>";
    }
    ?>
</select>
    </div>
</div>
          <input type="hidden" class="unitid" name="id">
          <div class="form-group">
            <label for="edit_company_name" class="col-sm-3 control-label">Category</label>
            <div class="col-sm-9">
              <select class="form-control" name="edit_company_name" id="edit_company_name" required>
                <?php
                $sql = "SELECT * FROM company_name";
                $query = $conn->query($sql);
                while ($row = $query->fetch_assoc()) {
                  echo "<option value='".$row['company_name']."'>".$row['company_name']."</option>";
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_product_name" class="col-sm-3 control-label">Product Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_product_name" name="edit_product_name" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_unit" class="col-sm-3 control-label">Unit</label>
            <div class="col-sm-9">
              <select class="form-control" name="edit_unit" id="edit_unit" required>
                <?php
                $sql = "SELECT * FROM units";
                $query = $conn->query($sql);
                while ($row = $query->fetch_assoc()) {
                  echo "<option value='".$row['unit']."'>".$row['unit']."</option>";
                }
                ?>
              </select>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Product Modal -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Deleting...</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="product_add_delete.php">
          <input type="hidden" class="unitid" name="id">
          <div class="text-center">
            <p>DELETE PRODUCT</p>
            <h2 id="del_product" class="bold"></h2>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
        <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>