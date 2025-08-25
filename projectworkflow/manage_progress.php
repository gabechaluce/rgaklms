<?php 
include 'db_connect.php';

// Check if project ID is set
if(!isset($_GET['pid']) || empty($_GET['pid'])) {
    die("Invalid project ID");
}

$project_id = intval($_GET['pid']);

// Check project status
$project_query = $conn->query("SELECT status FROM project_list WHERE id = $project_id");
if($project_query && $project_query->num_rows > 0) {
    $project = $project_query->fetch_assoc();
    $project_status = $project['status'];
    
    // If project is "To be reviewed" (status = 1) and user is not admin, prevent access
    if($project_status == 1 && $_SESSION['login_type'] != 1) {
        echo "<script>alert('You cannot add progress while the project is under review.'); window.location.href='index.php?page=view_project&id=$project_id';</script>";
        exit;
    }
}

// Continue with normal processing if project is not in review or user is admin
if(isset($_GET['id'])){
    $qry = $conn->query("SELECT * FROM user_productivity WHERE id = ".$_GET['id'])->fetch_array();
    foreach($qry as $k => $v){
        $$k = $v;
    }
}

// Get current date and time
$current_date = date("Y-m-d");
$current_time = date("H:i");

// Generate auto subject if editing
$auto_subject = "Progress update on " . date("M d, Y");
?>
<body>
<div class="container-fluid">
    <form action="ajax.php?action=save_progress" id="manage-progress" enctype="multipart/form-data" method="POST">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="project_id" value="<?php echo isset($_GET['pid']) ? $_GET['pid'] : '' ?>">
        
        <!-- Hidden fields for auto-generated values -->
        <input type="hidden" name="date" value="<?php echo isset($date) ? date("Y-m-d",strtotime($date)) : $current_date ?>">
        <input type="hidden" name="start_time" value="<?php echo isset($start_time) ? date("H:i",strtotime($start_time)) : $current_time ?>">
        <input type="hidden" name="end_time" value="<?php echo isset($end_time) ? date("H:i",strtotime($end_time)) : $current_time ?>">
        <input type="hidden" name="subject" value="<?php echo isset($subject) ? $subject : $auto_subject ?>">
        
        <div class="col-lg-12">
            <div class="row">
                <!-- LEFT COLUMN -->
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="">Project Task</label>
                        <select class="form-control form-control-sm select2" name="task_id" required>
                            <option></option>
                            <?php 
                            $tasks = $conn->query("SELECT * FROM task_list WHERE project_id = {$_GET['pid']} ORDER BY task ASC");
                            while($row = $tasks->fetch_assoc()):
                            ?>
                            <option value="<?php echo $row['id'] ?>" 
                                <?php echo isset($task_id) && $task_id == $row['id'] ? "selected" : '' ?>>
                                <?php echo ucwords($row['task']) ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
       
                    <!-- File Preview Area -->
                    <div id="file-preview-container" class="mt-3 border p-3 rounded">
                        <h6><b>Uploaded Files</b></h6>
                        <div id="file-preview-box" class="border p-2 bg-light min-height-100" style="min-height: 100px;">
                            
                            <?php
                            if (isset($id)) {
                                $progress_id = $id;
                                // Use the progress_files table instead of uploaded_files
                                $files = $conn->query("SELECT * FROM progress_files WHERE upload_id = $progress_id AND is_deleted = 0");                                
                                // Check if query was successful and has rows
                                if ($files && $files->num_rows > 0) {
                                    while($file = $files->fetch_assoc()):
                                        $ext = strtolower(pathinfo($file['file_path'], PATHINFO_EXTENSION));
                                        $fileIcon = 'fas fa-file text-secondary';
                                        
                                        if(in_array($ext, ['pdf'])) $fileIcon = 'fas fa-file-pdf text-danger';
                                        elseif(in_array($ext, ['doc','docx'])) $fileIcon = 'fas fa-file-word text-primary';
                                        elseif(in_array($ext, ['xls','xlsx'])) $fileIcon = 'fas fa-file-excel text-success';
                                        elseif(in_array($ext, ['ppt','pptx'])) $fileIcon = 'fas fa-file-powerpoint text-warning';
                                        elseif(in_array($ext, ['jpg','jpeg','png','gif','webp'])) $fileIcon = 'fas fa-file-image text-info';

                                        // Make sure the path is correct
                                        $filepath = $file['file_path'];
                                        
                                        // Check if file exists
                                        $fileExists = file_exists($filepath);
                            ?>
                                <div class="uploaded-file position-relative d-inline-block m-2" data-id="<?php echo $file['id']; ?>">
                                    <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px; z-index: 100;">×</span>
                                    <?php if(in_array($ext, ['jpg','jpeg','png','gif','webp']) && $fileExists): ?>
                                        <img src="<?php echo $file['url'] ?>" class="img-thumbnail me-2" style="max-width: 80px; max-height: 80px;">
                                    <?php else: ?>
                                        <div class="d-flex align-items-center p-2 border rounded">
                                            <i class="<?php echo $fileIcon ?> me-2" style="font-size: 24px;"></i>
                                            <a href="<?php echo $file['url'] ?>" target="_blank"><?php echo $file['original_name']; ?></a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php 
                                    endwhile;
                                } else {
                                    echo '<p class="text-muted small">No files uploaded yet. Click Here.</p>';
                                }
                            } else {
                                echo '<p class="text-muted small">No files uploaded yet. Click Here.</p>';
                            }
                            ?>
                        </div>
                        <!-- Add a visible drop zone message -->
                        <div class="text-center mt-2 text-muted">
                            <i class="fas fa-cloud-upload-alt"></i> Drop files or Click here to upload
                        </div>
                    </div>
                </div>
                
                <!-- RIGHT COLUMN -->
                <div class="col-md-7">
                    <div class="form-group">
                        <label for="">Comment/Progress Description</label>
                        <textarea name="comment" id="comment" cols="30" rows="10" 
                                  class="summernote form-control" required>
                            <?php echo isset($comment) ? $comment : '' ?>
                        </textarea>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
