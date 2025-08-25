<?php
include 'db_connect.php';

// Ensure 'id' is set in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid inquiry ID.");
}

$id = intval($_GET['id']); // Ensure $id is a number
$inq_status = array("Serious", "Not Serious", "Done");
$quote_status = array("Quotation Sent", "Quotation Not Sent");

// Fetch inquiry details
$qry = $conn->query("SELECT * FROM inquiry_list WHERE id = $id");

if (!$qry || $qry->num_rows == 0) {
    die("Inquiry not found.");
}

$inquiry = $qry->fetch_assoc();
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
                                <dt><b class="border-bottom border-primary">Name</b></dt>
                                <dd><?php echo ucwords($inquiry['name']) ?></dd>
                                <dt><b class="border-bottom border-primary">Contact Info</b></dt>
                                <dd><?php echo $inquiry['contact'] ?></dd>
                                <dt><b class="border-bottom border-primary">Description</b></dt>
                                <dd><?php echo html_entity_decode($inquiry['description']) ?></dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dl>
                                <dt><b class="border-bottom border-primary">Business Name</b></dt>
                                <dd><?php echo $inquiry['business_name'] ?></dd>
                            </dl>
                            <dl>
                                <dt><b class="border-bottom border-primary">Inquiry Status</b></dt>
                                <dd><?php echo $inq_status[$inquiry['inquiry_status']] ?></dd>
                            </dl>
                            <dl>
                                <dt><b class="border-bottom border-primary">Quotation Status</b></dt>
                                <dd><?php echo $quote_status[$inquiry['quotation_status']] ?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('.delete_inquiry').click(function(){
        _conf("Are you sure to delete this inquiry?","delete_inquiry",[$(this).attr('data-id')]);
    });
</script>
<style>

body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}


</style>
</body>