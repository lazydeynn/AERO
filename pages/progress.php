<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_lang_id = isset($_GET['lang_id']) ? (int)$_GET['lang_id'] : 1;

$languages = [
    1 => ['name' => 'Python',     'bg' => 'bg-[#ff4b4b]', 'text' => 'text-[#ff4b4b]', 'border' => 'border-[#ff4b4b]'],
    2 => ['name' => 'C#',         'bg' => 'bg-[#00b894]', 'text' => 'text-[#00b894]', 'border' => 'border-[#00b894]'],
    3 => ['name' => 'C++',        'bg' => 'bg-[#8c7ae6]', 'text' => 'text-[#8c7ae6]', 'border' => 'border-[#8c7ae6]'],
    4 => ['name' => 'JavaScript', 'bg' => 'bg-[#e1b12c]', 'text' => 'text-[#e1b12c]', 'border' => 'border-[#e1b12c]'],
    5 => ['name' => 'Java',       'bg' => 'bg-[#ff7f50]', 'text' => 'text-[#ff7f50]', 'border' => 'border-[#ff7f50]'],
    6 => ['name' => 'Dart',       'bg' => 'bg-[#2f80ed]', 'text' => 'text-[#2f80ed]', 'border' => 'border-[#2f80ed]']
];

if (!array_key_exists($current_lang_id, $languages)) {
    $current_lang_id = 1;
}
$theme = $languages[$current_lang_id];

$prev_id = ($current_lang_id == 1) ? 6 : $current_lang_id - 1;
$next_id = ($current_lang_id == 6) ? 1 : $current_lang_id + 1;

$totalTopicsSql = "SELECT COUNT(*) as count FROM topics WHERE language_id = $current_lang_id";
$totalTopics = $conn->query($totalTopicsSql)->fetch_assoc()['count'];

$btnTextSql = "select name from languages where language_id = $current_lang_id";
$btnText1 = $conn->query($btnTextSql)->fetch_assoc()['name'];

$completedSql = "
    SELECT COUNT(DISTINCT topic_id) as count 
    FROM generated_quizzes 
    WHERE user_id = $user_id 
    AND topic_id IN (SELECT topic_id FROM topics WHERE language_id = $current_lang_id)
    AND score >= (total_items / 2)
";
$completed = $conn->query($completedSql)->fetch_assoc()['count'];
$percentage = ($totalTopics > 0) ? round(($completed / $totalTopics) * 100) : 0;


$timeSql = "
    SELECT SUM(q.duration_seconds) as total_seconds 
    FROM generated_quizzes q 
    JOIN topics t ON q.topic_id = t.topic_id 
    WHERE q.user_id = $user_id AND t.language_id = $current_lang_id
";
$timeData = $conn->query($timeSql)->fetch_assoc();

$totalSeconds = $timeData['total_seconds'] ?? 0;
$minutesSpent = ceil($totalSeconds / 60);

$nextTopicSql = "
    SELECT title, topic_id FROM topics 
    WHERE language_id = $current_lang_id 
    AND topic_id NOT IN (
        SELECT topic_id FROM generated_quizzes 
        WHERE user_id = $user_id 
        AND score >= (total_items / 2)
    ) 
    ORDER BY topic_id ASC 
    LIMIT 1
";
$nextTopicRes = $conn->query($nextTopicSql);


