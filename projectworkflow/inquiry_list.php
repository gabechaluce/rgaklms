<?php include 'db_connect.php'; ?>

<?php
// Default query condition
$where = "";

// Apply filtering if accessed from the notification link
if (isset($_GET['filter']) && $_GET['filter'] == 'pending') {
    $where = "WHERE inquiry_status != 2 AND TIMESTAMPDIFF(HOUR, date_created, NOW()) > 24";
}
?>
<body>
<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <?php if ($_SESSION['login_type'] != 8): ?>
                <div class="card-tools">
                    <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_inquiry">
                        <i class="fa fa-plus"></i> Add New Inquiry
                    </a>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-condensed m-0" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="15%">
                        <col width="20%">
                        <col width="15%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Name</th>
                            <th class="text-center">Contact Info</th>
                            <th class="text-center">Business Name</th>
                            <th class="text-center">Inquiry Status</th>
                            <th class="text-center">Quotation Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $inq_status = array("Serious", "Not Serious", "Done");
                        $quote_status = array("Quotation Sent", "Quotation Not Sent");

                        // Apply filtering condition dynamically
                        $qry = $conn->query("SELECT * FROM inquiry_list $where ORDER BY date_created DESC, name ASC");
                        while ($row = $qry->fetch_assoc()):
                        ?>
                            <tr>
                                <th class="text-center"><?php echo $i++ ?></th>
                                <td><?php echo ucwords($row['name']) ?></td>
                                <td><?php echo $row['contact'] ?></td>
                                <td><?php echo $row['business_name'] ?></td>
                                <td><?php echo $inq_status[$row['inquiry_status']] ?></td>
                                <td><?php echo $quote_status[$row['quotation_status']] ?></td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info dropdown-toggle" data-toggle="dropdown">
                                        Action
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item view_inquiry" href="./index.php?page=view_inquiry&id=<?php echo $row['id'] ?>">View</a>
                                        <div class="dropdown-divider"></div>
                                        <?php if ($_SESSION['login_type'] != 8): ?>
                                            <a class="dropdown-item" href="./index.php?page=edit_inquiry&id=<?php echo $row['id'] ?>">Edit</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete_inquiry" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
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
        $('#list').dataTable();

        $('.delete_inquiry').click(function () {
            _conf("Are you sure to delete this inquiry?", "delete_inquiry", [$(this).attr('data-id')]);
        });
    });

    function delete_inquiry($id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_inquiry',
            method: 'POST',
            data: { id: $id },
            success: function (resp) {
                if (resp == 1) {
                    alert_toast("Inquiry successfully deleted", 'success');
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


</style>
</body>