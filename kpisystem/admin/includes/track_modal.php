<!-- Add New Track Modal -->
<div class="modal fade" id="addtrack">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Add New Track</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="track_add.php" id="manage-track">
          <input type="hidden" name="id" value="">
          <div class="form-group">
            <label for="date" class="col-sm-3 control-label">Date</label>
            <div class="col-sm-9">
              <input type="date" class="form-control" name="date" id="date" required>
            </div>
          </div>
          <div class="form-group">
            <label for="time" class="col-sm-3 control-label">Time</label>
            <div class="col-sm-9">
              <input type="time" class="form-control" name="time" id="time" required>
            </div>
          </div>
          <div class="form-group">
            <label for="destination" class="col-sm-3 control-label">Destination</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="destination" id="destination" required>
            </div>
          </div>
          <div class="form-group">
            <label for="address" class="col-sm-3 control-label">Address</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="address" id="address" required>
            </div>
          </div>
          <div class="form-group">
            <label for="purpose" class="col-sm-3 control-label">Purpose</label>
            <div class="col-sm-9">
              <textarea name="purpose" id="purpose" class="form-control" rows="3" required></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="vehicle" class="col-sm-3 control-label">Vehicle</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="vehicle" id="vehicle" required>
            </div>
          </div>
          <div class="form-group">
            <label for="driver" class="col-sm-3 control-label">Driver</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="driver" id="driver" required>
            </div>
          </div>
          <div class="form-group">
            <label for="dept" class="col-sm-3 control-label">Department Requested by</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="dept" id="dept" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            <button type="submit" class="btn btn-primary btn-flat" name="add"><i class="fa fa-save"></i> Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Edit Track Modal -->
<div class="modal fade" id="edittrack">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Edit Track</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="track_edit.php" id="edit-track">
          <input type="hidden" name="id" class="trackid">
          <div class="form-group">
            <label for="edit_date" class="col-sm-3 control-label">Date</label>
            <div class="col-sm-9">
              <input type="date" class="form-control" name="edit_date" id="edit_date" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_time" class="col-sm-3 control-label">Time</label>
            <div class="col-sm-9">
              <input type="time" class="form-control" name="edit_time" id="edit_time" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_destination" class="col-sm-3 control-label">Destination</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="edit_destination" id="edit_destination" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_address" class="col-sm-3 control-label">Address</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="edit_address" id="edit_address" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_purpose" class="col-sm-3 control-label">Purpose</label>
            <div class="col-sm-9">
              <textarea name="edit_purpose" id="edit_purpose" class="form-control" rows="3" required></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_vehicle" class="col-sm-3 control-label">Vehicle</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="edit_vehicle" id="edit_vehicle" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_driver" class="col-sm-3 control-label">Driver</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="edit_driver" id="edit_driver" required>
            </div>
          </div>
          <div class="form-group">
            <label for="edit_dept" class="col-sm-3 control-label">Department Requested by</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" name="edit_dept" id="edit_dept" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            <button type="submit" class="btn btn-success btn-flat" name="edit"><i class="fa fa-check-square-o"></i> Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Delete Track Modal -->
<div class="modal fade" id="deletetrack">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title"><b>Deleting...</b></h4>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="POST" action="track_delete.php">
          <input type="hidden" class="trackid" name="id">
          <div class="text-center">
            <p>DELETE TRACK</p>
            <h2 id="del_track" class="bold"></h2>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default btn-flat pull-left" data-dismiss="modal"><i class="fa fa-close"></i> Close</button>
            <button type="submit" class="btn btn-danger btn-flat" name="delete"><i class="fa fa-trash"></i> Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>