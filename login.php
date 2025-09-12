<?php 
session_start();
include('./projectworkflow/db_connect.php');
ob_start();

// Redirect logged-in users to the dashboard
if (isset($_SESSION['login_id'])) {
    header("Location: Hello_there.php"); // Replace "index.php" with your dashboard or home page
    exit();
}

// Set headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Load system settings if not already set
if (!isset($_SESSION['system'])) {
    $system = $conn->query("SELECT * FROM system_settings")->fetch_array();
    foreach ($system as $k => $v) {
        $_SESSION['system'][$k] = $v;
    }
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">
<?php include './projectworkflow/header.php' ?>
<head>
<link rel="icon" type="image/x-icon" href="logo.jpg">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;  
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5ecde;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
            position: relative;
        }
        
        /* Moving geometry background */
        .geometry-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }
        
        .geometry {
            position: absolute;
            opacity: 0.15;
        }
        
        .triangle {
            width: 0;
            height: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            border-bottom: 86px solid #3D2217;
            animation: float 15s infinite ease-in-out;
        }
        
        .square {
            width: 70px;
            height: 70px;
            background-color: #3D2217;
            transform: rotate(45deg);
            animation: rotate 20s infinite linear, float 18s infinite ease-in-out;
        }
        
        .circle-bg {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 5px solid #3D2217;
            animation: pulse 10s infinite alternate, float 25s infinite ease-in-out;
        }
        
        .hexagon {
            width: 80px;
            height: 46px;
            background-color: #3D2217;
            position: relative;
            animation: rotate 30s infinite linear, float 20s infinite ease-in-out;
        }
        
        .hexagon:before,
        .hexagon:after {
            content: "";
            position: absolute;
            width: 0;
            height: 0;
            border-left: 40px solid transparent;
            border-right: 40px solid transparent;
        }
        
        .hexagon:before {
            border-bottom: 23px solid #3D2217;
            top: -23px;
        }
        
        .hexagon:after {
            border-top: 23px solid #3D2217;
            bottom: -23px;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
            }
            25% {
                transform: translateY(-30px) translateX(20px);
            }
            50% {
                transform: translateY(20px) translateX(-20px);
            }
            75% {
                transform: translateY(-10px) translateX(30px);
            }
        }
        
        @keyframes rotate {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2);
            }
        }
        .login-container {
  width: 90%;
  max-width: 1000px;
  height: 600px;
  display: flex;
  overflow: hidden;
  border-radius: 20px;
  position: relative;
  box-shadow: 0 40px 80px rgba(244, 10, 10, 0.2);
  transform: translateY(-5px);
  transition: all 0.3s ease;
  z-index: 1;
  backdrop-filter: blur(10px);
  border: 2px solid #3D2217;
}



        .welcome-text{
         color: #f5ecde;
        }
        .sign-in-header{
          color: #;
        }
        .left-panel {
            width: 50%;
            background: linear-gradient(135deg,#3D2217 0%,  #3D2217 100%);
            padding: 50px;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .welcome-text {
            position: relative;
            z-index: 2;
            margin-top: 100px;
        }
        
        .welcome-text h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        .welcome-text p {
            font-size: 1.2rem;
            opacity: 0.9;
            line-height: 1.5;
        }
        
        .decorative-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.5;
            z-index: 1;
        }
        
        .circle {
            position: absolute;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .circle-1 {
            width: 250px;
            height: 250px;
            top: 50%;
            left: 30%;
            transform: translate(-50%, -50%);
        }
        
        .circle-2 {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
        }
        
        .plus {
            position: absolute;
            color: rgba(255, 255, 255, 0.2);
            font-size: 25px;
        }
        
        .plus-1 {
            top: 80px;
            left: 270px;
        }
        
        .plus-2 {
            bottom: 80px;
            left: 400px;
        }
        
        .dot {
            position: absolute;
            width: 15px;
            height: 15px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
        }
        
        .dot-1 {
            top: 160px;
            left: 490px;
        }
        
        .dot-2 {
            bottom: 120px;
            left: 160px;
        }
        
        .wave {
            position: absolute;
            stroke: rgba(255, 255, 255, 0.2);
            stroke-width: 1;
            fill: none;
        }
        
        .wave-1 {
            top: 50px;
            left: 50px;
            width: 100px;
        }
        
        .wave-2 {
            bottom: 50px;
            right: 50px;
            width: 150px;
        }
        
        .right-panel {
            width: 50%;
            background-color: #f5ecde;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-left: 2px  #f5ecde;
        }
        
        .sign-in-header {
            margin-bottom: 40px;
        }
        
        .sign-in-header h2 {
            font-size: 2.5rem;
            color: #444;
            font-weight: 600;
        }
        
        .input-group {
            margin-bottom: 25px;
            position: relative;
        }
        
        
        .input-field {
            width: 100%;
            padding: 15px 15px 15px 45px;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 16px;
           
            outline: none;
            background-color: #f5ecde;
            color: #3D2217;
            border-color: #3D2217;
        }
        
        .input-field:focus {
            border-color: #3D2217;
        }
        
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            
        }
        
        .checkbox-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background-color: #f5ecde;
        }
        
        .remember-me {
    display: flex;
    align-items: center;
    position: relative;
    background-color: #f5ecde;
}

