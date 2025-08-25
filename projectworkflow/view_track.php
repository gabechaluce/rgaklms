<?php
include 'db_connect.php';

// Ensure 'id' is set in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid track ID.");
}

$id = intval($_GET['id']); // Ensure $id is a number

// Fetch track details
$qry = $conn->query("SELECT * FROM track WHERE id = $id");

if (!$qry || $qry->num_rows == 0) {
    die("Track not found.");
}

$track = $qry->fetch_assoc();

// Convert time to 12-hour format
$time_12h = date("h:i A", strtotime($track['time']));
?>
<body>
<div class="col-lg-12">
    <div class="row">
        <div class="col-md-12">
            <div class="callout callout-info">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-sm-6">
                            <dl>
                                <dt><b class="border-bottom border-primary">Date</b></dt>
                                <dd><?php echo ucwords($track['date']) ?></dd>
                                <dt><b class="border-bottom border-primary">Time</b></dt>
                                <dd><?php echo $time_12h ?></dd>
                                <dt><b class="border-bottom border-primary">Destination</b></dt>
                                <dd><?php echo html_entity_decode($track['destination']) ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt><b class="border-bottom border-primary">Address</b></dt>
                                <dd><?php echo $track['address'] ?></dd>
                            </dl>
                            <dl>
                                <dt><b class="border-bottom border-primary">Purpose</b></dt>
                                <dd><?php echo $track['purpose'] ?></dd>
                            </dl>
                            <dl>
                                <dt><b class="border-bottom border-primary">Vehicle</b></dt>
                                <dd><?php echo $track['vehicle'] ?></dd>
                            </dl>
                            <dl>
                                <dt><b class="border-bottom border-primary">Driver</b></dt>
                                <dd><?php echo $track['driver'] ?></dd>
                            </dl>
                            <dl>
                                <dt><b class="border-bottom border-primary">Department</b></dt>
                                <dd><?php echo $track['dept'] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.delete_track').click(function(){
        _conf("Are you sure to delete this track?","delete_track",[$(this).attr('data-id')]);
    });
</script>
<style>
    body, .wrapper, .content-wrapper {
        background-color:#f4f1ed !important;
    }
</style>
</body>