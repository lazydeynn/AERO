<?php
session_start();
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: register.php");
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : "";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Verification Code - AERO</title>
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
            <i class="fa-solid fa-envelope text-6xl text-gray-800 drop-shadow-xl"></i>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Verification Code</h1>
        <p class="text-xs text-gray-500 mb-8">Enter the code sent to your email</p>

        <?php if ($error): ?>
            <p class="text-red-500 text-xs mb-4 bg-red-50 p-2 rounded"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="../actions/verify_action.php" method="POST" class="space-y-6">

            <div class="flex justify-center gap-2">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" maxlength="1" id="otp-<?php echo $i; ?>" name="otp[]"
                        class="w-12 h-12 border border-gray-300 rounded-lg text-center text-xl font-bold focus:outline-none focus:border-blue-600 focus:ring-1 focus:ring-blue-600 transition"
                        oninput="moveToNext(this, 'otp-<?php echo $i + 1; ?>')"
                        onkeydown="moveToPrev(event, this, 'otp-<?php echo $i - 1; ?>')"
                        required>
                <?php endfor; ?>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition shadow-lg text-sm">
                Verify
            </button>

        </form>

        <p class="text-[10px] text-gray-400 mt-6">
            Didn't receive an email? Please check the <b>spam</b> or junk folder
        </p>
    </div>

    <script>
        function moveToNext(current, nextFieldID) {
            if (current.value.length >= 1) {
                const next = document.getElementById(nextFieldID);
                if (next) next.focus();
            }
        }

        function moveToPrev(event, current, prevFieldID) {
            if (event.key === "Backspace" && current.value === "") {
                const prev = document.getElementById(prevFieldID);
                if (prev) prev.focus();
            }
        }
    </script>
</body>

</html>