<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rack Notification Test</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../rack/rack_notifications.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Rack Notification Test</h1>
        <p>Use this page to test sending rack notifications to the navbar notification system.</p>
        
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                Send Test Notification
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="notif-type" class="form-label">Notification Type</label>
                    <select id="notif-type" class="form-select">
                        <option value="info">Info</option>
                        <option value="warning">Warning</option>
                        <option value="alert">Alert</option>
                        <option value="low_stock">Low Stock</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="notif-message" class="form-label">Message</label>
                    <input type="text" id="notif-message" class="form-control" 
                        value="This is a test rack notification">
                </div>
                
                <div class="mb-3">
                    <label for="notif-key" class="form-label">Unique Key</label>
                    <input type="text" id="notif-key" class="form-control" 
                        value="test_notification">
                </div>
                
                <button id="send-btn" class="btn btn-primary">Send Notification</button>
            </div>
        </div>
        
        <div id="result" class="alert" style="display: none;"></div>
    </div>
    
    <script>
        $(function() {
            $('#send-btn').click(function() {
                const type = $('#notif-type').val();
                const message = $('#notif-message').val();
                const key = $('#notif-key').val();
                
                // Clear previous result
                const result = $('#result');
                result.removeClass('alert-success alert-danger').hide();
                
                if (!message) {
                    result.addClass('alert-danger')
                          .text('Message is required')
                          .show();
                    return;
                }
                
                try {
                    // Call the notification function
                    addRackNotification(type, message, key);
                    
                    result.addClass('alert-success')
                          .html('Notification sent!<br>Check the navbar notification icon.')
                          .show();
                } catch (e) {
                    result.addClass('alert-danger')
                          .text('Error: ' + e.message)
                          .show();
                }
            });
        });
    </script>
</body>
</html>
