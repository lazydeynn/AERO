<?php
session_start();
include '../config/db_conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (!isset($_SESSION['temp_user_id'])) {
        header("Location: ../auth/register.php?error=Session expired");
        exit;
    }

    if (isset($_POST['otp']) && is_array($_POST['otp'])) {
        $entered_code = implode("", $_POST['otp']);
    } else {
        header("Location: ../auth/verify.php?error=Invalid code format");
        exit;
    }

    $user_id = $_SESSION['temp_user_id'];

    $stmt = $conn->prepare("SELECT verification_code FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && $result['verification_code'] === $entered_code) {

        $update = $conn->prepare("UPDATE users SET is_verified = 1, verification_code = NULL WHERE user_id = ?");
        $update->bind_param("i", $user_id);
        $update->execute();

        $userQuery = $conn->query("SELECT fullname, role FROM users WHERE user_id = $user_id");
        $userRow = $userQuery->fetch_assoc();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['fullname'] = $userRow['fullname'];
        $_SESSION['role'] = $userRow['role'];

        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_email']);

        header("Location: ../auth/verified_success.php");
        exit;
    } else {
        header("Location: ../auth/verify.php?error=Invalid Verification Code");
        exit;
    }
} else {
    header("Location: ../auth/verify.php");
    exit;
}
