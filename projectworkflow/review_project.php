<!-- REVIEW_PROJECT CODE -->

<?php
include 'db_connect.php';

// Ensure 'id' is set in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid project ID.");
}

$id = intval($_GET['id']); // Ensure $id is a number
$stat = array("Pending", "To be reviewed", "On-Progress", "On-Hold", "Over Due", "Done");

// Fetch project details
$qry = $conn->query("SELECT * FROM project_list WHERE id = $id");

if (!$qry || $qry->num_rows == 0) {
    die("Project not found.");
}

$project = $qry->fetch_assoc();

// Extract variables from the fetched project
foreach ($project as $k => $v) {
    $$k = $v;
}

// Fetch task progress
$tprog = $conn->query("SELECT * FROM task_list WHERE project_id = $id")->num_rows;
$cprog = $conn->query("SELECT * FROM task_list WHERE project_id = $id AND status = 3")->num_rows;
$prog = $tprog > 0 ? ($cprog / $tprog) * 100 : 0;
$prog = $prog > 0 ? number_format($prog, 2) : $prog;

// Fetch user productivity
$prod = $conn->query("SELECT * FROM user_productivity WHERE project_id = $id")->num_rows;

// Update project status if needed
if ($status == 0 && strtotime(date('Y-m-d')) >= strtotime($start_date)) {
    $status = ($prod > 0 || $cprog > 0) ? 2 : 1;
} elseif ($status == 0 && strtotime(date('Y-m-d')) > strtotime($end_date)) {
    $status = 4;
}

// Fetch project manager details (Fixed SQL issue)
$manager = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE id = '$manager_id'");

$manager = ($manager && $manager->num_rows > 0) ? $manager->fetch_assoc() : null;

// Existing team member check
$is_team_member = false;
if(!empty($user_ids) && isset($_SESSION['login_id'])) {
    $user_ids_array = explode(',', $user_ids);
    $is_team_member = in_array($_SESSION['login_id'], $user_ids_array);
}

// Project Manager check - THIS IS STEP 7
$is_manager = false;
if(!empty($manager_id) && isset($_SESSION['login_id'])) {
    $manager_ids_array = explode(',', $manager_id);
    $is_manager = in_array($_SESSION['login_id'], $manager_ids_array);
}

// This uses both checks
$show_review_message = ($status == 1) && ($is_team_member || $is_manager);
// Determine if user can add tasks/productivity based on project status
// Only allow when project is NOT in "To be reviewed" status (status != 1)
$can_add_tasks = ($status != 1) || $_SESSION['login_type'] == 1; // Admin can always add
?>
<body>
<div class="col-lg-12">
<?php if ($show_review_message): ?>
    <div class="alert alert-info">
        TO BE REVIEWED BY THE PROJECT MANAGER
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-12">
        <div class="callout callout-info">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-6">
                        <dl>
                            <dt><b class="border-bottom border-primary">Project Name</b></dt>
                            <dd><?php echo ucwords($name) ?></dd>
                            <dt><b class="border-bottom border-primary">Description</b></dt>
                            <dd>
                                <div id="desc-short" class="desc-short"><?php echo substr(strip_tags(html_entity_decode($description)), 0, 200); ?>...</div>
                                <div id="desc-full" class="desc-full" style="display: none;"><?php echo html_entity_decode($description); ?></div>
                                <button id="toggleDesc" class="btn btn-link p-0">See More</button>
                            </dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        <dl>
                            <dt><b class="border-bottom border-primary">Start Date</b></dt>
                            <dd><?php echo date("F d, Y",strtotime($start_date)) ?></dd>
                        </dl>
                        <dl>
                            <dt><b class="border-bottom border-primary">End Date</b></dt>
                            <dd><?php echo date("F d, Y",strtotime($end_date)) ?></dd>
                        </dl>
                        <dl>
                            <dt><b class="border-bottom border-primary">Status</b></dt>
                            <dd>
                                <?php
                                  if($stat[$status] =='Pending'){
                                    echo "<span class='badge badge-secondary'>{$stat[$status]}</span>";
                                  }elseif($stat[$status] =='To be reviewed'){
                                    echo "<span class='badge badge-primary'>{$stat[$status]}</span>";
                                  }elseif($stat[$status] =='On-Progress'){
                                    echo "<span class='badge badge-info'>{$stat[$status]}</span>";
                                  }elseif($stat[$status] =='On-Hold'){
                                    echo "<span class='badge badge-warning'>{$stat[$status]}</span>";
                                  }elseif($stat[$status] =='Over Due'){
                                    echo "<span class='badge badge-danger'>{$stat[$status]}</span>";
                                  }elseif($stat[$status] =='Done'){
                                    echo "<span class='badge badge-success'>{$stat[$status]}</span>";
                                  }
                                ?>
                            </dd>
                        </dl>
                        <dl>
                        <dl>
                            <dt><b class="border-bottom border-primary">Project Manager</b></dt>
                            <dd>
                                <?php 
                                if(!empty($manager_id)):
                                    $managers = $conn->query("SELECT *, CONCAT(firstname,' ',lastname) as name FROM users WHERE id IN ($manager_id)");
                                    while($row = $managers->fetch_assoc()):
                                ?>
                                <div class="d-flex align-items-center mt-1">
                                    <img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Avatar">
                                    <b><?php echo ucwords($row['name']) ?></b>
                                </div>
                                <?php 
                                    endwhile;
                                else: 
                                ?>
                                    <small><i>No Manager Assigned</i></small>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <span><b>Team Member/s:</b></span>
                <div class="card-tools">
                    <!-- <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="manage_team">Manage</button> -->
                </div>
            </div>
            <div class="card-body">
                <ul class="users-list clearfix">
                    <?php 
                    if(!empty($user_ids)):
                        $members = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where id in ($user_ids) order by concat(firstname,' ',lastname) asc");
                        while($row=$members->fetch_assoc()):
                    ?>
                            <li>
                                <img src="assets/uploads/<?php echo $row['avatar'] ?>" alt="User Image">
                                <a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['name']) ?></a>
                                <!-- <span class="users-list-date">Today</span> -->
                            </li>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </ul>
            </div>
        </div>
    </div>


                <?php if ($is_manager && $status == 1): ?>