.remember-me input[type="checkbox"] {
    opacity: 0;
    position: absolute;
    cursor: pointer;
    height: 0;
    width: 0;
    background-color: #f5ecde;
}

.remember-me label {
    padding-left: 30px;
    cursor: pointer;
    color: #666;
    position: relative;
    background-color: #f5ecde;
}

.remember-me label:before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 18px;
    height: 18px;
    border: 2px solid #ddd;
    border-radius: 4px;
    background-color: #fff;
    transition: all 0.2s ease;
}

.remember-me input[type="checkbox"]:checked + label:before {
    background-color: #f5ecde;
    border-color: #3D2217;
}

.remember-me input[type="checkbox"]:checked + label:after {
    content: '';
    position: absolute;
    left: 6px;
    top: 50%;
    transform: translateY(-60%) rotate(45deg);
    width: 6px;
    height: 10px;
    border: solid #3D2217;
    border-width: 0 2px 2px 0;
    background-color: #f5ecde;
}

.remember-me input[type="checkbox"]:focus + label:before {
    box-shadow: 0 0 0 3px rgba(61, 34, 23, 0.2);
}

.remember-me:hover label:before {
    border-color: #3D2217;
}
        
        .forgot-password {
            color: #666;
            text-decoration: none;
        }
        
        .forgot-password:hover {
            text-decoration: underline;
            color: #8257E5;
        }
        
        .sign-in-btn {
    background-color: #3D2217;
    color: #f5ecde;
    padding: 15px;
    border-radius: 50px;
    cursor: pointer;
    font-size: 16px;
    font-weight: 600;
    transition: all 0.3s;
    width: 100%;
    margin-bottom: 20px;
    border: 2px solid #f5ecde; /* Added 'solid' here */
}

.sign-in-btn:hover {
    background-color: #f5ecde;
    color: #3D2217;
    border: 1px solid #3D2217; /* Added 'solid' here */
}
        
        .create-account {
            text-align: center;
            font-size: 14px;
            color: #666;
        }
        
        .create-account a {
            color: #8257E5;
            text-decoration: none;
            font-weight: 600;
        }
        
        .create-account a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-size: 14px;
        }
        
        .alert-danger {
            background-color: #fde8e8;
            color: #e53e3e;
            border: 1px solid #f8d7da;
        }
        
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                height: auto;
            }
            
            .left-panel, .right-panel {
                width: 100%;
                padding: 30px;
            }
            
            .left-panel {
                padding-bottom: 50px;
                padding-top: 50px;
            }
            
            .welcome-text {
                margin-top: 20px;
            }
            
            .welcome-text h1 {
                font-size: 2.5rem;
            }
            
            .geometry {
                opacity: 0.08;
            }
        }
        #icon
        {
          color: #3D2217;
        }
      ::placeholder{
        color: #3D2217;
      }
    </style>
    <style>

.toggle-password {
    position: absolute;
    right: 24px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #3D2217;
    font-size: 20px;
}

