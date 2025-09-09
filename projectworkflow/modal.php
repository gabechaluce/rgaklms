<?php
// Get the isAdmin function if not already included
if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['login_id']) && isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1;
    }
}
// Add new permission check functions
if (!function_exists('canEditUrgentMeetings')) {
    function canEditUrgentMeetings() {
        return isset($_SESSION['login_type']) && in_array($_SESSION['login_type'], [1, 2, 3, 5, 7, 10]);
    }
}
if (!function_exists('canAddEvents')) {
    function canAddEvents() {
        $allowed_roles = [1, 13]; // Admin (1) and HR (13)
        return isset($_SESSION['login_type']) && in_array($_SESSION['login_type'], $allowed_roles);
    }
}
// Only include modals if user has appropriate permissions
if (canEditUrgentMeetings()):
?>

<!-- Add Event Modal (Admin Only) -->
<div id="ModalAdd" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Event</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addEventForm">
                    <div class="form-group">
                        <label>Event Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="start_date" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" class="form-control" name="start_time" id="start_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" class="form-control" name="end_time" id="end_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Event Type</label>
                        <?php if (canAddEvents()): ?>
                        <select class="form-control" name="event_type" id="event_type" required>
                            <option value="urgent_meeting" style="color:#FF0000;">Urgent Meeting</option>
                            <option value="events_holidays" style="color:#FF8C00;">Events & Holidays</option>
                        </select>
                        <?php else: ?>
                        <input type="hidden" name="event_type" value="urgent_meeting">
                        <div class="form-control" style="color:#FF0000;">Urgent Meeting</div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Urgent Meeting Visibility Options -->
                    <div id="urgentMeetingOptions" style="display:none;">
                        <div class="form-group">
                            <label>Select Who Can See This Meeting</label>
                            <div class="alert alert-info">
                                <small>Select specific users and/or entire positions who should see this urgent meeting</small>
                            </div>
                        </div>
                        
                        <!-- Individual User Selection -->
                        <div id="userSelectionContainer" style="margin-top:15px;">
                            <label>Select Specific Users</label>
                            <div class="user-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                <?php
                                // Fetch all users from database
                                $users = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name, type FROM users ORDER BY firstname, lastname");
                                while($row = $users->fetch_assoc()):
                                    $position_names = [
                                        1 => 'General Manager',
                                        2 => 'Project Coordinator',
                                        3 => 'Designer',
                                        4 => 'Inventory Coordinator',
                                        5 => 'Estimator',
                                        6 => 'Accounting',
                                        7 => 'Project Manager',
                                        8 => 'Purchasing',
                                        9 => 'Sales',
                                        10 => 'Admin'
                                    ];
                                    $position_name = isset($position_names[$row['type']]) ? $position_names[$row['type']] : 'Staff';
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input user-checkbox" type="checkbox" name="selected_users[]" value="<?php echo $row['id']; ?>" id="user_<?php echo $row['id']; ?>">
                                    <label class="form-check-label" for="user_<?php echo $row['id']; ?>">
                                        <?php echo $row['name']; ?> (<?php echo $position_name; ?>)
                                    </label>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <!-- Position Selection -->
                        <div id="positionSelectionContainer" style="margin-top:15px;">
                            <label>Select Positions</label>
                            <div class="position-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                <?php
                                $positions = [
                                    1 => 'General Manager',
                                    2 => 'Project Coordinator',
                                    3 => 'Designer',
                                    4 => 'Inventory Coordinator',
                                    5 => 'Estimator',
                                    6 => 'Accounting',
                                    7 => 'Project Manager',
                                    8 => 'Purchasing',
                                    9 => 'Sales',
                                    10 => 'Admin'
                                ];
                                foreach ($positions as $id => $name): ?>
                                <div class="form-check">
                                    <input class="form-check-input position-checkbox" type="checkbox" name="selected_positions[]" value="<?php echo $id; ?>" id="position_<?php echo $id; ?>">
                                    <label class="form-check-label" for="position_<?php echo $id; ?>"><?php echo $name; ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Event Modal -->
