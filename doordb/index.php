<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "rfid_database";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Access Logs</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="text-right mb-4 space-x-2">
    <a href="register_user.php" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        + Register User
    </a>
    <a href="manage_users.php" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
        ⚙ Manage Users
    </a>
</div>
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-semibold text-center mb-6">RFID Access Logs</h1>

        <div class="bg-white shadow-md rounded-lg p-6">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-gray-200 text-gray-700">
                        <th class="py-2 px-4 border">ID</th>
                        <th class="py-2 px-4 border">Username</th>
                        <th class="py-2 px-4 border">Role</th>
                        <th class="py-2 px-4 border">RFID Tag</th>
                        <th class="py-2 px-4 border">Action </th>
                        <th class="py-2 px-4 border">Timestamp</th>
                    </tr>
                </thead>
                <tbody id="rfidLogs"></tbody>
            </table>
        </div>
    </div>
    

    <script>
        function fetchLogs() {
            $.ajax({
                url: 'fetch_logs.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    let tableRows = '';
                    response.forEach(log => {
                        tableRows += `<tr class='border-b'>
                            <td class='py-2 px-4 border'>${log.id}</td>
                            <td class='py-2 px-4 border'>${log.username || 'Unknown'}</td>
                            <td class='py-2 px-4 border'>${log.role || 'N/A'}</td>
                            <td class='py-2 px-4 border'>${log.rfid_tag}</td>
                            <td class='py-2 px-4 border'>${log.status}</td>
                            <td class='py-2 px-4 border'>${log.timestamp}</td>
                        </tr>`;
                    });
                    $('#rfidLogs').html(tableRows);
                }
            });
        }
        setInterval(fetchLogs, 2000);
    </script>
</body>
</html>
