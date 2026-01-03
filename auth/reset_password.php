<?php
if (!isset($_GET['email']) || !isset($_GET['token'])) {
    header("Location: login.php");
    exit;
}

$email = $_GET['email'];
$token = $_GET['token'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reset Password - AERO</title>
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

        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">New Password</h1>
            <p class="text-xs text-gray-500 mt-2">Create a strong password for your account.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-6 rounded">
                <p class="text-red-500 text-xs font-bold"><?php echo htmlspecialchars($_GET['error']); ?></p>
            </div>
        <?php endif; ?>

        <form action="../actions/reset_password_action.php" method="POST" onsubmit="return validateReset()" class="space-y-6">

            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">New Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" required class="w-full border-b border-gray-300 py-2 text-sm focus:outline-none focus:border-black transition-colors" placeholder="Min 8 chars, 1 number">
                    <i class="fa-regular fa-eye absolute right-0 top-3 text-gray-400 cursor-pointer hover:text-black transition text-xs" onclick="togglePassword('password', this)"></i>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Confirm Password</label>
                <div class="relative">
                    <input type="password" name="confirm_password" id="confirm_password" required class="w-full border-b border-gray-300 py-2 text-sm focus:outline-none focus:border-black transition-colors" placeholder="Re-type password">
                    <i class="fa-regular fa-eye absolute right-0 top-3 text-gray-400 cursor-pointer hover:text-black transition text-xs" onclick="togglePassword('confirm_password', this)"></i>
                </div>
                <p id="match-error" class="text-[10px] text-red-500 mt-1 hidden font-bold">Passwords do not match</p>
            </div>

            <button type="submit" class="w-full bg-black text-white font-bold py-3 rounded-full hover:bg-gray-800 transition shadow-lg text-xs tracking-widest uppercase">
                Update Password
            </button>
        </form>

    </div>

    <script>
        function togglePassword(fieldId, icon) {
            const passField = document.getElementById(fieldId);
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

        function validateReset() {
            const p1 = document.getElementById('password').value;
            const p2 = document.getElementById('confirm_password').value;
            const errorText = document.getElementById('match-error');

            if (p1.length < 8 || !/\d/.test(p1)) {
                alert("Password must be at least 8 characters and contain a number.");
                return false;
            }

            if (p1 !== p2) {
                errorText.classList.remove('hidden');
                return false;
            }

            errorText.classList.add('hidden');
            return true;
        }
    </script>
</body>

</html>