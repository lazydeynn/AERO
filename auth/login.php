<?php
session_start();
include '../config/db_conn.php';

if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .hero-bg {
            background-color: #1e1e1e;
            background-image: url('../assets/images/elements/3d-bg.png');
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
        }

        .hero-content {
            position: relative;
            z-index: 10;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>

<body class="h-screen w-full overflow-hidden flex font-[Poppins]">

    <div class="hidden md:flex w-1/2 hero-bg flex-col justify-between p-12 text-white">
        <div class="hero-overlay"></div>

        <div class="hero-content">
            <img src="../assets/images/logo/aero_white.svg" alt="AERO" class="h-6 w-auto">
        </div>

        <div class="hero-content mb-20">
            <h1 class="text-6xl font-medium leading-tight">Hello there!</h1>
            <h2 class="text-6xl font-bold uppercase tracking-wide mb-6">WELCOME</h2>
            <p class="text-gray-300 text-sm tracking-wide font-light">Hello there! Ready to continue your journey?</p>
        </div>

        <div></div>
    </div>

    <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8 overflow-y-auto no-scrollbar">
        <div class="w-full max-w-md">

            <div class="mb-10">
                <h2 class="text-3xl font-bold text-gray-900 mb-1">Start Learning Now!</h2>
                <p class="text-gray-500 text-xs">Enter your credentials to access your account.</p>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-6 rounded-r">
                    <p class="text-red-500 text-xs font-bold"><?php echo htmlspecialchars($_GET['error']); ?></p>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['msg'])): ?>
                <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-6 rounded-r">
                    <p class="text-green-600 text-xs font-bold"><?php echo htmlspecialchars($_GET['msg']); ?></p>
                </div>
            <?php endif; ?>

            <form action="../actions/login_action.php" method="POST" class="space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full border border-gray-300 px-4 py-3 rounded-lg text-sm focus:outline-none focus:border-black transition" placeholder="sample@gmail.com">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="w-full border border-gray-300 px-4 py-3 rounded-lg text-sm focus:outline-none focus:border-black transition pr-10" placeholder="••••••••">
                        <i class="fa-regular fa-eye absolute right-3 top-3.5 text-gray-400 cursor-pointer hover:text-black transition" onclick="togglePassword(this)"></i>
                    </div>

                    <div class="flex justify-end mt-2">
                        <a href="forgot_password.php" class="text-[10px] font-bold text-gray-400 hover:text-black hover:underline transition">Forgot Password?</a>
                    </div>
                </div>

                <button type="submit" class="w-full bg-black text-white font-bold py-3 rounded-lg mt-4 hover:bg-gray-800 transition shadow-lg text-sm tracking-wide">
                    LOGIN
                </button>

                <div class="text-center pt-6 space-y-4">
                    <div class="text-xs text-gray-500">
                        Don't have an account? <a href="register.php" class="font-bold text-black hover:underline">Sign Up</a>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        function togglePassword(icon) {
            const passField = document.getElementById('password');
            if (passField.type === "password") {
                passField.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passField.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>