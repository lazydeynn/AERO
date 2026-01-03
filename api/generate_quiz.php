<?php

ob_start();
session_start();

include '../config/db_conn.php';
include '../config/ai_helper.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$topic_id = isset($_POST['topic_id']) ? (int)$_POST['topic_id'] : 0;

try {
    $stmt = $conn->prepare("SELECT ai_context_summary, content_description, title FROM topics WHERE topic_id = ?");
    $stmt->bind_param("i", $topic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $topic = $result->fetch_assoc();

    if (!$topic) {
        throw new Exception("Topic not found.");
    }

    $context = "";
    if (!empty($topic['ai_context_summary'])) {
        $context = $topic['ai_context_summary'];
    } elseif (!empty($topic['content_description'])) {
        $context = $topic['content_description'];
    } else {
        $context = "Programming topic: " . $topic['title'];
    }

    if (strlen($context) < 5) {
        $context = "Generate a general quiz about " . $topic['title'];
    }

    $response = generateQuizFromAI($context);

    if (isset($response['error'])) {
        throw new Exception($response['error']);
    }

    $stmt = $conn->prepare("INSERT INTO generated_quizzes (user_id, topic_id, date_taken) VALUES (?, ?, NOW())");
    $stmt->bind_param("ii", $user_id, $topic_id);
    $stmt->execute();
    $quiz_id = $stmt->insert_id;

    $qStmt = $conn->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");

    foreach ($response as $q) {
        $correct = strtoupper(substr($q['correct_option'], 0, 1));

        $qStmt->bind_param(
            "issssss",
            $quiz_id,
            $q['question_text'],
            $q['option_a'],
            $q['option_b'],
            $q['option_c'],
            $q['option_d'],
            $correct
        );
        $qStmt->execute();
    }

    $total_questions = count($response);
    $updateStmt = $conn->prepare("UPDATE generated_quizzes SET total_items = ? WHERE quiz_id = ?");
    $updateStmt->bind_param("ii", $total_questions, $quiz_id);
    $updateStmt->execute();

    header("Location: ../pages/take_quiz.php?quiz_id=" . $quiz_id);
    exit;
} catch (Exception $e) {
    $errorMsg = urlencode($e->getMessage());
    header("Location: ../pages/topic_list.php?error=" . $errorMsg);
    exit;
}