<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" method="POST" action="editEventTitle.php" id="editEventForm">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">Edit Schedule</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="title" class="col-sm-2 control-label">Activity</label>
                        <div class="col-sm-10">
                            <textarea rows="4" cols="10" id="title" class="form-control" name="title" maxlength="300" required></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="color" class="control-label">Type of Activity</label>
                        <div class="col-sm-10">
                            <select name="color" class="form-control" id="editColor" required>
                                <option value="">Choose</option>
                                <option style="color:#FF0000;" value="#FF0000">URGENT MEETING</option>
                                <option style="color:#FF8C00;" value="#FF8C00">HOLIDAY AND EVENT SCHEDULE</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                    </div>
                    <div class="form-group">
                        <label>Start Time</label>
                        <input type="time" class="form-control" name="start_time" id="edit_start_time" required>
                    </div>

                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" class="form-control" name="end_date" id="edit_end_date" required>
                    </div>
                    <div class="form-group">
                        <label>End Time</label>
                        <input type="time" class="form-control" name="end_time" id="edit_end_time" required>
                    </div>

                    <!-- Urgent Meeting Visibility Options -->
                    <div id="editUrgentMeetingOptions" style="display:none; margin-top:20px;">
                        <div class="form-group">
                            <label>Update Who Can See This Meeting</label>
                            <div class="alert alert-info">
                                <small>Update specific users and/or positions who should see this urgent meeting</small>
                            </div>
                        </div>
                        
                        <!-- Individual User Selection -->
                        <div id="editUserSelectionContainer" style="margin-top:15px;">
                            <label>Select Specific Users</label>
                            <div class="user-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                <?php
                                $users = $conn->query("SELECT id, CONCAT(firstname, ' ', lastname) as name, type FROM users ORDER BY firstname, lastname");
                                while($row = $users->fetch_assoc()):
                                    $position_names = [
                                        1 => 'General Manager',
                                        2 => 'Project Coordinator',
                                        3 => 'Designer',
                                        4 => 'Inventory Coordinator',
                                        5 => 'Estimator',
                                        6 => 'Accounting',
                                        7 => 'Project Manager',
                                        8 => 'Purchasing',
                                        9 => 'Sales',
                                        10 => 'Admin'
                                    ];
                                    $position_name = isset($position_names[$row['type']]) ? $position_names[$row['type']] : 'Staff';
                                ?>
                                <div class="form-check">
                                    <input class="form-check-input edit-user-checkbox" type="checkbox" name="selected_users[]" value="<?php echo $row['id']; ?>" id="edit_user_<?php echo $row['id']; ?>">
                                    <label class="form-check-label" for="edit_user_<?php echo $row['id']; ?>">
                                        <?php echo $row['name']; ?> (<?php echo $position_name; ?>)
                                    </label>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <!-- Position Selection -->
                        <div id="editPositionSelectionContainer" style="margin-top:15px;">
                            <label>Select Entire Positions</label>
                            <div class="position-checkboxes" style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                                <?php foreach ($positions as $id => $name): ?>
                                <div class="form-check">
                                    <input class="form-check-input edit-position-checkbox" type="checkbox" name="selected_positions[]" value="<?php echo $id; ?>" id="edit_position_<?php echo $id; ?>">
                                    <label class="form-check-label" for="edit_position_<?php echo $id; ?>"><?php echo $name; ?></label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group text-right">
                        <button type="button" class="btn btn-danger" id="deleteEventBtn">
                            <i class="fas fa-trash-alt"></i> Delete Event
                        </button>
                    </div>
                    <input type="hidden" name="id" class="form-control" id="id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.form-group {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}
.form-group label {
    flex: 0 0 140px;
    text-align: left;
    font-weight: bold;
}
   
.form-group .col-sm-10 {
    flex: 1;
}
textarea.form-control {
    height: auto;
}
.form-control {
    width: 100%;
}
.user-checkboxes, .position-checkboxes {
    background-color: #f9f9f9;
    margin-bottom: 15px;
}
.modal-header .close {
    margin: -1rem auto -1rem -1rem;
    padding: 1rem;
}
.fc-edit-btn {
    position: absolute;
    right: 5px;
    top: 5px;
    color: white;
    background: rgba(0,0,0,0.3);
    border-radius: 3px;
    padding: 2px 5px;
    cursor: pointer;
    z-index: 10;
    display: none;
}
.fc-event:hover .fc-edit-btn {
    display: block;
}
.fc-edit-btn:hover {
    background: rgba(0,0,0,0.5);
}
.current-date-display {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    font-weight: bold;
    color: #495057;
    padding: 0.375rem 0.75rem;
}
.current-date-display[readonly] {
    cursor: not-allowed;
    opacity: 1;
}
#deleteEventBtn {
    margin-top: 20px;
    padding: 8px 15px;
}
#deleteEventBtn:hover {
    background-color: #c82333;
    border-color: #bd2130;
}
#addUrgentMeetingBtn {
    background-color: #FF0000;
    border-color: #FF0000;
    font-weight: bold;
    padding: 8px 10px;
    border-radius: 2px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
