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
    // Fetch project data
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

    // Initialize all project fields with defaults if empty
    $full_name = isset($project['full_name']) ? $project['full_name'] : '';
    $location = isset($project['location']) ? $project['location'] : '';
    $dimension = isset($project['dimension']) ? $project['dimension'] : '';
    $project_cost = isset($project['project_cost']) ? $project['project_cost'] : '';
    
    // Fetch user productivity
    $prod = $conn->query("SELECT * FROM user_productivity WHERE project_id = $id")->num_rows;

    // Update project status if needed
    if ($status == 0 && strtotime(date('Y-m-d')) >= strtotime($start_date)) {
        $status = ($prod > 0 || $cprog > 0) ? 2 : 1;
    } elseif ($status == 0 && strtotime(date('Y-m-d')) > strtotime($end_date)) {
        $status = 4;
    }

    // Initialize all ID fields with defaults
    $coordinator_ids = isset($project['coordinator_ids']) ? trim($project['coordinator_ids']) : '';
    $designer_ids = isset($project['designer_ids']) ? trim($project['designer_ids']) : '';
    $estimator_ids = isset($project['estimator_ids']) ? trim($project['estimator_ids']) : '';
    $inventory_ids = isset($project['inventory_ids']) ? trim($project['inventory_ids']) : '';
    $manager_id = isset($project['manager_id']) ? trim($project['manager_id']) : '';
    $user_ids = isset($project['user_ids']) ? trim($project['user_ids']) : '';

    // Fetch project manager details - with empty check
    $manager = null;
    if (!empty($manager_id)) {
        $manager_query = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE id IN ($manager_id)");
        $manager = ($manager_query && $manager_query->num_rows > 0) ? $manager_query : null;
    }

    // Fetch team roles with proper empty checks
    $coordinators = null;
    if (!empty($coordinator_ids)) {
        $coordinators = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 2 AND FIND_IN_SET(id, '$coordinator_ids')");
    }

    $designers = null;
    if (!empty($designer_ids)) {
        $designers = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 3 AND FIND_IN_SET(id, '$designer_ids')");
    }

    $estimators = null;
    if (!empty($estimator_ids)) {
        $estimators = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 5 AND FIND_IN_SET(id, '$estimator_ids')");
    }

    $inventory_coordinators = null;
    if (!empty($inventory_ids)) {
        $inventory_coordinators = $conn->query("SELECT *, CONCAT(firstname, ' ', lastname) AS name FROM users WHERE type = 4 AND FIND_IN_SET(id, '$inventory_ids')");
    }

    // Check if current user is a team member
    $is_team_member = false;
    if (!empty($user_ids) && isset($_SESSION['login_id'])) {
        $user_ids_array = explode(',', $user_ids);
        $is_team_member = in_array($_SESSION['login_id'], $user_ids_array);
    }

    // Project Manager check
    $is_manager = false;
    if (!empty($manager_id) && isset($_SESSION['login_id'])) {
        $manager_ids_array = explode(',', $manager_id);
        $is_manager = in_array($_SESSION['login_id'], $manager_ids_array);
    }

    // This uses both checks
    $show_review_message = ($status == 1) && ($is_team_member || $is_manager);
    
    // Determine if user can add tasks/productivity based on project status
    $can_add_tasks = ($status != 1) || $_SESSION['login_type'] == 1; // Admin can always add

    // Determine which view to show based on project status
    $is_review_mode = ($status == 1);
    
    // Add this function to check if a user has task permissions
    function userCanManageTasks($user_type) {
        // Allow these roles to manage tasks:
        $allowed_roles = array(1, 2, 3, 7, 10);
        return in_array($user_type, $allowed_roles);
    }
    
    // Then replace the current can_add_tasks check with:
    $can_add_tasks = ($status != 1) || userCanManageTasks($_SESSION['login_type']);
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?php echo $is_review_mode ? 'Review Project' : 'View Project'; ?></title>
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
            body, .wrapper, .content-wrapper {
                background-color:#f4f1ed !important;
            }

            /* Image Preview Modal Styles */
            .image-preview-modal {
                display: none;
                position: fixed;
                z-index: 9999;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0,0,0,0.9);
                animation: fadeIn 0.3s;
            }

            .image-preview-content {
                position: relative;
                margin: auto;
                padding: 20px;
                width: 90%;
                max-width: 1200px;
                height: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .preview-image {
                max-width: 100%;
                max-height: 90vh;
                object-fit: contain;
                border-radius: 8px;
                box-shadow: 0 4px 20px rgba(0,0,0,0.5);
                transition: transform 0.3s ease;
            }

            .preview-image:hover {
                transform: scale(1.02);
            }

            .close-preview {
                position: absolute;
                top: 15px;
                right: 30px;
                color: #fff;
                font-size: 40px;
                font-weight: bold;
                cursor: pointer;
                transition: opacity 0.3s;
                z-index: 10000;
            }

            .close-preview:hover {
                opacity: 0.7;
            }

            .image-info {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(0,0,0,0.7);
                color: white;
                padding: 10px 20px;
                border-radius: 20px;
                font-size: 14px;
                white-space: nowrap;
            }

            /* Navigation arrows */
            .nav-arrow {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(0,0,0,0.5);
                color: white;
                border: none;
                font-size: 24px;
                padding: 15px 20px;
                cursor: pointer;
                border-radius: 50%;
                transition: all 0.3s ease;
                z-index: 10000;
            }

            .nav-arrow:hover {
                background: rgba(0,0,0,0.8);
                transform: translateY(-50%) scale(1.1);
            }

            .nav-arrow.prev {
                left: 20px;
            }

            .nav-arrow.next {
                right: 20px;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            /* Clickable image styles */
            .clickable-image {
                cursor: pointer;
                transition: all 0.3s ease;
                border-radius: 8px;
            }

            .clickable-image:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                filter: brightness(1.1);
            }

            /* Image gallery grid */
            .image-gallery {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-top: 15px;
            }

            .image-item {
                position: relative;
                overflow: hidden;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }

            .image-item:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            }

            .image-item img {
                width: 100%;
                height: 200px;
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .image-item:hover img {
                transform: scale(1.1);
            }

            .image-overlay {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(transparent, rgba(0,0,0,0.7));
                color: white;
                padding: 15px 10px 10px;
                font-size: 12px;
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .image-item:hover .image-overlay {
                opacity: 1;
            }

            /* Progress Images Styles */
            .progress-image {
                max-width: 200px;
                max-height: 150px;
                object-fit: cover;
                border-radius: 8px;
                margin: 10px 10px 10px 0;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }

            .progress-image:hover {
                transform: scale(1.05);
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                filter: brightness(1.1);
            }

            .progress-images-container {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 10px;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .image-preview-content {
                    width: 95%;
                    padding: 10px;
                }

                .close-preview {
                    right: 15px;
                    font-size: 30px;
                }

                .nav-arrow {
                    font-size: 20px;
                    padding: 10px 15px;
                }

                .image-gallery {
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 10px;
                }

                .image-item img {
                    height: 150px;
                }

                .progress-image {
                    max-width: 120px;
                    max-height: 90px;
                }
            }
        </style>
    </head>
    <body>
    <!-- Image Preview Modal -->
    <div id="imageModal" class="image-preview-modal">
        <div class="image-preview-content">
            <span class="close-preview" onclick="closeImagePreview()">&times;</span>
            <button class="nav-arrow prev" onclick="navigateImage(-1)">❮</button>
            <img id="modalImage" class="preview-image" alt="Preview">
            <button class="nav-arrow next" onclick="navigateImage(1)">❯</button>
            <div id="imageInfo" class="image-info"></div>
        </div>
    </div>

    <div class="col-lg-12">
        <?php if ($show_review_message): ?>
            <div class="alert alert-info">
                TO BE REVIEWED BY THE PROJECT MANAGER
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">
            <div class="callout callout-info" style="position: relative;">
        <!-- Add Edit Button Here -->
        <?php if($_SESSION['login_type'] == 1 || $is_manager): ?>
            <div class="edit-project-btn">
        <a class="btn btn-sm btn-primary" href="./index.php?page=edit_project&id=<?php echo $id ?>">
            <i class="fa fa-edit"></i> Edit Project
        </a>
    </div>

        <?php endif; ?>
        
        <div class="col-md-12">
            <div class="row">
              <div class="col-sm-6">
    <dl>
        <dt><b class="border-bottom border-primary">Project Name</b></dt>
        <dd><?php echo ucwords($name) ?></dd>
     
        <!-- Add new Location field -->
        <dt><b class="border-bottom border-primary">Location</b></dt>
        <dd><?php echo !empty($location) ? ucwords($location) : '<small><i>Not specified</i></small>' ?></dd>
        
     
        <!-- Add new Project Cost field -->
        <dt><b class="border-bottom border-primary">Project Cost</b></dt>
        <dd><?php echo !empty($project_cost) ? '₱' . number_format($project_cost, 2) : '<small><i>Not specified</i></small>' ?></dd>

        <!-- Files section remains the same -->
        <dt><b class="border-bottom border-primary">Files</b></dt>
        <dd>
            <?php 
            // Get current user ID and type
            $current_user_id = $_SESSION['login_id'];
            $current_user_type = $_SESSION['login_type'];
            $is_admin = ($current_user_type == 1);
            
            // Query files with uploaded_by information
            $files_query = $conn->query("SELECT 'project' as source, id, filename, original_name, url as file_path, file_type, uploaded_by 
                                    FROM uploaded_files 
                                    WHERE project_id = $id AND is_deleted = 0
                                    UNION
                                    SELECT 'progress' as source, id, filename, original_name, file_path, file_type, uploaded_by 
                                    FROM progress_files 
                                    WHERE project_id = $id AND is_deleted = 0");
            
            if ($files_query && $files_query->num_rows > 0): 
                $images = array();
                $documents = array();
                
                // Separate images from documents
                while($file = $files_query->fetch_assoc()) {
                    $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                    $is_image = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                    
                    if ($is_image) {
                        $images[] = $file;
                    } else {
                        $documents[] = $file;
                    }
                }
                ?>
                
                <div class="file-container">
                    <!-- Display Images in Gallery -->
                    <?php if (!empty($images)): ?>
                        <h6><strong>Images:</strong></h6>
                        <div class="image-gallery">
                            <?php foreach($images as $index => $image): 
                                $can_delete = ($is_admin || $current_user_id == $image['uploaded_by']);
                            ?>
                                <div class="image-item">
                                    <img src="<?php echo $image['file_path'] ?>" 
                                         alt="<?php echo htmlspecialchars($image['original_name']) ?>"
                                         class="clickable-image"
                                         onclick="openImagePreview(<?php echo $index ?>, 'images')"
                                         data-src="<?php echo $image['file_path'] ?>"
                                         data-name="<?php echo htmlspecialchars($image['original_name']) ?>"
                                         data-source="<?php echo $image['source'] ?>">
                                    
                                    <div class="image-overlay">
                                        <?php echo htmlspecialchars($image['original_name']) ?>
                                        <br><small>(<?php echo $image['source'] == 'project' ? 'Project File' : 'Progress File' ?>)</small>
                                    </div>
                                    
                                    <?php if($can_delete): ?>
                                        <button class="btn btn-sm btn-danger delete-file-btn" 
                                                style="position: absolute; top: 5px; right: 5px; opacity: 0.8;"
                                                data-id="<?php echo $image['id'] ?>" 
                                                data-source="<?php echo $image['source'] ?>"
                                                data-name="<?php echo htmlspecialchars($image['original_name']) ?>">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Display Documents -->
                    <?php if (!empty($documents)): ?>
                        <h6><strong>Documents:</strong></h6>
                        <?php foreach($documents as $file): 
                            $ext = strtolower(pathinfo($file['original_name'], PATHINFO_EXTENSION));
                            $icon = 'fa-file text-secondary';
                            switch ($ext) {
                                case 'pdf': $icon = 'fa-file-pdf text-danger'; break;
                                case 'doc': case 'docx': $icon = 'fa-file-word text-primary'; break;
                                case 'xls': case 'xlsx': $icon = 'fa-file-excel text-success'; break;
                                case 'ppt': case 'pptx': $icon = 'fa-file-powerpoint text-warning'; break;
                                case 'dwg': $icon = 'fa-file-alt text-secondary'; break;
                            }
                            
                            $can_delete = ($is_admin || $current_user_id == $file['uploaded_by']);
                            ?>
                            <div class="file-attachment d-flex align-items-center mb-2">
                                <i class="fas <?php echo $icon ?> mr-2"></i>
                                <a href="<?php echo $file['file_path'] ?>" target="_blank" download class="flex-grow-1">
                                    <?php echo htmlspecialchars($file['original_name']) ?>
                                </a>
                                <small class="text-muted mx-2">
                                    (<?php echo $file['source'] == 'project' ? 'Project File' : 'Progress File' ?>)
                                </small>
                                <?php if($can_delete): ?>
                                    <button class="btn btn-sm btn-danger delete-file-btn" 
                                            data-id="<?php echo $file['id'] ?>" 
                                            data-source="<?php echo $file['source'] ?>"
                                            data-name="<?php echo htmlspecialchars($file['original_name']) ?>">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Store images data for JavaScript -->
                <script>
                window.projectImages = <?php echo json_encode(array_map(function($img) {
                    return [
                        'src' => $img['file_path'],
                        'name' => $img['original_name'],
                        'source' => $img['source']
                    ];
                }, $images)); ?>;
                </script>
                
            <?php else: ?>
                <span>No files uploaded.</span>
            <?php endif; ?>
        </dd>

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
        <dt><b class="border-bottom border-primary">Project Manager</b></dt>
        <dd>
            <?php 
            if($manager && $manager->num_rows > 0):
                while($row = $manager->fetch_assoc()):
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
    <dl>    
        <dt><b class="border-bottom border-primary">Project Designer</b></dt>
        <dd>
            <?php 
            if($designers && $designers->num_rows > 0):
                while($row = $designers->fetch_assoc()):
            ?>
            <div class="d-flex align-items-center mt-1">
                <img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Avatar">
                <b><?php echo ucwords($row['name']) ?></b>
            </div>
            <?php 
                endwhile;
            else: 
            ?>
                <small><i>No Project Designer Assigned</i></small>
            <?php endif; ?>
        </dd>
    </dl>
    <dl>
        <dt><b class="border-bottom border-primary">Estimator</b></dt>
        <dd>
            <?php 
            if($estimators && $estimators->num_rows > 0):
                while($row = $estimators->fetch_assoc()):
            ?>
            <div class="d-flex align-items-center mt-1">
                <img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Avatar">
                <b><?php echo ucwords($row['name']) ?></b>
            </div>
            <?php 
                endwhile;
            else: 
            ?>
                <small><i>No Estimators Assigned</i></small>
            <?php endif; ?>
        </dd>
    </dl>
    <dl>
        <dt><b class="border-bottom border-primary">Project Coordinator</b></dt>
        <dd>
            <?php 
            if($coordinators && $coordinators->num_rows > 0):
                while($row = $coordinators->fetch_assoc()):
            ?>
            <div class="d-flex align-items-center mt-1">
                <img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Avatar">
                <b><?php echo ucwords($row['name']) ?></b>
            </div>
            <?php 
                endwhile;
            else: 
            ?>
                <small><i>No Project Coordinator Assigned</i></small>
            <?php endif; ?>
        </dd>
    </dl>

    <dl>
        <dt><b class="border-bottom border-primary">Inventory Coordinator</b></dt>
        <dd>
            <?php 
            if($inventory_coordinators && $inventory_coordinators->num_rows > 0):
                while($row = $inventory_coordinators->fetch_assoc()):
            ?>
            <div class="d-flex align-items-center mt-1">
                <img class="img-circle img-thumbnail p-0 shadow-sm border-info img-sm mr-3" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="Avatar">
                <b><?php echo ucwords($row['name']) ?></b>
            </div>
            <?php 
                endwhile;
            else: 
            ?>
                <small><i>No Inventory Coordinators Assigned</i></small>
            <?php endif; ?>
        </dd>
    </dl>
</div> 
            </div>
        </div>
    </div>
</div>
</div>
</div>

<!-- Team Members and Task List Section -->
<div class="row">
    <!-- Team Member section -->
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <span><b>Team Member/s:</b></span>
            </div>
            <div class="card-body">
                <ul class="users-list clearfix">
                    <?php 
                    if(!empty($user_ids)):
                        // Only show users with type = 3 (team members)
                        $members = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users 
                        WHERE id IN ($user_ids) AND type = 9
                        ORDER BY concat(firstname,' ',lastname) ASC");
                        
                        // Check if there are any team members
                        if($members && $members->num_rows > 0): 
                            while($row=$members->fetch_assoc()):
                    ?>
                    <li>
                        <img src="assets/uploads/<?php echo $row['avatar'] ?>" alt="User Image">
                        <a class="users-list-name" href="javascript:void(0)"><?php echo ucwords($row['name']) ?></a>
                    </li>
                    <?php 
                            endwhile;
                        else:
                    ?>
                        <small><i>No Team Members Assigned</i></small>
                    <?php
                        endif;
                    else:
                    ?>
                        <small><i>No Team Members Assigned</i></small>
                    <?php
                    endif;
                    ?>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Task List section -->
    <?php if(!$is_review_mode): ?>
    <div class="col-md-8">
        <div class="card card-outline card-primary">
            <div class="card-header">
                <span><b>Task List:</b></span>
                <?php if(userCanManageTasks($_SESSION['login_type']) && $can_add_tasks): ?>
                <div class="card-tools">
                    <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_task"><i class="fa fa-plus"></i> New Task</button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                <table class="table table-condensed m-0 table-hover">
                    <colgroup>
                        <col width="5%">
                        <col width="25%">
                        <col width="30%">
                        <col width="15%">
                        <col width="15%">
                    </colgroup>
                    <thead>
                        <th>#</th>
                        <th>Task</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $tasks = $conn->query("SELECT * FROM task_list where project_id = {$id} order by task asc");
                        while($row=$tasks->fetch_assoc()):
                            $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
                            unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                            $desc = strtr(html_entity_decode($row['description']),$trans);
                            $desc=str_replace(array("<li>","</li>"), array("",", "), $desc);
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++ ?></td>
                                <td class=""><b><?php echo ucwords($row['task']) ?></b></td>
                                <td class=""><p class="truncate"><?php echo strip_tags($desc) ?></p></td>
                                <td>
                                    <?php 
                                    if($row['status'] == 1){
                                        echo "<span class='badge badge-secondary'>Pending</span>";
                                    }elseif($row['status'] == 2){
                                        echo "<span class='badge badge-primary'>On-Progress</span>";
                                    }elseif($row['status'] == 3){
                                        echo "<span class='badge badge-success'>Done</span>";
                                    }
                                    ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle" data-toggle="dropdown" aria-expanded="true">
                                    Action
                                    </button>
                                    <div class="dropdown-menu" style="">
                                    <a class="dropdown-item view_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['task'] ?>">View</a>
                                    <div class="dropdown-divider"></div>
                                    <?php if(userCanManageTasks($_SESSION['login_type']) && $can_add_tasks): ?>
                                    <a class="dropdown-item edit_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['task'] ?>">Edit</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item delete_task" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                                    <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                        endwhile;
                        ?>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Progress/Activity Section - Only shown in full view mode -->
<?php if(!$is_review_mode): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <b>Members Progress/Activity</b>
                <?php if($can_add_tasks): ?>
                <div class="card-tools">
                    <button class="btn btn-primary bg-gradient-primary btn-sm" type="button" id="new_productivity"><i class="fa fa-plus"></i> New Productivity</button>
                </div>
                <?php endif; ?>
            </div>
            <div class="card-body d-flex flex-column">
            <?php 
                $progress = $conn->query("SELECT p.*,concat(u.firstname,' ',u.lastname) as uname,u.avatar,t.task FROM user_productivity p inner join users u on u.id = p.user_id inner join task_list t on t.id = p.task_id where p.project_id = $id order by unix_timestamp(p.date_created) desc ");
                $progress_counter = 0;
                while($row = $progress->fetch_assoc()):
                    $progress_counter++;
                ?>
                    <div class="post">
                        <div class="user-block">
                            <?php if($_SESSION['login_id'] == $row['user_id'] && $can_add_tasks): ?>
                            <span class="btn-group dropleft float-right">
                            <span class="btndropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu">
                                <a class="dropdown-item manage_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"  data-task="<?php echo $row['task'] ?>">Edit</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item delete_progress" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">Delete</a>
                            </div>
                            </span>
                            <?php endif; ?>
                            <img class="img-circle img-bordered-sm" src="assets/uploads/<?php echo $row['avatar'] ?>" alt="user image">
                            <span class="username">
                            <a href="#"><?php echo ucwords($row['uname']) ?>[ <?php echo ucwords($row['task']) ?> ]</a>
                            </span>
                            <span class="description">
                                <span class="fa fa-calendar-day"></span>
                                <span><b><?php echo date('M d, Y',strtotime($row['date'])) ?></b></span>
                                <span class="fa fa-user-clock"></span>
                                <span>Time: <b><?php echo date('h:i A',strtotime($row['date_created'])) ?></b></span>
                            </span>
                        </div>
                        <!-- /.user-block -->
                        <div class="progress-content">
                            <?php 
                            // Process the comment to extract images and make them clickable
                            $comment = html_entity_decode($row['comment']);
                            
                            // Use DOMDocument to parse and modify images
                            $dom = new DOMDocument();
                            libxml_use_internal_errors(true);
                            $dom->loadHTML('<?xml encoding="UTF-8">' . $comment, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
                            libxml_clear_errors();
                            
                            $images = $dom->getElementsByTagName('img');
                            $progress_images = array();
                            
                            foreach ($images as $img) {
                                $src = $img->getAttribute('src');
                                $alt = $img->getAttribute('alt') ?: 'Progress Image';
                                
                                // Add to progress images array for JavaScript
                                $progress_images[] = array(
                                    'src' => $src,
                                    'name' => $alt,
                                    'source' => 'progress'
                                );
                                
                                // Add click handler and styling to the image
                                $img->setAttribute('class', 'progress-image clickable-image');
                                $img->setAttribute('onclick', 'openImagePreview(' . (count($progress_images) - 1) . ', \'progress_' . $progress_counter . '\')');
                                $img->setAttribute('data-progress-id', $progress_counter);
                            }
                            
                            echo $dom->saveHTML();
                            ?>
                            
                            <!-- Store progress images for JavaScript -->
                            <?php if (!empty($progress_images)): ?>
                            <script>
                            window.progressImages_<?php echo $progress_counter ?> = <?php echo json_encode($progress_images); ?>;
                            </script>
                            <?php endif; ?>
                        </div>
                        <p>
                            <!-- <a href="#" class="link-black text-sm"><i class="fas fa-link mr-1"></i> Demo File 1 v2</a> -->
                        </p>
                    </div>
                    <div class="post clearfix"></div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

</div>

<?php if ($is_manager && $status == 1): ?>
<!-- Accept/Decline Actions for Project Managers -->
<div class="card-footer border-top border-info">
    <div class="d-flex w-100 justify-content-center align-items-center">
        <button class="btn btn-success mx-2" onclick="updateProjectStatus(<?= $id ?>, 'accepted')">Accept</button>
        <button class="btn btn-danger mx-2" onclick="updateProjectStatus(<?= $id ?>, 'declined')">Decline</button>
    </div>
</div>
<?php endif; ?>

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
    // Image Preview Variables
    let currentImageIndex = 0;
    let imageGallery = [];
    let currentGalleryType = '';

    // Enhanced Image Preview Functions
    function openImagePreview(index, gallery) {
        currentImageIndex = index;
        currentGalleryType = gallery;
        
        if (gallery === 'images' && window.projectImages) {
            imageGallery = window.projectImages;
        } else if (gallery.startsWith('progress_')) {
            // Handle progress images
            const progressId = gallery.split('_')[1];
            const progressImagesVar = 'progressImages_' + progressId;
            if (window[progressImagesVar]) {
                imageGallery = window[progressImagesVar];
            }
        }
        
        if (imageGallery.length > 0) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const imageInfo = document.getElementById('imageInfo');
            
            modalImage.src = imageGallery[currentImageIndex].src;
            imageInfo.textContent = imageGallery[currentImageIndex].name + 
                ' (' + (imageGallery[currentImageIndex].source === 'project' ? 'Project File' : 'Progress Image') + ')';
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
            
            // Update navigation arrows visibility
            updateNavigationArrows();
        }
    }

    function closeImagePreview() {
        const modal = document.getElementById('imageModal');
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore scrolling
        
        // Reset variables
        imageGallery = [];
        currentGalleryType = '';
    }

    function navigateImage(direction) {
        if (imageGallery.length === 0) return;
        
        currentImageIndex += direction;
        
        if (currentImageIndex >= imageGallery.length) {
            currentImageIndex = 0;
        } else if (currentImageIndex < 0) {
            currentImageIndex = imageGallery.length - 1;
        }
        
        const modalImage = document.getElementById('modalImage');
        const imageInfo = document.getElementById('imageInfo');
        
        modalImage.src = imageGallery[currentImageIndex].src;
        imageInfo.textContent = imageGallery[currentImageIndex].name + 
            ' (' + (imageGallery[currentImageIndex].source === 'project' ? 'Project File' : 'Progress Image') + ')';
        
        updateNavigationArrows();
    }

    function updateNavigationArrows() {
        const prevArrow = document.querySelector('.nav-arrow.prev');
        const nextArrow = document.querySelector('.nav-arrow.next');
        
        if (imageGallery.length <= 1) {
            prevArrow.style.display = 'none';
            nextArrow.style.display = 'none';
        } else {
            prevArrow.style.display = 'block';
            nextArrow.style.display = 'block';
        }
    }

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('imageModal');
        if (modal.style.display === 'block') {
            switch(e.key) {
                case 'Escape':
                    closeImagePreview();
                    break;
                case 'ArrowLeft':
                    navigateImage(-1);
                    break;
                case 'ArrowRight':
                    navigateImage(1);
                    break;
            }
        }
    });

    // Close modal when clicking outside the image
    document.getElementById('imageModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImagePreview();
        }
    });

    // Description toggle functionality
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

    // Scroll to top button functionality
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

    <?php if(!$is_review_mode): ?>
    // Task management handlers - only added in full view mode
    $('#new_task').click(function(){
        uni_modal("New Task For <?php echo ucwords($name) ?>","manage_task.php?pid=<?php echo $id ?>","mid-large")
    });
    
    $('.edit_task').click(function(){
        uni_modal("Edit Task: "+$(this).attr('data-task'),"manage_task.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),"mid-large")
    });
    
    $('.view_task').click(function(){
        uni_modal("Task Details","view_task.php?id="+$(this).attr('data-id'),"mid-large")
    });
    
    $('#new_productivity').click(function(){
        uni_modal("<i class='fa fa-plus'></i> New Progress","manage_progress.php?pid=<?php echo $id ?>",'large')
    });
    
    $('.manage_progress').click(function(){
        uni_modal("<i class='fa fa-edit'></i> Edit Progress","manage_progress.php?pid=<?php echo $id ?>&id="+$(this).attr('data-id'),'large')
    });
    
    $('.delete_progress').click(function(){
        _conf("Are you sure to delete this progress?","delete_progress",[$(this).attr('data-id')])
    });
    
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
    <?php endif; ?>

    // Project status update for project managers
    function updateProjectStatus(projectId, action) {
    start_load();
    if (action === 'accepted') {
        // Accept project - set to 'On-Progress' status
        $.ajax({
            url: 'ajax.php?action=update_project_status',
            method: 'POST',
            data: { id: projectId, status: 2 }, // 2='On-Progress'
            success: function(resp) {
                if(resp == 1) {
                    alert_toast('Project accepted', 'success');
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
    } else if (action === 'declined') {
        // Decline project - delete it
        Swal.fire({
            title: 'Confirm Project Deletion',
            text: 'Declining this project will permanently delete it. Are you sure?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'ajax.php?action=delete_project',
                    method: 'POST',
                    data: { id: projectId },
                    success: function(resp) {
                        if(resp == 1) {
                            alert_toast('Project declined and deleted', 'success');
                            setTimeout(() => location.href = 'index.php?page=project_list', 1500);
                        } else {
                            alert_toast('Error deleting project', 'error');
                            end_load();
                        }
                    },
                    error: function() {
                        alert_toast('An error occurred', 'error');
                        end_load();
                    }
                });
            } else {
                end_load();
            }
        });
    }
}
$('.delete_task').click(function(){
_conf("Are you sure to delete this task?","delete_task",[$(this).attr('data-id')])
});

function delete_task($id){
    start_load()
    $.ajax({
        url:'ajax.php?action=delete_task',
        method:'POST',
        data:{id:$id},
        success:function(resp){
            if(resp==1){
                alert_toast("Task successfully deleted",'success')
                setTimeout(function(){
                    location.reload()
                },1500)
            }
        }
    })
}
    // File deletion handling
    $(document).ready(function(){
        $('.delete-file-btn').click(function(){
            var fileId = $(this).data('id');
            var source = $(this).data('source');
            var fileName = $(this).data('name');
            
            Swal.fire({
                title: 'Delete File',
                text: 'Are you sure you want to delete "' + fileName + '"?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'ajax.php?action=' + (source === 'project' ? 'delete_file' : 'delete_progress_file'),
                        method: 'POST',
                        data: {id: fileId},
                        success: function(resp){
                            if(resp == 1){
                                Swal.fire('Deleted!', 'File has been deleted.', 'success');
                                $('[data-id="'+fileId+'"][data-source="'+source+'"]').closest('.file-attachment').remove();
                                
                                // Update file count display if needed
                                if($('.file-attachment').length === 0){
                                    $('dt:contains("Files")').next('dd').html('<span>No files uploaded.</span>');
                                }
                            } else if(resp == 0) {
                                Swal.fire('Error!', 'You do not have permission to delete this file.', 'error');
                            } else {
                                Swal.fire('Error!', 'Failed to delete file.', 'error');
                            }
                        },
                        error: function(){
                            Swal.fire('Error!', 'An error occurred while deleting the file.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>

<style>
    /* File attachment styling */
    .file-attachment {
        display: flex;
        align-items: center;
        margin: 10px 0;
        padding: 10px;
        background-color: #f9f9f9;
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        transition: all 0.3s ease;
    }

    .file-attachment:hover {
        background-color: #f0f0f0;
        border-color: #ccc;
    }

    .file-attachment i {
        font-size: 24px;
        margin-right: 10px;
        color: #555;
    }

    .file-attachment .fa-file-pdf {
        color: #dc3545;
    }

    .file-attachment .fa-file-word {
        color: #2b579a;
    }

    .file-attachment .fa-file-excel {
        color: #217346;
    }

    .file-attachment .fa-file-powerpoint {
        color: #d24726;
    }

    .file-attachment .fa-file-image {
        color: #20c997;
    }

    .file-attachment a {
        color: #333;
        text-decoration: none;
        font-weight: 500;
    }

    .file-attachment a:hover {
        text-decoration: underline;
    }

    /* Image styling in description */
    .project-description img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin: 10px 0;
    }

    .file-attachment {
        padding: 10px;
        margin: 5px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .file-attachment:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }

    .file-thumbnail {
        max-width: 150px;
        max-height: 150px;
        border-radius: 3px;
    }

    .file-download {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #333;
        text-decoration: none;
    }

    .file-name {
        font-size: 0.9em;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .delete-file-btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        line-height: 1;
    }

    /* Progress Images Styles */
    .progress-image {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin: 10px 10px 10px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .progress-image:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        filter: brightness(1.1);
    }

    .progress-images-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
    }

    .progress-content img {
        max-width: 200px;
        max-height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin: 10px 10px 10px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .progress-content img:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(0,0,0,0.3);
        filter: brightness(1.1);
    }

    .edit-project-btn {
        position: absolute;
        top: 15px;
        right: 20px;
        z-index: 999;
    }
    
    .edit-project-btn .btn {
        background-color: #0d50f9 !important;
        border-color: #0d50f9 !important;
        color: white !important;
    }
</style>
</body>
</html>