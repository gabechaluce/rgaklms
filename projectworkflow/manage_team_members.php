<?php
// File: manage_team_members.php
include 'db_connect.php';

if(isset($_GET['pid'])){
    $project_id = $_GET['pid'];
    
    // Get current project data
    $qry = $conn->query("SELECT * FROM project_list WHERE id = {$project_id}");
    if($qry->num_rows > 0){
        $project = $qry->fetch_assoc();
        $current_user_ids = !empty($project['user_ids']) ? explode(',', $project['user_ids']) : array();
        
        // Get all users who are not designers or estimators
        $users = $conn->query("SELECT *, CONCAT(lastname, ', ', firstname) as name FROM users WHERE type != 3 AND type != 5 AND type != 1 ORDER BY CONCAT(lastname, ', ', firstname) ASC");
    }
}
?>

<div class="container-fluid">
    <form action="" id="manage-team-form">
        <input type="hidden" name="project_id" value="<?php echo $project_id ?>">
        <div class="form-group">
            <label for="team_members">Select Team Members</label>
            <select name="user_ids[]" id="team_members" class="form-control select2" multiple="multiple">
                <?php 
                while($row = $users->fetch_assoc()):
                    $selected = in_array($row['id'], $current_user_ids) ? 'selected' : '';
                    // Check if user is not a designer (type=4) or estimator (type=5)
                    if($row['type'] != 3 && $row['type'] != 5):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo $selected ?>><?php echo ucwords($row['name']) ?></option>
                <?php 
                    endif;
                endwhile;
                ?>
            </select>
        </div>
    </form>
</div>

<script>
    $(document).ready(function(){
        $('.select2').select2({
            placeholder: "Please select team members",
            width: '100%'
        });
        
        $('#manage-team-form').submit(function(e){
            e.preventDefault();
            start_load();
            
            $.ajax({
                url: 'ajax.php?action=save_team_members',
                method: 'POST',
                data: $(this).serialize(),
                success: function(resp){
                    if(resp == 1){
                        alert_toast("Team members updated successfully", "success");
                        setTimeout(function(){
                            location.reload();
                        }, 1500);
                    }
                }
            });
        });
    });
</script>

