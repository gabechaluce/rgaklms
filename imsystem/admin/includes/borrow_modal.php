<div class="modal fade" id="addnew">
    <div class="modal-dialog">
        <div class="modal-content">
           <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><b>Borrow Equipment</b></h4>
           </div>
           <div class="modal-body">
             <form class="form-horizontal" method="POST" action="borrow_add.php">
                <div class="form-group">
                    <label for="project" class="col-sm-3 control-label">Project</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="project" name="project" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="lastname" class="col-sm-3 control-label">Lastname</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="lastname" name="lastname" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="title" class="col-sm-3 control-label">Equipment</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="title" name="title[]" required>
                    </div>
                </div>                
                <div class="form-group">
                    <label for="quantity" class="col-sm-3 control-label">Quantity</label>
                    <div class="col-sm-9">
                        <input type="number" class="form-control" id="quantity" name="quantity[]" min="1" max="999" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="store" class="col-sm-3 control-label">Store</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="store" name="store" placeholder="Optional">
                    </div>
                </div>
                <span id="append-div"></span>
                <div class="form-group">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button class="btn btn-primary btn-xs btn-flat" id="append"><i class="fa fa-plus"></i> Add Equipment</button>
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