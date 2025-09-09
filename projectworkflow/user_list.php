<?php include'db_connect.php' ?>
<body>
    
<div class="col-lg-12">
    <div class="card card-outline card-success">
        <div class="card-header">
            <div class="card-tools">
                <a class="btn btn-block btn-sm btn-default btn-flat border-primary" href="./index.php?page=new_user"><i class="fa fa-plus"></i> Add New User</a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table tabe-hover table-bordered m-0" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="25%">
                        <col width="25%">
                        <col width="25%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        // Updated type array to match the new user roles from the form
                        $type = array(
                            '',
                            'General Manager',         // type 1
                            'Project Coordinator',     // type 2
                            'Designer',               // type 3
                            'Inventory Coordinator',  // type 4
                            'Estimator',              // type 5
                            'Accounting',             // type 6
                            'Project Manager',        // type 7
                            'Purchasing',             // type 8
                            'Sales',                  // type 9
                            'Admin'                   // type 10
                        );
                        // Modified query to exclude users with "not yet" in firstname
                        $qry = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users WHERE firstname NOT LIKE 'not yet%' order by concat(firstname,' ',lastname) asc");
                        while($row= $qry->fetch_assoc()):
                        ?>
                        <tr>
                            <th class="text-center"><?php echo $i++ ?></th>
                            <td><b><?php echo ucwords($row['name']) ?></b></td>
                            <td><b><?php echo $row['email'] ?></b></td>
                            <td><b><?php echo isset($type[$row['type']]) ? $type[$row['type']] : 'Unknown Role' ?></b></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                Action
                                </button>
                                <div class="dropdown-menu" style="">
                                <a class="dropdown-item view_user" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">View</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="./index.php?page=edit_user&id=<?php echo $row['id'] ?>">Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item delete_user" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
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
    $(document).ready(function(){
        $('#list').dataTable()
    $('.view_user').click(function(){
        uni_modal("<i class='fa fa-id-card'></i> User Details","view_user.php?id="+$(this).attr('data-id'))
    })
    $('.delete_user').click(function(){
    _conf("Are you sure to delete this user?","delete_user",[$(this).attr('data-id')])
    })
    })
    function delete_user($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_user',
            method:'POST',
            data:{id:$id},
            success:function(resp){
                if(resp==1){
                    alert_toast("Data successfully deleted",'success')
                    setTimeout(function(){
                        location.reload()
                    },1500)

                }
            }
        })
    }
</script>
<style>
body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}
</style>
</body>