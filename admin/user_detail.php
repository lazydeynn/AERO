<?php
include '../config/db_conn.php';
include '../components/admin_sidebar.php';

$uid = (int)$_GET['id'];
$user = $conn->query("SELECT * FROM users WHERE user_id = $uid")->fetch_assoc();

$quizzes = $conn->query("SELECT COUNT(*) as c FROM generated_quizzes WHERE user_id = $uid")->fetch_assoc()['c'];
$topics = $conn->query("SELECT COUNT(*) as c FROM user_progress WHERE user_id = $uid AND is_completed = 1")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">



<head>
    <meta charset="UTF-8">
    <title>User Details</title>
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
    <main class="ml-64 flex-1 p-10">
        <a href="manage_users.php" class="text-gray-500 mb-4 inline-block">← Back</a>
        <h1 class="text-3xl font-bold mb-6"><?php echo $user['fullname']; ?>'s Progress</h1>

        <div class="grid grid-cols-2 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <p class="text-gray-500 text-xs uppercase font-bold">Quizzes Taken</p>
                <p class="text-4xl font-bold"><?php echo $quizzes; ?></p>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-sm">
                <p class="text-gray-500 text-xs uppercase font-bold">Topics Completed</p>
                <p class="text-4xl font-bold"><?php echo $topics; ?></p>
            </div>
        </div>

    </main>
</body>

</html>