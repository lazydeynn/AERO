<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$lang_id = isset($_GET['lang_id']) ? (int)$_GET['lang_id'] : 1;
$selected_topic_id = isset($_GET['topic_id']) ? (int)$_GET['topic_id'] : 0;
$langSql = "SELECT * FROM languages WHERE language_id = $lang_id";
$langRow = $conn->query($langSql)->fetch_assoc();
$langName = $langRow['name'];
$colors = [
    'Python' => 'bg-[#ff4b4b]',
    'C#' => 'bg-[#00b894]',
    'C++' => 'bg-[#8c7ae6]',
    'JavaScript' => 'bg-[#e1b12c]',
    'Java' => 'bg-[#ff7f50]',
    'Dart' => 'bg-[#2f80ed]'
];
$themeColor = $colors[$langName] ?? 'bg-gray-800';
$topicSql = "SELECT t.*, MAX(q.score) as best_score, MAX(q.total_items) as total_items
             FROM topics t 
             LEFT JOIN generated_quizzes q ON t.topic_id = q.topic_id AND q.user_id = $user_id
             WHERE t.language_id = $lang_id 
             GROUP BY t.topic_id";
$topicResult = $conn->query($topicSql);

$topics = [];
$firstTopicId = 0;
while ($row = $topicResult->fetch_assoc()) {
    $topics[] = $row;
    if ($firstTopicId == 0) $firstTopicId = $row['topic_id'];
}

if ($selected_topic_id == 0 && count($topics) > 0) {
    $selected_topic_id = $topics[0]['topic_id'];
}