</style>

    
</head>
<body>
    <!-- Moving geometry background -->
    <div class="geometry-background">
        <div class="geometry triangle" style="top: 15%; left: 10%;"></div>
        <div class="geometry square" style="top: 70%; left: 20%;"></div>
        <div class="geometry circle-bg" style="top: 25%; left: 85%;"></div>
        <div class="geometry triangle" style="top: 80%; left: 80%;"></div>
        <div class="geometry square" style="top: 10%; left: 55%;"></div>
        <div class="geometry hexagon" style="top: 50%; left: 70%;"></div>
        <div class="geometry circle-bg" style="top: 60%; left: 30%;"></div>
        <div class="geometry triangle" style="top: 40%; left: 40%;"></div>
    </div>

    <div class="login-container">
        <div class="left-panel">
            <div class="decorative-elements">
                <!-- Wavy lines on top left -->
                <svg class="wave wave-1" viewBox="0 0 100 30">
                    <path d="M0,15 Q10,5 20,15 T40,15 T60,15 T80,15 T100,15" />
                    <path d="M0,20 Q10,10 20,20 T40,20 T60,20 T80,20 T100,20" />
                    <path d="M0,25 Q10,15 20,25 T40,25 T60,25 T80,25 T100,25" />
                </svg>
                
                <!-- Circles -->
                <div class="circle circle-1"></div>
                <div class="circle circle-2"></div>
                
                <!-- Plus signs -->
                <div class="plus plus-1">+</div>
                <div class="plus plus-2">+</div>
                
                <!-- Dots -->
                <div class="dot dot-1"></div>
                <div class="dot dot-2"></div>
                
                <!-- Wavy lines on bottom right -->
                <svg class="wave wave-2" viewBox="0 0 150 50">
                    <path d="M0,25 Q15,10 30,25 T60,25 T90,25 T120,25 T150,25" />
                    <path d="M0,30 Q15,15 30,30 T60,30 T90,30 T120,30 T150,30" />
                    <path d="M0,35 Q15,20 30,35 T60,35 T90,35 T120,35 T150,35" />
                    <path d="M0,40 Q15,25 30,40 T60,40 T90,40 T120,40 T150,40" />
                    <path d="M0,45 Q15,30 30,45 T60,45 T90,45 T120,45 T150,45" />
                </svg>
            </div>
            
            <div class="welcome-text">
                <h1>Welcome!</h1>
                <p>You can sign in to access with your account.</p>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="sign-in-header">
            <h2 style="color: #3D2217;">Sign In</h2>

            </div>
            
            <form action="" id="login-form">
                <div class="input-group">
                    <i class="fas fa-user input-icon" id="icon"></i>
                    <input type="text" class="input-field" name="email" required placeholder="Username">
                </div>
                <div class="input-group">
    <i class="fas fa-lock input-icon" id="icon"></i>
    <input type="password" class="input-field" name="password" id="password-field" required placeholder="Password">
    <i class="fas fa-eye toggle-password" id="togglePassword" style="cursor: pointer; margin-left: -30px;"></i>
</div>
                <div class="checkbox-group">
                    <div class="remember-me">
                        <input style ="color: #f5ecde"type="checkbox" id="remember" name="remember">
                        <label for="remember" style="color: #3D2217;">Remember me</label>
                    </div>
                   
                </div>
                
                <button type="submit" class="sign-in-btn">Sign In</button>
                
            
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            $('#login-form').submit(function(e){
                e.preventDefault();
                // Show loading state
                $('.sign-in-btn').text('Signing in...').prop('disabled', true);
                
                if ($(this).find('.alert-danger').length > 0) {
                    $(this).find('.alert-danger').remove();
                }
                
                $.ajax({
                    url: './projectworkflow/ajax.php?action=login',
                    method: 'POST',
                    data: $(this).serialize(),
                    error: err => {
                        console.log(err);
                        $('.sign-in-btn').text('Sign In').prop('disabled', false);
                    },
                    success: function(resp){
                        if (resp == 1) {
                            location.href = 'Hello_there.php'; // Redirect after successful login
                        } else {
                            $('#login-form').prepend('<div class="alert alert-danger">Username or password is incorrect.</div>');
                            $('.sign-in-btn').text('Sign In').prop('disabled', false);
                        }
                    }
                });
            });
        });
        
        // Animation for decorative elements
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.plus, .dot');
            
            elements.forEach(element => {
                // Create subtle floating animation
                setInterval(() => {
                    const randomY = Math.random() * 10 - 5;
                    const randomX = Math.random() * 10 - 5;
                    
                    element.style.transform = `translate(${randomX}px, ${randomY}px)`;
                    element.style.transition = 'transform 3s ease-in-out';
                }, 3000);
            });
            
            // Add dynamic geometry creation
            setInterval(() => {
                createRandomGeometry();
            }, 8000);
            
            function createRandomGeometry() {
                const shapes = ['triangle', 'square', 'circle-bg', 'hexagon'];
                const randomShape = shapes[Math.floor(Math.random() * shapes.length)];
                
                const geomBg = document.querySelector('.geometry-background');
                const newGeom = document.createElement('div');
                newGeom.classList.add('geometry');
                newGeom.classList.add(randomShape);
                
                // Random position
                newGeom.style.top = Math.random() * 100 + '%';
                newGeom.style.left = Math.random() * 100 + '%';
                newGeom.style.opacity = '0';
                
                geomBg.appendChild(newGeom);
                
                // Fade in
                setTimeout(() => {
                    newGeom.style.opacity = '0.15';
                    newGeom.style.transition = 'opacity 2s ease-in-out';
                }, 100);
                
                // Remove after animation
                setTimeout(() => {
                    newGeom.style.opacity = '0';
                    setTimeout(() => {
                        geomBg.removeChild(newGeom);
                    }, 2000);
                }, 15000);
            }
        });
        // Update the geometry-background HTML to include more shapes
