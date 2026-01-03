<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $bio = $_POST['bio'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $gender = $_POST['gender'];
    $skills = $_POST['skills'];

    $picSqlPart = "";
    if (!empty($_FILES['profile_image']['name'])) {
        $picName = time() . '_' . $_FILES['profile_image']['name']; // Unique name
        $target = "../assets/uploads/" . $picName;

        if (!is_dir("../assets/uploads/")) {
            mkdir("../assets/uploads/", 0777, true);
        }

        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target)) {
            $picSqlPart = ", profile_image='$picName'";
        }
    }

    $bannerSqlPart = "";
    if (!empty($_FILES['profile_banner']['name'])) {
        $bannerName = time() . '_' . $_FILES['profile_banner']['name']; // Unique name
        $target = "../assets/uploads/" . $bannerName;

        if (move_uploaded_file($_FILES['profile_banner']['tmp_name'], $target)) {
            $bannerSqlPart = ", profile_banner='$bannerName'";
        }
    }

    $sql = "UPDATE users SET bio=?, phone=?, location=?, gender=?, skills=? $picSqlPart $bannerSqlPart WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $bio, $phone, $location, $gender, $skills, $user_id);

    if ($stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}

$userSql = "SELECT * FROM users WHERE user_id = $user_id";
$user = $conn->query($userSql)->fetch_assoc();

$profilePic = !empty($user['profile_image'])
    ? "../assets/uploads/" . $user['profile_image']
    : "../assets/images/elements/no-profile.png";

$profileBanner = !empty($user['profile_banner'])
    ? "../assets/uploads/" . $user['profile_banner']
    : "../assets/images/elements/no-banner.png";

$userSkills = !empty($user['skills']) ? explode(',', $user['skills']) : [];
$totalQuizzes = $conn->query("SELECT COUNT(*) as count FROM generated_quizzes WHERE user_id = $user_id AND score IS NOT NULL")->fetch_assoc()['count'];
$avgData = $conn->query("SELECT AVG((score / total_items) * 100) as avg FROM generated_quizzes WHERE user_id = $user_id AND score IS NOT NULL")->fetch_assoc();
$avgScore = round($avgData['avg'] ?? 0);
$timeData = $conn->query("SELECT SUM(duration_seconds) as total_seconds FROM generated_quizzes WHERE user_id = $user_id")->fetch_assoc();
$totalMinutes = ceil(($timeData['total_seconds'] ?? 0) / 60);

$langSql = "SELECT DISTINCT l.name, l.description, l.language_id 
            FROM generated_quizzes q 
            JOIN topics t ON q.topic_id = t.topic_id 
            JOIN languages l ON t.language_id = l.language_id 
            WHERE q.user_id = $user_id";
