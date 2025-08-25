<?php include 'includes/session.php'; ?>
<div class="modal fade" id="addnew">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Add New Selection</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="inventory_selection_add.php">
          <div class="form-group">
            <label for="name" class="col-sm-3 control-label">Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="name" name="name" required>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary btn-flat" name="add">Save</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="edit">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Edit Selection</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="inventory_selection_edit.php">
          <input type="hidden" class="selid" name="id">
          <div class="form-group">
            <label for="edit_name" class="col-sm-3 control-label">Name</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="edit_name" name="name" required>
            </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success btn-flat" name="edit">Update</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="delete">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><b>Delete Selection</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="inventory_selection_delete.php">
          <input type="hidden" class="selid" name="id">
          <div class="text-center">
            <p>DELETE SELECTION</p>
            <h2 id="del_sel" class="bold"></h2>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-danger btn-flat" name="delete">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>