<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$historySql = "SELECT q.*, t.title, l.name as language_name 
               FROM generated_quizzes q
               JOIN topics t ON q.topic_id = t.topic_id
               JOIN languages l ON t.language_id = l.language_id
               WHERE q.user_id = $user_id
               ORDER BY q.date_taken DESC LIMIT 3";
$history = $conn->query($historySql);

$topicSql = "SELECT t.topic_id, t.title, l.name as lang_name, l.language_id,
                    MAX(q.score) as best_score, MAX(q.total_items) as total_items
             FROM topics t
             JOIN languages l ON t.language_id = l.language_id
             LEFT JOIN generated_quizzes q ON t.topic_id = q.topic_id AND q.user_id = $user_id
             GROUP BY t.topic_id
             ORDER BY l.language_id, t.order_sequence";
$topics = $conn->query($topicSql);

function getLangColor($lang)
{
    $colors = [
        'Python' => 'text-[#ff4b4b] bg-[#ff4b4b]/10 border-[#ff4b4b]',
        'C#' => 'text-[#00b894] bg-[#00b894]/10 border-[#00b894]',
        'C++' => 'text-[#8c7ae6] bg-[#8c7ae6]/10 border-[#8c7ae6]',
        'JavaScript' => 'text-[#e1b12c] bg-[#e1b12c]/10 border-[#e1b12c]',
        'Java' => 'text-[#ff7f50] bg-[#ff7f50]/10 border-[#ff7f50]',
        'Dart' => 'text-[#2f80ed] bg-[#2f80ed]/10 border-[#2f80ed]'
    ];
    return $colors[$lang] ?? 'text-gray-500 bg-gray-100 border-gray-200';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz Hub - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css?v=2.2">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <div class="max-w-6xl mx-auto">

                <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Quiz Center</h1>
                        <p class="text-sm text-gray-500 mt-1">Test your skills, track your scores, and master new topics.</p>
                    </div>
                    <div class="relative w-full md:w-64">
                        <input type="text" id="searchInput" onkeyup="filterQuizzes()" placeholder="Find a quiz..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:border-black transition bg-white shadow-sm">
                        <i class="fa-solid fa-search absolute left-4 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <?php if ($history->num_rows > 0): ?>
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Recent Activity</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                        <?php while ($h = $history->fetch_assoc()):
                            $percent = ($h['total_items'] > 0) ? round(($h['score'] / $h['total_items']) * 100) : 0;
                            $statusColor = ($percent >= 50) ? "text-green-500" : "text-red-500";
                        ?>
                            <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wide text-gray-400"><?php echo $h['language_name']; ?></span>
                                    <h3 class="font-bold text-gray-800 text-sm mb-1"><?php echo $h['title']; ?></h3>
                                    <p class="text-xs text-gray-500"><?php echo date("M d, H:i", strtotime($h['date_taken'])); ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="block text-2xl font-bold <?php echo $statusColor; ?>"><?php echo $percent; ?>%</span>
                                    <a href="quiz_result.php?quiz_id=<?php echo $h['quiz_id']; ?>" class="text-[10px] underline text-gray-400 hover:text-black">View</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <h2 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4">Available Quizzes</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="quizGrid">

                    <?php if ($topics->num_rows > 0): ?>
                        <?php while ($t = $topics->fetch_assoc()):
                            $langStyle = getLangColor($t['lang_name']);
                            $hasTaken = !is_null($t['best_score']);
                            $percent = 0;
                            if ($hasTaken && $t['total_items'] > 0) {
                                $percent = round(($t['best_score'] / $t['total_items']) * 100);
                            }

                            $btnText = "Start Quiz";
                            $btnClass = "bg-black text-white hover:bg-gray-800";
                            $statusBadge = "";

                            if ($hasTaken) {
                                if ($percent >= 50) {
                                    $statusBadge = '<span class="absolute top-4 right-4 bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full">Passed ' . $percent . '%</span>';
                                    $btnText = "Retake";
                                    $btnClass = "bg-white border border-gray-200 text-gray-700 hover:bg-gray-50";
                                } else {
                                    $statusBadge = '<span class="absolute top-4 right-4 bg-red-100 text-red-700 text-[10px] font-bold px-2 py-1 rounded-full">Failed ' . $percent . '%</span>';
                                    $btnText = "Try Again";
                                    $btnClass = "bg-red-500 text-white hover:bg-red-600";
                                }
                            } else {
                                $statusBadge = '<span class="absolute top-4 right-4 bg-blue-50 text-blue-600 text-[10px] font-bold px-2 py-1 rounded-full">New</span>';
                            }
                        ?>

                            <div class="quiz-card bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative hover:shadow-md transition group">
                                <?php echo $statusBadge; ?>

                                <div class="mb-4">
                                    <span class="px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border <?php echo $langStyle; ?>">
                                        <?php echo $t['lang_name']; ?>
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition quiz-title">
                                    <?php echo $t['title']; ?>
                                </h3>
                                <p class="text-xs text-gray-500 mb-6">Test your knowledge on <?php echo $t['title']; ?> concepts.</p>

                                <form action="../api/generate_quiz.php" method="POST">
                                    <input type="hidden" name="topic_id" value="<?php echo $t['topic_id']; ?>">
                                    <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold shadow-sm transition transform active:scale-95 <?php echo $btnClass; ?>">
                                        <?php echo $btnText; ?>
                                    </button>
                                </form>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-span-full text-center py-20 text-gray-400">
                            <p>No quiz topics found. Please add topics to the database.</p>
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        </main>
    </div>

    <script>
        function filterQuizzes() {
            let input = document.getElementById('searchInput').value.toLowerCase();
            let cards = document.querySelectorAll('.quiz-card');

            cards.forEach(card => {
                let title = card.querySelector('.quiz-title').innerText.toLowerCase();
                if (title.includes(input)) {
                    card.style.display = "block";
                } else {
                    card.style.display = "none";
                }
            });
        }
    </script>

</body>

</html>