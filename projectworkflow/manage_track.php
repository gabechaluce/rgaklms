<?php
// This file is included by both new_track.php and edit_track.php
// For edit_track.php, the variables will already be defined
// For new_track.php, we need to initialize them

if(!isset($track_id)) {
    $track_id = "";
    $date = "";
    $time = "";
    $destination = "";
    $address = "";
    $purpose = "";
    $vehicle = "";
    $driver = "";
    $dept = "";
}
?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <h3 class="card-title"><?php echo $track_id ? "Edit Track" : "New Track"; ?></h3>
        </div>
        <div class="card-body">
            <form action="" id="manage-track">
                <input type="hidden" name="id" value="<?php echo $track_id; ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="date">Date</label>
                            <input type="date" name="date" id="date" class="form-control form-control-sm" value="<?php echo $date; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="time">Time</label>
                            <input type="time" name="time" id="time" class="form-control form-control-sm" value="<?php echo $time; ?>" required>
                            <small class="form-text text-muted">Time will be displayed in AM/PM format</small>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="destination">Destination</label>
                    <input type="text" name="destination" id="destination" class="form-control form-control-sm" value="<?php echo $destination; ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control form-control-sm" value="<?php echo $address; ?>" required>
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <textarea name="purpose" id="purpose" cols="30" rows="3" class="form-control" required><?php echo $purpose; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="vehicle">Vehicle</label>
                    <input type="text" name="vehicle" id="vehicle" class="form-control form-control-sm" value="<?php echo $vehicle; ?>" required>
                </div>
                <div class="form-group">
                    <label for="driver">Driver</label>
                    <input type="text" name="driver" id="driver" class="form-control form-control-sm" value="<?php echo $driver; ?>" required>
                </div>
                <div class="form-group">
                    <label for="dept">Department Requested by</label>
                    <input type="text" name="dept" id="dept" class="form-control form-control-sm" value="<?php echo $dept; ?>" required>
                </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="d-flex w-100 justify-content-center align-items-center">
                <button class="btn btn-flat btn-primary mx-2" form="manage-track">Save</button>
                <button class="btn btn-flat btn-default mx-2" type="button" onclick="location.href='index.php?page=track_list'">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('#manage-track').submit(function(e){
        e.preventDefault();
        start_load();
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully saved",'success');
                    setTimeout(function(){
                        location.href = 'index.php?page=track_list';
                    },1500);
                }
            }
        });
    });
</script>