if ($nextTopicRes->num_rows > 0) {
    $row = $nextTopicRes->fetch_assoc();
    $sTitle = $row['title'];
    $sId = $row['topic_id'];

    $suggestedText = "Based on your progress, you are ready to tackle <span class='{$theme['text']} font-bold'>$sTitle</span>.";
    $btnLink = "topic_list.php?lang_id=$current_lang_id&topic_id=$sId";
    $btnText = "Start";
} else {
    $suggestedText = "Incredible! You have completed all topics in <span class='{$theme['text']} font-bold'>{$theme['name']}</span>.";
    $btnLink = "#";
    $btnText1 = "Complete";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Progress - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 overflow-hidden">

    <?php include '../components/navbar.php'; ?>

    <div class="flex h-[calc(100vh-4rem)]">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex-1 <?php echo $theme['bg']; ?> p-8 overflow-y-auto transition-colors duration-500">

            <div class="max-w-6xl mx-auto h-full flex flex-col">
                <div class="bg-white rounded-[40px] shadow-2xl flex-1 p-10 relative flex flex-col justify-center items-center">

                    <a href="?lang_id=<?php echo $prev_id; ?>" class="absolute left-6 top-1/2 transform -translate-y-1/2 p-3 rounded-full bg-gray-100 hover:bg-gray-200 transition shadow-sm">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <a href="?lang_id=<?php echo $next_id; ?>" class="absolute right-6 top-1/2 transform -translate-y-1/2 p-3 rounded-full bg-gray-100 hover:bg-gray-200 transition shadow-sm">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>

                    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                        <div class="md:col-span-2 flex flex-col justify-center">
                            <h2 class="text-2xl font-bold <?php echo $theme['text']; ?> mb-2">
                                <?php echo $theme['name']; ?> Progress Summary
                            </h2>
                            <p class="text-gray-400 text-xs mb-6">
                                Track your journey in <?php echo $theme['name']; ?>. Completing quizzes increases your mastery level.
                            </p>

                            <div class="w-full h-3 bg-gray-100 rounded-full mb-2">
                                <div class="h-3 rounded-full <?php echo $theme['bg']; ?> transition-all duration-1000" style="width: <?php echo $percentage; ?>%"></div>
                            </div>
                            <div class="text-right text-xs font-bold text-gray-400"><?php echo $completed; ?>/<?php echo $totalTopics; ?> Topics</div>
                        </div>

                        <div class="flex justify-center items-center">
                            <div class="relative w-32 h-32">
                                <div class="w-full h-full rounded-full border-8 border-gray-100 flex items-center justify-center">
                                    <span class="text-3xl font-bold text-gray-700"><?php echo $percentage; ?>%</span>
                                </div>
                                <div class="absolute inset-0 rounded-full border-8 <?php echo $theme['border']; ?> opacity-20"></div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-8">

                        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 mb-1">Total Time Spent</h3>
                                <p class="text-xs text-gray-400 mb-4"><?php echo date("F j, Y"); ?></p>
                            </div>

                            <div class="flex flex-col items-center justify-center py-2">
                                <span class="text-5xl font-bold <?php echo $theme['text']; ?>">
                                    <?php echo $minutesSpent; ?>
                                </span>
                                <span class="text-sm text-gray-400 font-medium">Minutes</span>
                            </div>

                            <div class="flex items-end justify-center space-x-2 h-8 opacity-50 mt-2">
                                <div class="w-2 bg-gray-200 rounded-t h-[40%]"></div>
                                <div class="w-2 bg-gray-200 rounded-t h-[70%]"></div>
                                <div class="w-2 <?php echo $theme['bg']; ?> rounded-t h-[100%]"></div>
                                <div class="w-2 bg-gray-200 rounded-t h-[60%]"></div>
                                <div class="w-2 bg-gray-200 rounded-t h-[30%]"></div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm flex flex-col justify-between">
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 mb-1">Suggested Step</h3>
                                <p class="text-xs text-gray-400 mb-4">Next Topic</p>
                                <p class="text-sm font-medium text-gray-600 leading-relaxed">
                                    <?php echo $suggestedText; ?>
                                </p>
                            </div>

                            <div class="mt-4 flex justify-end">
                                <a href="<?php echo $btnLink; ?>" class="px-6 py-2 rounded-full text-xs font-bold border hover:bg-gray-50 transition text-gray-600">
                                    Start <?php echo $btnText1; ?>
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </main>
    </div>

</body>

</html>