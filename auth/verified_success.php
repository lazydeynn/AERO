<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verified! - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-white flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md text-center p-6">

        <div class="absolute top-8 left-8 text-3xl font-bold tracking-widest">AERO</div>

        <div class="mb-6 flex justify-center">
            <div class="bg-green-100 p-6 rounded-3xl shadow-sm">
                <i class="fa-solid fa-check text-5xl text-green-500"></i>
            </div>
        </div>

        <h1 class="text-2xl font-bold text-green-500 mb-2">Verified!</h1>
        <p class="text-xs text-gray-500 mb-8">Your email has been verified</p>

        <a href="../pages/dashboard.php" class="block w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg text-sm">
            Enter AERO
        </a>

    </div>
</body>

</html>