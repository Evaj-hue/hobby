/**
 * Rack Notification System
 * 
 * This script integrates the rack system with the navbar notification system.
 * It provides functions to create notifications that will appear in the navbar.
 */

// Track recently sent notifications to avoid duplicates
const sentNotifications = {
  unrecognized: 0,
  low_stock: 0,
  empty: 0,
  item_added: 0,
  item_removed: 0
};

/**
 * Add a notification to the navbar notification system
 * 
 * @param {string} type - The notification type (info, warning, alert, low_stock)
 * @param {string} message - The notification message
 * @param {string} key - A unique key to avoid duplicate notifications
 */
function addRackNotification(type, message, key) {
  // Prevent sending duplicate notifications within 5 minutes
  const now = Date.now();
  const cooldownPeriod = 5 * 60 * 1000; // 5 minutes in milliseconds
  
  if (sentNotifications[key] && (now - sentNotifications[key] < cooldownPeriod)) {
    console.log(`Skipping duplicate notification (${key}): too recent`);
    return;
  }
  
  // Record this notification
  sentNotifications[key] = now;
  
  // Map type to action for activity_logs
  let action = 'Rack Info';
  if (type === 'warning' || type === 'unrecognized') {
    action = 'Rack Warning';
  } else if (type === 'alert' || type === 'empty') {
    action = 'Rack Alert';
  } else if (type === 'low_stock') {
    action = 'Rack Low Stock';
  }
  
  // Create notification data
  const notificationData = {
    username: 'System',
    action: action,
    details: message,
    link: 'rack/rack_dashboard.php'
  };
  
  console.log(`Sending notification: ${message}`);
  
  // Use AJAX to send the notification to the server
  $.ajax({
    url: '../admin/add_activity_log.php',
    type: 'POST',
    data: notificationData,
    success: function(response) {
      console.log('Notification sent successfully:', response);
    },
    error: function(xhr, status, error) {
      console.error('Error sending notification:', error);
    }
  });
}

// Initialize when document is ready
$(document).ready(function() {
  console.log('Rack notification system initialized.');
});
