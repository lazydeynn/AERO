<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['topic_id'])) {
    header("Location: ../pages/topic_list.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$topic_id = (int)$_GET['topic_id'];
$lang_id = isset($_GET['lang_id']) ? (int)$_GET['lang_id'] : 1;


$check = $conn->query("SELECT save_id FROM saved_lessons WHERE user_id = $user_id AND topic_id = $topic_id");

if ($check->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO saved_lessons (user_id, topic_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $topic_id);
    $stmt->execute();
}

header("Location: ../pages/downloads.php?status=saved");
exit;
