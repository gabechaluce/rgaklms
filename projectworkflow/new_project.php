<?php if(!isset($conn)){ include 'db_connect.php'; } ?>
<body>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-project">

				<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>" REQUIRED>
				<!-- Modified form fields section -->
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="" class="control-label">Project Name</label>
            <input type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>" REQUIRED>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="form-group">
            <label for="" class="control-label">Location</label>
         <input type="text" class="form-control form-control-sm" name="location" value="<?php echo isset($location) ? $location : '' ?>" REQUIRED placeholder="Project location">
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <?php 
            // Check if current user can edit project cost (Estimator or Designer)
            $user_type = isset($_SESSION['login_type']) ? $_SESSION['login_type'] : '';
            $can_edit_cost = ($user_type == 5 || $user_type == 3); // 5=Estimator, 3=Designer
            $cost_disabled = $can_edit_cost ? '' : 'disabled';
            $cost_class = $can_edit_cost ? 'form-control form-control-sm' : 'form-control form-control-sm disabled-select';
            ?>
            <label for="" class="control-label">Project Cost
                <?php if (!$can_edit_cost): ?>
                    <small class="text-muted">(Only Estimators and Designers can edit)</small>
                <?php endif; ?>
            </label>
            <input type="number" step="0.01" class="<?php echo $cost_class; ?>" name="project_cost" value="<?php echo isset($project_cost) ? $project_cost : '0.00' ?>" <?php echo $cost_disabled; ?>>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="">Status</label>
            <?php if(isset($id) && $id != ''): ?>
                <select name="status" id="status" class="custom-select custom-select-sm" REQUIRED>
                    <option value="" disabled selected>Select Status</option>
                    <option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>Pending</option>
                    <option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>To be reviewed</option>
                    <option value="2" <?= isset($status) && $status == 2 ? 'selected' : '' ?>>On-Progress</option>
                    <option value="3" <?= isset($status) && $status == 3 ? 'selected' : '' ?>>On-Hold</option>
                    <option value="5" <?= isset($status) && $status == 5 ? 'selected' : '' ?>>Done</option>
                </select>
            <?php else: ?>
                <input type="hidden" name="status" value="1">
                <input type="text" class="form-control form-control-sm" value="To be reviewed" readonly>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-md-6">
        <!-- Removed bill type field as requested -->
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label for="" class="control-label">Start Date</label>
            <input type="date" class="form-control form-control-sm" autocomplete="off" name="start_date" value="<?php echo isset($start_date) ? date("Y-m-d",strtotime($start_date)) : '' ?>" REQUIRED>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label for="" class="control-label">End Date</label>
            <input type="date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="<?php echo isset($end_date) ? date("Y-m-d",strtotime($end_date)) : '' ?>" REQUIRED>
        </div>
    </div>
