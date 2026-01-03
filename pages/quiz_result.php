<?php
include '../config/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: dashboard.php");
    exit;
}

$quiz_id = (int)$_POST['quiz_id'];
$answers = $_POST['answers'] ?? [];
$sql = "SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id";
$result = $conn->query($sql);
$score = 0;
$total = $result->num_rows;

while ($q = $result->fetch_assoc()) {
    $qid = $q['question_id'];
    $correct = strtoupper(trim($q['correct_option']));

    $user_choice = isset($answers[$qid]) ? strtoupper(trim($answers[$qid])) : null;

    if ($user_choice === $correct) {
        $score++;
    }

    if ($user_choice) {
        $updateStmt = $conn->prepare("UPDATE quiz_questions SET user_answer = ? WHERE question_id = ?");
        $updateStmt->bind_param("si", $user_choice, $qid);
        $updateStmt->execute();
    }
}

$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;
$percentage = ($total > 0) ? round(($score / $total) * 100) : 0;

$saveScoreSql = "UPDATE generated_quizzes SET score = $score, total_items = $total, duration_seconds = $duration WHERE quiz_id = $quiz_id";
$conn->query($saveScoreSql);

$langSql = "SELECT t.language_id 
            FROM generated_quizzes q 
            JOIN topics t ON q.topic_id = t.topic_id 
            WHERE q.quiz_id = $quiz_id";
$langRes = $conn->query($langSql);
$langRow = $langRes->fetch_assoc();
$lang_id = $langRow['language_id'] ?? 1;
$message = "Keep practicing!";
$color = "text-red-500";
$emoji = "😐";

if ($percentage >= 50) {
    $message = "Good job!";
    $color = "text-yellow-500";
    $emoji = "🙂";
}
if ($percentage >= 80) {
    $message = "Outstanding!";
    $color = "text-green-500";
    $emoji = "🎉";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz Results</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex flex-col">

    <?php include '../components/navbar.php'; ?>

    <main class="flex-1 flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl p-10 text-center">

            <div class="text-6xl mb-4"><?php echo $emoji; ?></div>

            <h1 class="text-3xl font-bold text-gray-900 mb-2">Quiz Completed!</h1>
            <p class="text-gray-500 mb-8">Here is how you performed on this topic.</p>

            <div class="relative w-40 h-40 mx-auto mb-8 flex items-center justify-center rounded-full border-8 border-gray-100">
                <div class="text-center">
                    <span class="block text-4xl font-bold <?php echo $color; ?>"><?php echo $score; ?></span>
                    <span class="text-gray-400 text-sm">out of <?php echo $total; ?></span>
                </div>
            </div>

            <h2 class="text-2xl font-bold <?php echo $color; ?> mb-8"><?php echo $message; ?></h2>

            <div class="flex flex-col space-y-3">
                <a href="topic_list.php?lang_id=<?php echo $lang_id; ?>" class="bg-black text-white py-3 rounded-full font-bold hover:bg-gray-800 transition">
                    Back to Topics
                </a>
                <a href="take_quiz.php?quiz_id=<?php echo $quiz_id; ?>" class="text-gray-400 text-sm hover:text-gray-600">
                    Retake Quiz
                </a>
            </div>

        </div>
    </main>

</body>

</html>