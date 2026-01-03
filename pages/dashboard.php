<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$fullname = $_SESSION['fullname'];

$sql = "
    SELECT 
        l.language_id, 
        l.name,
        (SELECT COUNT(*) FROM topics WHERE language_id = l.language_id) as total_topics,
        (SELECT COUNT(DISTINCT topic_id) FROM generated_quizzes 
         WHERE user_id = $user_id 
         AND score >= (total_items / 2) 
         AND topic_id IN (SELECT topic_id FROM topics WHERE language_id = l.language_id)
        ) as completed_topics
    FROM languages l
";

$result = $conn->query($sql);
$progress_data = [];

$colors = [
    'Python' => 'bg-[#ff4b4b]',
    'C#' => 'bg-[#00b894]',
    'C++' => 'bg-[#8c7ae6]',
    'JavaScript' => 'bg-[#e1b12c]',
    'Java' => 'bg-[#ff7f50]',
    'Dart' => 'bg-[#2f80ed]'
];

while ($row = $result->fetch_assoc()) {
    $total = $row['total_topics'];
    $done = $row['completed_topics'];
    $percent = ($total > 0) ? round(($done / $total) * 100) : 0;

    $progress_data[] = [
        'id' => $row['language_id'],
        'name' => $row['name'],
        'percent' => $percent,
        'done' => $done,
        'total' => $total,
        'color' => $colors[$row['name']] ?? 'bg-gray-500'
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Home - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css?v=2.1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">

    <?php include '../components/navbar.php'; ?>

    <div class="flex">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto h-[calc(100vh-4rem)]">
            <div class="max-w-5xl mx-auto">

                <div class="relative bg-gradient-to-r from-blue-600 to-blue-500 rounded-3xl p-10 text-white shadow-lg mb-10 overflow-hidden">
                    <div class="relative z-10 max-w-lg">
                        <h1 class="text-3xl font-bold mb-2">Welcome back <?php echo htmlspecialchars($fullname); ?>!</h1>
                        <p class="text-blue-100 mb-8 text-sm">Continue learning where you left off.</p>
                        <div class="flex space-x-4">
                            <a href="languages.php" class="bg-white text-blue-600 px-8 py-2 rounded-full font-bold text-sm hover:bg-gray-100 transition shadow">View Courses</a>
                        </div>
                    </div>
                    <img src="../assets/images/elements/3d-char.png"
                        class="absolute bottom-0 right-10 h-64 w-auto hidden lg:block transform translate-y-4" alt="3D">
                </div>

                <h2 class="text-xl font-bold mb-6 text-gray-800">My Progress</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <?php foreach ($progress_data as $p): ?>
                        <a href="topic_list.php?lang_id=<?php echo $p['id']; ?>" class="block">
                            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition hover:-translate-y-1">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="font-bold text-lg text-gray-800"><?php echo $p['name']; ?></h3>
                                    <span class="text-xs font-semibold text-gray-400">
                                        <?php echo $p['done']; ?>/<?php echo $p['total']; ?>
                                    </span>
                                </div>

                                <div class="w-full bg-gray-100 rounded-full h-2 mb-2">
                                    <div class="<?php echo $p['color']; ?> h-2 rounded-full transition-all duration-1000"
                                        style="width: <?php echo $p['percent']; ?>%"></div>
                                </div>
                                <p class="text-[10px] text-gray-400 text-right"><?php echo $p['percent']; ?>% Complete</p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </main>
    </div>
</body>

</html>