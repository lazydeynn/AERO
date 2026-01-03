<?php

session_start();
include '../config/db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        header("Location: ../auth/reset_password.php?email=$email&token=0&error=Passwords do not match");
        exit;
    }

    if (strlen($password) < 8 || !preg_match('/\d/', $password)) {
        header("Location: ../auth/reset_password.php?email=$email&token=0&error=Password too weak");
        exit;
    }

    $new_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_hash, $email);

    if ($stmt->execute()) {
        header("Location: ../auth/login.php?msg=Password updated successfully! Please login.");
        exit;
    } else {
        header("Location: ../auth/reset_password.php?email=$email&token=0&error=Database update failed");
        exit;
    }
} else {
    header("Location: ../auth/login.php");
    exit;
}