document.addEventListener('DOMContentLoaded', function() {
    const geometryBackground = document.querySelector('.geometry-background');
    
    // Clear existing shapes
    geometryBackground.innerHTML = '';
    
    // Create 30 initial shapes
    for (let i = 0; i < 30; i++) {
        createRandomGeometry(true);
    }
    
    // Animation for decorative elements
    const elements = document.querySelectorAll('.plus, .dot');
    
    elements.forEach(element => {
        // Create subtle floating animation
        setInterval(() => {
            const randomY = Math.random() * 10 - 5;
            const randomX = Math.random() * 10 - 5;
            
            element.style.transform = `translate(${randomX}px, ${randomY}px)`;
            element.style.transition = 'transform 3s ease-in-out';
        }, 3000);
    });
    
    // Add dynamic geometry creation - replace old shapes occasionally
    setInterval(() => {
        createRandomGeometry();
    }, 5000);
    
    function createRandomGeometry(isInitial = false) {
        const shapes = ['triangle', 'square', 'circle-bg', 'hexagon'];
        const randomShape = shapes[Math.floor(Math.random() * shapes.length)];
        
        const geomBg = document.querySelector('.geometry-background');
        const newGeom = document.createElement('div');
        newGeom.classList.add('geometry');
        newGeom.classList.add(randomShape);
        
        // Random position
        newGeom.style.top = Math.random() * 100 + '%';
        newGeom.style.left = Math.random() * 100 + '%';
        newGeom.style.opacity = '0';
        
        // Add hover effect
        newGeom.addEventListener('mouseenter', function() {
            flyAway(this);
        });
        
        geomBg.appendChild(newGeom);
        
        // Fade in
        setTimeout(() => {
            newGeom.style.opacity = '0.15';
            newGeom.style.transition = 'opacity 2s ease-in-out, transform 1.5s ease-out';
        }, isInitial ? Math.random() * 2000 : 100);
        
        // Remove after animation (for non-initial shapes to maintain around 30 shapes)
        if (!isInitial) {
            setTimeout(() => {
                newGeom.style.opacity = '0';
                setTimeout(() => {
                    if (geomBg.contains(newGeom)) {
                        geomBg.removeChild(newGeom);
                    }
                }, 2000);
            }, 20000);
        }
    }
    
    function flyAway(element) {
        // Determine direction to fly (random)
        const directionX = Math.random() > 0.5 ? 1 : -1;
        const directionY = Math.random() > 0.5 ? 1 : -1;
        
        // Calculate distance to fly
        const distanceX = directionX * (Math.random() * 500 + 300);
        const distanceY = directionY * (Math.random() * 500 + 300);
        
        // Apply transform
        element.style.transform = `translate(${distanceX}px, ${distanceY}px) rotate(${Math.random() * 720 - 360}deg)`;
        element.style.transition = 'transform 1.5s ease-out, opacity 1s ease-out';
        element.style.opacity = '0';
        
        // Remove element after animation
        setTimeout(() => {
            if (element.parentNode) {
                element.parentNode.removeChild(element);
                // Create a new shape to replace the one that flew away
                createRandomGeometry(true);
            }
        }, 1500);
    }
});

    const togglePassword = document.getElementById("togglePassword");
    const passwordField = document.getElementById("password-field");

    togglePassword.addEventListener("click", function () {
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });


    </script>
</body>
</html>