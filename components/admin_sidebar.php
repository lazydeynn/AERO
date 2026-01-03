<?php
$current_page = basename($_SERVER['PHP_SELF']);

$active_class = "bg-blue-600 text-white font-bold shadow-md";
$inactive_class = "text-gray-400 hover:bg-gray-800 hover:text-white transition";
?>

<aside class="w-64 bg-black text-white h-screen flex flex-col fixed z-50">

    <div class="h-20 flex items-center justify-center border-b border-gray-800 gap-3">
        <img src="../assets/images/logo/aero_white.svg" alt="AERO" class="h-4 w-auto">

        <span class="text-blue-500 text-[10px] font-bold uppercase tracking-widest border border-blue-500 px-2 py-0.5 rounded">
            ADMIN
        </span>
    </div>

    <nav class="flex-1 p-4 space-y-2 overflow-y-auto">

        <a href="dashboard.php" class="block py-3 px-4 rounded-lg <?php echo ($current_page == 'dashboard.php') ? $active_class : $inactive_class; ?>">
            <i class="fa-solid fa-chart-line mr-3 w-5"></i> Dashboard
        </a>

        <a href="manage_languages.php" class="block py-3 px-4 rounded-lg <?php echo ($current_page == 'manage_languages.php') ? $active_class : $inactive_class; ?>">
            <i class="fa-solid fa-layer-group mr-3 w-5"></i> Manage Courses
        </a>

        <a href="manage_topics.php" class="block py-3 px-4 rounded-lg <?php echo ($current_page == 'manage_topics.php' || $current_page == 'add_topic.php') ? $active_class : $inactive_class; ?>">
            <i class="fa-solid fa-book-open mr-3 w-5"></i> Manage Topics
        </a>

        <a href="manage_users.php" class="block py-3 px-4 rounded-lg <?php echo ($current_page == 'manage_users.php' || $current_page == 'user_detail.php') ? $active_class : $inactive_class; ?>">
            <i class="fa-solid fa-users mr-3 w-5"></i> Manage Users
        </a>

        <a href="manage_feedback.php" class="block py-3 px-4 rounded-lg <?php echo ($current_page == 'manage_feedback.php') ? $active_class : $inactive_class; ?>">
            <i class="fa-solid fa-comment-dots mr-3 w-5"></i> User Feedback
        </a>

        <?php
        $report_active = "bg-red-600 text-white font-bold shadow-md";
        $report_inactive = "text-red-400 hover:bg-gray-800 hover:text-red-300 transition";
        ?>
        <a href="manage_reports.php" class="block py-3 px-4 rounded-lg <?php echo ($current_page == 'manage_reports.php') ? $report_active : $report_inactive; ?>">
            <i class="fa-solid fa-flag mr-3 w-5"></i> Reports
        </a>

    </nav>

    <div class="p-4 border-t border-gray-800">
        <a href="../auth/logout.php" class="flex items-center justify-center space-x-2 py-2 text-xs text-gray-500 hover:text-white transition">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>

</aside>