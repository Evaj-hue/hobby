<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rfid_tag = strtoupper(trim($_POST["rfid_tag"]));
    $username = trim($_POST["username"]);
    $role = trim($_POST["role"]);

    // Check if RFID already exists
    $check = $conn->prepare("SELECT * FROM users WHERE rfid_tag = ?");
    $check->bind_param("s", $rfid_tag);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $message = "RFID tag is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (rfid_tag, username, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $rfid_tag, $username, $role);

        if ($stmt->execute()) {
            $message = "User registered successfully!";
        } else {
            $message = "Error: " . $stmt->error;
        }
        $stmt->close();
    }
    $check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register New RFID User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-4 text-center">Register RFID User</h2>
        
        <?php if ($message): ?>
            <div class="mb-4 p-3 rounded bg-blue-100 text-blue-800 text-center">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block font-medium">RFID Tag (Hex)</label>
                <input type="text" name="rfid_tag" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block font-medium">Username</label>
                <input type="text" name="username" class="w-full border rounded px-3 py-2" required>
            </div>
            <div>
                <label class="block font-medium">Role</label>
                <select name="role" class="w-full border rounded px-3 py-2" required>
                    <option value="admin">Admin</option>
                    <option value="barista">Barista</option>
                    <option value="staff">Staff</option>
                    
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-full">
                Register User
            </button>
        </form>
    </div>
    <div class="mb-4">
    <a href="index.php" class="bg-gray-500 text-white px-3 py-1 rounded hover:bg-gray-600">
        ← Back to Dashboard
    </a>
</div>
</body>
</html>
