<?php
// Start the session at the very beginning of the file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if the user is not logged in
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit();
}

// NEW CODE: Redirect Project Managers directly to the project workflow
if (isset($_SESSION['login_type']) && $_SESSION['login_type'] == 14 || $_SESSION['login_type'] == 13   || $_SESSION['login_type'] == 8 || $_SESSION['login_type'] == 6  || $_SESSION['login_type'] == 5 || $_SESSION['login_type'] == 4 || $_SESSION['login_type'] == 3 || $_SESSION['login_type'] == 2) {
    header("Location: ./projectworkflow/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<link rel="icon" type="image/x-icon" href="logo.jpg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff0000;
            --secondary-color: #f5ecde;
            --accent-color: #613d2f;
            --text-color: #3D2217;
        }
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #3D2217;
            color: var(--text-color);
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
       
        .navbar {
            background-color: var(--secondary-color) !important;
            box-shadow: 0 1px 4px #f5ecde,
                        0 1px 4px #f5ecde;
            padding: 0.8rem 2rem;
            border-radius: 15px;
            margin: 1rem auto;
            width: 95%;
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid rgba(255, 0, 0, 0.1);
            border: 3px solid var(--text-color);
        }

        .navbar-brand {
            color: var(--primary-color) !important;
            font-weight: 500;
            font-size: 1.8rem;
        }
       
        /* Main content container with border */
        .content-container {
            border: 2px solid var(--secondary-color);
            border-radius: 30px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 90%;
            max-height: 90%;
            background-color: var(--secondary-color);
            position: relative;
            background-color: var(--secondary-color) !important;
            box-shadow: 0 1px 4px #f5ecde,
                        0 1px 4px #f5ecde;
            border: 3px solid var(--text-color);
        }
       
        /* RGA title styling */
        .rga-title {
            font-size: 42px;
            color: var(--primary-color);
            font-weight: 400; /* Changed from 500 to 400 */
            letter-spacing: 1px;
            text-align: center;
            display: block;
            margin-bottom: 2rem;
            margin-top: 30px;
            font-variant: small-caps; /* Extracted from font shorthand */
            font-family: sans-serif; /* Extracted from font shorthand */
            line-height: 1; /* Extracted from font shorthand */
        }
       
        /* Welcome heading styling */
        .welcome-heading {
            font-size: 35px;
            font-weight: 500;
            color: var(--primary-color);
            text-align: center;
           
            margin-top: 120px;
        }
       
        /* Lead text styling */
        .lead {
            color: #3D2217;
            text-align: center;
            font-size: 25px;
            margin-bottom: 3rem;
        }

        /* Modified Button container */
        .button-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            border: 1px solid var(--primary-color);
            border-radius: 8px;
            padding: 6px 10px;
            margin: 5px auto;
            background-color: var(--secondary-color);
            width: fit-content;
            display: inline-flex;  /* Changed from flex */
            padding: 6px 7px;  /* Reduced padding */
            margin: 5px;  /* Added consistent margin */
            font-size: 20px;
        }

        /* Navigation button styling */
        .nav-button {
            background-color: white;
            color: var(--primary-color);
            border: none;
            border-radius: 8px;
            padding: 6px 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
            white-space: nowrap;
            justify-content: center;
        }

        /* Optional: Mobile responsiveness */
        @media (max-width: 768px) {
            .button-container {
                flex-direction: column;
                padding: 0.5rem 1rem;
            }
        }
       
        .nav-button:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            color: var(--secondary-color);
            background-color: var(--text-color);
        }
       
        .nav-button i {
            margin-right: 10px;
            font-size: 1.2rem;
            justify-self: center;
        }
       
        /* Sign out button styling */
        .logout-btn {
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            background-color: transparent;
            border-radius: 6px;
            padding: 0.5rem 1.5rem;
            transition: all 0.3s ease;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
       
        .logout-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
       
        .logout-btn i {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <h1 class="navbar-brand">RGA Dashboard</h1>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <a class="logout-btn" href="../../projectworkflow/ajax.php?action=logout" >
                    <i class="fas fa-sign-out-alt"></i> Sign Out
                </a>
            </div>
        </div>
    </nav>
    
    <!-- Inside the content-container div -->
    <div class="content-container">
        <div class="text-center">
            <h1 class="rga-title">RGA kitchen and laundry maintenance services</h1>
            <h2 class="welcome-heading">Welcome, <?php echo $_SESSION['login_name'] ?? 'Administrator'; ?>!</h2>
            <p class="lead">We're glad to have you here. Let's get things done today!</p>
        </div>

<!-- Inside the Button Containers Row section -->
<div class="d-flex justify-content-center flex-wrap" style="width: 100%; text-align: center;">
    <!-- Project Workflow Container -->
    <div class="button-container" style="margin: 5px;">
        <?php 
        // Debug output
        // echo "User type: " . (isset($_SESSION['login_type']) ? $_SESSION['login_type'] : 'Not set');
        
        // Modified condition to be more inclusive or force display for testing
        if(true || (isset($_SESSION['login_type']) && in_array($_SESSION['login_type'], [1, 2, 3, 4, 8, 13, 14]))): 
        ?>
            <a href="./projectworkflow/index.php" class="nav-button">
                <i class="fas fa-tasks"></i>
                Project Workflow
            </a>
        <?php endif; ?>
    </div>
         

    <!-- Inventory Container -->
    <div class="button-container" style="margin: 5px;">
        <?php if(isset($_SESSION['login_type']) && ($_SESSION['login_type'] == 1 || $_SESSION['login_type'] == 2 || $_SESSION['login_type'] == 5 || $_SESSION['login_type'] == 6)): ?>
            <a href="./imsystem/admin/home.php" class="nav-button">
                <i class="fas fa-box-open"></i>
                Inventory and Equipment
            </a>
        <?php endif; ?>
    </div>
    <!-- Inventory Container -->
    <div class="button-container" style="margin: 5px;">
        <?php if(isset($_SESSION['login_type']) && ($_SESSION['login_type'] == 1 )): ?>
            <a href="./kpisystem/admin/home.php" class="nav-button">
               <i class="fas fa-chart-bar"></i>
                K P I
            </a>
        <?php endif; ?>
    </div>
</div>

    <!-- Bootstrap Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>