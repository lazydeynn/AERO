<?php
include '../config/db_conn.php';
include 'check_admin.php';

$studentCount = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'];
$topicCount = $conn->query("SELECT COUNT(*) as c FROM topics")->fetch_assoc()['c'];
$quizCount = $conn->query("SELECT COUNT(*) as c FROM generated_quizzes")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex">

    <?php include('../components/admin_sidebar.php') ?>

    <main class="ml-64 flex-1 p-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">System Overview</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Total Users</p>
                        <h2 class="text-4xl font-bold text-gray-800 mt-2"><?php echo $studentCount; ?></h2>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-xl">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Active Topics</p>
                        <h2 class="text-4xl font-bold text-gray-800 mt-2"><?php echo $topicCount; ?></h2>
                    </div>
                    <div class="w-12 h-12 bg-green-50 text-green-600 rounded-full flex items-center justify-center text-xl">
                        <i class="fa-solid fa-book"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold">Quizzes Generated</p>
                        <h2 class="text-4xl font-bold text-gray-800 mt-2"><?php echo $quizCount; ?></h2>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center text-xl">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                </div>
            </div>
        </div>

    </main>
</body>

</html>