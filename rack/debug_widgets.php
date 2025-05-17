<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Widget Debug Tool</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        pre {
            background: #f4f4f4;
            padding: 10px;
            border-radius: 5px;
        }
        .debug-section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .value-widget {
            text-align: center;
            padding: 10px;
            background: #f0f0f0;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .value-widget .label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .value-widget .value {
            font-size: 24px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1>Widget Debug Tool</h1>
        
        <div class="debug-section">
            <h2>Test Widgets</h2>
            <div class="row">
                <div class="col-md-3">
                    <div class="value-widget bg-warning bg-opacity-25">
                        <div class="label">Weight</div>
                        <div id="weight" class="value">0.00 kg</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="value-widget bg-success bg-opacity-25">
                        <div class="label">Items</div>
                        <div id="items" class="value">0</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="value-widget bg-danger bg-opacity-25">
                        <div class="label">Unrecognized</div>
                        <div id="unrecognized" class="value">NO</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="value-widget bg-primary bg-opacity-25">
                        <div class="label">Specified Items</div>
                        <div id="specified" class="value">0</div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button id="refresh" class="btn btn-primary">Refresh Now</button>
            </div>
        </div>
        
        <div class="debug-section">
            <h2>API Response</h2>
            <pre id="api-response">Loading...</pre>
        </div>
        
        <div class="debug-section">
            <h2>Latest Database Records</h2>
            <h3>Weight Table</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Weight</th>
                        <th>Time</th>
                        <th>Date</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include "config.php";
                    $sql = "SELECT id, weight, time, date, created_at FROM weight ORDER BY created_at DESC LIMIT 5";
                    $result = mysqli_query($conn, $sql);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['weight'] . "</td>";
                            echo "<td>" . $row['time'] . "</td>";
                            echo "<td>" . $row['date'] . "</td>";
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            
            <h3>Config Table</h3>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Item Weight</th>
                        <th>Tolerance</th>
                        <th>Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT id, item_weight, tolerance, updated_at FROM config ORDER BY id DESC LIMIT 1";
                    $result = mysqli_query($conn, $sql);
                    
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . $row['item_weight'] . "</td>";
                            echo "<td>" . $row['tolerance'] . "%</td>";
                            echo "<td>" . $row['updated_at'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No config found</td></tr>";
                    }
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
        function refreshWidgets() {
            // Add timestamp to prevent caching
            const timestamp = new Date().getTime();
            
            $.ajax({
                url: `getvalue.php?_=${timestamp}`,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Update widgets
                    $('#weight').text(data.weight.toFixed(2) + ' kg');
                    $('#items').text(data.item_count);
                    $('#unrecognized').text(data.unrecognized === 1 ? "YES" : "NO");
                    $('#specified').text(data.specified_items || 0);
                    
                    // Show raw response
                    $('#api-response').text(JSON.stringify(data, null, 2));
                },
                error: function(xhr, status, error) {
                    console.error("API error:", error);
                    $('#api-response').text("Error: " + error + "\n\nResponse: " + xhr.responseText);
                }
            });
        }
        
        // Refresh on page load
        $(document).ready(function() {
            refreshWidgets();
            
            // Refresh button click
            $('#refresh').click(function() {
                refreshWidgets();
            });
            
            // Auto refresh every 3 seconds
            setInterval(refreshWidgets, 3000);
        });
    </script>
</body>
</html>
