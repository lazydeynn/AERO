<?php
// aero/index.php
session_start();

// If already logged in, skip everything and go to dashboard
if (isset($_SESSION['user_id'])) {
    // FIXED PATH: pages/dashboard.php
    header("Location: pages/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Welcome to AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
    <style>
        /* Ensure splash stays visible until redirect */
        .splash-container {
            animation: none;
            /* We handle timing with JS now */
            opacity: 1;
            visibility: visible;
        }

        /* Only animate the circle and logo reveal */
        .invert-circle {
            animation: expandCircle 1.2s cubic-bezier(0.65, 0, 0.35, 1) 1.5s forwards;
        }

        .splash-logo-img {
            animation: logoReveal 0.8s ease-out 0.5s forwards;
        }
    </style>
</head>

<body class="h-screen w-full overflow-hidden bg-white">

    <div class="splash-container">
        <div class="invert-circle"></div>
        <img src="assets/images/logo/aero_white.svg" alt="AERO" class="splash-logo-img">
    </div>

    <script>
        // Redirect to login.php after 3.5 seconds (Animation time)
        setTimeout(function() {
            window.location.href = 'auth/login.php'; // Correct: auth is in root
        }, 3500);
    </script>

</body>

</html>