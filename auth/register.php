<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: ../pages/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>AERO - Join Us</title>
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

        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Modal Animation */
        .modal {
            transition: opacity 0.25s ease;
        }

        body.modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
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
            <h1 class="text-6xl font-medium leading-tight">Join Us</h1>
            <h2 class="text-6xl font-bold uppercase tracking-wide mb-6">EXPLORE</h2>
            <p class="text-gray-300 text-sm tracking-wide font-light">Create your account and start your journey.</p>
        </div>
        <div></div>
    </div>

    <div class="w-full md:w-1/2 bg-white flex items-center justify-center p-8 overflow-y-auto no-scrollbar">
        <div class="w-full max-w-md">
            <h2 class="text-2xl font-bold mb-6 text-black">Join us now!</h2>

            <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-50 text-red-500 p-3 rounded text-xs mb-4 border-l-4 border-red-500">
                    <?php echo htmlspecialchars($_GET['error']); ?>
                </div>
            <?php endif; ?>

            <form action="../actions/register_action.php" method="POST" onsubmit="return validateForm()" class="space-y-4">

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Name</label>
                    <input type="text" name="fullname" required placeholder="First Name, Last Name" class="w-full border border-gray-300 px-4 py-3 rounded-lg text-sm focus:outline-none focus:border-black transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" required placeholder="sample@gmail.com" class="w-full border border-gray-300 px-4 py-3 rounded-lg text-sm focus:outline-none focus:border-black transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required placeholder="8-16 characters" class="w-full border border-gray-300 px-4 py-3 rounded-lg text-sm focus:outline-none focus:border-black transition pr-10">
                        <i class="fa-regular fa-eye absolute right-3 top-3.5 text-gray-400 cursor-pointer hover:text-black" onclick="togglePassword(this)"></i>
                    </div>
                    <p id="pass-error" class="text-[10px] text-red-500 mt-1 hidden font-medium">Must be 8+ chars & include a number.</p>
                </div>

                <div class="grid grid-cols-1 gap-y-2 text-[10px] text-gray-500 mt-2">
                    <label class="flex items-center">
                        <input type="checkbox" disabled class="mr-2 accent-black w-3 h-3" id="check-num">
                        <span id="label-num">Must contain one number</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" disabled class="mr-2 accent-black w-3 h-3" id="check-len">
                        <span id="label-len">Minimum of 8 characters</span>
                    </label>

                    <label class="flex items-center mt-2 cursor-pointer">
                        <input type="checkbox" name="terms" id="terms" required class="mr-2 accent-black w-3 h-3">
                        <span>I agree to the <button type="button" onclick="toggleModal('terms-modal')" class="font-bold text-black hover:underline ml-1 focus:outline-none">Terms & Privacy</button></span>
                    </label>
                </div>

                <button type="submit" class="w-full bg-black text-white font-bold py-3 rounded-lg mt-4 hover:bg-gray-800 transition shadow-lg">
                    SIGN ME UP!
                </button>

            </form>

            <div class="mt-6 text-center text-xs text-gray-500">
                Already have an account? <a href="login.php" class="font-bold text-black hover:underline">Login</a>
            </div>
        </div>
    </div>

    <div id="terms-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50">

        <div class="modal-overlay absolute w-full h-full bg-gray-900 opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-3xl mx-auto rounded-xl shadow-2xl z-50 overflow-y-auto max-h-[90vh]">

            <div class="modal-content py-4 text-left px-6">
                <div class="flex justify-between items-center pb-3 border-b">
                    <p class="text-xl font-bold text-gray-800">Terms of Service & Privacy Policy</p>
                    <div class="modal-close cursor-pointer z-50" onclick="toggleModal('terms-modal')">
                        <i class="fa-solid fa-xmark text-gray-500 hover:text-black text-xl"></i>
                    </div>
                </div>

                <div class="my-5 text-xs text-gray-600 space-y-4 h-96 overflow-y-auto p-2 bg-gray-50 rounded border border-gray-100">

                    <h3 class="font-bold text-gray-900 text-sm">1. Terms of Use</h3>
                    <p>Welcome to AERO. By accessing our website, you agree to be bound by these Terms of Use, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws.</p>

                    <h3 class="font-bold text-gray-900 text-sm">2. Use License</h3>
                    <p>Permission is granted to temporarily download one copy of the materials (information or software) on AERO's website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>
                    <ul class="list-disc pl-5">
                        <li>Modify or copy the materials;</li>
                        <li>Use the materials for any commercial purpose, or for any public display;</li>
                        <li>Attempt to decompile or reverse engineer any software contained on AERO's website;</li>
                    </ul>

                    <h3 class="font-bold text-gray-900 text-sm">3. Disclaimer</h3>
                    <p>The materials on AERO's website are provided on an 'as is' basis. AERO makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property.</p>

                    <h3 class="font-bold text-gray-900 text-sm">4. Privacy Policy</h3>
                    <p>Your privacy is important to us. It is AERO's policy to respect your privacy regarding any information we may collect from you across our website.</p>
                    <p>We only ask for personal information when we truly need it to provide a service to you. We collect it by fair and lawful means, with your knowledge and consent. We also let you know why we’re collecting it and how it will be used.</p>
                    <p>We don’t share any personally identifying information publicly or with third-parties, except when required to by law.</p>

                    <h3 class="font-bold text-gray-900 text-sm">5. User Data</h3>
                    <p>As a student or instructor, your progress, grades, and uploaded content are stored securely. We do not sell your academic data to advertisers.</p>
                </div>

                <div class="flex justify-end pt-2">
                    <button onclick="acceptTerms()" class="px-6 py-2 bg-black text-white rounded-lg font-bold text-sm hover:bg-gray-800 transition shadow-lg">
                        I Accept
                    </button>
                    <button onclick="toggleModal('terms-modal')" class="ml-3 px-6 py-2 bg-gray-200 text-gray-600 rounded-lg font-bold text-sm hover:bg-gray-300 transition">
                        Close
                    </button>
                </div>
            </div>
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

        document.getElementById('password').addEventListener('input', function() {
            const val = this.value;
            const numCheck = document.getElementById('check-num');
            const lenCheck = document.getElementById('check-len');
            const numLabel = document.getElementById('label-num');
            const lenLabel = document.getElementById('label-len');

            if (/\d/.test(val)) {
                numCheck.checked = true;
                numLabel.classList.add('text-green-600', 'font-bold');
            } else {
                numCheck.checked = false;
                numLabel.classList.remove('text-green-600', 'font-bold');
            }

            if (val.length >= 8) {
                lenCheck.checked = true;
                lenLabel.classList.add('text-green-600', 'font-bold');
            } else {
                lenCheck.checked = false;
                lenLabel.classList.remove('text-green-600', 'font-bold');
            }
        });

        function validateForm() {
            const password = document.getElementById('password').value;
            const hasNumber = /\d/.test(password);
            const hasLength = password.length >= 8;

            if (!hasNumber || !hasLength) {
                document.getElementById('pass-error').classList.remove('hidden');
                document.getElementById('password').classList.add('border-red-500');
                return false;
            }
            return true;
        }

        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            modal.classList.toggle('opacity-0');
            modal.classList.toggle('pointer-events-none');
            document.body.classList.toggle('modal-active');
        }

        function acceptTerms() {
            document.getElementById('terms').checked = true;
            toggleModal('terms-modal');
        }

        const overlay = document.querySelector('.modal-overlay');
        overlay.addEventListener('click', function() {
            toggleModal('terms-modal');
        });
    </script>
</body>

</html>