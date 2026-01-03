<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($conn)) {
    include '../config/db_conn.php';
}

$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();

if (!$user || $user['role'] !== 'admin') {
    header("Location: ../pages/dashboard.php?error=unauthorized");
    exit;
}
