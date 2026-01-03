<?php

session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$conn->query("DELETE FROM generated_quizzes WHERE user_id = $user_id");

$sql = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    session_unset();
    session_destroy();
    header("Location: ../auth/login.php?msg=Your account has been permanently deleted.");
    exit;
} else {
    header("Location: ../pages/profile.php?error=Failed to delete account. Please try again.");
    exit;
}