$studiedLangs = $conn->query($langSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css?v=2.8">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .no-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .no-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .no-scrollbar::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 4px;
        }

        .modal {
            transition: opacity 0.25s ease;
        }

        body.modal-active {
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">

    <?php include '../components/navbar.php'; ?>

    <div class="flex flex-1 overflow-hidden">

        <aside class="w-full md:w-80 bg-[#1e1e1e] text-white flex flex-col items-center py-10 px-6 overflow-y-auto z-20 shadow-xl relative">

            <button onclick="toggleModal('modal-id')" class="absolute top-4 right-4 text-gray-400 hover:text-white transition" title="Edit Profile">
                <i class="fa-solid fa-pen-to-square text-lg"></i>
            </button>

            <div class="w-32 h-32 rounded-full p-1 bg-white mb-4 shadow-lg overflow-hidden">
                <img src="<?php echo $profilePic; ?>" alt="Profile" class="w-full h-full rounded-full object-cover">
            </div>

            <p class="text-xs text-gray-400 mb-6 text-center italic px-4">
                <?php echo !empty($user['bio']) ? htmlspecialchars($user['bio']) : "No bio added yet."; ?>
            </p>

            <h2 class="text-xl font-bold text-center"><?php echo htmlspecialchars($user['fullname']); ?></h2>
            <p class="text-sm text-gray-400 mb-8">Student</p>

            <div class="w-full space-y-4 text-sm text-gray-300 mb-8 px-2">
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-phone w-5 text-center text-gray-500"></i>
                    <span><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : "-"; ?></span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-location-dot w-5 text-center text-gray-500"></i>
                    <span><?php echo !empty($user['location']) ? htmlspecialchars($user['location']) : "-"; ?></span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-user w-5 text-center text-gray-500"></i>
                    <span><?php echo !empty($user['gender']) ? htmlspecialchars($user['gender']) : "-"; ?></span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fa-solid fa-envelope w-5 text-center text-gray-500"></i>
                    <span class="truncate text-xs"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
            </div>

            <div class="w-full mb-9">
                <a href="downloads.php" class="group block w-full bg-[#2a2a2a] hover:bg-[#333] border border-gray-700 rounded-xl p-4 transition-all duration-300 transform hover:scale-[1.02] relative overflow-hidden">
                    <div class="absolute right-0 top-0 h-full w-1 bg-blue-500 group-hover:w-full transition-all duration-500 opacity-10"></div>
                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center space-x-3">
                            <div class="bg-blue-500/20 p-2 rounded-lg text-blue-400 group-hover:text-blue-300">
                                <i class="fa-solid fa-folder-arrow-down text-lg"></i>
                            </div>
                            <div class="text-left">
                                <span class="block font-bold text-sm text-white">My Downloads</span>
                                <span class="block text-[10px] text-gray-400">Access your PDF library</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-chevron-right text-xs text-gray-500 group-hover:text-white transition"></i>
                    </div>
                </a>
            </div>

            <div class="w-full px-2">
                <h3 class="text-xs font-bold text-gray-500 mb-3 uppercase tracking-wider">Skills</h3>
                <div class="flex flex-wrap gap-2">
                    <?php if (!empty($userSkills)): ?>
                        <?php foreach ($userSkills as $skill): ?>
                            <span class="bg-[#2d2d2d] border border-gray-700 text-gray-300 text-[10px] px-3 py-1 rounded-full">
                                <?php echo htmlspecialchars(trim($skill)); ?>
                            </span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-[10px] text-gray-500 italic">No skills added yet.</span>
                    <?php endif; ?>
                </div>
            </div>

        </aside>

        <main class="flex-1 overflow-y-auto bg-gray-50 no-scrollbar relative p-0">

            <div class="w-full h-72 md:h-80 relative bg-black">
                <img src="<?php echo $profileBanner; ?>" alt="Profile Banner" class="w-full h-full object-cover opacity-90">
                <div class="absolute inset-0 bg-gradient-to-t from-gray-50 via-transparent to-transparent"></div>
            </div>

            <div class="max-w-6xl mx-auto px-8 md:px-12 -mt-10 relative z-10 pb-20">

                <div class="mb-16">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Achievements</h2>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Lifetime Stats</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-bolt"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-gray-900"><?php echo $totalQuizzes; ?></h3>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-wide">Quizzes Taken</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                            <div class="w-14 h-14 bg-green-50 text-green-600 rounded-xl flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-bullseye"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-gray-900"><?php echo $avgScore; ?>%</h3>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-wide">Average Score</p>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                            <div class="w-14 h-14 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-hourglass-half"></i>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-gray-900"><?php echo $totalMinutes; ?><span class="text-sm text-gray-400 ml-1">min</span></h3>
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-wide">Time Spent</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Languages Studied</h2>
                        <a href="languages.php" class="text-xs font-bold text-blue-600 hover:underline">View All Courses</a>
                    </div>

                    <?php if ($studiedLangs->num_rows > 0): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <?php while ($lang = $studiedLangs->fetch_assoc()):
                                $border = "border-l-4 border-gray-400";
                                if ($lang['name'] == 'Python') $border = "border-l-4 border-[#ff4b4b]";
                                if ($lang['name'] == 'C#') $border = "border-l-4 border-[#00b894]";
                                if ($lang['name'] == 'JavaScript') $border = "border-l-4 border-[#e1b12c]";
                            ?>
                                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition <?php echo $border; ?>">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-800 mb-1"><?php echo $lang['name']; ?></h3>
                                            <p class="text-xs text-gray-500 line-clamp-2 mb-4"><?php echo $lang['description']; ?></p>
                                        </div>
                                        <i class="fa-solid fa-code text-gray-200 text-2xl"></i>
                                    </div>
                                    <a href="topic_list.php?lang_id=<?php echo $lang['language_id']; ?>" class="text-xs font-bold text-black border-b border-black pb-0.5 hover:text-gray-600 hover:border-gray-600 transition">Continue Learning</a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-white p-10 rounded-xl border border-dashed border-gray-300 text-center">
                            <p class="text-gray-500 mb-4">You haven't started any languages yet.</p>
                            <a href="languages.php" class="bg-black text-white px-6 py-2 rounded-full text-sm font-bold hover:bg-gray-800 transition">Browse Languages</a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <div class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-50" id="modal-id">
        <div class="modal-overlay absolute w-full h-full bg-black opacity-50"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-lg mx-auto rounded-2xl shadow-lg z-50 overflow-y-auto max-h-[90vh]">
            <div class="modal-content py-6 text-left px-6">
                <div class="flex justify-between items-center pb-3 border-b border-gray-100 mb-4">
                    <p class="text-xl font-bold text-gray-800">Edit Profile Details</p>
                    <div class="modal-close cursor-pointer z-50 p-2 hover:bg-gray-100 rounded-full" onclick="toggleModal('modal-id')">
                        <i class="fa-solid fa-xmark"></i>
                    </div>
                </div>

                <form method="POST" enctype="multipart/form-data">
                    <div class="space-y-4">

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Profile Picture</label>
                                <input type="file" name="profile_image" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-black hover:file:bg-gray-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Banner Image</label>
                                <input type="file" name="profile_banner" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-black hover:file:bg-gray-200">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Bio</label>
                            <textarea name="bio" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-black" placeholder="Tell us about yourself..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Phone Number</label>
                                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-black">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Location</label>
                                <input type="text" name="location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-black">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Gender</label>
                            <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-black">
                                <option value="Male" <?php if (($user['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if (($user['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if (($user['gender'] ?? '') == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Skills (comma separated)</label>
                            <input type="text" name="skills" value="<?php echo htmlspecialchars($user['skills'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-black" placeholder="e.g. Python, UI/UX, Data Science">
                            <p class="text-[10px] text-gray-400 mt-1">Separate each skill with a comma.</p>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-6 border-t border-gray-100 mt-6">
                        <button type="button" onclick="openDeleteConfirmation()" class="text-xs font-bold text-red-500 hover:text-red-700 transition flex items-center">
                            <i class="fa-solid fa-trash-can mr-2"></i> Delete Account
                        </button>

                        <div class="flex space-x-2">
                            <button type="button" onclick="toggleModal('modal-id')" class="px-4 py-2 bg-gray-100 rounded-lg text-gray-500 text-sm font-medium hover:bg-gray-200 transition">Cancel</button>
                            <button type="submit" name="update_profile" class="px-4 py-2 bg-black rounded-lg text-white text-sm font-medium hover:bg-gray-800 transition">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="delete-modal" class="modal opacity-0 pointer-events-none fixed w-full h-full top-0 left-0 flex items-center justify-center z-[60]">

        <div class="modal-overlay absolute w-full h-full bg-black opacity-60"></div>

        <div class="modal-container bg-white w-11/12 md:max-w-md mx-auto rounded-2xl shadow-2xl z-50 overflow-hidden transform transition-all">

            <div class="bg-red-50 p-6 flex justify-center">
                <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center text-red-600 animate-pulse">
                    <i class="fa-solid fa-triangle-exclamation text-4xl"></i>
                </div>
            </div>

            <div class="p-8 text-center">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Delete Account?</h3>
                <p class="text-sm text-gray-500 leading-relaxed mb-6">
                    This action is <span class="text-red-600 font-bold uppercase">permanent</span>. All your quiz scores, progress, and profile data will be erased from AERO forever.
                </p>

                <div class="flex flex-col space-y-3">
                    <a href="../actions/delete_account_action.php"
                        class="w-full bg-red-600 text-white font-bold py-3 rounded-xl hover:bg-red-700 transition shadow-lg text-sm tracking-wide">
                        YES, DELETE EVERYTHING
                    </a>
                    <button onclick="toggleModal('delete-modal')"
                        class="w-full bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition text-sm">
                        NO, KEEP MY ACCOUNT
                    </button>
                </div>
            </div>

            <div class="bg-gray-50 py-4 px-8 text-center border-t border-gray-100">
                <p class="text-[10px] text-gray-400 uppercase tracking-widest font-bold">Aero Security Protocol</p>
            </div>
        </div>
    </div>
    <script>
        function toggleModal(modalID) {
            const modal = document.getElementById(modalID);
            modal.classList.toggle("opacity-0");
            modal.classList.toggle("pointer-events-none");
            document.body.classList.toggle("modal-active");
        }

        function openDeleteConfirmation() {
            toggleModal('modal-id');
            setTimeout(() => {
                toggleModal('delete-modal');
            }, 300);
        }
    </script>
</body>

</html>