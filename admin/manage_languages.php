<?php
include '../config/db_conn.php';
include '../components/admin_sidebar.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_lang'])) {
    $name = trim($_POST['name']);
    $desc = trim($_POST['description']);

    $icon = "default.png";
    if (!empty($_FILES['icon']['name'])) {
        $target_dir = "../assets/images/icons/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $fileName = basename($_FILES['icon']['name']);
        $target_file = $target_dir . $fileName;

        if (move_uploaded_file($_FILES['icon']['tmp_name'], $target_file)) {
            $icon = $fileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO languages (name, description, icon_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $desc, $icon);
    $stmt->execute();
    header("Location: manage_languages.php?msg=added");
    exit;
}

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    $topicsQuery = $conn->query("SELECT topic_id FROM topics WHERE language_id = $id");

    while ($topic = $topicsQuery->fetch_assoc()) {
        $tid = $topic['topic_id'];

        $conn->query("DELETE FROM saved_lessons WHERE topic_id = $tid");
        $conn->query("DELETE FROM user_progress WHERE topic_id = $tid");
        $conn->query("DELETE FROM generated_quizzes WHERE topic_id = $tid");
    }

    $conn->query("DELETE FROM topics WHERE language_id = $id");
    $conn->query("DELETE FROM languages WHERE language_id = $id");
    header("Location: manage_languages.php?msg=deleted");
    exit;
}

$languages = $conn->query("SELECT * FROM languages");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Courses - Admin</title>
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

    <?php include('../components/admin_sidebar.php'); ?>

    <main class="ml-64 flex-1 p-10">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Course Management</h1>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold border-b">
                            <tr>
                                <th class="p-4">Icon</th>
                                <th class="p-4">Course Name</th>
                                <th class="p-4">Description</th>
                                <th class="p-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <?php while ($row = $languages->fetch_assoc()): ?>
                                <tr class="border-b last:border-0 hover:bg-gray-50">
                                    <td class="p-4">
                                        <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center overflow-hidden border border-gray-100 p-1">
                                            <img src="../assets/images/icons/<?php echo htmlspecialchars($row['icon_path']); ?>"
                                                alt="<?php echo $row['name']; ?>"
                                                class="w-full h-full object-contain"
                                                onerror="this.src='../assets/images/icons/default_code.png'">
                                        </div>
                                    </td>
                                    <td class="p-4 font-bold text-gray-800"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td class="p-4 text-gray-500 truncate max-w-xs"><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td class="p-4 text-right">
                                        <a href="manage_languages.php?delete_id=<?php echo $row['language_id']; ?>"
                                            onclick="return confirm('Delete this course? All topics inside it will be lost.');"
                                            class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 px-3 py-1 rounded">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">Add New Course</h2>
                    <form method="POST" enctype="multipart/form-data" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Course Name</label>
                            <input type="text" name="name" required class="w-full border p-2 rounded focus:border-black outline-none" placeholder="e.g. Ruby">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                            <textarea name="description" required rows="3" class="w-full border p-2 rounded focus:border-black outline-none" placeholder="Short description..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Icon (PNG/JPG)</label>
                            <input type="file" name="icon" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-black file:text-white hover:file:bg-gray-800 cursor-pointer">
                        </div>

                        <button type="submit" name="add_lang" class="w-full bg-black text-white font-bold py-3 rounded-lg hover:bg-gray-800 transition shadow-lg">
                            Create Course
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</body>

</html>