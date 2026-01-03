<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
if (!isset($_GET['id'])) {
    header("Location: forum.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$thread_id = (int)$_GET['id'];

$currentUser = $conn->query("SELECT role FROM users WHERE user_id = $user_id")->fetch_assoc();
$isAdmin = ($currentUser['role'] === 'admin');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_reply'])) {
    $body = trim($_POST['body']);
    if (!empty($body)) {
        $stmt = $conn->prepare("INSERT INTO forum_replies (thread_id, user_id, body) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $thread_id, $user_id, $body);
        $stmt->execute();
        header("Location: view_thread.php?id=$thread_id#latest");
        exit;
    }
}

$threadSql = "SELECT f.*, u.fullname, u.profile_image, u.role FROM forum_threads f JOIN users u ON f.user_id = u.user_id WHERE f.thread_id = $thread_id";
$threadResult = $conn->query($threadSql);
if ($threadResult->num_rows == 0) die("Thread not found.");
$thread = $threadResult->fetch_assoc();

$replySql = "SELECT r.*, u.fullname, u.profile_image, u.role FROM forum_replies r JOIN users u ON r.user_id = u.user_id WHERE r.thread_id = $thread_id ORDER BY r.created_at ASC";
$replies = $conn->query($replySql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($thread['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 h-screen flex flex-col overflow-hidden">

    <?php include '../components/navbar.php'; ?>

    <div class="flex flex-1 overflow-hidden">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto no-scrollbar">
            <div class="max-w-4xl mx-auto">
                <a href="forum.php" class="inline-flex items-center text-gray-500 hover:text-black mb-6 transition"><i class="fa-solid fa-arrow-left mr-2"></i> Back</a>

                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 mb-8 relative group">

                    <div class="absolute top-8 right-8 flex space-x-3">
                        <?php if ($thread['user_id'] == $user_id || $isAdmin): ?>
                            <a href="../actions/forum_action.php?action=delete&type=thread&id=<?php echo $thread['thread_id']; ?>"
                                onclick="return confirm('Delete this entire discussion?');"
                                class="text-gray-400 hover:text-red-600 transition" title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($thread['user_id'] != $user_id): ?>
                            <button onclick="openReport('thread', <?php echo $thread['thread_id']; ?>)" class="text-gray-400 hover:text-orange-500 transition" title="Report">
                                <i class="fa-solid fa-flag"></i>
                            </button>
                        <?php endif; ?>
                    </div>

                    <div class="flex items-center mb-6 border-b border-gray-100 pb-6">
                        <img src="<?php echo !empty($thread['profile_image']) ? "../assets/uploads/" . $thread['profile_image'] : "../assets/images/elements/no-profile.png"; ?>" class="w-12 h-12 rounded-full object-cover mr-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900"><?php echo htmlspecialchars($thread['title']); ?></h1>
                            <p class="text-xs text-gray-400">By <?php echo htmlspecialchars($thread['fullname']); ?> • <?php echo date("M d, Y", strtotime($thread['created_at'])); ?></p>
                        </div>
                    </div>
                    <div class="prose max-w-none text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($thread['body'])); ?></div>
                </div>

                <h3 class="text-lg font-bold text-gray-700 mb-6"><?php echo $replies->num_rows; ?> Replies</h3>
                <div class="space-y-6 mb-24">
                    <?php while ($rep = $replies->fetch_assoc()):
                        $isRepAdmin = ($rep['role'] == 'admin');
                        $borderClass = $isRepAdmin ? "border-l-4 border-purple-500 bg-purple-50/50" : "border border-gray-100";
                    ?>
                        <div class="bg-white p-6 rounded-xl shadow-sm <?php echo $borderClass; ?> relative group">

                            <div class="absolute top-4 right-4 flex space-x-2 opacity-0 group-hover:opacity-100 transition">
                                <?php if ($rep['user_id'] == $user_id || $isAdmin): ?>
                                    <a href="../actions/forum_action.php?action=delete&type=reply&id=<?php echo $rep['reply_id']; ?>"
                                        onclick="return confirm('Delete this comment?');"
                                        class="text-gray-400 hover:text-red-600 text-xs" title="Delete">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                                <?php if ($rep['user_id'] != $user_id): ?>
                                    <button onclick="openReport('reply', <?php echo $rep['reply_id']; ?>)" class="text-gray-400 hover:text-orange-500 text-xs" title="Report">
                                        <i class="fa-solid fa-flag"></i>
                                    </button>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-start">
                                <img src="<?php echo !empty($rep['profile_image']) ? "../assets/uploads/" . $rep['profile_image'] : "../assets/images/elements/no-profile.png"; ?>" class="w-8 h-8 rounded-full mr-3">
                                <div>
                                    <span class="font-bold text-sm text-gray-900 block"><?php echo htmlspecialchars($rep['fullname']); ?></span>
                                    <span class="text-[10px] text-gray-400"><?php echo date("M d, g:i a", strtotime($rep['created_at'])); ?></span>
                                    <p class="text-sm text-gray-700 mt-2"><?php echo nl2br(htmlspecialchars($rep['body'])); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <div id="latest"></div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-200 sticky bottom-6">
                    <form method="POST" class="flex gap-4">
                        <textarea name="body" required rows="1" class="flex-1 border border-gray-300 rounded-xl p-3 text-sm focus:outline-none focus:border-black" placeholder="Write a reply..."></textarea>
                        <button type="submit" name="post_reply" class="bg-black text-white px-6 py-2 rounded-xl font-bold text-sm hover:bg-gray-800">Post</button>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <div id="reportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl w-96">
            <h2 class="text-lg font-bold mb-4">Report Content</h2>
            <form action="../actions/forum_action.php" method="POST">
                <input type="hidden" name="report_content" value="1">
                <input type="hidden" name="target_type" id="reportType">
                <input type="hidden" name="target_id" id="reportId">
                <input type="hidden" name="thread_id_ref" value="<?php echo $thread_id; ?>">

                <label class="block text-sm text-gray-600 mb-2">Reason for reporting:</label>
                <select name="reason" class="w-full border border-gray-300 rounded p-2 mb-4 text-sm">
                    <option value="Spam">Spam</option>
                    <option value="Harassment">Harassment / Hate Speech</option>
                    <option value="Inappropriate Content">Inappropriate Content</option>
                    <option value="False Information">False Information</option>
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('reportModal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-500 hover:text-black">Cancel</button>
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-red-700">Submit Report</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openReport(type, id) {
            document.getElementById('reportType').value = type;
            document.getElementById('reportId').value = id;
            document.getElementById('reportModal').classList.remove('hidden');
        }
    </script>
</body>

</html>