<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $msg = trim($_POST['message']);
    if (!empty($msg)) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION['user_id'], $msg);
        $stmt->execute();
        $success = "Feedback sent! Thank you.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Send Feedback</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 p-10 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-xl shadow-lg max-w-md w-full">
        <h1 class="text-2xl font-bold mb-4">Feedback</h1>
        <?php if (isset($success)) echo "<p class='text-green-600 mb-4'>$success</p>"; ?>
        <form method="POST">
            <textarea name="message" class="w-full border p-3 rounded-lg mb-4" rows="5" placeholder="Tell us what you think..."></textarea>
            <div class="flex gap-2">
                <a href="dashboard.php" class="flex-1 text-center py-2 text-gray-500">Cancel</a>
                <button type="submit" class="flex-1 bg-black text-white py-2 rounded-lg font-bold">Send</button>
            </div>
        </form>
    </div>
</body>

</html>