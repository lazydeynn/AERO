<?php
include '../config/db_conn.php';
include '../components/admin_sidebar.php';

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    if ($id == $_SESSION['user_id']) {
        header("Location: manage_users.php?error=cannot_delete_self");
        exit;
    }

    $conn->query("DELETE FROM users WHERE user_id = $id");
    header("Location: manage_users.php?msg=deleted");
    exit;
}

$sql = "SELECT * FROM users ORDER BY date_registered DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Users - AERO</title>
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
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">User Management</h1>
            <div class="text-sm text-gray-500">
                Total Users: <span class="font-bold text-black"><?php echo $result->num_rows; ?></span>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm">
                <p class="font-bold">Error</p>
                <p>You cannot delete your own admin account while logged in.</p>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                    <tr>
                        <th class="p-4 border-b">ID</th>
                        <th class="p-4 border-b">User</th>
                        <th class="p-4 border-b">Role</th>
                        <th class="p-4 border-b">Registered</th>
                        <th class="p-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-sm text-gray-700">
                    <?php while ($row = $result->fetch_assoc()):
                        $roleColor = ($row['role'] == 'admin') ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600';
                        $avatar = !empty($row['profile_image']) ? "../assets/uploads/" . $row['profile_image'] : "../assets/images/elements/no-profile.png";
                    ?>
                        <tr class="hover:bg-gray-50 transition border-b border-gray-100">
                            <td class="p-4 text-gray-400">#<?php echo $row['user_id']; ?></td>

                            <td class="p-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 mr-3 overflow-hidden">
                                        <img src="<?php echo $avatar; ?>" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900"><?php echo htmlspecialchars($row['fullname']); ?></div>
                                        <div class="text-xs text-gray-400"><?php echo htmlspecialchars($row['email']); ?></div>
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="px-2 py-1 rounded text-xs font-bold uppercase <?php echo $roleColor; ?>">
                                    <?php echo $row['role']; ?>
                                </span>
                            </td>

                            <td class="p-4 text-gray-500">
                                <?php echo date("M d, Y", strtotime($row['date_registered'])); ?>
                            </td>

                            <td class="p-4 text-right">
                                <?php if ($row['role'] !== 'admin'): ?>
                                    <a href="user_detail.php?id=<?php echo $row['user_id']; ?>" class="text-blue-600 hover:underline text-xs mr-3">View Progress</a>
                                    <a href="manage_users.php?delete_id=<?php echo $row['user_id']; ?>"
                                        onclick="return confirm('Are you sure? This user and their progress will be permanently deleted.');"
                                        class="text-red-500 hover:text-red-700 font-bold text-xs bg-red-50 hover:bg-red-100 px-3 py-1 rounded transition">
                                        Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-xs text-gray-300 italic">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>