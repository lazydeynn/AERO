<?php
session_start();
include '../config/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Languages - AERO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/style.css?v=2.1">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50">

    <?php include '../components/navbar.php'; ?>

    <div class="flex">
        <?php include '../components/sidebar.php'; ?>

        <main class="flex-1 p-8 overflow-y-auto h-[calc(100vh-4rem)]">

            <div class="max-w-6xl mx-auto">
                <div class="relative w-full mb-10">
                    <input type="text" placeholder="Search topics..." class="w-full p-4 pl-12 rounded-full border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-black transition">
                    <svg class="w-5 h-5 absolute left-5 top-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold mb-6 text-gray-800">Language Modules</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php
                    $sql = "SELECT * FROM languages";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $colorClass = 'bg-gray-100';
                            if ($row['name'] == 'Python') $colorClass = 'bg-[#ff4b4b]';
                            if ($row['name'] == 'C#') $colorClass = 'bg-[#00b894]';
                            if ($row['name'] == 'JavaScript') $colorClass = 'bg-[#e1b12c]';
                            if ($row['name'] == 'Java') $colorClass = 'bg-[#ff7f50]';
                            if ($row['name'] == 'C++') $colorClass = 'bg-[#8c7ae6]';
                            if ($row['name'] == 'Dart') $colorClass = 'bg-[#2f80ed]';
                    ?>
                            <div class="bg-white rounded-2xl shadow-sm p-6 relative hover-card border border-gray-100 flex flex-col justify-between h-64">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-2xl <?php echo $colorClass; ?> flex items-center justify-center text-white text-xl font-bold mb-4 shadow-lg transform -translate-y-2">
                                        <?php echo substr($row['name'], 0, 2); ?>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-800"><?php echo $row['name']; ?></h3>
                                </div>
                                <p class="text-xs text-gray-500 text-center mt-2 px-2 line-clamp-2">
                                    <?php echo $row['description']; ?>
                                </p>
                                <div class="mt-4 flex justify-end items-center border-t border-gray-100 pt-3">
                                    <a href="language_intro.php?lang_id=<?php echo $row['language_id']; ?>"
                                        class="text-xs font-bold text-gray-400 hover:text-gray-800 flex items-center transition-colors">
                                        Explore <span class="ml-1 text-lg">›</span>
                                    </a>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </main>
    </div>
</body>

</html>