<?php include 'db_connect.php' ?>
<body>
    
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <!-- Header content if needed -->
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-condensed m-0" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>File Name</th>
                            <th>Project</th>
                            <th>File Type</th>
                            <th>Upload Date</th>
                            <th>Uploaded By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $where1 = "";
                        $where2 = "";
                        // Apply role-based filters if needed
                        if($_SESSION['login_type'] == 2){
                            $where1 = " WHERE f.project_id IN (SELECT id FROM project_list WHERE manager_id = '{$_SESSION['login_id']}') ";
                            $where2 = " WHERE p.project_id IN (SELECT id FROM project_list WHERE manager_id = '{$_SESSION['login_id']}') ";
                        }elseif($_SESSION['login_type'] == 3){
                            $where1 = " WHERE f.project_id IN (SELECT id FROM project_list WHERE FIND_IN_SET('{$_SESSION['login_id']}', user_ids)) ";
                            $where2 = " WHERE p.project_id IN (SELECT id FROM project_list WHERE FIND_IN_SET('{$_SESSION['login_id']}', user_ids)) ";
                        }
                        
                        $qry = $conn->query("(SELECT f.id, f.original_name, f.file_type, f.url, f.upload_date, f.project_id, 
                          p.name as project_name, CONCAT(u.firstname, ' ', u.lastname) as uploader,
                          f.uploaded_by,
                          'uploaded_files' as source
                          FROM uploaded_files f 
                          LEFT JOIN project_list p ON f.project_id = p.id 
                          LEFT JOIN users u ON f.uploaded_by = u.id 
                          $where1 AND (f.is_deleted = 0 OR f.is_deleted IS NULL))
                        UNION ALL
                        (SELECT p.id, p.original_name, p.file_type, p.url, p.upload_date, p.project_id,
                         pl.name as project_name, CONCAT(u.firstname, ' ', u.lastname) as uploader,
                         p.uploaded_by,
                         'progress_files' as source
                         FROM progress_files p
                         LEFT JOIN project_list pl ON p.project_id = pl.id
                         LEFT JOIN users u ON p.uploaded_by = u.id
                         $where2 AND (p.is_deleted = 0 OR p.is_deleted IS NULL))
                        ORDER BY upload_date DESC");
                        while($row = $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $i++ ?></td>
                            <td>
                                <p><b><?php echo $row['original_name'] ?></b></p>
                            </td>
                            <td>
                                <p><?php echo $row['project_name'] ? $row['project_name'] : 'No Project' ?></p>
                            </td>
                            <td>
                                <p><?php echo strtoupper($row['file_type']) ?></p>
                            </td>
                            <td><b><?php echo date("M d, Y h:i A", strtotime($row['upload_date'])) ?></b></td>
                            <td><b><?php echo $row['uploader'] ?></b></td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="<?php echo $row['url'] ?>" class="btn btn-primary btn-sm" target="_blank">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <?php if($_SESSION['login_id'] == $row['uploaded_by'] || $_SESSION['login_type'] == 1): ?>
                                    <?php if($row['source'] == 'uploaded_files'): ?>
                                    <button type="button" class="btn btn-danger btn-sm delete_file" data-id="<?php echo $row['id'] ?>">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                    <?php else: ?>
                                    <button type="button" class="btn btn-danger btn-sm delete_progress_file" data-id="<?php echo $row['id'] ?>">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                    <?php endif; ?>
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
<style>
    table p{
        margin: unset !important;
    }
    table td{
        vertical-align: middle !important;
    }
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .btn-group {
        display: flex;
        flex-wrap: nowrap;
    }
    .btn-group .btn {
        white-space: nowrap;
    }
</style>
<script>
    $(document).ready(function(){
        $('#list').dataTable()
        
        $('.delete_file').click(function(){
            _conf("Are you sure to delete this file?","delete_file",[$(this).attr('data-id')])
        })
        
        $('.delete_progress_file').click(function(){
            _conf("Are you sure to delete this file?","delete_progress_file",[$(this).attr('data-id')])
        })
    })
    
    function delete_file($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_file',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("File successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)
                }
            }
        })
    }
    
    function delete_progress_file($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_progress_file',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("File successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)
                } else {
                    console.error("Failed to delete file, response:", resp);
                    alert_toast("Failed to delete file",'error')
                    end_load()
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", status, error);
                alert_toast("Error occurred during deletion",'error')
                end_load()
            }
        })
    }
</script>
<style>
body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}
</style>
</body>