$(document).ready(function(){
    // Add this at the beginning of your $(document).ready function
// Update the loadProgressFiles function to also add files to the editor content if needed
function loadProgressFiles() {
    var progressId = $('input[name="id"]').val();
    if (progressId && parseInt(progressId) > 0) {
        $.ajax({
            url: 'ajax.php?action=get_progress_files',
            method: 'POST',
            data: { progress_id: progressId },
            dataType: 'json',
            success: function(response) {
                if (response && response.length > 0) {
                    $('#file-preview-box').empty();
                    
                    // Create HTML for editor if needed
                    var existingContent = $('.summernote').summernote('code');
                    var filesToInsert = [];
                    
                    $.each(response, function(index, file) {
                        var ext = file.file_type.toLowerCase();
                        var filePreviewHtml = '';
                        
                        // Check if file reference already exists in editor content
                        var fileAlreadyInEditor = existingContent.includes(file.url);
                        
                        if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
                            filePreviewHtml = `<div class="uploaded-file position-relative d-inline-block m-2" data-id="${file.id}">
                                <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px; z-index: 100;">×</span>
                                <img src="${file.url}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                            </div>`;
                            
                            // Only add to insertion array if not already in editor
                            if (!fileAlreadyInEditor) {
                                filesToInsert.push({
                                    type: 'image',
                                    url: file.url,
                                    html: `<div class="file-attachment">
                                        <img src="${file.url}" class="img-fluid" style="max-width: 300px; max-height: 200px;">
                                    </div>`
                                });
                            }
                        } else {
                            var fileIcon = getFileIcon(ext);
                            filePreviewHtml = `<div class="uploaded-file position-relative d-inline-block m-2" data-id="${file.id}">
                                <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px; z-index: 100;">×</span>
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="${fileIcon}" style="font-size: 24px; margin-right: 10px;"></i>
                                    <a href="${file.url}" target="_blank">${file.original_name}</a>
                                </div>
                            </div>`;
                            
                            // Only add to insertion array if not already in editor
                            if (!fileAlreadyInEditor) {
                                filesToInsert.push({
                                    type: 'document',
                                    url: file.url,
                                    name: file.original_name,
                                    icon: fileIcon,
                                    html: `<div class="file-attachment">
                                        <p><i class="${fileIcon}" style="font-size: 16px; margin-right: 5px;"></i> 
                                        <a href="${file.url}" target="_blank">${file.original_name}</a></p>
                                    </div>`
                                });
                            }
                        }
                        
                        $('#file-preview-box').append(filePreviewHtml);
                    });
                    
                    // If there are files to insert, add them to the editor
                    if (filesToInsert.length > 0) {
                        // Create a container for files if it doesn't exist
                        var filesHtml = '<div class="all-attachments"><p><strong>Attachments:</strong></p>';
                        $.each(filesToInsert, function(index, file) {
                            filesHtml += file.html;
                        });
                        filesHtml += '</div>';
                        
                        // Append to existing content if we have actual content
                        if (existingContent && !existingContent.includes('<div class="all-attachments">')) {
                            $('.summernote').summernote('code', existingContent + filesHtml);
                        }
                    }
                    
                    initializeFileRemoveButtons();
                } else {
                    $('#file-preview-box').html('<p class="text-muted small">No files uploaded yet. Drag files here or use the attachment button in the editor.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error loading files:", error);
                $('#file-preview-box').html('<p class="text-danger">Error loading files. Please refresh the page.</p>');
            }
        });
    }
}
// Call this function when editing
if ($('input[name="id"]').val()) {
    loadProgressFiles();
}
    // Initialize Summernote
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            [ 'style', [ 'style' ] ],
            [ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 
                        'superscript', 'subscript', 'clear'] ],
            [ 'fontname', [ 'fontname' ] ],
            [ 'fontsize', [ 'fontsize' ] ],
            [ 'color', [ 'color' ] ],
            [ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
            [ 'table', [ 'table' ] ],
            [ 'insert', ['picture', 'file' ]  ],
            [ 'view', [ 'undo', 'redo', 'fullscreen', 'help' ] ]
        ],
        callbacks: {
          // In Summernote callbacks
onImageUpload: function(files) {
    uploadFile(files[0], 'image', this, true, false); // Editor only
},

// In file button handler
click: function() {
    uploadFile(file, 'document', context, true, false); // Editor only
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
   // Modify the uploadFile function parameters
function uploadFile(file, fileType, editor, insertInEditor = false, addToPreview = false) {
    var formData = new FormData();
    formData.append('file', file);
    formData.append('type', fileType);
    var progressId = $('input[name="id"]').val() || 0;
    var projectId = $('input[name="project_id"]').val() || 0;
    var taskId = $('select[name="task_id"]').val() || 0;
    formData.append('progress_id', progressId);
    formData.append('project_id', projectId);
    formData.append('task_id', taskId);

    $('#file-preview-box').append('<div class="uploading-indicator"><i class="fas fa-spinner fa-spin"></i> Uploading...</div>');

    $.ajax({
        url: 'ajax.php?action=upload_progress_file',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            $('.uploading-indicator').remove();
            if (response.success) {
                var imageUrl = response.url + '?t=' + new Date().getTime();
                
                // Add to preview box if needed
                if (addToPreview) {
                    var filePreviewHtml = '';
                    $('#file-preview-box p.text-muted').remove();

                    if (fileType === 'image') {
                        filePreviewHtml = `
                            <div class="uploaded-file position-relative d-inline-block m-2" data-id="${response.id}">
                                <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px; z-index: 100;">×</span>
                                <img src="${imageUrl}" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                            </div>`;
                    } else {
                        var fileIcon = getFileIcon(response.extension);
                        filePreviewHtml = `
                            <div class="uploaded-file position-relative d-inline-block m-2" data-id="${response.id}">
                                <span class="remove-file-btn position-absolute top-0 end-0 bg-danger text-white px-2" style="cursor: pointer; border-radius: 0 0 0 5px; z-index: 100;">×</span>
                                <div class="d-flex align-items-center p-2 border rounded">
                                    <i class="${fileIcon}" style="font-size: 24px; margin-right: 10px;"></i>
                                    <a href="${response.url}" target="_blank">${response.originalName}</a>
                                </div>
                            </div>`;
                    }
                    $('#file-preview-box').append(filePreviewHtml);
                    initializeFileRemoveButtons();
                }

                // Insert into editor if needed
                if (insertInEditor) {
                    if (fileType === 'image') {
                        $(editor).summernote('insertImage', imageUrl);
                    } else {
                        var fileIcon = getFileIcon(response.extension);
                        var commentHtml = `
                            <div class="file-attachment">
                                <p><i class="${fileIcon}" style="font-size: 16px; margin-right: 5px;"></i> 
                                <a href="${response.url}" target="_blank">${response.originalName}</a></p>
                            </div>`;
                        $(editor).summernote('pasteHTML', commentHtml);
                    }
                }
            } else {
                alert_toast('Error uploading file: ' + (response.error || 'Unknown error'), "error");
            }
        },
        error: function(xhr, status, error) {
            $('.uploading-indicator').remove();
            console.error("Upload error:", xhr.responseText);
            alert_toast('Error uploading file. Please try again.', "error");
        }
    });
}
    // Function to initialize file remove buttons
    function initializeFileRemoveButtons() {
        // Make sure the remove buttons are properly initialized
        $('.uploaded-file').each(function() {
            $(this).hover(
                function() { $(this).find('.remove-file-btn').show(); },
                function() { $(this).find('.remove-file-btn').hide(); }
            );
        });
    }
    
    // Initialize remove buttons on page load
    initializeFileRemoveButtons();
    
    /// Update the AJAX call in the remove file button event handler
    $('#file-preview-box').on('click', '.remove-file-btn', function() {
    var $fileDiv = $(this).closest('.uploaded-file');
    var fileId = $fileDiv.data('id');
    
    // Remove the confirmation and directly delete the file
    $.ajax({
        url: 'ajax.php?action=delete_progress_file',
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

    // Enable drag and drop file uploading
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

    // Also make the area clickable to select files
    $uploadArea.on('click', function(e) {
        if ($(e.target).closest('.uploaded-file').length === 0 && 
            $(e.target).closest('a').length === 0 && 
            $(e.target).closest('.remove-file-btn').length === 0) {
            $fileInput.click();
        }
    });
    
    $fileInput.on('change', function() {
        if (this.files.length > 0) {
            handleFiles(this.files);
            // Reset the input to allow selecting the same file again
            $(this).val('');
        }
    });

    // Modified to NOT insert files into the editor when using drag & drop or click upload
  // Update handleFiles function
function handleFiles(files) {
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var ext = file.name.split('.').pop().toLowerCase();
        var fileType = ['jpg','jpeg','png','gif','webp'].includes(ext) ? 'image' : 'document';
        uploadFile(file, fileType, $('.summernote')[0], true, true); // Both editor and preview
    }
}

    // Submit form with AJAX
    $('#manage-progress').submit(function(e){
        e.preventDefault();
        start_load(); // custom function from your admin_class or layout

        var formData = new FormData($(this)[0]);
        $.ajax({
            url: 'ajax.php?action=save_progress',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(resp){
                if(resp == 1){
                    alert_toast('Data successfully saved', "success");
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                } else {
                    console.error("Error response:", resp);
                    alert_toast('Failed to save data', "error");
                    end_load();
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX error:", error);
                alert_toast('Failed to save data', "error");
                end_load();
            }
        });
    });

    // Add a button to insert selected files into the editor
    $('<button type="button" class="btn btn-sm btn-primary mb-2" id="insert-files-btn"><i class="fas fa-arrow-right"></i> Insert Selected Files to Editor</button>')
        .prependTo('#file-preview-container');

    // Store selected files
    var selectedFiles = [];

    // Add ability to select files in preview
    $('#file-preview-container').on('click', '.uploaded-file', function(e) {
        // Ignore clicks on remove button or links
        if ($(e.target).closest('.remove-file-btn').length || $(e.target).closest('a').length) {
            return;
        }
        
        // Toggle selection
        $(this).toggleClass('selected-file');
        
        // Update selected files count
        updateSelectedFilesCount();
    });

    function updateSelectedFilesCount() {
        var count = $('.uploaded-file.selected-file').length;
        if (count > 0) {
            $('#insert-files-btn').text(`Insert ${count} Selected Files to Editor`).show();
        } else {
            $('#insert-files-btn').hide();
        }
    }

    // Handle insert files button click
    $('#file-preview-container').on('click', '#insert-files-btn', function() {
        $('.uploaded-file.selected-file').each(function() {
            var fileId = $(this).data('id');
            var isImage = $(this).find('img').length > 0;
            
            if (isImage) {
                var imageUrl = $(this).find('img').attr('src');
                var commentHtml = `<div class="file-attachment">
                    <img src="${imageUrl}" class="img-fluid" style="max-width: 300px; max-height: 200px;">
                </div>`;
                $('.summernote').summernote('pasteHTML', commentHtml);
            } else {
                var fileUrl = $(this).find('a').attr('href');
                var fileName = $(this).find('a').text();
                var fileIconClass = $(this).find('i').attr('class');
                
                var commentHtml = `<div class="file-attachment">
                    <p><i class="${fileIconClass}" style="font-size: 16px; margin-right: 5px;"></i> 
                    <a href="${fileUrl}" target="_blank">${fileName}</a></p>
                </div>`;
                $('.summernote').summernote('pasteHTML', commentHtml);
            }
            
            // Deselect after inserting
            $(this).removeClass('selected-file');
        });
        
        // Update count
        updateSelectedFilesCount();
    });
});

</script>
<style>
body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}
.uploaded-file {
    position: relative;
    margin: 5px;
    transition: all 0.3s;
    border-radius: 4px;
    overflow: hidden;
}
.remove-file-btn {
    display: none;
    z-index: 100;
    position: absolute;
    top: 0;
    right: 0;
    border-radius: 0 0 0 5px;
}
.uploaded-file:hover .remove-file-btn {
    display: block;
}
.uploaded-file:hover {
    opacity: 0.8;
    box-shadow: 0 0 5px rgba(0,0,0,0.2);
}
#file-preview-container {
    transition: all 0.3s;
    cursor: pointer;
}
.dragover {
    border-color: #007bff !important;
    background-color: #e9f3ff !important;
    box-shadow: 0 0 10px rgba(0,123,255,0.3);
}
#file-preview-box {
    min-height: 100px;
    display: flex;
    flex-wrap: wrap;
    align-content: flex-start;
}
.uploading-indicator {
    width: 100%;
    text-align: center;
    padding: 10px;
    color: #007bff;
}
/* Add CSS for selected files */
.uploaded-file.selected-file {
    border: 2px solid #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
}

/* Style for the insert button */
#insert-files-btn {
    display: none; /* Initially hidden */
    margin-bottom: 10px;
}

/* Make upload area more obvious */
#file-preview-container {
    border: 2px dashed #ccc;
    transition: all 0.3s;
    cursor: pointer;
    padding: 15px;
}

#file-preview-container:hover {
    border-color: #aaa;
}

/* Change cursor on files to indicate they're selectable */
.uploaded-file {
    cursor: pointer;
}
/* Add this to your stylesheet */
.progress-attachments {
    background-color: #f9f9f9;
    border-radius: 5px;
    padding: 10px;
    margin-top: 10px;
}

.progress-attachments h6 {
    margin-bottom: 10px;
    color: #555;
}

.file-previews {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.attachment-preview {
    transition: transform 0.2s;
}

.attachment-preview:hover {
    transform: scale(1.05);
}

.file-link {
    text-decoration: none;
    color: #333;
    background: white;
    transition: all 0.2s;
    min-width: 130px;
}

.file-link:hover {
    background: #f0f0f0;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>
</body>