$currentTopic = null;
foreach ($topics as $t) {
    if ($t['topic_id'] == $selected_topic_id) {
        $currentTopic = $t;
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $langName; ?> Topics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        @keyframes breathe {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }

        .animate-breathe {
            animation: breathe 3s infinite ease-in-out;
        }

        .topic-content-area h1,
        .topic-content-area h2,
        .topic-content-area h3 {
            color: #1a1a1a;
            font-weight: 700;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .topic-content-area h2 {
            font-size: 1.5rem;
            border-bottom: 1px solid #eee;
            padding-bottom: 2px;
        }

        .topic-content-area p {
            margin-bottom: 1rem;
        }

        .topic-content-area code {
            background: #f1f1f1;
            color: #e83e8c;
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            font-size: 0.9em;
        }

        .topic-content-area pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 1.25rem;
            border-radius: 8px;
            overflow-x: auto;
            margin: 1.5rem 0;
            line-height: 1.5;
        }

        .topic-content-area ul {
            list-style-type: disc;
            margin-left: 2rem;
            margin-bottom: 1.25rem;
        }

        .topic-content-area li {
            margin-bottom: 0.5rem;
        }

        .topic-content-area .w3-example {
            background-color: #E7E9EB;
            padding: 1rem;
            border-radius: 5px;
            margin: 1rem 0;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">

    <?php include '../components/navbar.php'; ?>

    <div class="flex flex-1 overflow-hidden relative">

        <aside class="w-72 bg-white border-r border-gray-200 flex flex-col h-full overflow-y-auto hidden md:block">
            <div class="<?php echo $themeColor; ?> p-6 text-white">
                <h1 class="text-2xl font-bold"><?php echo $langName; ?></h1>
                <p class="text-xs opacity-80 mt-1">Course Curriculum</p>
            </div>
            <div class="flex-1">
                <?php foreach ($topics as $t):
                    $isPassed = false;
                    if ($t['total_items'] > 0) {
                        $percent = ($t['best_score'] / $t['total_items']) * 100;
                        if ($percent >= 50) $isPassed = true;
                    }
                    $isActive = ($t['topic_id'] == $selected_topic_id)
                        ? 'bg-gray-100 border-l-4 border-black font-semibold text-black'
                        : 'hover:bg-gray-50 text-gray-600 border-l-4 border-transparent';
                ?>
                    <a href="?lang_id=<?php echo $lang_id; ?>&topic_id=<?php echo $t['topic_id']; ?>"
                        class="block p-4 border-b border-gray-100 text-sm transition flex justify-between items-center <?php echo $isActive; ?>">
                        <div class="flex items-center">
                            <span class="w-6 h-6 flex items-center justify-center rounded-full text-[10px] mr-3 
                            <?php echo $isPassed ? 'bg-green-100 text-green-700' : 'bg-gray-200 text-gray-500'; ?>">
                                <?php echo $t['order_sequence']; ?>
                            </span>
                            <span><?php echo $t['title']; ?></span>
                        </div>
                        <?php if ($isPassed): ?>
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </aside>

        <main class="flex-1 p-10 overflow-y-auto" id="main-content">
            <?php if ($currentTopic):
                $passStatus = "Not Started";
                $badgeColor = "bg-gray-200 text-gray-600";
                if ($currentTopic['total_items'] > 0) {
                    $percent = round(($currentTopic['best_score'] / $currentTopic['total_items']) * 100);
                    if ($percent >= 50) {
                        $passStatus = "Completed ($percent%)";
                        $badgeColor = "bg-green-100 text-green-800";
                    } else {
                        $passStatus = "In Progress ($percent%)";
                        $badgeColor = "bg-yellow-100 text-yellow-800";
                    }
                }
            ?>
                <div class="max-w-3xl transition-opacity duration-500" id="topic-content">
                    <div class="flex items-center space-x-4 mb-2">
                        <h2 class="text-4xl font-bold text-gray-900"><?php echo $currentTopic['title']; ?></h2>
                        <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $badgeColor; ?>">
                            <?php echo $passStatus; ?>
                        </span>
                    </div>

                    <div class="topic-content-area text-gray-800 leading-relaxed mb-8">
                        <?php echo $currentTopic['content_description'] ? nl2br(htmlspecialchars_decode($currentTopic['content_description'])) : "Master the fundamentals of " . $currentTopic['title'] . " with our AI-powered assessment."; ?>
                    </div>

                    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Study Materials</h3>
                        <p class="text-gray-500 mb-6">Download the lesson for offline reading or generate a quiz to test your skills.</p>

                        <div class="flex flex-col md:flex-row gap-4">
                            <a href="../actions/save_lesson.php?topic_id=<?php echo $selected_topic_id; ?>&lang_id=<?php echo $lang_id; ?>"
                                class="bg-white border-2 border-gray-200 text-gray-700 px-6 py-3 rounded-full font-bold hover:bg-gray-50 hover:border-gray-300 transition flex items-center justify-center">
                                <i class="fa-solid fa-download mr-2 text-blue-500"></i> Download Lesson
                            </a>

                            <form action="../api/generate_quiz.php" method="POST" id="genForm" class="flex-1 md:flex-none">
                                <input type="hidden" name="topic_id" value="<?php echo $selected_topic_id; ?>">
                                <button type="button" onclick="triggerQuizGeneration()" class="w-full bg-black text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-gray-800 transition transform hover:scale-105 flex items-center justify-center">
                                    <span>✨ Generate AI Quiz</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center mt-20 text-gray-400">Select a topic to begin.</div>
            <?php endif; ?>
        </main>

        <div id="quiz-overlay" class="absolute inset-0 z-50 bg-white flex flex-col items-center justify-center hidden transition-colors duration-1000 ease-in-out">

            <div class="mb-8 animate-breathe">
                <img src="../assets/images/logo/aero_white.svg" id="overlay-logo" alt="AERO" class="h-16 w-auto invert transition-all duration-1000">
            </div>

            <div class="flex space-x-1 items-center">
                <p id="overlay-text" class="text-gray-800 text-lg tracking-widest font-light transition-colors duration-1000 uppercase">
                    Preparing your Questions
                </p>
                <div class="flex space-x-1 ml-1" id="loading-dots">
                    <div class="w-1.5 h-1.5 bg-gray-800 rounded-full animate-bounce transition-colors duration-1000" style="animation-delay: 0s"></div>
                    <div class="w-1.5 h-1.5 bg-gray-800 rounded-full animate-bounce transition-colors duration-1000" style="animation-delay: 0.1s"></div>
                    <div class="w-1.5 h-1.5 bg-gray-800 rounded-full animate-bounce transition-colors duration-1000" style="animation-delay: 0.2s"></div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function triggerQuizGeneration() {
            const overlay = document.getElementById('quiz-overlay');
            const logo = document.getElementById('overlay-logo');
            const text = document.getElementById('overlay-text');
            const dots = document.getElementById('loading-dots').children;

            overlay.classList.remove('hidden');

            setTimeout(() => {
                overlay.classList.remove('bg-white');
                overlay.classList.add('bg-[#1e1e1e]');
                logo.classList.remove('invert');
                text.classList.remove('text-gray-800');
                text.classList.add('text-white');
                text.innerText = "YOUR QUESTIONS ARE READY";

                for (let dot of dots) {
                    dot.classList.remove('bg-gray-800');
                    dot.classList.add('bg-white');
                }
            }, 2000);

            setTimeout(() => {
                document.getElementById('genForm').submit();
            }, 3500);
        }
    </script>
</body>

</html>