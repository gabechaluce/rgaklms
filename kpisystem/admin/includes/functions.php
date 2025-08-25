<?php
// Add this function to your includes/functions.php or similar file
function display_session_messages() {
    // Handle error messages
    if (isset($_SESSION['error'])) {
        echo '<div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-warning"></i> Error!</h4>
                <ul>';
        
        // Ensure $_SESSION['error'] is an array
        $errors = is_array($_SESSION['error']) ? $_SESSION['error'] : [$_SESSION['error']];
        
        foreach ($errors as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo '</ul></div>';
        unset($_SESSION['error']);
    }

    // Handle success messages
    if (isset($_SESSION['success'])) {
        echo '<div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-check"></i> Success!</h4>
                ' . htmlspecialchars($_SESSION['success']) . '
              </div>';
        unset($_SESSION['success']);
    }

    // Handle warning messages
    if (isset($_SESSION['warning'])) {
        echo '<div class="alert alert-warning alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-exclamation-triangle"></i> Warning!</h4>
                ' . htmlspecialchars($_SESSION['warning']) . '
              </div>';
        unset($_SESSION['warning']);
    }

    // Handle info messages
    if (isset($_SESSION['info'])) {
        echo '<div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> Info!</h4>
                ' . htmlspecialchars($_SESSION['info']) . '
              </div>';
        unset($_SESSION['info']);
    }
}

// Usage in your pages:
// <?php display_session_messages(); ?>