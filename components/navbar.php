<nav class="bg-[#1e1e1e] text-white h-20 flex items-center shadow-md sticky top-0 z-50">
    <div class="w-full flex justify-between items-center px-6 md:px-10">

        <a href="dashboard.php" class="hover:opacity-80 transition">
            <img src="../assets/images/logo/aero_white.svg" alt="AERO" class="h-6 w-auto">
        </a>

        <div class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-300">
            <a href="dashboard.php" class="hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'text-white font-bold' : ''; ?>">Home</a>
            <a href="quizzes.php" class="hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'quizzes.php' ? 'text-white font-bold' : ''; ?>">Quizzes</a>

            <?php
            $currentPage = basename($_SERVER['PHP_SELF']);
            $isLangActive = ($currentPage == 'languages.php' || $currentPage == 'language_intro.php' || $currentPage == 'topic_list.php');
            ?>
            <a href="languages.php" class="hover:text-white transition <?php echo $isLangActive ? 'text-white font-bold' : ''; ?>">Languages</a>
            <a href="progress.php" class="hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'progress.php' ? 'text-white font-bold' : ''; ?>">Progress</a>
            <a href="forum.php" class="hover:text-white transition <?php echo basename($currentPage == 'forum.php' || $currentPage == 'view_thread.php') ? 'text-white font-bold' : ''; ?>">Community</a>
            <a href="profile.php" class="hover:text-white transition <?php echo ($currentPage == 'profile.php' || $currentPage == 'downloads.php') ? 'text-white font-bold' : ''; ?>">Profile</a>
            <a href="about.php" class="hover:text-white transition <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'text-white font-bold' : ''; ?>">About</a>
            <a href="../auth/logout.php" class="hover:text-white transition">Logout</a>
        </div>

        <div class="md:hidden text-gray-300 cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </div>
    </div>
</nav>