</div>
<div class="row">
    <!-- Project Manager field - now visible for all user types -->
    <div class="col-md-6">
        <div class="form-group">
            <label for="" class="control-label">Project Manager</label>
            <select class="form-control form-control-sm select2" multiple="multiple" name="manager_id[]" REQUIRED>
                <option></option>
                <?php 
                // Query all users with type 7 (Project Manager)
                $employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 7 order by concat(firstname,' ',lastname) asc ");
                while($row= $employees->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($manager_id) && in_array($row['id'],explode(',',$manager_id)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <?php 
            // Get current user type
            $user_type = isset($_SESSION['login_type']) ? $_SESSION['login_type'] : '';
            $is_project_manager = ($user_type == 7); // type 7 is Project Manager
            $disabled_attr = $is_project_manager ? '' : 'disabled';
            $select_class = $is_project_manager ? 'form-control form-control-sm select2' : 'form-control form-control-sm select2 disabled-select';
            ?>
            <label for="" class="control-label">Project Team Members 
                <?php if (!$is_project_manager): ?>
                    <small class="text-muted">(Only Project Managers can select)</small>
                <?php endif; ?>
            </label>
            <select class="<?php echo $select_class; ?>" multiple="multiple" name="user_ids[]" <?php echo $disabled_attr; ?>>
                <option></option>
                <?php 
                $employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 9 order by concat(firstname,' ',lastname) asc ");
                while($row= $employees->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'],explode(',',$user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>
<div class="row">
<div class="col-md-6">
        <div class="form-group">
            <?php 
            $disabled_attr = $is_project_manager ? '' : 'disabled';
            $select_class = $is_project_manager ? 'form-control form-control-sm select2' : 'form-control form-control-sm select2 disabled-select';
            ?>
            <label for="" class="control-label">Project Coordinator
                <?php if (!$is_project_manager): ?>
                    <small class="text-muted">(Only Project Managers can select)</small>
                <?php endif; ?>
            </label>
            <select class="<?php echo $select_class; ?>" multiple="multiple" name="coordinator_ids[]" <?php echo $disabled_attr; ?>>
                <option></option>
                <?php 
                $employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 2 order by concat(firstname,' ',lastname) asc ");
                while($row= $employees->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($coordinator_ids) && in_array($row['id'],explode(',',$coordinator_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <?php 
            $disabled_attr = $is_project_manager ? '' : 'disabled';
            $select_class = $is_project_manager ? 'form-control form-control-sm select2' : 'form-control form-control-sm select2 disabled-select';
            ?>
            <label for="" class="control-label">Designer
                <?php if (!$is_project_manager): ?>
                    <small class="text-muted">(Only Project Managers can select)</small>
                <?php endif; ?>
            </label>
            <select class="<?php echo $select_class; ?>" multiple="multiple" name="designer_ids[]" <?php echo $disabled_attr; ?>>
                <option></option>
                <?php 
                $employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 3 order by concat(firstname,' ',lastname) asc ");
                while($row= $employees->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($designer_ids) && in_array($row['id'],explode(',',$designer_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <?php 
            $disabled_attr = $is_project_manager ? '' : 'disabled';
            $select_class = $is_project_manager ? 'form-control form-control-sm select2' : 'form-control form-control-sm select2 disabled-select';
            ?>
            <label for="" class="control-label">Project Estimator
                <?php if (!$is_project_manager): ?>
                    <small class="text-muted">(Only Project Managers can select)</small>
                <?php endif; ?>
            </label>
            <select class="<?php echo $select_class; ?>" multiple="multiple" name="estimator_ids[]" <?php echo $disabled_attr; ?>>
                <option></option>
                <?php 
                $employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 5 order by concat(firstname,' ',lastname) asc ");
                while($row= $employees->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($estimator_ids) && in_array($row['id'],explode(',',$estimator_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <?php 
            $disabled_attr = $is_project_manager ? '' : 'disabled';
            $select_class = $is_project_manager ? 'form-control form-control-sm select2' : 'form-control form-control-sm select2 disabled-select';
            ?>
            <label for="" class="control-label">Inventory Coordinator
                <?php if (!$is_project_manager): ?>
                    <small class="text-muted">(Only Project Managers can select)</small>
                <?php endif; ?>
            </label>
            <select class="<?php echo $select_class; ?>" multiple="multiple" name="inventory_ids[]" <?php echo $disabled_attr; ?>>
                <option></option>
                <?php 
                $employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 4 order by concat(firstname,' ',lastname) asc ");
                while($row= $employees->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($inventory_ids) && in_array($row['id'],explode(',',$inventory_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="form-group">
			<label for="" class="control-label">Project Description & Equipment List</label>
			<textarea name="description" cols="30" rows="10" class="summernote form-control" REQUIRED>
<?php echo isset($description) ? trim($description) : '' ?></textarea>
		</div>
	</div>
</div>
<div id="file-preview-container" class="mt-3">
<h6><B>Uploaded Files</B></h6>
    <div id="file-preview-box" class="border p-2 bg-light"></div>
</div>

			</form>
		</div>
		<div class="card-footer border-top border-info">
			<div class="d-flex w-100 justify-content-center align-items-center">
				<button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-project">Save</button>
				<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=project_list'">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
            ['fontname', ['fontname']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ol', 'ul', 'paragraph', 'height']],
            ['table', ['table']],
            ['insert', ['picture', 'link', 'file']], 
            ['view', ['undo', 'redo', 'fullscreen', 'help']],
        ],
        placeholder: "Input Equipment will be used in the Project and Description of the Project...",
        callbacks: {
            onImageUpload: function(files) {
                uploadFile(files[0], 'image', this);
            },
            onMediaDelete: function(target) {
                // Optional: Add code to delete file from server when removed from editor
            }
        },
        buttons: {
            file: function(context) {
                var ui = $.summernote.ui;
                var button = ui.button({
                    contents: '<i class="fa fa-paperclip"></i>',
                    tooltip: 'Attach File',
                    click: function() {
                        var fileInput = $('<input type="file">')
                            .attr('accept', '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.dwg')
                            .css('display', 'none');
                        
                        fileInput.click();
                        
                        fileInput.on('change', function() {
                            if (fileInput[0].files.length > 0) {
                                var file = fileInput[0].files[0];
                                uploadFile(file, 'document', context);
                            }
                        });
                    }
                });
                return button.render();
            }
        }
    });

// Helper function to determine file icon based on extension
function getFileIcon(extension) {
    const fileIcons = {
        'pdf': 'fas fa-file-pdf text-danger',
        'doc': 'fas fa-file-word text-primary',
        'docx': 'fas fa-file-word text-primary',
        'xls': 'fas fa-file-excel text-success',
        'xlsx': 'fas fa-file-excel text-success',
        'ppt': 'fas fa-file-powerpoint text-warning',
        'pptx': 'fas fa-file-powerpoint text-warning',
        'jpg': 'fas fa-file-image text-info',
        'jpeg': 'fas fa-file-image text-info',
        'png': 'fas fa-file-image text-info',
        'gif': 'fas fa-file-image text-info',
        'webp': 'fas fa-file-image text-info',
        'default': 'fas fa-file text-secondary'
    };
    return fileIcons[extension.toLowerCase()] || fileIcons['default'];
}

// Function to handle file uploads (both images and documents)
function uploadFile(file, fileType, editor) {
    var formData = new FormData();
    formData.append('file', file);
    formData.append('type', fileType);
    var projectId = $('input[name="id"]').val(); // Get project ID from hidden input
    formData.append('project_id', projectId || 0); // Use 0 if new project
    $.ajax({
        url: 'ajax.php?action=upload_file',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                var filePreviewHtml = '';
                
                if (fileType === 'image') {
                    var imageUrl = response.url + '?t=' + new Date().getTime();
                    $(editor).summernote('insertImage', imageUrl);
                    
                    filePreviewHtml = `<div class="uploaded-file">
                        <img src="${imageUrl}" class="img-thumbnail" style="max-width: 100px; max-height: 100px; margin-right: 10px;">
                    </div>`;
                } else {
                    var fileIcon = getFileIcon(response.extension);
                    filePreviewHtml = `<div class="uploaded-file d-flex align-items-center">
                        <i class="${fileIcon}" style="font-size: 24px; margin-right: 10px;"></i>
                        <a href="${response.url}" target="_blank">${response.originalName}</a>
                    </div>`;
                }
                // Modify the filePreviewHtml creation
if (fileType === 'image') {
    filePreviewHtml = `<div class="uploaded-file position-relative d-inline-block" data-id="${response.id}">
        <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px">×</span>
        <img src="${imageUrl}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
    </div>`;
} else {
    filePreviewHtml = `<div class="uploaded-file position-relative d-inline-block" data-id="${response.id}">
        <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px">×</span>
        <div class="d-flex align-items-center p-2">
            <i class="${fileIcon}" style="font-size: 24px; margin-right: 10px;"></i>
            <a href="${response.url}" target="_blank">${response.originalName}</a>
        </div>
    </div>`;
}
                
                $('#file-preview-box').append(filePreviewHtml);
            } else {
                alert('File upload failed: ' + response.message);
            }
        },
        error: function() {
            alert('Error uploading file.');
        }
    });
}

// Add this after the filePreviewHtml is appended
$('#file-preview-box').on('click', '.remove-file-btn', function() {
    var $fileDiv = $(this).closest('.uploaded-file');
    var fileId = $fileDiv.data('id');
    
    // Remove the confirmation and directly delete the file
    $.ajax({
        url: 'ajax.php?action=delete_file',
        method: 'POST',
        data: { id: fileId },
        success: function(resp) {
            if (resp == 1) {
                $fileDiv.remove();
                alert_toast('File successfully deleted', "success");
            } else {
                alert_toast('Failed to delete file', "error");
            }
        },
        error: function() {
            alert_toast('Error deleting file', "error");
        }
    });
});

// Helper function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Document ready function to set up drag and drop upload
$(document).ready(function() {
    var $uploadArea = $('#uploadFile');
    var $fileInput = $('#fileInput');
    var $editor = $('.summernote'); // Adjust selector as needed for your editor

    // Click to select files
    $uploadArea.on('click', function() {
        $fileInput.click();
    });

    // Prevent default drag behaviors
    $uploadArea.on('dragenter dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    $uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    // Handle file drop
    $uploadArea.on('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');

        var files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });

    // Handle file input change
    $fileInput.on('change', function(e) {
        var files = this.files;
        handleFiles(files);
    });

    // Process files
    function handleFiles(files) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var ext = file.name.split('.').pop().toLowerCase();
            
            // Determine file type based on extension
            var fileType = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext) ? 'image' : 'document';
            
            // Upload each file
            uploadFile(file, fileType, $editor);
        }
    }
});

// Initialize Select2 with conditional disabling
$('.select2').select2({
    placeholder: "Please select here",
    width: "100%"
});

// Disable select2 dropdowns for non-project managers
$('.select2.disabled-select').select2('destroy').select2({
    placeholder: "Only Project Managers can select",
    width: "100%",
    disabled: true
});

// Custom handling for description box focus/blur
const descriptionField = $('.note-editable');

descriptionField.on('focus', function () {
    if (descriptionField.text() === "Input Equipment will be used in the Project and Description of the Project...") {
        descriptionField.text('');
    }
});

descriptionField.on('blur', function () {
    if (descriptionField.text().trim() === '') {
        descriptionField.text("Input Equipment will be used in the Project and Description of the Project...");
    }
});

// Handle form submission
$('#manage-project').submit(function (e) {
    e.preventDefault();
    start_load();
    
    // Before submitting, temporarily enable disabled select fields to include their values
    $('.disabled-select').prop('disabled', false);
    
    $.ajax({
        url: 'ajax.php?action=save_project',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        success: function (resp) {
            if (resp == 1) {
                alert_toast('Data successfully saved', "success");
                setTimeout(function () {
                    location.reload();
                }, 1500);
            }
        },
        complete: function() {
            // Re-disable the select fields after submission
            $('.disabled-select').prop('disabled', true);
            end_load();
        }
    });
});

});
        
</script>
<style>

body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}

.upload-area {
    border: 2px dashed #ccc;
    border-radius: 5px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: border-color 0.3s;
    background-color: #f8f9fa;
}

.upload-area:hover, .upload-area.highlight {
    border-color: #007bff;
    background-color: #e9f3ff;
}

#filePreview {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.file-preview-item {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    display: flex;
    align-items: center;
}

.file-preview-item i {
    margin-right: 5px;
    font-size: 1.2em;
}
#file-preview-box {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.uploaded-file {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    padding: 5px;
    background: #fff;
    border-radius: 5px;
}

/* File attachment styling within description */
.project-description .file-attachment {
    display: flex;
    align-items: center;
    margin: 10px 0;
    padding: 10px;
    background-color: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 5px;
}

.project-description .file-attachment i {
    font-size: 24px;
    margin-right: 10px;
}

.project-description .fa-file-pdf {
    color: #dc3545;
}

.project-description .fa-file-word {
    color: #2b579a;
}

.project-description .fa-file-excel {
    color: #217346;
}

.project-description .fa-file-powerpoint {
    color: #d24726;
}

.project-description .fa-file-image {
    color: #20c997;
}

.project-description .file-attachment a {
    color: #333;
    text-decoration: none;
    font-weight: 500;
}

.project-description .file-attachment a:hover {
    text-decoration: underline;
}

.uploaded-file {
    position: relative;
    margin: 10px;
    transition: opacity 0.3s;
}

.remove-file-btn {
    display: none;
    z-index: 100;
    position: absolute;
    top: -5px;
    right: -5px;
    border-radius: 8px; /* Makes the button perfectly circular */
}

.file-icon {
    position: relative; /* Make the file icon a positioning context */
    border-radius: 8px; /* Curves the corners of the file icon */
}

.uploaded-file {
    position: relative; /* Keep this for fallback */
    border-radius: 10px; /* Curves the corners of the entire uploaded file element */
}

.uploaded-file:hover .remove-file-btn {
    display: block;
}

.uploaded-file:hover {
    opacity: 0.8;
}

/* New field styling */
.form-group label {
    font-weight: 600;
    color: #495057;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Enhanced input styling */
input[type="number"] {
    text-align: right;
}

/* Status badge styling */
.badge-status {
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    border-radius: 0.25rem;
}

/* Disabled select styling */
.disabled-select {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    cursor: not-allowed !important;
}

.select2-container--default .select2-selection--multiple.disabled-select,
.select2-container--default.select2-container--disabled .select2-selection--multiple {
    background-color: #f8f9fa !important;
    color: #6c757d !important;
    cursor: not-allowed !important;
    border-color: #dee2e6 !important;
}

.select2-container--default.select2-container--disabled .select2-selection__choice {
    background-color: #e9ecef !important;
    color: #6c757d !important;
}

/* Visual indicator for restricted fields */
.form-group label small.text-muted {
    font-style: italic;
    font-size: 0.75em;
}
</style>
</body>