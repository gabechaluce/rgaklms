<?php if(!isset($conn)){ include 'db_connect.php'; } ?>

<body>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-project">
			<input type="hidden" name="id" value="<?php echo isset($_GET['id']) ? $_GET['id'] : (isset($id) ? $id : ''); ?>">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Name</label>
							<input type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Status</label>
							<select name="status" id="status" class="custom-select custom-select-sm">
								<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Pending</option>
								<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>To be reviewed</option>
								<option value="2" <?php echo isset($status) && $status == 2 ? 'selected' : '' ?>>On-Progress</option>
								<option value="3" <?php echo isset($status) && $status == 3 ? 'selected' : '' ?>>On-Hold</option>
								<option value="5" <?php echo isset($status) && $status == 5 ? 'selected' : '' ?>>Done</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Start Date</label>
							<input type="date" class="form-control form-control-sm" autocomplete="off" name="start_date" value="<?php echo isset($start_date) ? date("Y-m-d",strtotime($start_date)) : '' ?>">
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">End Date</label>
							<input type="date" class="form-control form-control-sm" autocomplete="off" name="end_date" value="<?php echo isset($end_date) ? date("Y-m-d",strtotime($end_date)) : '' ?>">
						</div>
					</div>
				</div>
				<div class="row">
                <?php if(in_array($_SESSION['login_type'], [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14])): ?>
                    <div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Project Manager</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="manager_id[]">
								<option></option>
								<?php 
								$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 14 order by concat(firstname,' ',lastname) asc ");
								while($row= $employees->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($manager_id) && in_array($row['id'],explode(',',$manager_id)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
					<?php else: ?>
					<input type="hidden" name="manager_id" value="<?php echo $_SESSION['login_id'] ?>">
					<?php endif; ?>
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Project Team Members</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="user_ids[]">
								<option></option>
								<?php 
								$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 3 order by concat(firstname,' ',lastname) asc ");
								while($row= $employees->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'],explode(',',$user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Project Coordinator</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="user_ids[]">
								<option></option>
								<?php 
								$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 2 order by concat(firstname,' ',lastname) asc ");
								while($row= $employees->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'],explode(',',$user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
                    <div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Project Estimator</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="user_ids[]">
								<option></option>
								<?php 
								$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 6 order by concat(firstname,' ',lastname) asc ");
								while($row= $employees->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'],explode(',',$user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
                    <div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Project Designer</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="user_ids[]">
								<option></option>
								<?php 
								$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 4 order by concat(firstname,' ',lastname) asc ");
								while($row= $employees->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'],explode(',',$user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
                    <div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Inventory Coordinator</label>
							<select class="form-control form-control-sm select2" multiple="multiple" name="user_ids[]">
								<option></option>
								<?php 
								$employees = $conn->query("SELECT *,concat(firstname,' ',lastname) as name FROM users where type = 5 order by concat(firstname,' ',lastname) asc ");
								while($row= $employees->fetch_assoc()):
								?>
								<option value="<?php echo $row['id'] ?>" <?php echo isset($user_ids) && in_array($row['id'],explode(',',$user_ids)) ? "selected" : '' ?>><?php echo ucwords($row['name']) ?></option>
								<?php endwhile; ?>
							</select>
						</div>
					</div>
                </div>
</div>
				<div class="row">
					<div class="col-md-10">
						<div class="form-group">
							<label for="" class="control-label">Description</label>
							<textarea name="description" id="" cols="30" rows="10" class="summernote form-control">
								<?php echo isset($description) ? $description : '' ?>
							</textarea>
						</div>
					</div>
				</div>
				<div id="file-preview-container" class="mt-3">
    <h6><B>Uploaded Files</B></h6>
    <div id="file-preview-box" class="border p-2 bg-light">
	<?php
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];
    $files = $conn->query("SELECT * FROM uploaded_files WHERE project_id = $project_id");
    while($file = $files->fetch_assoc()):
        $ext = strtolower(pathinfo($file['file_path'], PATHINFO_EXTENSION));
        $fileIcon = 'fas fa-file text-secondary';
        
        if(in_array($ext, ['pdf'])) $fileIcon = 'fas fa-file-pdf text-danger';
        elseif(in_array($ext, ['doc','docx'])) $fileIcon = 'fas fa-file-word text-primary';
        elseif(in_array($ext, ['xls','xlsx'])) $fileIcon = 'fas fa-file-excel text-success';
        elseif(in_array($ext, ['ppt','pptx'])) $fileIcon = 'fas fa-file-powerpoint text-warning';
        elseif(in_array($ext, ['jpg','jpeg','png','gif','webp'])) $fileIcon = 'fas fa-file-image text-info';

        $filepath = 'uploads/' . $file['file_path'];
?>
    <div class="uploaded-file position-relative d-inline-block" data-id="<?php echo $file['id']; ?>">
        <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px">×</span>
        <?php if(in_array($ext, ['jpg','jpeg','png','gif','webp'])): ?>
            <img src="<?php echo $filepath ?>" class="img-thumbnail me-2" style="max-width: 80px; max-height: 80px;">
        <?php else: ?>
            <div class="d-flex align-items-center p-2">
                <i class="<?php echo $fileIcon ?> me-2" style="font-size: 24px;"></i>
                <a href="<?php echo $filepath ?>" target="_blank"><?php echo basename($file['original_name']); ?></a>
            </div>
        <?php endif; ?>
    </div>
<?php endwhile; } ?>
    </div>
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
	$(document).ready(function(){
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
				['view', ['undo', 'redo', 'fullscreen', 'help']]
			],
			placeholder: "Input Equipment will be used in the Project and Description of the Project...",
			callbacks: {
				onImageUpload: function(files) {
					uploadFile(files[0], 'image', this);
				},
				onMediaDelete: function(target) {
					// Optional: Handle file deletion from server
				}
			},
		});

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

    function uploadFile(file, fileType, editor) {
        var formData = new FormData();
        formData.append('file', file);
        formData.append('type', fileType);
        var projectId = $('input[name="id"]').val();
        formData.append('project_id', projectId || 0);
        
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
                    var imageUrl = response.url + '?t=' + new Date().getTime();
                    
                    if (fileType === 'image') {
                        $(editor).summernote('insertImage', imageUrl);
                        
                        filePreviewHtml = `<div class="uploaded-file position-relative d-inline-block" data-id="${response.id}">
                            <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px">×</span>
                            <img src="${imageUrl}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                        </div>`;
                    } else {
                        var fileIcon = getFileIcon(response.extension);
                        filePreviewHtml = `<div class="uploaded-file position-relative d-inline-block" data-id="${response.id}">
                            <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px">×</span>
                            <div class="d-flex align-items-center p-2">
                                <i class="${fileIcon}" style="font-size: 24px; margin-right: 10px;"></i>
                                <a href="${response.url}" target="_blank">${response.originalName}</a>
                            </div>
                        </div>`;
                    }
                    
                    $('#file-preview-box').append(filePreviewHtml);
                }
            },
            error: function() {
                alert('Error uploading file.');
            }
        });
    }

    // Add event listener for remove button - placed OUTSIDE the uploadFile function
    $('#file-preview-box').on('click', '.remove-file-btn', function() {
        var $fileDiv = $(this).closest('.uploaded-file');
        var fileId = $fileDiv.data('id');
        
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

    $('.select2').select2({
        placeholder: "Please select here",
        width: "100%"
    });

    // Drag and drop file handling
    var $uploadArea = $('#file-preview-container');
    var $fileInput = $('<input type="file" id="fileInput" multiple style="display: none;">');
    $('body').append($fileInput);

    $uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('dragover');
    }).on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
    }).on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('dragover');
        var files = e.originalEvent.dataTransfer.files;
        handleFiles(files);
    });

    function handleFiles(files) {
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var ext = file.name.split('.').pop().toLowerCase();
            var fileType = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext) ? 'image' : 'document';
            uploadFile(file, fileType, $('.summernote'));
        }
    }
});

$('#manage-project').submit(function(e) {
    e.preventDefault();
    start_load();
    let formData = new FormData($(this)[0]);
    $.ajax({
        url: 'ajax.php?action=save_project',
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        success: function(resp) {
            if (resp == 1) {
                alert_toast('Data successfully saved', "success");
                setTimeout(function() {
                    location.href = 'index.php?page=project_list';
                }, 1500);
            } else {
                alert_toast('Error: ' + resp, "danger");
            }
        },
        error: function(xhr, status, error) {
            alert_toast('An error occurred: ' + error, "danger");
            console.error(xhr.responseText);
        }
    });
});
</script>
<style>
	body, .wrapper, .content-wrapper {
		background-color:#f4f1ed !important;
	}
	.uploaded-file {
		display: flex;
		align-items: center;
		border: 1px solid #ddd;
		padding: 5px;
		background: #fff;
		border-radius: 5px;
		margin: 5px;
	}
	.dragover {
		border-color: #007bff !important;
		background-color: #e9f3ff !important;
	}
	.uploaded-file {
    position: relative;
    margin: 10px;
    transition: opacity 0.3s;
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

</style>
</body>