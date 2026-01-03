<?php

session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';

if ($action === 'delete') {
    $type = $_GET['type'];
    $id = (int)$_GET['id'];

    $isAdmin = false;
    $roleCheck = $conn->query("SELECT role FROM users WHERE user_id = $user_id")->fetch_assoc();
    if ($roleCheck && $roleCheck['role'] === 'admin') {
        $isAdmin = true;
    }

    if ($type === 'thread') {
        $check = $conn->query("SELECT user_id FROM forum_threads WHERE thread_id = $id")->fetch_assoc();
        if ($check && ($check['user_id'] == $user_id || $isAdmin)) {
            $conn->query("DELETE FROM forum_threads WHERE thread_id = $id");
            header("Location: ../pages/forum.php?msg=deleted");
            exit;
        }
    } elseif ($type === 'reply') {
        $check = $conn->query("SELECT user_id, thread_id FROM forum_replies WHERE reply_id = $id")->fetch_assoc();
        if ($check && ($check['user_id'] == $user_id || $isAdmin)) {
            $conn->query("DELETE FROM forum_replies WHERE reply_id = $id");
            header("Location: ../pages/view_thread.php?id=" . $check['thread_id'] . "&msg=deleted");
            exit;
        }
    }
    die("Access denied or invalid item.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_content'])) {
    $type = $_POST['target_type'];
    $id = (int)$_POST['target_id'];
    $reason = trim($_POST['reason']);
    $redirect_thread = (int)$_POST['thread_id_ref'];

    if (!empty($reason)) {
        $stmt = $conn->prepare("INSERT INTO reports (user_id, target_type, target_id, reason) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $user_id, $type, $id, $reason);
        $stmt->execute();

        header("Location: ../pages/view_thread.php?id=$redirect_thread&msg=reported");
        exit;
    }
}

header("Location: ../pages/forum.php");
exit;
