<?php
include '../config/db_conn.php';
include '../components/admin_sidebar.php';

$sql = "SELECT f.*, u.fullname, u.email FROM feedback f JOIN users u ON f.user_id = u.user_id ORDER BY f.date_sent DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Feedback - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 flex">
    <main class="ml-64 flex-1 p-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">User Feedback</h1>
        <div class="grid gap-4">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex justify-between mb-2">
                        <h3 class="font-bold text-gray-900"><?php echo htmlspecialchars($row['fullname']); ?></h3>
                        <span class="text-xs text-gray-400"><?php echo date("M d, Y", strtotime($row['date_sent'])); ?></span>
                    </div>
                    <p class="text-gray-600 text-sm"><?php echo htmlspecialchars($row['message']); ?></p>
                    <div class="mt-3 pt-3 border-t border-gray-100 text-xs text-gray-400">
                        Contact: <?php echo htmlspecialchars($row['email']); ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </main>
</body>

</html>