#addUrgentMeetingBtn:hover {
    background-color: #cc0000;
    border-color: #cc0000;
}
#addUrgentMeetingBtn i {
    margin-right: 5px;
}
/* Add these CSS rules to your existing <style> section */

/* Mobile Calendar Modal Fixes */
@media (max-width: 768px) {
    .modal-dialog {
        margin: 10px;
        max-width: calc(100vw - 20px);
        width: auto;
    }
    
    .modal-content {
        max-height: calc(100vh - 20px);
        overflow-y: auto;
    }
    
    .modal-body {
        max-height: calc(100vh - 150px);
        overflow-y: auto;
        padding: 15px;
    }
    
    /* Fix for date input calendar popups */
    .form-group input[type="date"] {
        position: relative;
        z-index: 1060; /* Higher than modal z-index */
    }
    
    /* Ensure calendar dropdown appears correctly */
    input[type="date"]::-webkit-calendar-picker-indicator {
        position: relative;
        z-index: 1061;
    }
    
    /* Fix for calendar positioning */
    .modal .form-group {
        position: relative;
        overflow: visible;
    }
    
    /* Better form layout on mobile */
    .form-group {
        display: block;
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
        width: 100%;
        text-align: left;
        flex: none;
    }
    
    .form-group .col-sm-10 {
        width: 100%;
        padding: 0;
        flex: none;
    }
    
    .form-control {
        width: 100%;
        box-sizing: border-box;
        font-size: 16px; /* Prevents zoom on iOS */
        padding: 8px 12px;
    }
}

