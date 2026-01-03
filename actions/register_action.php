<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

session_start();
include '../config/db_conn.php';

require '../phpmailer/Exception.php';
require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $terms = isset($_POST['terms']);

    if (empty($fullname) || empty($email) || empty($password) || !$terms) {
        header("Location: ../auth/register.php?error=All fields are required");
        exit;
    }

    $check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: ../auth/register.php?error=Email already registered");
        exit;
    }

    $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
    $verification_code = rand(100000, 999999);

    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password_hash, verification_code, is_verified) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssss", $fullname, $email, $hashed_pass, $verification_code);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $_SESSION['temp_user_id'] = $user_id;
        $_SESSION['temp_email'] = $email;

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];;
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_EMAIL'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@aero.com', 'AERO Learning');
            $mail->addAddress($email, $fullname);

            $mail->isHTML(true);
            $mail->Subject = 'Verify your AERO Account';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; text-align: center; padding: 20px;'>
                    <h2>Welcome to AERO!</h2>
                    <p>Please enter the code below to verify your account:</p>
                    <h1 style='background: #f4f4f4; padding: 10px; display: inline-block; letter-spacing: 5px;'>$verification_code</h1>
                    <p>If you did not request this, please ignore this email.</p>
                </div>
            ";
            $mail->AltBody = "Your verification code is: $verification_code";

            $mail->send();
            header("Location: ../auth/verify.php");
            exit;
        } catch (Exception $e) {
            $conn->query("DELETE FROM users WHERE user_id = $user_id");
            header("Location: ../auth/register.php?error=Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            exit;
        }
    } else {
        header("Location: ../auth/register.php?error=Database error");
        exit;
    }
} else {
    header("Location: ../auth/register.php");
    exit;
}