<div class="card-footer border-top border-info">
    <div class="d-flex w-100 justify-content-center align-items-center">
        <button class="btn btn-success mx-2" onclick="updateProjectStatus(<?= $id ?>, 'accepted')">Accept</button>
        <button class="btn btn-danger mx-2" onclick="updateProjectStatus(<?= $id ?>, 'declined')">Decline</button>
    </div>
</div>
<?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>
<style>
         html {
            scroll-behavior: smooth;
        }
    .desc-short {
    display: inline;
}
.desc-full {
    display: none;
}

    .users-list>li img {
        border-radius: 50%;
        height: 67px;
        width: 67px;
        object-fit: cover;
    }
    .users-list>li {
        width: 33.33% !important
    }
    .truncate {
        -webkit-line-clamp:1 !important;
    }
</style>
<script>
    $('#new_task').click(function(){
        uni_modal("New Task For <?php echo ucwords($name) ?>","manage_task.php?pid=<?php echo $id ?>","mid-large")
    })
    $('.edit_task').click(function(){
        uni_modal("Edit Task: "+$(this).attr('data-task'),"manage_task.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),"mid-large")
    })
    $('.view_task').click(function(){
        uni_modal("Task Details","view_task.php?id="+$(this).attr('data-id'),"mid-large")
    })
    $('#new_productivity').click(function(){
        uni_modal("<i class='fa fa-plus'></i> New Progress","manage_progress.php?pid=<?php echo $id ?>",'large')
    })
    $('.manage_progress').click(function(){
        uni_modal("<i class='fa fa-edit'></i> Edit Progress","manage_progress.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),'large')
    })
    $('.delete_progress').click(function(){
    _conf("Are you sure to delete this progress?","delete_progress",[$(this).attr('data-id')])
    })
    function delete_progress($id){
        start_load()
        $.ajax({
            url:'ajax.php?action=delete_progress',
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
<script>
document.getElementById("toggleDesc").addEventListener("click", function() {
    var shortDesc = document.getElementById("desc-short");
    var fullDesc = document.getElementById("desc-full");
    if (shortDesc.style.display === "none") {
        shortDesc.style.display = "inline";
        fullDesc.style.display = "none";
        this.textContent = "See More";
    } else {
        shortDesc.style.display = "none";
        fullDesc.style.display = "inline";
        this.textContent = "See Less";
    }
});

</script>
<!-- Scroll to Top Button -->
<button id="scrollToTop" class="btn btn-primary" style="
    position: fixed; 
    bottom: 20px; 
    right: 20px; 
    width: 50px; 
    height: 50px; 
    border-radius: 50%;
    font-size: 20px;
    text-align: center;
    line-height: 30px;
    padding: 10px;
    background-color: #894b0d; 
    color: white;
    border: none;
    opacity: 0;
    visibility: hidden;
    transform: scale(0.8);
    transition: opacity 0.5s ease, visibility 0.5s ease, transform 0.5s ease;
    z-index: 1000;
    cursor: pointer;
">
    ↑
</button>

<script>
    var scrollButton = document.getElementById("scrollToTop");

    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollButton.style.opacity = "1";
            scrollButton.style.visibility = "visible";
            scrollButton.style.transform = "scale(1)";
        } else {
            scrollButton.style.opacity = "0";
            scrollButton.style.visibility = "hidden";
            scrollButton.style.transform = "scale(0.8)";
        }
    });

    // Scroll to top when clicking the button
    scrollButton.addEventListener("click", function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>
<script>
function updateProjectStatus(projectId, action) {
    start_load();
    const newStatus = action === 'accepted' ? 2 : 0; // 2='On-Progress', 0='Pending'
    $.ajax({
        url: 'ajax.php?action=update_project_status',
        method: 'POST',
        data: { id: projectId, status: newStatus },
        success: function(resp) {
            if(resp == 1) {
                alert_toast('Status updated successfully', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                alert_toast('Error updating status', 'error');
                end_load();
            }
        },
        error: function() {
            alert_toast('An error occurred', 'error');
            end_load();
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