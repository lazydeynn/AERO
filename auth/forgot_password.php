<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Forgot Password - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl">

        <a href="login.php" class="text-gray-400 hover:text-black text-sm mb-6 inline-block">
            <i class="fa-solid fa-arrow-left mr-2"></i> Back to Login
        </a>

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Forgot Password?</h1>
            <p class="text-xs text-gray-500 mt-2">Enter your email address and we'll send you a link to reset your password.</p>
        </div>

        <?php if (isset($_GET['msg'])): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-3 mb-6 rounded">
                <p class="text-green-600 text-xs font-bold"><?php echo htmlspecialchars($_GET['msg']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-6 rounded">
                <p class="text-red-500 text-xs font-bold"><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        <?php endif; ?>

        <form action="../actions/forgot_password_action.php" method="POST" class="space-y-6">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Email Address</label>
                <input type="email" name="email" required class="w-full border-b border-gray-300 py-2 text-sm focus:outline-none focus:border-black transition-colors" placeholder="juan@example.com">
            </div>

            <button type="submit" class="w-full bg-black text-white font-bold py-3 rounded-full hover:bg-gray-800 transition shadow-lg text-xs tracking-widest uppercase">
                Send Reset Link
            </button>
        </form>

    </div>
</body>

</html>