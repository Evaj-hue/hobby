<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Handle update
if (isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    $stmt->close();
    $message = "Role updated successfully!";
}

// Handle delete
if (isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
    $message = "User deleted successfully!";
}

// Fetch all users
$result = $conn->query("SELECT * FROM users ORDER BY username ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage RFID Users</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-4xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Manage RFID Users</h2>

        <?php if (!empty($message)): ?>
            <div class="mb-4 p-3 rounded bg-green-100 text-green-800 text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <table class="min-w-full table-auto border border-gray-300">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="py-2 px-4 border">Username</th>
                    <th class="py-2 px-4 border">RFID Tag</th>
                    <th class="py-2 px-4 border">Role</th>
                    <th class="py-2 px-4 border text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $result->fetch_assoc()): ?>
                    <tr class="border-b">
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <td class="py-2 px-4 border"><?= htmlspecialchars($user['username']) ?></td>
                            <td class="py-2 px-4 border"><?= htmlspecialchars($user['rfid_tag']) ?></td>
                            <td class="py-2 px-4 border">
                                <select name="role" class="border rounded px-2 py-1 w-full">
                                    <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="barista" <?= $user['role'] == 'barista' ? 'selected' : '' ?>>Barista</option>
                                    <option value="staff" <?= $user['role'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                                    <option value="guest" <?= $user['role'] == 'guest' ? 'selected' : '' ?>>Guest</option>
                                </select>
                            </td>
                            <td class="py-2 px-4 border text-center space-x-2">
                                <button type="submit" name="update" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600">
                                    Save
                                </button>
                                <button type="submit" name="delete" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                    onclick="return confirm('Are you sure you want to delete this user?');">
                                    Delete
                                </button>
                            </td>
                        </form>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div class="mb-4">
    <a href="index.php" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">
        ← Back to Dashboard
    </a>
</div>
</body>
</html>

<?php $conn->close(); ?>
