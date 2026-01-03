<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['topic_id'])) {
    header("Location: downloads.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$topic_id = (int)$_GET['topic_id'];

$sql = "SELECT t.title, t.content_description, t.ai_context_summary, l.name as language_name 
        FROM topics t 
        JOIN languages l ON t.language_id = l.language_id 
        WHERE t.topic_id = $topic_id";
$topic = $conn->query($sql)->fetch_assoc();

if (!$topic) die("Topic not found.");

$qSql = "SELECT qq.* FROM generated_quizzes gq
         JOIN quiz_questions qq ON gq.quiz_id = qq.quiz_id
         WHERE gq.topic_id = $topic_id AND gq.user_id = $user_id
         LIMIT 10";
$questions = $conn->query($qSql);

if (isset($_GET['download'])) {
    $filename = "AERO_Lesson_" . preg_replace('/[^a-zA-Z0-9]/', '_', $topic['title']) . ".html";
    header("Content-Type: text/html");
    header("Content-Disposition: attachment; filename=\"$filename\"");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo $topic['title']; ?> - Offline Lesson</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            line-height: 1.8;
            color: #1f2937;
            padding: 40px 20px;
            background: #f9fafb;
        }

        .paper {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 60px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        .header {
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 20px;
            margin-bottom: 40px;
        }

        .badge {
            background: #eff6ff;
            color: #3b82f6;
            padding: 4px 12px;
            border-radius: 99px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        h1 {
            margin: 15px 0 10px 0;
            font-size: 32px;
        }

        .content {
            font-size: 16px;
            color: #374151;
            white-space: pre-wrap;
        }

        .quiz-section {
            margin-top: 60px;
            border-top: 2px dashed #e5e7eb;
            padding-top: 40px;
        }

        .q-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .q-text {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .ans {
            color: #059669;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-download {
            background: #1e1e1e;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: bold;
            display: inline-block;
        }
    </style>
</head>

<body>
    <div class="paper">
        <?php if (!isset($_GET['download'])): ?>
            <a href="downloads.php" style="text-decoration:none; color:#6b7280; font-size:14px;">← Back to Library</a>
        <?php endif; ?>

        <div class="header">
            <span class="badge"><?php echo $topic['language_name']; ?></span>
            <h1><?php echo $topic['title']; ?></h1>
            <p style="color: #6b7280; font-size: 14px;">Downloaded from AERO</p>
        </div>

        <div class="content">
            <?php
            if (!empty($topic['content_description'])) {
                echo nl2br($topic['content_description']);
            } elseif (!empty($topic['ai_context_summary'])) {
                echo nl2br($topic['ai_context_summary']);
            } else {
                echo "No content available for this topic yet.";
            }
            ?>
        </div>

        <?php if ($questions->num_rows > 0): ?>
            <div class="quiz-section">
                <h3>Review Questions</h3>
                <?php $i = 1;
                while ($q = $questions->fetch_assoc()): ?>
                    <div class="q-card">
                        <div class="q-text"><?php echo $i++; ?>. <?php echo $q['question_text']; ?></div>
                        <div style="font-size: 14px; color: #4b5563; margin-bottom: 8px;">
                            A) <?php echo $q['option_a']; ?> <br>
                            B) <?php echo $q['option_b']; ?> <br>
                            C) <?php echo $q['option_c']; ?> <br>
                            D) <?php echo $q['option_d']; ?>
                        </div>
                        <div class="ans">Correct: Option <?php echo $q['correct_option']; ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($_GET['download'])): ?>
            <div style="text-align:center; margin-top:50px; border-top:1px solid #eee; padding-top:20px;">
                <a href="view_lesson.php?topic_id=<?php echo $topic_id; ?>&download=1" class="btn-download">⬇ Download to Device</a>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>