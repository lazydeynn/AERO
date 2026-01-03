<?php
include '../config/db_conn.php';

$quiz_id = $_GET['quiz_id'] ?? 0;

$sql = "SELECT q.*, t.title as topic_title, l.name as language_name 
        FROM generated_quizzes q 
        JOIN topics t ON q.topic_id = t.topic_id 
        JOIN languages l ON t.language_id = l.language_id
        WHERE q.quiz_id = $quiz_id";
$quizResult = $conn->query($sql);

if ($quizResult->num_rows == 0) die("Quiz not found.");
$quiz = $quizResult->fetch_assoc();
$topicTitle = strtoupper($quiz['language_name']);

$qSql = "SELECT * FROM quiz_questions WHERE quiz_id = $quiz_id ORDER BY question_id ASC";
$questionsResult = $conn->query($qSql);

$questions = [];
while ($row = $questionsResult->fetch_assoc()) {
    $questions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Quiz: <?php echo $quiz['topic_title']; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .option-btn {
            transition: all 0.2s ease;
        }

        .option-btn.selected {
            background-color: #1f2937;
            color: white;
            border-color: #1f2937;
        }

        .option-btn:hover:not(.selected) {
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col">

    <?php include '../components/navbar.php'; ?>

    <main class="flex-1 flex items-center justify-center p-6">

        <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl p-10 relative min-h-[600px] flex flex-col">

            <div id="progress-container" class="flex space-x-2 mb-10 w-full max-w-md mx-auto"></div>

            <div class="text-center mb-10">
                <h2 class="text-[#ff4b4b] font-bold text-2xl uppercase tracking-wider mb-2"><?php echo $topicTitle; ?></h2>
                <h3 class="text-xl font-bold text-gray-900">Question number <span id="q-number">1</span></h3>
            </div>

            <form id="quizForm" action="quiz_result.php" method="POST" class="flex-1 flex flex-col">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                <input type="hidden" name="duration" id="duration_input" value="0">

                <div id="question-container" class="flex-1 flex flex-col justify-center items-center w-full">
                </div>

                <div id="answers-store"></div>
            </form>

            <div class="mt-10 flex justify-center">
                <button onclick="nextQuestion()" class="bg-blue-600 text-white px-12 py-3 rounded-full font-semibold shadow-lg hover:bg-blue-700 transition transform hover:scale-105">
                    Next
                </button>
            </div>

        </div>
    </main>

    <script>
        const questions = <?php echo json_encode($questions); ?>;
        let currentIndex = 0;
        let userAnswers = {};
        let startTime = Date.now();

        function updateDuration() {
            let endTime = Date.now();
            let timeSpentSeconds = Math.floor((endTime - startTime) / 1000);
            document.getElementById('duration_input').value = timeSpentSeconds;
        }

        const container = document.getElementById('question-container');
        const qNumberSpan = document.getElementById('q-number');
        const nextBtn = document.querySelector('button[onclick="nextQuestion()"]');
        const progressContainer = document.getElementById('progress-container');

        function updateProgressBar() {
            progressContainer.innerHTML = '';
            questions.forEach((q, index) => {
                const bar = document.createElement('div');
                bar.className = 'h-2 w-full rounded-full transition-all duration-300';
                if (index <= currentIndex) {
                    bar.classList.add('bg-[#ff4b4b]');
                } else {
                    bar.classList.add('bg-gray-200');
                }
                progressContainer.appendChild(bar);
            });
        }

        function renderQuestion() {
            const q = questions[currentIndex];
            qNumberSpan.innerText = currentIndex + 1;

            container.innerHTML = `
                <p class="text-gray-700 text-xl font-medium text-center mb-10 max-w-3xl leading-relaxed">${q.question_text}</p>
                <div class="w-full max-w-xl space-y-4">
                    ${renderOption(q, 'A')}
                    ${renderOption(q, 'B')}
                    ${renderOption(q, 'C')}
                    ${renderOption(q, 'D')}
                </div>
            `;

            if (currentIndex === questions.length - 1) {
                nextBtn.innerText = "Submit Quiz";
                nextBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                nextBtn.classList.add('bg-black', 'hover:bg-gray-800');
            } else {
                nextBtn.innerText = "Next";
                nextBtn.classList.add('bg-blue-600');
            }
            updateProgressBar();
        }

        function renderOption(q, opt) {
            const text = q['option_' + opt.toLowerCase()];
            const isSelected = userAnswers[q.question_id] === opt ? 'selected' : 'bg-gray-100 text-gray-600';

            return `
                <div onclick="selectOption(${q.question_id}, '${opt}')" 
                     class="option-btn w-full p-4 rounded-xl border border-transparent text-center cursor-pointer font-medium text-sm shadow-sm ${isSelected}"
                     id="btn-${q.question_id}-${opt}">
                     ${text}
                </div>
            `;
        }

        function selectOption(qId, opt) {
            userAnswers[qId] = opt;

            ['A', 'B', 'C', 'D'].forEach(o => {
                const btn = document.getElementById(`btn-${qId}-${o}`);
                if (btn) {
                    btn.classList.remove('selected');
                    btn.classList.add('bg-gray-100', 'text-gray-600');
                }
            });

            const activeBtn = document.getElementById(`btn-${qId}-${opt}`);
            activeBtn.classList.remove('bg-gray-100', 'text-gray-600');
            activeBtn.classList.add('selected');

            let input = document.getElementById(`input-${qId}`);
            if (!input) {
                input = document.createElement('input');
                input.type = 'hidden';
                input.id = `input-${qId}`;
                input.name = `answers[${qId}]`;
                document.getElementById('answers-store').appendChild(input);
            }
            input.value = opt;
        }

        function nextQuestion() {
            const currentQId = questions[currentIndex].question_id;

            if (!userAnswers[currentQId]) {
                alert("Please select an answer.");
                return;
            }

            updateDuration();

            if (currentIndex < questions.length - 1) {
                currentIndex++;
                renderQuestion();
            } else {
                document.getElementById('quizForm').submit();
            }
        }

        renderQuestion();
    </script>
</body>

</html>