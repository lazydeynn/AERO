<?php

if (!isset($conn)) {
    include_once '../config/db_conn.php';
}

$sidebar_user_id = $_SESSION['user_id'] ?? 0;

$langColors = [
    'Python' => ['bg' => 'bg-[#ff4b4b]', 'text' => 'text-white'],
    'C#' => ['bg' => 'bg-[#00b894]', 'text' => 'text-white'],
    'C++' => ['bg' => 'bg-[#8c7ae6]', 'text' => 'text-white'],
    'JavaScript' => ['bg' => 'bg-[#e1b12c]', 'text' => 'text-white'],
    'Java' => ['bg' => 'bg-[#ff7f50]', 'text' => 'text-white'],
    'Dart' => ['bg' => 'bg-[#2f80ed]', 'text' => 'text-white']
];

$sidebarLangs = $conn->query("SELECT * FROM languages ORDER BY language_id ASC");

$recentSql = "SELECT q.date_taken, t.title, l.name as lang_name, q.score, q.total_items
              FROM generated_quizzes q
              JOIN topics t ON q.topic_id = t.topic_id
              JOIN languages l ON t.language_id = l.language_id
              WHERE q.user_id = $sidebar_user_id
              ORDER BY q.date_taken DESC LIMIT 3";
$recentActivity = $conn->query($recentSql);

$suggestSql = "SELECT t.language_id, t.order_sequence, l.name as lang_name 
               FROM generated_quizzes q 
               JOIN topics t ON q.topic_id = t.topic_id
               JOIN languages l ON t.language_id = l.language_id
               WHERE q.user_id = $sidebar_user_id 
               ORDER BY q.date_taken DESC LIMIT 1";
$lastTaken = $conn->query($suggestSql)->fetch_assoc();

$suggestedTopic = null;
$suggestionText = "Start your journey today!";
$suggestionLink = "languages.php";
$btnText = "Browse Courses";

if ($lastTaken) {
    $nextSeq = $lastTaken['order_sequence'] + 1;
    $lid = $lastTaken['language_id'];

    $nextSql = "SELECT topic_id, title FROM topics 
                WHERE language_id = $lid AND order_sequence >= $nextSeq 
                ORDER BY order_sequence ASC LIMIT 1";
    $nextRes = $conn->query($nextSql);

    if ($nextRes->num_rows > 0) {
        $suggestedTopic = $nextRes->fetch_assoc();
        $suggestionText = "Since you finished the last topic, try <b>" . $suggestedTopic['title'] . "</b> next.";
        $suggestionLink = "topic_list.php?lang_id=$lid&topic_id=" . $suggestedTopic['topic_id'];
        $btnText = "Start " . $lastTaken['lang_name'];
    } else {
        $suggestionText = "You completed all topics in " . $lastTaken['lang_name'] . "! Try a new language.";
        $suggestionLink = "languages.php";
        $btnText = "Find New Course";
    }
} else {
    $suggestionText = "Ready to code? Start with Python Introduction.";
    $suggestionLink = "topic_list.php?lang_id=1";
    $btnText = "Start Python";
}
?>

<aside class="w-72 bg-white border-r border-gray-200 flex flex-col h-[calc(100vh-4rem)] sticky top-16 overflow-y-auto hidden lg:block no-scrollbar">
    <div class="p-6">

        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Course Library</h3>
        <div class="space-y-3">
            <?php while ($lang = $sidebarLangs->fetch_assoc()):
                $style = $langColors[$lang['name']] ?? ['bg' => 'bg-gray-600', 'text' => 'text-white'];
            ?>
                <a href="topic_list.php?lang_id=<?php echo $lang['language_id']; ?>"
                    class="flex justify-between items-center <?php echo $style['bg']; ?> <?php echo $style['text']; ?> p-3 rounded-xl hover:opacity-90 transition shadow-sm group">
                    <span class="font-bold text-sm flex items-center">
                        <?php echo $lang['name']; ?>
                    </span>
                    <span class="text-[10px] bg-white/20 px-2 py-1 rounded group-hover:bg-white/30 transition">
                        View
                    </span>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="mt-8 bg-gray-50 border border-gray-100 rounded-2xl p-5">
            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Recent Activity</h4>

            <div class="space-y-4">
                <?php if ($recentActivity->num_rows > 0): ?>
                    <?php while ($act = $recentActivity->fetch_assoc()): ?>
                        <div class="border-l-2 border-gray-200 pl-3">
                            <p class="text-xs font-semibold text-gray-800">
                                <?php echo $act['title']; ?>
                                <span class="text-[10px] text-gray-400 block font-normal">
                                    <?php echo $act['lang_name']; ?>
                                </span>
                            </p>
                            <p class="text-[10px] text-gray-400 mt-1">
                                Score: <?php echo $act['score']; ?>/<?php echo $act['total_items']; ?> &bull;
                                <?php echo date("M d, H:i", strtotime($act['date_taken'])); ?>
                            </p>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-xs text-gray-400 italic">No quizzes taken yet.</p>
                <?php endif; ?>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Recommended</h4>
                <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                    <?php echo $suggestionText; ?>
                </p>
                <a href="<?php echo $suggestionLink; ?>" class="block w-full bg-black text-white text-center text-xs font-bold py-2.5 rounded-lg hover:bg-gray-800 transition shadow-md">
                    <?php echo $btnText; ?>
                </a>
            </div>
        </div>

    </div>
</aside>