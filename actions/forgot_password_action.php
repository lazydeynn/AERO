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
    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT user_id, fullname FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $fullname = $row['fullname'];

        $token = bin2hex(random_bytes(16));
        $reset_link = "http://localhost/aero/auth/reset_password.php?token=$token&email=$email";

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_EMAIL'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('no-reply@aero.com', 'AERO Support');
            $mail->addAddress($email, $fullname);

            $mail->isHTML(true);
            $mail->Subject = 'Reset Your Password';
            $mail->Body    = "
                <div style='font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Password Reset Request</h2>
                    <p>Hi $fullname,</p>
                    <p>We received a request to reset your password. Click the link below to create a new password:</p>
                    <a href='$reset_link' style='background: black; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Reset Password</a>
                    <p>If you didn't ask for this, you can ignore this email.</p>
                </div>
            ";

            $mail->send();
            header("Location: ../auth/forgot_password.php?msg=Reset link sent! Check your email.");
        } catch (Exception $e) {
            header("Location: ../auth/forgot_password.php?error=Mailer Error: {$mail->ErrorInfo}");
        }
    } else {
        header("Location: ../auth/forgot_password.php?error=Email not found in our records.");
    }
}
