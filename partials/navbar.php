<div class="navbar">
    <h2>Admin</h2>
    <div class="nav-links">
        <!-- Combined Notification Icon with Dropdown -->
        <div class="notification-dropdown">
            <i class="fa fa-bell" id="notification-icon"></i>
            <span class="badge" id="notification-badge"></span> <!-- Badge for all notifications -->
            <div class="dropdown-menu dark-theme" id="notification-dropdown">
                <!-- Notification tabs -->
                <div class="notification-tabs">
                    <button class="tab-button active" data-target="activity-tab">Activity</button>
                    <button class="tab-button" data-target="stock-tab">Low Stock</button>
                </div>
                
                <!-- Activity Tab Content -->
                <div class="tab-content active" id="activity-tab">
                    <h6 class="dropdown-header">Recent Activity</h6>
                    <ul id="notification-list">
                        <!-- Dynamic Notifications will be appended here -->
                    </ul>
                    <div class="view-all">
                        <a href="activity_logs.php" id="view-all-link">View All Activity</a>
                    </div>
                </div>
                
                <!-- Stock Tab Content -->
                <div class="tab-content" id="stock-tab">
                    <h6 class="dropdown-header">Low Stock Items</h6>
                    <ul id="stock-notification-list">
                        <!-- Dynamic Low Stock Notifications will be appended here -->
                    </ul>
                </div>
            </div>
        </div>
        
        <a href="profile.php">Profile</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<!-- Navbar CSS -->
