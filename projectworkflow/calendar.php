<?php
include('db_connect.php');
date_default_timezone_set("Asia/Manila");

// Function to check admin status
function isAdmin() {
    return isset($_SESSION['login_id']) && isset($_SESSION['login_type']) && $_SESSION['login_type'] == 1;
}

// Function to check if user can edit urgent meetings
function canEditUrgentMeeting() {
    $allowed_roles = [1, 2, 3, 5, 6, 7, 10];
    return isset($_SESSION['login_type']) && in_array($_SESSION['login_type'], $allowed_roles);
}

// Function to check if user can add events
function canAddEvents() {
    $allowed_roles = [1, 2, 3, 5, 6, 7, 10]; // Adjust these role IDs as needed
    return isset($_SESSION['login_type']) && in_array($_SESSION['login_type'], $allowed_roles);
}

// Fetch events from the database
$sql = "SELECT id, title, start_datetime AS start, end_datetime AS end, color FROM events";
$result = $conn->query($sql);
$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            "id" => $row['id'],
            "title" => $row['title'],
            "start" => $row['start'],
            "end" => $row['end'],
            "color" => $row['color']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Calendar</title>
    <!-- External Libraries -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Modern Design Styles */
        :root {
            --dark-brown: #3D2217;
            --cream-background: #f5ecde;
            --bright-red: #ff0000;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 10px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--cream-background);
        }

        .calendar-container {
            display: flex;
            flex-direction: column;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(61, 34, 23, 0.1);
            overflow: hidden;
        }

        .calendar-main {
            flex: 1;
            background-color: white;
            padding: 15px;
            order: 2;
        }

        .color-scheme-sidebar {
            background-color: rgba(61, 34, 23, 0.05);
            padding: 15px;
            border-top: 1px solid rgba(61, 34, 23, 0.1);
            order: 1;
        }

        #calendar {
            width: 100%;
            font-size: 14px;
        }

        .fc-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            color: var(--dark-brown);
            flex-wrap: wrap;
            gap: 10px;
        }

        .fc-toolbar-title {
            font-size: 1.2rem;
            font-weight: bold;
            color: var(--dark-brown);
            text-align: center;
            flex: 1;
            min-width: 200px;
        }

        .fc-button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
        }

        .fc-button {
            background-color: rgba(61, 34, 23, 0.1) !important;
            color: var(--dark-brown) !important;
            border: none !important;
            transition: background-color 0.3s ease;
            font-size: 12px !important;
            padding: 6px 8px !important;
            margin: 2px !important;
        }

        .fc-button:hover {
            background-color: rgba(61, 34, 23, 0.2) !important;
        }

        .fc-event {
            border-radius: 4px;
            border: none;
            opacity: 0.9;
            transition: all 0.3s ease;
            font-size: 11px;
        }

        .fc-event:hover {
            opacity: 1;
            transform: scale(1.02);
        }

        .color-scheme-label {
            color: var(--dark-brown);
            font-weight: bold;
            margin-bottom: 15px;
            display: block;
            font-size: 16px;
            text-align: center;
        }

        .color-samples {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .color-sample {
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            padding: 0 15px;
            color: white;
            font-size: 14px;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0,0,0,0.3);
        }

        .action-buttons {
            display: grid;
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .add-urgent-meeting, .add-event-btn {
            color: white;
            border: none;
            padding: 12px 15px;
            border-radius: 8px;
            width: 100%;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .add-urgent-meeting {
            background-color: var(--bright-red);
        }

        .add-urgent-meeting:hover {
            background-color: #cc0000;
            transform: translateY(-2px);
        }

        .add-event-btn {
            background-color: #FF8C00;
        }

        .add-event-btn:hover {
            background-color: #ff7700;
            transform: translateY(-2px);
        }

        /* Clock Styling - Keep as requested */
        #clock-container {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 2.2rem;
            font-weight: bold;
            color: var(--dark-brown);
            z-index: 1000;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            body {
                padding: 5px;
            }

            .calendar-container {
                border-radius: 8px;
                margin: 0;
            }

            .calendar-main {
                padding: 10px;
            }

            .color-scheme-sidebar {
                padding: 15px;
            }

            .fc-toolbar {
                flex-direction: column;
                gap: 10px;
                margin-bottom: 10px;
            }

            .fc-toolbar-title {
                font-size: 1.1rem;
                order: 1;
                margin-bottom: 5px;
            }

            .fc-left, .fc-right {
                order: 2;
                display: flex;
                justify-content: center;
                width: 100%;
            }

            .fc-button {
                font-size: 11px !important;
                padding: 8px 6px !important;
                min-width: 60px;
            }

            .fc-day-header {
                font-size: 12px;
                padding: 8px 2px;
            }

            .fc-day-number {
                font-size: 14px;
            }

            .fc-event {
                font-size: 10px;
                padding: 2px 4px;
            }

            .fc-time {
                display: none;
            }

            .color-scheme-label {
                font-size: 14px;
                margin-bottom: 10px;
            }

            .color-samples {
                grid-template-columns: 1fr;
                gap: 8px;
                margin-bottom: 15px;
            }

            .color-sample {
                height: 35px;
                font-size: 13px;
                padding: 0 12px;
            }

            .action-buttons {
                gap: 8px;
            }

            .add-urgent-meeting, .add-event-btn {
                padding: 12px;
                font-size: 13px;
            }

            #clock-container {
                position: static;
                text-align: center;
                font-size: 1.8rem;
                margin-bottom: 10px;
                background: rgba(255, 255, 255, 0.9);
                padding: 10px;
                border-radius: 8px;
                margin: 10px;
            }
        }

        @media (max-width: 480px) {
            .fc-toolbar-title {
                font-size: 1rem;
            }

            .fc-button {
                font-size: 10px !important;
                padding: 6px 4px !important;
                min-width: 50px;
            }

            .fc-day-header {
                font-size: 11px;
                padding: 6px 1px;
            }

            .fc-day-number {
                font-size: 13px;
            }

            .color-sample {
                font-size: 12px;
                height: 32px;
            }

            #clock-container {
                font-size: 1.5rem;
            }
        }

        /* Desktop Styles */
        @media (min-width: 769px) {
            .calendar-container {
                flex-direction: row;
            }

            .calendar-main {
                flex: 3;
                order: 1;
                padding: 20px;
            }

            .color-scheme-sidebar {
                flex: 1;
                order: 2;
                border-top: none;
                border-left: 1px solid rgba(61, 34, 23, 0.1);
                padding: 20px;
            }

            .color-samples {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }

        /* Enhanced Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .fc-day-grid-container,
            .fc-day-grid,
            .fc-row {
                position: static !important;
                width: 100% !important;
            }
            .fc-day-grid {
                display: table;
                table-layout: fixed;
                width: 100%;
            }
            .fc-day-grid table {
                width: 100%;
                border-collapse: collapse;
            }
            .fc-day-grid td {
                border: 1px solid #ccc;
                padding: 8px;
                vertical-align: top;
                width: 14.28%;
                page-break-inside: avoid;
            }
            .fc-day-number {
                font-weight: bold;
                display: block;
                margin-bottom: 5px;
            }
            .fc-event {
                display: block;
                margin: 2px 0;
                border-radius: 4px;
                padding: 4px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print-header {
                font-size: 1.5rem;
                margin: 20px 0;
                text-align: center;
                page-break-after: avoid;
            }
            .calendar-container,
            .calendar-main,
            #calendar {
                visibility: visible;
                position: static;
                width: 100%;
            }
            @page {
                size: landscape;
                margin: 15mm;
            }
        }
    </style>
</head>
<body>
    <div id="clock-container">
        <span id="clock"></span>
    </div>
    <div class="calendar-container">
        <div class="calendar-main">
            <div id="calendar"></div>
        </div>
        <div class="color-scheme-sidebar">
            <span class="color-scheme-label">ACTIVITY COLOR SCHEME:</span>
            <div class="color-samples">
                <div class="color-sample" style="background-color: #ff0000;">
                    Urgent Meeting
                </div>
                <div class="color-sample" style="background-color: #FF8C00;">
                    Events & Holidays
                </div>
                <div class="color-sample" style="background-color: #0071c5;">
                    Project Schedule
                </div>
            </div>
            
<div class="action-buttons">
    <?php if (canEditUrgentMeeting()): ?>
        <button class="add-urgent-meeting" id="addUrgentMeetingBtn">
            <span>⚠️</span> Add Urgent Meeting
        </button>
    <?php endif; ?>
    
    <?php if (canAddEvents()): ?>
        <button class="add-event-btn" id="addEventBtn">
            <span>🎉</span> Add Event/Holiday
        </button>
    <?php endif; ?>
    
    <button class="add-urgent-meeting" id="printCalendarBtn" style="background-color: #3D2217;">
        <span>🖨️</span> Print Calendar
    </button>
</div>
        </div>
    </div>
    <!-- Include Modals -->
    <?php include('modal.php'); ?>
    <script>
        $(document).ready(function() {
            // Responsive calendar configuration
            function getCalendarConfig() {
                const isMobile = window.innerWidth <= 768;
                
                return {
                    header: {
                        left: isMobile ? 'prev,next' : 'prev,next today printButton',
                        center: 'title',
                        right: isMobile ? 'today' : 'month,agendaWeek,agendaDay,listWeek,listMonth'
                    },
                    views: {
                        month: { buttonText: 'Month' },
                        agendaWeek: { buttonText: 'Week' },
                        agendaDay: { buttonText: 'Day' },
                        listWeek: { buttonText: 'W.List' },
                        listMonth: { buttonText: 'M.List' }
                    },
                    defaultView: isMobile ? 'listMonth' : 'month',
                    height: isMobile ? 'auto' : 600,
                    aspectRatio: isMobile ? 1.0 : 1.35,
                    editable: <?php echo canEditUrgentMeeting() ? 'true' : 'false'; ?>,
                    eventLimit: isMobile ? 2 : false,
                    eventLimitText: 'more',
                    selectable: <?php echo canAddEvents() ? 'true' : 'false'; ?>,
                    timeFormat: "h:mma",
                    allDaySlot: false,
                    eventSources: [
                        { url: "fetch_date.php", type: 'GET', dataType: 'json' },
                        { url: "fetch_events.php", type: 'GET', dataType: 'json' }
                    ],
                    eventRender: function(event, element) {
                        element.css("background-color", event.color);
                        
                        const eventType = event.color === '#FF0000' ? 'Urgent Meeting' : 'Event/Holiday';
                        const startTime = moment(event.start).format('MMM D, YYYY h:mm A');
                        const endTime = event.end ? moment(event.end).format('MMM D, YYYY h:mm A') : "Ongoing";
                        
                        // Simplified tooltip for mobile
                        const tooltipContent = window.innerWidth <= 768 ? 
                            `${event.title} - ${eventType}` :
                            `
                                <div class="event-tooltip">
                                    <h6>${event.title}</h6>
                                    <p><strong>Type:</strong> ${eventType}</p>
                                    <div class="event-times">
                                        <span>Start: ${startTime}</span><br>
                                        <span>End: ${endTime}</span>
                                    </div>
                                </div>
                            `;
                        
                        element.tooltip({
                            title: tooltipContent,
                            html: true,
                            placement: 'auto',
                            container: 'body',
                            trigger: 'hover'
                        });
                        
                        element.css('cursor', 'pointer');
                    },
                    windowResize: function(view) {
                        // Reinitialize calendar on window resize for better responsiveness
                        const newConfig = getCalendarConfig();
                        $('#calendar').fullCalendar('destroy');
                        $('#calendar').fullCalendar(newConfig);
                    }
                };
            }

            // Initialize calendar
            $('#calendar').fullCalendar(getCalendarConfig());

            // Clock functionality
            function updateClock() {
                const options = {
                    timeZone: 'Asia/Manila',
                    hour12: true,
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                };
                const manilaTime = new Intl.DateTimeFormat('en-US', options).format(new Date());
                document.getElementById('clock').innerText = manilaTime;
            }
            setInterval(updateClock, 1000);
            updateClock(); // Initial call

            // Print button functionality
            $('#printCalendarBtn').on('click', function() {
                // Create print window
                var printWindow = window.open('', '_blank');
               
                // Create full HTML content
                var htmlContent = `
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <title>Calendar Print</title>
                        <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
                        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

                        <style>
                            ${$('style').html()}
                             /* Logo styling */
            .logo-container {
                text-align: center;
                margin-bottom: 10px;
            }
           
            .logo-image {
                max-width: 230px;
                height: auto;
            }
   @media print {
  /* Calendar header styling */
  .fc-toolbar h2 {
    font-size: 18px !important;
    font-weight: bold !important;
    text-align: center !important;
    margin: 10px 0 !important;
  }
 
  /* Days of week header styling */
  .fc-day-header {
    font-weight: bold !important;
    padding: 8px 2px !important;
    text-align: center !important;
    border: 1px solid #ddd !important;
    background-color: #f7f7f7 !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
 
  /* Calendar grid fixing */
  .fc-view-container {
    height: auto !important;
    overflow: visible !important;
  }
 
  .fc-day-grid-container {
    height: auto !important;
    overflow: visible !important;
  }
 
  /* Make sure table cells are properly sized */
  .fc-row {
    height: auto !important;
    min-height: 80px !important;
    overflow: visible !important;
    display: table-row !important;
  }
 
  .fc-day-grid td {
    border: 1px solid #ddd !important;
    vertical-align: top !important;
    padding: 2px !important;
    height: auto !important;
  }
 
  /* Ensure date numbers are visible */
  .fc-day-number {
    float: right !important;
    clear: right !important;
    padding: 2px !important;
  }
 
  /* Event styling */
  .fc-event {
    margin: 1px 0 !important;
    padding: 1px 3px !important;
    font-size: 11px !important;
    line-height: 1.2 !important;
    border-radius: 2px !important;
    white-space: normal !important;
  }
 
  /* Force all content to be visible */
  .fc-scroller {
    overflow: visible !important;
    height: auto !important;
  }
 
  /* Page settings */
  @page {
    size: portrait;
    margin: 0.5cm;
  }
 
  /* Fix alignment of week row */
  .fc-row .fc-content-skeleton {
    position: relative !important;
  }
 
  /* Ensure no content is cut off */
  .fc-basic-view .fc-body .fc-row {
    min-height: 80px !important;
  }
 
  /* Hide navigation buttons and toolbar elements */
  .fc-toolbar .fc-left button,
  .fc-toolbar .fc-right button,
  .fc-toolbar .fc-button-group,
  .fc-toolbar .fc-prev-button,
  .fc-toolbar .fc-next-button,
  .fc-toolbar .fc-today-button,
  .fc-toolbar .fc-month-button,
  .fc-toolbar .fc-agendaWeek-button,
  .fc-toolbar .fc-agendaDay-button,
  .fc-toolbar .fc-listWeek-button,
  .fc-toolbar .fc-listMonth-button,
  .fc-toolbar .fc-printButton {
    display: none !important;
  }
 
  /* Override the previous rule to ensure buttons are hidden */
  .fc-toolbar .fc-left,
  .fc-toolbar .fc-right {
    visibility: hidden !important;
    display: none !important;
  }

  /* Center the title and make it the only visible element in the toolbar */
  .fc-toolbar .fc-center {
    display: block !important;
    text-align: center !important;
    margin: 0 auto !important;
    width: 100% !important;
  }
 
  /* Additional rule to ensure all buttons are hidden */
  .fc button {
    display: none !important;
  }
}
  .fc-toolbar .fc-center h2 {
  display: none !important;
}

.print-header {
  font-family: 'Roboto', sans-serif;
  font-size: 1.5rem;
  font-weight: 700;
  margin: 20px 0;
  text-align: center;
  page-break-after: avoid;
}
                        </style>
                    </head>
                    <body>
                     <div class="logo-container">
            <img src="rga.png" alt="RGA Logo" class="logo-image">
        </div>
                        <h2 class="print-header">Calendar Report of the ${moment().format("MMMM YYYY")}</h2>
                        ${$('.calendar-main').html()}
                    </body>
                    </html>
                `;
               
                // Write content to print window
                printWindow.document.write(htmlContent);
                printWindow.document.close();
               
                // Add delay for styles to load
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            });
        });
    </script>
</body>
</html>