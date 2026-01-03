<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_thread'])) {
    $title = trim($_POST['title']);
    $body = trim($_POST['body']);

    if (!empty($title) && !empty($body)) {
        $stmt = $conn->prepare("INSERT INTO forum_threads (user_id, title, body) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $body);
        $stmt->execute();
        header("Location: forum.php?success=created");
        exit;
    }
}

$sql = "SELECT f.*, u.fullname, u.profile_image, 
        (SELECT COUNT(*) FROM forum_replies WHERE thread_id = f.thread_id) as reply_count 
        FROM forum_threads f 
        JOIN users u ON f.user_id = u.user_id 
        ORDER BY f.created_at DESC";
$threads = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Community Forum - AERO</title>
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
            <div class="max-w-5xl mx-auto">

                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Community Forum</h1>
                        <p class="text-sm text-gray-500">Ask questions, share solutions, and help others.</p>
                    </div>
                    <button onclick="toggleModal('thread-modal')" class="bg-black text-white px-6 py-3 rounded-full font-bold shadow-lg hover:bg-gray-800 transition">
                        + New Discussion
                    </button>
                </div>

                <div class="space-y-4">
                    <?php if ($threads->num_rows > 0): ?>
                        <?php while ($row = $threads->fetch_assoc()):
                            $avatar = !empty($row['profile_image']) ? "../assets/uploads/" . $row['profile_image'] : "../assets/images/elements/no-profile.png";
                        ?>
                            <a href="view_thread.php?id=<?php echo $row['thread_id']; ?>" class="block bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div class="flex items-start space-x-4">
                                        <img src="<?php echo $avatar; ?>" class="w-10 h-10 rounded-full object-cover">
                                        <div>
                                            <h3 class="font-bold text-lg text-gray-900 mb-1"><?php echo htmlspecialchars($row['title']); ?></h3>
                                            <p class="text-gray-500 text-sm line-clamp-2"><?php echo htmlspecialchars(substr($row['body'], 0, 150)) . '...'; ?></p>
                                            <div class="mt-3 flex items-center space-x-4 text-xs text-gray-400">
                                                <span>Posted by <span class="font-bold text-gray-600"><?php echo htmlspecialchars($row['fullname']); ?></span></span>
                                                <span>•</span>
                                                <span><?php echo date("M d, Y", strtotime($row['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 text-gray-400 bg-gray-50 px-3 py-1 rounded-lg">
                                        <i class="fa-regular fa-comment"></i>
                                        <span class="text-sm font-bold"><?php echo $row['reply_count']; ?></span>
                                    </div>
                                </div>
                            </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-20 text-gray-400">
                            <i class="fa-regular fa-comments text-4xl mb-4"></i>
                            <p>No discussions yet. Be the first to post!</p>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </main>
    </div>

    <div id="thread-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded-2xl p-8 relative">
            <button onclick="toggleModal('thread-modal')" class="absolute top-4 right-4 text-gray-400 hover:text-black">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
            <h2 class="text-2xl font-bold mb-6">Start a Discussion</h2>
            <form method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" required class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:border-black" placeholder="What's on your mind?">
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Details</label>
                    <textarea name="body" required rows="5" class="w-full border border-gray-300 p-3 rounded-lg focus:outline-none focus:border-black" placeholder="Describe your question or topic..."></textarea>
                </div>
                <button type="submit" name="create_thread" class="w-full bg-blue-600 text-white font-bold py-3 rounded-xl hover:bg-blue-700 transition">Post Thread</button>
            </form>
        </div>
    </div>

    <script>
        function toggleModal(id) {
            document.getElementById(id).classList.toggle('hidden');
        }
    </script>
</body>

</html>