/* For very small screens */
@media (max-width: 480px) {
    .modal-dialog {
        margin: 5px;
        max-width: calc(100vw - 10px);
    }
    
    .modal-body {
        padding: 10px;
        max-height: calc(100vh - 120px);
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    /* Better button spacing on mobile */
    .modal-footer {
        padding: 10px 15px;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .modal-footer .btn {
        flex: 1;
        min-width: 120px;
    }
}

/* Ensure the calendar popup doesn't get cut off */
.modal-content {
    overflow: visible;
}

/* Fix for scrollable areas within modal */
@media (max-width: 768px) {
    .user-checkboxes, .position-checkboxes {
        max-height: 120px;
    }
}

/* Fix for date input focus state */
input[type="date"]:focus {
    z-index: 1061;
    position: relative;
}

/* Prevent horizontal scrolling issues */
.modal-body * {
    box-sizing: border-box;
}

/* iOS specific fixes */
@supports (-webkit-touch-callout: none) {
    input[type="date"] {
        -webkit-appearance: none;
        -webkit-border-radius: 4px;
        border-radius: 4px;
    }
}

/* Better textarea handling on mobile */
@media (max-width: 768px) {
    textarea.form-control {
        min-height: 80px;
        resize: vertical;
    }
    
    /* Improved select styling */
    select.form-control {
        height: auto;
        padding: 8px 12px;
    }
    
    /* Better checkbox/radio styling */
    .form-check {
        padding-left: 1.25rem;
        margin-bottom: 0.5rem;
    }
    
    .form-check-label {
        font-size: 14px;
        line-height: 1.4;
    }
}


body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}




</style>

<script>
$(document).ready(function() {
    // Function to combine date and time
    function combineDateTime(dateStr, timeStr) {
        if (!dateStr || !timeStr) return null;
        return moment(`${dateStr} ${timeStr}`).format('YYYY-MM-DD HH:mm:ss');
    }

    // Function to split datetime into date and time
    function splitDateTime(datetimeStr) {
        if (!datetimeStr) return { date: '', time: '' };
        const m = moment(datetimeStr);
        return {
            date: m.format('YYYY-MM-DD'),
            time: m.format('HH:mm')
        };
    }

    // Show/hide urgent meeting options based on event type selection
    $('#event_type').change(function() {
        if ($(this).val() === 'urgent_meeting') {
            $('#urgentMeetingOptions').show();
        } else {
            $('#urgentMeetingOptions').hide();
            $('.user-checkbox').prop('checked', false);
            $('.position-checkbox').prop('checked', false);
        }
    }).trigger('change');

    // Event delete function
    $('#deleteEventBtn').click(function() {
        const eventId = $('#ModalEdit #id').val();
        if (!eventId || isNaN(eventId) || parseInt(eventId) <= 0) {
            Swal.fire("Error", "Invalid event ID format", "error");
            return;
        }

        Swal.fire({
            title: 'Confirm Delete',
            text: "Are you sure you want to delete this event?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                deleteEvent(eventId);
            }
        });
    });

    function deleteEvent(eventId) {
        $.ajax({
            url: 'delete_event.php',
            type: 'POST',
            data: { id: eventId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Deleted!',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        $('#ModalEdit').modal('hide');
                        $('#calendar').fullCalendar('refetchEvents');
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        html: `<p>${response.message}</p><small>Event ID: ${eventId}</small>`,
                        icon: 'error'
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = "Could not delete event";
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    errorMsg += `<br><small>${error}</small>`;
                }
                Swal.fire({
                    title: 'Error!',
                    html: errorMsg,
                    icon: 'error'
                });
            }
        });
    }

    // Add Event Form Submission
    $("#addEventForm").on("submit", function(e) {
        e.preventDefault();
        
        // Combine date and time fields
        const start = combineDateTime($('#start_date').val(), $('#start_time').val());
        const end = combineDateTime($('#end_date').val(), $('#end_time').val());
        
        // Create form data
        const formData = {
            title: $('input[name="title"]').val(),
            start: start,
            end: end,
            event_type: $('select[name="event_type"]').val(),
            selected_users: $('input[name="selected_users[]"]:checked').map(function() {
                return this.value;
            }).get(),
            selected_positions: $('input[name="selected_positions[]"]:checked').map(function() {
                return this.value;
            }).get()
        };
        
        $.ajax({
            url: "add_event.php",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    $('#calendar').fullCalendar('renderEvent', {
                        id: response.event.id,
                        title: response.event.title,
                        start: response.event.start,
                        end: response.event.end,
                        color: response.event.color,
                        event_type: response.event.event_type,
                        created_by: response.event.created_by
                    }, true);
                    
                    $('#ModalAdd').modal('hide');
                    $('#addEventForm')[0].reset();
                    Swal.fire("Success", response.message, "success");
                } else {
                    Swal.fire("Error", response.message, "error");
                }
            },
            error: function(xhr, status, error) {
                Swal.fire("Error", "Could not add event: " + error, "error");
            }
        });
    });

    // Edit Event Form Submission
    $("#editEventForm").on("submit", function(e) {
        e.preventDefault();
        
        // Combine date and time
        const start = combineDateTime($('#edit_start_date').val(), $('#edit_start_time').val());
        const end = combineDateTime($('#edit_end_date').val(), $('#edit_end_time').val());
        
        // Create FormData object
        const formData = new FormData(this);
        formData.set('start', start);
        formData.set('end', end);
        
        $.ajax({
            url: 'editEventVisibility.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success"
                    }).then(() => {
                        $('#ModalEdit').modal('hide');
                        $('#calendar').fullCalendar('refetchEvents');
                    });
                } else {
                    Swal.fire("Error!", response.message, "error");
                }
            },
            error: function(xhr, status, error) {
                Swal.fire("Error!", "Could not update event: " + error, "error");
            }
        });
    });

    // Initialize FullCalendar
    $('#calendar').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay,listWeek,listMonth'
        },
        views: {
            month: { buttonText: 'Month' },
            agendaWeek: { buttonText: 'Week' },
            agendaDay: { buttonText: 'Day' },
            listWeek: { buttonText: 'Weekly List' },
            listMonth: { buttonText: 'Monthly List' }
        },
        defaultView: 'month',
        editable: <?php echo canEditUrgentMeetings() ? 'true' : 'false'; ?>,
        eventLimit: false,
        selectable: <?php echo canAddEvents() ? 'true' : 'false'; ?>,
        timeFormat: "h:mma",
        allDaySlot: false,
        
        // When a user selects a date range
        select: function(start, end) {
            <?php if (canEditUrgentMeetings()): ?>
            // Set default values
            const now = moment();
            $('#start_date').val(now.format('YYYY-MM-DD'));
            $('#start_time').val(now.format('HH:mm'));
            $('#end_date').val(now.format('YYYY-MM-DD'));
            $('#end_time').val(now.add(1, 'hour').format('HH:mm'));
            
            $('#ModalAdd').modal('show');
            <?php endif; ?>
        },
        
        // When an event is clicked
        eventClick: function(event) {
            // Get current user ID and check if they created this event
            var currentUserId = <?php echo $_SESSION['login_id'] ?? 0; ?>;
            var isCreator = (event.created_by == currentUserId);
            var isAdmin = <?php echo ($_SESSION['login_type'] ?? 0) == 1 ? 'true' : 'false'; ?>;
            
            // Split dates into date and time components
            var startParts = splitDateTime(event.start);
            var endParts = event.end ? splitDateTime(event.end) : { date: '', time: '' };
            
            // Set modal values
            $('#ModalEdit #id').val(event.id);
            $('#ModalEdit #title').val(event.title);
            $('#ModalEdit #edit_start_date').val(startParts.date);
            $('#ModalEdit #edit_start_time').val(startParts.time);
            $('#ModalEdit #edit_end_date').val(endParts.date);
            $('#ModalEdit #edit_end_time').val(endParts.time);
            $('#ModalEdit #editColor').val(event.color || '#FF0000');
            
            // Show/hide edit options based on permissions
            if (event.color == '#FF0000' && (isCreator || isAdmin)) {
                $('#editUrgentMeetingOptions').show();
                loadEventVisibility(event.id);
            } else {
                $('#editUrgentMeetingOptions').hide();
            }
            
            // Show delete button only for creator or admin
            if (isCreator || isAdmin) {
                $('#deleteEventBtn').show();
            } else {
                $('#deleteEventBtn').hide();
            }
            
            $('#ModalEdit').modal('show');
        },
        
        // Fetch events and project dates
        eventSources: [
            {
                url: "fetch_date.php",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log("Project dates loaded successfully:", response);
                },
                error: function(xhr, status, error) {
                    console.error("Error loading fetch_date.php:", error);
                }
            },
            {
                url: 'fetch_events.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Ensure urgent meetings are red
                    return response.map(function(event) {
                        if (event.event_type === 'urgent_meeting') {
                            event.color = '#FF0000';
                        }
                        return event;
                    });
                },
                error: function(xhr, status, error) {
                    console.error("Error loading events:", error);
                }
            }
        ],
        
        eventRender: function(event, element) {
            element.css("background-color", event.color);

            // Create formatted time strings
            const startTime = moment(event.start).format('MMM D, YYYY h:mm A');
            const endTime = event.end ?
                moment(event.end).format('MMM D, YYYY h:mm A') :
                "Ongoing";

            // Initialize tooltip
            element.tooltip({
                title: `
                    <div class="event-tooltip">
                        <h6>${event.title}</h6>
                        <div class="event-times">
                            <span>Start: ${startTime}</span><br>
                            <span>End: ${endTime}</span>
                        </div>
                    </div>
                `,
                html: true,
                placement: 'auto',
                container: 'body',
                trigger: 'hover'
            });

            element.css('cursor', 'pointer');

            <?php if (canEditUrgentMeetings()): ?>
            // Add edit button to each event for authorized users
            if (event.id) {
                const editButton = $(`
                    <span class="fc-edit-btn" title="Edit Event">
                        <i class="fas fa-edit"></i>
                    </span>
                `);
                editButton.click(function(e) {
                    e.stopPropagation();
                    openEditModal(event);
                });
                element.find('.fc-content').prepend(editButton);
            }
            <?php endif; ?>
        },
        
        // Handle event drag & drop update
        eventDrop: function(event, delta, revertFunc) {
            <?php if (canEditUrgentMeetings()): ?>
            // For non-admins, only allow moving urgent meetings
            if ((event.color !== '#FF0000' && event.backgroundColor !== '#FF0000') &&
                <?php echo !isAdmin() ? 'true' : 'false'; ?>) {
                revertFunc();
                Swal.fire("Restricted", "You can only move urgent meetings (red events)", "warning");
                return;
            }
            
            let id = event.id;
            let start = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            let end = event.end ? moment(event.end).format('YYYY-MM-DD HH:mm:ss') : start;
            $.ajax({
                url: 'editEventDate.php',
                type: "POST",
                data: { id: id, start: start, end: end },
                success: function(response) {
                    console.log("Event drop update response:", response);
                    try {
                        let result = JSON.parse(response);
                        if (result.status === "success") {
                            Swal.fire("Updated!", "Event has been updated successfully.", "success");
                        } else {
                            Swal.fire("Error!", result.message, "error");
                            revertFunc();
                        }
                    } catch (e) {
                        console.error("JSON Parsing Error:", e);
                        Swal.fire("Error!", "Unexpected response from server.", "error");
                        revertFunc();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating event:", error);
                    Swal.fire("Error!", "Could not update event.", "error");
                    revertFunc();
                }
            });
            <?php else: ?>
            // Non-authorized users cannot move events
            revertFunc();
            Swal.fire("Unauthorized", "You don't have permission to modify events.", "warning");
            <?php endif; ?>
        }
    });

    // Function to load event visibility settings
    function loadEventVisibility(eventId) {
        $.ajax({
            url: 'get_event_visibility.php',
            type: 'GET',
            data: { event_id: eventId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Clear all checkboxes first
                    $('.edit-user-checkbox').prop('checked', false);
                    $('.edit-position-checkbox').prop('checked', false);
                    
                    // Check selected users
                    if (response.data.selected_users && response.data.selected_users.length > 0) {
                        response.data.selected_users.forEach(userId => {
                            $(`#edit_user_${userId}`).prop('checked', true);
                        });
                    }
                    
                    // Check selected positions
                    if (response.data.selected_positions && response.data.selected_positions.length > 0) {
                        response.data.selected_positions.forEach(positionId => {
                            $(`#edit_position_${positionId}`).prop('checked', true);
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading event visibility:', error);
            }
        });
    }

    // Update clock function
    function updateClock() {
        const options = { timeZone: 'Asia/Manila', hour12: true, hour: '2-digit', minute: '2-digit', second: '2-digit' };
        const manilaTime = new Intl.DateTimeFormat('en-US', options).format(new Date());
        document.getElementById('clock').innerText = manilaTime;
    }
    // Update clock every second
    setInterval(updateClock, 1000);
    updateClock(); // Initial call to set time immediately
});

document.addEventListener("DOMContentLoaded", function () {
    const eventTypeSelect = document.querySelector("select[name='event_type']");
    function updateTextColor() {
        const selectedOption = eventTypeSelect.options[eventTypeSelect.selectedIndex];
        eventTypeSelect.style.color = selectedOption.style.color;
    }
    eventTypeSelect.addEventListener("change", updateTextColor);
    updateTextColor(); // Apply initial color on load
});
$(document).ready(function() {
    // Handle Add Urgent Meeting button click
    $('#addUrgentMeetingBtn').on('click', function() {
        // Set default values for urgent meeting
        const now = moment();
        $('#start_date').val(now.format('YYYY-MM-DD'));
        $('#start_time').val(now.format('HH:mm'));
        $('#end_date').val(now.format('YYYY-MM-DD'));
        $('#end_time').val(now.add(1, 'hour').format('HH:mm'));
        
        // Set event type to urgent meeting
        $('#event_type').val('urgent_meeting').trigger('change');
        
        // Show the modal
        $('#ModalAdd').modal('show');
    });

    // Handle Add Event/Holiday button click
    $('#addEventBtn').on('click', function() {
        // Set default values for event/holiday
        const now = moment();
        $('#start_date').val(now.format('YYYY-MM-DD'));
        $('#start_time').val('00:00');
        $('#end_date').val(now.format('YYYY-MM-DD'));
        $('#end_time').val('23:59');
        
        // Set event type to events/holidays
        $('#event_type').val('events_holidays').trigger('change');
        
        // Show the modal
        $('#ModalAdd').modal('show');
    });
});
</script>
<?php endif; ?>