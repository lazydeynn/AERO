<?php
include '../config/db_conn.php';
include 'check_admin.php';

if (isset($_GET['action'])) {
    $report_id = (int)$_GET['report_id'];

    if ($_GET['action'] == 'dismiss') {
        $conn->query("UPDATE reports SET status = 'resolved' WHERE report_id = $report_id");
    } elseif ($_GET['action'] == 'delete_content') {
        $r = $conn->query("SELECT target_type, target_id FROM reports WHERE report_id = $report_id")->fetch_assoc();
        if ($r) {
            if ($r['target_type'] == 'thread') {
                $conn->query("DELETE FROM forum_threads WHERE thread_id = " . $r['target_id']);
            } else {
                $conn->query("DELETE FROM forum_replies WHERE reply_id = " . $r['target_id']);
            }
            $conn->query("UPDATE reports SET status = 'resolved' WHERE report_id = $report_id");
        }
    }
    header("Location: manage_reports.php");
    exit;
}

$sql = "SELECT r.*, u.fullname as reporter_name 
        FROM reports r 
        JOIN users u ON r.user_id = u.user_id 
        WHERE r.status = 'pending' 
        ORDER BY r.date_reported DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Reports - AERO</title>
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

    <?php include('../components/admin_sidebar.php') ?>

    <main class="ml-64 flex-1 p-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Reports</h1>

        <?php if ($result->num_rows == 0): ?>
            <div class="bg-green-100 text-green-700 p-6 rounded-xl border border-green-200">
                <i class="fa-solid fa-check-circle mr-2"></i> All good! No pending reports.
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-xs uppercase font-bold text-gray-500">
                        <tr>
                            <th class="p-4">Reported By</th>
                            <th class="p-4">Type</th>
                            <th class="p-4">Reason</th>
                            <th class="p-4">Date</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="p-4 font-bold"><?php echo htmlspecialchars($row['reporter_name']); ?></td>
                                <td class="p-4 uppercase text-xs font-bold text-gray-500"><?php echo $row['target_type']; ?></td>
                                <td class="p-4 text-red-600 font-medium"><?php echo htmlspecialchars($row['reason']); ?></td>
                                <td class="p-4 text-gray-400"><?php echo date("M d", strtotime($row['date_reported'])); ?></td>
                                <td class="p-4 text-right space-x-2">
                                    <?php
                                    $link = ($row['target_type'] == 'thread') ? "../pages/view_thread.php?id=" . $row['target_id'] : "../pages/forum.php";
                                    ?>
                                    <a href="<?php echo $link; ?>" target="_blank" class="text-blue-500 hover:underline mr-2">View Context</a>

                                    <a href="manage_reports.php?action=dismiss&report_id=<?php echo $row['report_id']; ?>" class="bg-gray-200 text-gray-700 px-3 py-1 rounded text-xs font-bold hover:bg-gray-300">Dismiss</a>

                                    <a href="manage_reports.php?action=delete_content&report_id=<?php echo $row['report_id']; ?>" class="bg-red-600 text-white px-3 py-1 rounded text-xs font-bold hover:bg-red-700" onclick="return confirm('Permanently delete the reported content?');">Delete Content</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>

</html>