<?php include('db_connect.php'); ?>

<div class="col-lg-12">
    <div class="card card-outline card-primary">
        <div class="card-header">
            <h5 class="card-title">All Notifications</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="notification-list">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Project</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT n.*, p.name as project_name 
                        FROM project_notifications n
                        JOIN project_list p ON p.id = n.project_id
                        WHERE n.user_id = '{$_SESSION['login_id']}'
                        ORDER BY n.created_at DESC");
                        
                    while($row = $qry->fetch_assoc()):
                        $read_class = $row['is_read'] ? '' : 'font-weight-bold';
                    ?>
                    <tr class="<?php echo $read_class; ?>">
                        <td><?php echo $i++; ?></td>
                        <td><a href="index.php?page=project_details&id=<?php echo $row['project_id']; ?>"><?php echo $row['project_name']; ?></a></td>
                        <td><?php echo $row['message']; ?></td>
                        <td><?php echo date("M d, Y h:i A", strtotime($row['created_at'])); ?></td>
                        <td>
                            <?php if($row['is_read'] == 0): ?>
                            <button type="button" class="btn btn-sm btn-info mark-read" data-id="<?php echo $row['id']; ?>">Mark as Read</button>
                            <?php else: ?>
                            <span class="badge badge-success">Read</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#notification-list').dataTable();
    
    $('.mark-read').click(function(){
        var id = $(this).data('id');
        var _this = $(this);
        
        $.ajax({
            url: 'ajax.php?action=mark_notification_read',
            method: 'POST',
            data: {id: id},
            success: function(resp){
                if(resp == 1){
                    _this.closest('tr').removeClass('font-weight-bold');
                    _this.closest('td').html('<span class="badge badge-success">Read</span>');
                }
            }
        });
    });
});
</script>