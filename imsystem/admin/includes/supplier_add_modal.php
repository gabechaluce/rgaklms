<!-- Add -->
<div class="modal fade" id="addnew">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Add New Supplier</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="supplier_add_add.php">
          <div class="form-group">
            <label for="firstname" class="col-sm-3 control-label">First Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="firstname" name="firstname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="lastname" class="col-sm-3 control-label">Last Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="lastname" name="lastname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="businessname" class="col-sm-3 control-label">Company Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="businessname" name="businessname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="contact" class="col-sm-3 control-label">Contact</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="contact" name="contact" required>
            </div>
          </div>
          <div class="form-group">
            <label for="address" class="col-sm-3 control-label">Address</label>
            <div class="col-sm-9">
              <textarea class="form-control" id="address" name="address"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="city" class="col-sm-3 control-label">City</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="city" name="city">
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

<!-- Edit -->
<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Edit Supplier</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="supplier_edit.php">
          <input type="hidden" class="supplierid" name="id">
          <div class="form-group">
            <label for="edit_firstname" class="col-sm-3 control-label">First Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_firstname" name="edit_firstname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_lastname" class="col-sm-3 control-label">Last Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_lastname" name="edit_lastname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_businessname" class="col-sm-3 control-label">Company Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_businessname" name="edit_businessname" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_contact" class="col-sm-3 control-label">Contact</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_contact" name="edit_contact" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_address" class="col-sm-3 control-label">Address</label>
            <div class="col-sm-9">
              <textarea class="form-control" id="edit_address" name="edit_address"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_city" class="col-sm-3 control-label">City</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_city" name="edit_city">
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

<!-- Delete -->
<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Deleting...</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="supplier_delete.php">
          <input type="hidden" class="supplierid" name="id">
          <div class="text-center">
            <p>DELETE SUPPLIER</p>
            <h2 id="del_supplier" class="bold"></h2>
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