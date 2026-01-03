<?php
include '../config/db_conn.php';
include 'check_admin.php';

$message = "";
$error = "";

$prefill_lang = isset($_GET['prefill']) ? (int)$_GET['prefill'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lang_id = (int)$_POST['language_id'];
    $title = trim($_POST['title']);
    $sequence = (int)$_POST['order_sequence'];
    $content = $_POST['content_description'];
    $ai_summary = $_POST['ai_context_summary'];

    $pdf_path = NULL;
    if (!empty($_FILES['pdf_file']['name'])) {
        $target_dir = "../assets/pdfs/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $fileName = time() . "_" . preg_replace('/[^a-zA-Z0-9\._-]/', '', basename($_FILES["pdf_file"]["name"]));
        $target_file = $target_dir . $fileName;

        if (move_uploaded_file($_FILES["pdf_file"]["tmp_name"], $target_file)) {
            $pdf_path = "assets/pdfs/" . $fileName;
        } else {
            $error = "Failed to upload PDF.";
        }
    }

    if (empty($title) || empty($content) || empty($ai_summary)) {
        $error = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO topics (language_id, title, content_description, ai_context_summary, order_sequence, pdf_file_path) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssis", $lang_id, $title, $content, $ai_summary, $sequence, $pdf_path);

        if ($stmt->execute()) {
            header("Location: manage_topics.php?view_lang=$lang_id&msg=added");
            exit;
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

$langs = $conn->query("SELECT * FROM languages ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Topic - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 flex">

    <?php include '../components/admin_sidebar.php'; ?>

    <main class="ml-64 flex-1 p-10 h-screen overflow-y-auto">

        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Add New Topic</h1>
            <a href="manage_topics.php<?php echo $prefill_lang ? '?view_lang=' . $prefill_lang : ''; ?>" class="text-gray-500 hover:text-black font-bold text-sm">
                <i class="fa-solid fa-arrow-left mr-1"></i> Cancel
            </a>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Error</p>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <form method="POST" enctype="multipart/form-data" class="space-y-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Programming Language</label>
                        <div class="relative">
                            <select name="language_id" class="w-full border border-gray-200 bg-gray-50 p-3 rounded-xl focus:outline-none focus:border-black focus:bg-white transition appearance-none font-semibold">
                                <?php while ($l = $langs->fetch_assoc()): ?>
                                    <option value="<?php echo $l['language_id']; ?>" <?php echo ($prefill_lang == $l['language_id']) ? 'selected' : ''; ?>>
                                        <?php echo $l['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-4 text-gray-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sequence Number</label>
                        <input type="number" name="order_sequence" value="1" class="w-full border border-gray-200 bg-gray-50 p-3 rounded-xl focus:outline-none focus:border-black focus:bg-white transition font-semibold">
                        <p class="text-[10px] text-gray-400 mt-1 ml-1">Order in which the topic appears (1, 2, 3...)</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Topic Title</label>
                    <input type="text" name="title" required class="w-full border border-gray-200 bg-gray-50 p-4 rounded-xl focus:outline-none focus:border-black focus:bg-white transition leading-relaxed" placeholder="e.g. Introduction to Variables">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Lesson Content <span class="text-gray-300 font-normal normal-case">(Displayed in app)</span>
                    </label>
                    <textarea name="content_description" required rows="8" class="w-full border border-gray-200 bg-gray-50 p-4 rounded-xl focus:outline-none focus:border-black focus:bg-white transition leading-relaxed" placeholder="Type or paste the full lesson content here..."></textarea>
                </div>

                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                    <label class="block text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-robot mr-1"></i> AI Context Summary
                    </label>
                    <textarea name="ai_context_summary" required rows="4" class="w-full border border-blue-200 bg-white p-4 rounded-xl focus:outline-none focus:border-blue-500 transition text-sm" placeholder="Provide a dense summary of facts. The AI uses THIS text to generate quiz questions. Include definitions, key terms, and logic rules."></textarea>
                    <p class="text-[10px] text-blue-400 mt-2 font-medium">
                        * Essential for the Quiz Generator. If this is empty or too short, the quiz may fail.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Attach PDF Resource (Optional)</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500"><span class="font-bold">Click to upload</span> or drag and drop</p>
                                <p class="text-xs text-gray-400">PDF only (MAX. 5MB)</p>
                            </div>
                            <input type="file" name="pdf_file" accept="application/pdf" class="hidden" />
                        </label>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition shadow-lg transform active:scale-[0.99]">
                        Save Topic
                    </button>
                </div>

            </form>
        </div>

    </main>
</body>

</html>