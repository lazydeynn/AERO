<?php
session_start();
include '../config/db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: ../auth/login.php?error=Fields cannot be empty");
        exit;
    }

    $stmt = $conn->prepare("SELECT user_id, fullname, password_hash, role, is_verified FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        if (password_verify($password, $row['password_hash'])) {

            if ($row['is_verified'] == 1 || $row['role'] === 'admin') {

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['role'] = $row['role'];

                if ($row['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } else {
                    header("Location: ../pages/dashboard.php");
                }
                exit;
            } else {
                $_SESSION['temp_user_id'] = $row['user_id'];
                $_SESSION['temp_email'] = $email;
                header("Location: ../auth/verify.php?error=Please verify your email first");
                exit;
            }
        } else {
            header("Location: ../auth/login.php?error=Incorrect password");
            exit;
        }
    } else {
        header("Location: ../auth/login.php?error=Email not found");
        exit;
    }
} else {
    header("Location: ../auth/login.php");
    exit;
}
