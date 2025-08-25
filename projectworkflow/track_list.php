<?php include 'db_connect.php'; ?>

<body>
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header text-center">
            <h3 class="card-title">Track List</h3>
            <?php if ($_SESSION['login_type'] != 3): ?>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_track">
                        <i class="fa fa-plus"></i> Add New Track
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-condensed m-0" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="10%">
                        <col width="10%">
                        <col width="15%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                        <col width="15%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr class="text-center">
                            <th>#</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Destination</th>
                            <th>Address</th>
                            <th>Purpose</th>
                            <th>Vehicle</th>
                            <th>Driver</th>
                            <th>Dept. Requested by</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $where = "";
                        $qry = $conn->query("SELECT * FROM track $where ORDER BY id ASC");
                        while ($row = $qry->fetch_assoc()):
                            // Convert time to 12-hour format
                            $time_24h = $row['time'];
                            $time_12h = date("h:i A", strtotime($time_24h));
                        ?>
                            <tr class="text-center">
                                <th><?php echo $i++ ?></th>
                                <td><?php echo ucwords($row['date']) ?></td>
                                <td><?php echo $time_12h ?></td>
                                <td><?php echo $row['destination'] ?></td>
                                <td><?php echo $row['address'] ?></td>
                                <td><?php echo $row['purpose'] ?></td>
                                <td><?php echo $row['vehicle'] ?></td>
                                <td><?php echo $row['driver'] ?></td>
                                <td><?php echo $row['dept'] ?></td>
                                <td>
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info dropdown-toggle" data-toggle="dropdown">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item view_track" href="./index.php?page=view_track&id=<?php echo $row['id'] ?>">View</a>
                                        <div class="dropdown-divider"></div>
                                        <?php if ($_SESSION['login_type'] != 3): ?>
                                            <a class="dropdown-item" href="./index.php?page=edit_track&id=<?php echo $row['id'] ?>">Edit</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete_track" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#list').dataTable({
            "columnDefs": [
                { "className": "text-center", "targets": "_all" }
            ]
        });

        $('.delete_track').click(function () {
            _conf("Are you sure to delete this track?", "delete_track", [$(this).attr('data-id')]);
        });
    });

    function delete_track($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_track',
            method: 'POST',
            data: { id: $id },
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Track successfully deleted", 'success');
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>

<style>
    body, .wrapper, .content-wrapper {
        background-color:#f4f1ed !important;
    }
    
    .table th, .table td {
        vertical-align: middle !important;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-header h3 {
        margin: 0 auto;
    }
    
    .card-tools {
        position: absolute;
        right: 1rem;
    }
    
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    /* Fix for dropdown menu in responsive table */
    .dropdown-menu {
        position: absolute !important;
    }
</style>
</body>