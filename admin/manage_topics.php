<?php
include '../config/db_conn.php';
include 'check_admin.php';

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $check = $conn->query("SELECT language_id FROM topics WHERE topic_id = $id")->fetch_assoc();
    $lid = $check['language_id'];
    $conn->query("DELETE FROM topics WHERE topic_id = $id");
    header("Location: manage_topics.php?view_lang=$lid&msg=deleted");
    exit;
}

$selectedLangId = isset($_GET['view_lang']) ? (int)$_GET['view_lang'] : 0;
$selectedLangName = "";

if ($selectedLangId > 0) {
    $sql = "SELECT t.* FROM topics t WHERE t.language_id = $selectedLangId ORDER BY t.order_sequence ASC";
    $result = $conn->query($sql);
    $langNameCheck = $conn->query("SELECT name FROM languages WHERE language_id = $selectedLangId")->fetch_assoc();
    $selectedLangName = $langNameCheck['name'];
} else {
    $sql = "SELECT l.*, (SELECT COUNT(*) FROM topics WHERE language_id = l.language_id) as topic_count FROM languages l";
    $result = $conn->query($sql);
}

$langColors = [
    'Python' => 'bg-[#ff4b4b]',
    'C#' => 'bg-[#00b894]',
    'C++' => 'bg-[#8c7ae6]',
    'JavaScript' => 'bg-[#e1b12c]',
    'Java' => 'bg-[#ff7f50]',
    'Dart' => 'bg-[#2f80ed]'
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Topics - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .card-hover-fix {
            -mask-image: -webkit-radial-gradient(white, black);
            transform: translateZ(0);
        }
    </style>
</head>

<body class="bg-gray-50 flex">

    <?php include('../components/admin_sidebar.php') ?>

    <main class="ml-64 flex-1 p-10 h-screen overflow-y-auto">

        <div class="flex justify-between items-end mb-10">
            <div>
                <?php if ($selectedLangId > 0): ?>
                    <a href="manage_topics.php" class="text-gray-400 hover:text-black text-xs font-bold mb-2 inline-flex items-center transition">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Back to Languages
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">
                        Manage <span class="<?php echo strtolower($selectedLangName) == 'python' ? 'text-[#ff4b4b]' : 'text-blue-600'; ?>"><?php echo htmlspecialchars($selectedLangName); ?></span>
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">Organize your lessons and materials.</p>
                <?php else: ?>
                    <h1 class="text-3xl font-bold text-gray-900">Course Management</h1>
                    <p class="text-sm text-gray-500 mt-1">Select a language module to manage its content.</p>
                <?php endif; ?>
            </div>

            <a href="add_topic.php<?php echo ($selectedLangId > 0) ? '?prefill=' . $selectedLangId : ''; ?>"
                class="bg-black text-white px-6 py-3 rounded-xl text-sm font-bold hover:bg-gray-800 transition shadow-lg flex items-center transform hover:-translate-y-0.5">
                <i class="fa-solid fa-plus mr-2"></i> Add Topic
            </a>
        </div>

        <?php if ($selectedLangId == 0): ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($lang = $result->fetch_assoc()):
                    $bgClass = $langColors[$lang['name']] ?? 'bg-gray-600';
                ?>
                    <a href="manage_topics.php?view_lang=<?php echo $lang['language_id']; ?>"
                        class="card-hover-fix group relative overflow-hidden rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 block h-48 <?php echo $bgClass; ?>">

                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white opacity-10 rounded-full group-hover:scale-150 transition-transform duration-500"></div>

                        <div class="relative z-10 p-6 h-full flex flex-col justify-between text-white">
                            <div class="flex justify-between items-start">
                                <h2 class="text-2xl font-bold tracking-wide"><?php echo $lang['name']; ?></h2>
                                <span class="bg-white/20 px-3 py-1 rounded-full text-[10px] font-bold backdrop-blur-md border border-white/10">
                                    ID: <?php echo $lang['language_id']; ?>
                                </span>
                            </div>

                            <div class="flex justify-between items-end">
                                <div>
                                    <span class="text-3xl font-bold block"><?php echo $lang['topic_count']; ?></span>
                                    <span class="text-xs opacity-80 uppercase tracking-wider font-semibold">Topics</span>
                                </div>
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm group-hover:bg-white group-hover:text-black transition-colors duration-300">
                                    <i class="fa-solid fa-arrow-right text-sm"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endwhile; ?>
            </div>

        <?php else: ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <?php if ($result->num_rows > 0): ?>
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-gray-50/50 text-gray-400 text-[10px] uppercase tracking-wider font-bold border-b border-gray-100">
                            <tr>
                                <th class="p-5 w-20 text-center">Seq</th>
                                <th class="p-5">Topic Title</th>
                                <th class="p-5 w-1/3">AI Context</th>
                                <th class="p-5 text-right w-32">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm text-gray-700 divide-y divide-gray-50">
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition group">
                                    <td class="p-5 text-center">
                                        <span class="inline-block w-8 h-8 leading-8 rounded-full bg-gray-100 text-gray-600 font-bold text-xs">
                                            <?php echo $row['order_sequence']; ?>
                                        </span>
                                    </td>
                                    <td class="p-5">
                                        <div class="font-bold text-gray-900 text-base mb-1">
                                            <?php echo htmlspecialchars($row['title']); ?>
                                        </div>
                                        <?php if (!empty($row['pdf_file_path'])): ?>
                                            <span class="inline-flex items-center text-[10px] text-gray-400 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                                <i class="fa-regular fa-file-pdf text-red-400 mr-1"></i> PDF Attached
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-5">
                                        <p class="text-xs text-gray-400 leading-relaxed line-clamp-2 italic">
                                            <?php echo htmlspecialchars(substr($row['ai_context_summary'], 0, 100)) . '...'; ?>
                                        </p>
                                    </td>
                                    <td class="p-5 text-right">
                                        <a href="manage_topics.php?delete_id=<?php echo $row['topic_id']; ?>"
                                            onclick="return confirm('Are you sure?');"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-300 hover:text-red-500 hover:bg-red-50 transition"
                                            title="Delete Topic">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fa-solid fa-layer-group text-gray-300 text-2xl"></i>
                        </div>
                        <h3 class="text-gray-900 font-bold mb-1">No topics yet</h3>
                        <p class="text-gray-500 text-sm mb-6">Start building the curriculum for this language.</p>
                        <a href="add_topic.php?prefill=<?php echo $selectedLangId; ?>" class="text-blue-600 font-bold hover:underline text-sm">
                            Create first topic
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </main>
</body>

</html>