<style>
     /* Navbar Styling */
    .navbar {
        width: calc(100% - 200px); /* Adjust for sidebar width */
        height: 60px;
        background: #253529; /* Dark theme */
        color: white;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 20px;
        position: fixed;
        top: 0;
        left: 200px; /* Offset to align with sidebar */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1030; /* Ensure navbar stays above other elements */
        transition: left 0.3s ease; /* Smooth transition for responsiveness */
    }

    /* Navbar link styling */
    .nav-links a {
        color: white;
        text-decoration: none;
        margin-left: 20px;
    }

    .nav-links a:hover {
        text-decoration: underline;
    }

    /* Content styling to avoid overlap with navbar */
    .content {
        margin-top: 60px; /* Same height as the navbar */
        padding: 20px; /* Add padding to the content area */
    }

    /* Responsive Adjustments */
    @media screen and (max-width: 768px) {
        .navbar {
            width: 100%; /* Full width */
            left: 0; /* Align navbar with hidden sidebar */
        }

        .nav-links {
            display: flex;
            flex-wrap: wrap; /* Ensure links wrap on smaller screens */
        }

        .nav-links a {
            margin-left: 10px; /* Reduce space for smaller screens */
        }
    }
    .notification-dropdown {
        position: relative;
        display: inline-block;
        margin-right: 15px;
    }

    .notification-dropdown .fa-bell {
        font-size: 20px;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .notification-dropdown .fa-bell:hover {
        color: #4caf50;
    }
    
    .notification-dropdown .fa-bell.active {
        color: #4caf50;
    }

    .notification-dropdown .badge {
        position: absolute;
        top: -5px;
        right: -10px;
        background: red;
        color: white;
        font-size: 12px;
        border-radius: 50%;
        padding: 2px 6px;
        transition: all 0.3s;
    }
    
    .notification-dropdown .badge.active {
        background: #4caf50;
    }

    /* Tabs styling */
    .notification-tabs {
        display: flex;
        border-bottom: 1px solid #444;
        background-color: #222;
    }
    
    .tab-button {
        flex: 1;
        background: transparent;
        border: none;
        color: #ccc;
        padding: 10px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: bold;
    }
    
    .tab-button:hover {
        background-color: #333;
    }
    
    .tab-button.active {
        background-color: #333;
        color: #4caf50;
        border-bottom: 2px solid #4caf50;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }

    .dropdown-header {
        color: #4caf50;
        font-weight: bold;
        padding: 8px 10px;
        border-bottom: 1px solid #444;
        text-align: center;
    }

    .notification-dropdown .dropdown-menu {
        display: none;
        position: absolute;
        top: 30px;
        right: 0;
        background: #333; /* Dark background */
        color: #fff; /* White text */
        width: 350px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        z-index: 1031;
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 0.3s, transform 0.3s;
    }
    
    .notification-dropdown .dropdown-menu.show {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    .notification-dropdown .dropdown-menu ul {
        list-style: none;
        margin: 0;
        padding: 10px;
        max-height: 300px;
        overflow-y: auto; /* Scrollable if too many notifications */
    }

    .notification-dropdown .dropdown-menu ul li {
        padding: 10px;
        border-bottom: 1px solid #444; /* Subtle divider */
        line-height: 1.5;
        font-size: 0.9rem;
    }

    .notification-dropdown .dropdown-menu ul li:last-child {
        border-bottom: none;
    }

    .notification-dropdown .dropdown-menu ul li:hover {
        background: #444; /* Subtle hover effect */
        cursor: pointer;
    }
    
    .notification-dropdown .dropdown-menu ul li .timestamp {
        display: block;
        font-size: 0.75rem;
        color: #aaa;
        margin-top: 4px;
    }
    
    .notification-dropdown .dropdown-menu ul li .details {
        display: block;
        margin-top: 4px;
        color: #ccc;
    }

    .notification-dropdown .view-all {
        text-align: center;
        padding: 10px;
        background: #222; /* Darker background */
        border-top: 1px solid #444;
    }

    .notification-dropdown .view-all a {
        text-decoration: none;
        color: #4caf50; /* Green link for contrast */
        font-weight: bold;
    }

    .notification-dropdown .view-all a:hover {
        text-decoration: underline;
    }
    
    /* Alert link styling */
    .alert-link {
        color: #4caf50;
        text-decoration: none;
        font-weight: bold;
    }
    
    .alert-link:hover {
        text-decoration: underline;
    }
    
    /* Highlight classes for different notification types */
    .text-warning {
        color: #ffc107 !important;
    }
    
    .text-danger {
        color: #dc3545 !important;
    }
    
    .text-success {
        color: #28a745 !important;
    }
</style>

<!-- JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Notification elements
        const notificationIcon = document.getElementById("notification-icon");
        const notificationBadge = document.getElementById("notification-badge");
        const notificationList = document.getElementById("notification-list");
        const stockNotificationList = document.getElementById("stock-notification-list");
        const notificationDropdown = document.getElementById("notification-dropdown");
        
        const LOCAL_STORAGE_KEY = "lastSeenTimestamp"; // Key for local storage

        let isDropdownOpen = false;
        let activityCount = 0;
        let stockCount = 0;

        // Get the base URL for the application
        const baseUrl = window.location.origin + '/idealcozydesign/';

        // Retrieve the last seen timestamp from local storage (default to 0 if not found)
        let lastSeenTimestamp = localStorage.getItem(LOCAL_STORAGE_KEY) || 0;

        // Fetch activity notifications
        async function fetchNotifications() {
            try {
                const response = await fetch(`${baseUrl}admin/get_notificationlogs.php?last_seen=${lastSeenTimestamp}`);
                const logs = await response.json();

                if (logs.error) {
                    console.error("Error:", logs.error);
                    return;
                }

                notificationList.innerHTML = ""; // Clear current notifications

                if (logs.message) {
                    notificationList.innerHTML = `<li>${logs.message}</li>`;
                    activityCount = 0;
                } else {
                    logs.forEach(log => {
                        const li = document.createElement("li");
                        const timestamp = new Date(log.timestamp * 1000);
                        li.innerHTML = `
                            <strong>${log.action}</strong>
                            <span class="details">${log.details}</span>
                            <span class="timestamp">${timestamp.toLocaleString()}</span>
                            <a href="${baseUrl}admin/activity_logs.php?user=${log.username}" class="alert-link">View All by ${log.username}</a>
                        `;
                        notificationList.appendChild(li);
                    });

                    // Update the last seen timestamp to the most recent log
                    if (logs.length > 0) {
                        lastSeenTimestamp = logs[0].timestamp;
                    }
                    
                    // Update activity count
                    activityCount = logs.length;
                }

                // Update badge count (combined count of activity and stock)
                updateNotificationBadge();
            } catch (error) {
                console.error("Error fetching notifications:", error);
            }
        }

        // Fetch low stock items
        async function fetchLowStockItems() {
            try {
                const response = await fetch(`${baseUrl}admin/get_low_stock.php`);
                const data = await response.json();

                stockNotificationList.innerHTML = ""; // Clear current low stock items

                if (data.error) {
                    console.error("Error:", data.error);
                    return;
                }

                if (data.message) {
                    stockNotificationList.innerHTML = `<li>${data.message}</li>`;
                    stockCount = 0;
                } else {
                    data.forEach(item => {
                        const li = document.createElement("li");
                        // Calculate stock percentage
                        const stockPercentage = item.stock_threshold > 0 ? 
                            Math.round((item.units_in_stock / item.stock_threshold) * 100) : 0;
                        
                        // Set color class based on stock level
                        let colorClass = "text-danger";
                        if (stockPercentage >= 75) {
                            colorClass = "text-success";
                        } else if (stockPercentage >= 50) {
                            colorClass = "text-warning";
                        }
                        
                        li.innerHTML = `
                            <strong>${item.product_name}</strong>
                            <span class="${colorClass}">${item.units_in_stock} units left</span>
                            <span class="details">Threshold: ${item.stock_threshold} | Max: ${item.max_stock || 'Not set'}</span>
                            <span class="text-warning">Suggested reorder: ${item.reorder_amount} units</span>
                            <a href="${baseUrl}admin/${item.type === 'product' ? 'manage_products.php' : 'manage_merch.php'}?highlight=${item.id}" 
                               class="alert-link">Manage Item</a>
                        `;
                        stockNotificationList.appendChild(li);
                    });

                    // Update stock count
                    stockCount = data.length;
                }

                // Update badge count (combined count of activity and stock)
                updateNotificationBadge();
            } catch (error) {
                console.error("Error fetching low stock items:", error);
            }
        }
        
        // Update the notification badge with total count
        function updateNotificationBadge() {
            const totalCount = activityCount + stockCount;
            if (totalCount > 0) {
                notificationBadge.textContent = totalCount;
                notificationBadge.style.display = "block";
            } else {
                notificationBadge.style.display = "none";
            }
        }

        // Toggle notifications dropdown with animations
        function toggleNotificationDropdown(isOpen) {
            if (isOpen) {
                notificationDropdown.classList.add('show');
                notificationIcon.classList.add('active');
                
                // Hide the badge when dropdown is opened
                notificationBadge.style.display = "none";
                
                // Save current notification state
                if (activityCount > 0 || stockCount > 0) {
                    // Save the last seen timestamp to local storage
                    localStorage.setItem(LOCAL_STORAGE_KEY, lastSeenTimestamp);
                    
                    // Reset the counts as they've been viewed
                    activityCount = 0;
                    stockCount = 0;
                }
            } else {
                notificationDropdown.classList.remove('show');
                notificationIcon.classList.remove('active');
                notificationBadge.classList.remove('active');
            }
        }

        // Notification dropdown toggle
        notificationIcon.addEventListener("click", function(e) {
            e.stopPropagation();
            isDropdownOpen = !isDropdownOpen;
            toggleNotificationDropdown(isDropdownOpen);
        });

        // Close dropdown if clicked outside
        document.addEventListener("click", function(event) {
            if (isDropdownOpen && !notificationDropdown.contains(event.target) && event.target !== notificationIcon) {
                isDropdownOpen = false;
                toggleNotificationDropdown(false);
            }
        });

        // Prevent dropdown closing when clicking inside it
        notificationDropdown.addEventListener("click", function(e) {
            e.stopPropagation();
        });
        
        // Tab switching functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Show corresponding content
                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
            });
        });

        // Fix the View All Activity link
        document.getElementById("view-all-link").href = baseUrl + "admin/activity_logs.php";

        // Fetch data on page load
        fetchNotifications();
        fetchLowStockItems();

        // Periodically fetch new data
        setInterval(fetchNotifications, 30000); // Every 30 seconds
        setInterval(fetchLowStockItems, 60000); // Every minute
    });
</script>