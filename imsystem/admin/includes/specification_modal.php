<!-- Add Specification Modal -->
<div class="modal fade" id="addSpecModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Add New Specification</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="specification_add.php">
          <div class="form-group">
            <label for="spec_inventory_selection" class="col-sm-3 control-label">Inventory For</label>
            <div class="col-sm-9">
              <select class="form-control" id="spec_inventory_selection" name="inventory_selection" required>
                <option value="">- Select Inventory For -</option>
                <?php
                  $sql = "SELECT DISTINCT inventory_selection FROM products ORDER BY inventory_selection";
                  $query = $conn->query($sql);
                  while($row = $query->fetch_assoc()){
                    echo '<option value="'.$row['inventory_selection'].'">'.$row['inventory_selection'].'</option>';
                  }
                ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="spec_company_name" class="col-sm-3 control-label">Category</label>
            <div class="col-sm-9">
              <select class="form-control" id="spec_company_name" name="company_name" required>
                <option value="">- Select Category -</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="spec_product" class="col-sm-3 control-label">Product</label>
            <div class="col-sm-9">
              <select class="form-control" id="spec_product" name="product_id" required>
                <option value="">- Select Product -</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="spec_unit" class="col-sm-3 control-label">Unit</label>
            <div class="col-sm-9">
              <select class="form-control" id="spec_unit" name="unit" required>
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
          <div class="form-group">
            <label for="spec_name" class="col-sm-3 control-label">Specification Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="spec_name" name="spec_name" required>
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