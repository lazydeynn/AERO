<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$userSql = "SELECT * FROM users WHERE user_id = $user_id";
$user = $conn->query($userSql)->fetch_assoc();
$profilePic = !empty($user['profile_image'])
    ? "../assets/uploads/" . $user['profile_image']
    : "../assets/images/elements/no-profile.png";
$userSkills = !empty($user['skills']) ? explode(',', $user['skills']) : [];
$downSql = "SELECT s.save_id, s.date_saved, t.title, t.topic_id, t.content_description, l.name as language_name
            FROM saved_lessons s
            JOIN topics t ON s.topic_id = t.topic_id
            JOIN languages l ON t.language_id = l.language_id
            WHERE s.user_id = $user_id
            ORDER BY s.date_saved DESC";

$downloads = $conn->query($downSql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Downloads - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css?v=2.9">
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
    </style>
</head>

<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">

    <?php include '../components/navbar.php'; ?>

    <div class="flex flex-1 overflow-hidden">

        <aside class="w-full md:w-80 bg-[#1e1e1e] text-white flex flex-col items-center py-10 px-6 overflow-y-auto z-20 shadow-xl relative hidden md:flex">

            <a href="profile.php" class="absolute top-4 left-4 text-gray-400 hover:text-white transition" title="Back to Profile">
                <i class="fa-solid fa-arrow-left text-lg"></i>
            </a>

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

            <div class="w-full mb-8">
                <div class="group block w-full bg-blue-600 border border-blue-500 rounded-xl p-4 relative overflow-hidden shadow-lg">
                    <div class="flex items-center justify-between relative z-10">
                        <div class="flex items-center space-x-3">
                            <div class="bg-white/20 p-2 rounded-lg text-white">
                                <i class="fa-solid fa-folder-open text-lg"></i>
                            </div>
                            <div class="text-left">
                                <span class="block font-bold text-sm text-white">My Downloads</span>
                                <span class="block text-[10px] text-blue-100">Library Active</span>
                            </div>
                        </div>
                    </div>
                </div>
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

        <main class="flex-1 overflow-y-auto bg-gray-50 no-scrollbar relative p-8 md:p-12">

            <div class="max-w-6xl mx-auto">

                <div class="flex flex-col md:flex-row justify-between items-end mb-8 border-b border-gray-200 pb-6">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Resource Library</h1>
                        <p class="text-sm text-gray-500 mt-1">Access your generated study guides and past quiz results.</p>
                    </div>

                    <div class="relative mt-4 md:mt-0 w-full md:w-64">
                        <input type="text" placeholder="Search files..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:border-black transition bg-white shadow-sm">
                        <i class="fa-solid fa-search absolute left-4 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <?php if ($downloads->num_rows > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php while ($row = $downloads->fetch_assoc()):
                            $iconColor = "text-red-500";
                            $bgIcon = "bg-red-50";
                            if ($row['language_name'] == 'C#') {
                                $iconColor = "text-green-500";
                                $bgIcon = "bg-green-50";
                            }
                            if ($row['language_name'] == 'JavaScript') {
                                $iconColor = "text-yellow-500";
                                $bgIcon = "bg-yellow-50";
                            }
                        ?>

                            <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all duration-300 group hover:-translate-y-1">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-xl flex items-center justify-center text-2xl">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded-md uppercase font-bold tracking-wide">
                                            <?php echo $row['language_name']; ?>
                                        </span>
                                    </div>
                                </div>

                                <h3 class="font-bold text-gray-800 text-sm mb-1 truncate" title="<?php echo $row['title']; ?>">
                                    <?php echo $row['title']; ?>
                                </h3>
                                <p class="text-xs text-gray-400 mb-4">
                                    Saved on <?php echo date("M j, Y", strtotime($row['date_saved'])); ?>
                                </p>

                                <div class="flex items-center justify-between border-t border-gray-50 pt-4 mt-2">
                                    <div class="text-[10px] text-gray-400 font-medium">
                                        Type: <span class="text-gray-700">Lesson PDF</span>
                                    </div>

                                    <a href="view_lesson.php?topic_id=<?php echo $row['topic_id']; ?>" class="text-xs font-bold text-gray-700 flex items-center hover:text-blue-600 transition group-hover:underline">
                                        Read Now <i class="fa-solid fa-book-reader ml-2"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center h-96 text-center">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6 text-gray-300 text-4xl">
                            <i class="fa-regular fa-folder-open"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800">No Downloads Yet</h3>
                        <p class="text-gray-500 text-sm mt-2 max-w-sm mx-auto leading-relaxed">
                            Once you finish quizzes, your study materials and results will automatically appear here for safekeeping.
                        </p>
                        <a href="languages.php" class="mt-8 bg-black text-white px-8 py-3 rounded-full text-sm font-bold shadow-lg hover:bg-gray-800 transition transform hover:scale-105">
                            Start Learning Now
                        </a>
                    </div>
                <?php endif; ?>

            </div>
        </main>
    </div>
</body>

</html>