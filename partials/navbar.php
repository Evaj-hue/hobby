<div class="navbar">
    <h2>Admin</h2>
    <div class="nav-links">
        <!-- Notification Icon with Dropdown -->
        <div class="notification-dropdown">
            <i class="fa fa-bell" id="notification-icon"></i>
            <span class="badge" id="notification-badge"></span> <!-- Badge for new notifications -->
            <div class="dropdown-menu dark-theme" id="notification-dropdown">
                <ul id="notification-list">
                    <!-- Dynamic Notifications will be appended here -->
                </ul>
                <div class="view-all">
                    <a href="activity_logs.php" id="view-all-link">View All</a>
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
    }

    .notification-dropdown .fa-bell {
        font-size: 20px;
        cursor: pointer;
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
        display: none; /* Initially hidden */
    }

    .notification-dropdown .dropdown-menu {
        display: none;
        position: absolute;
        top: 30px;
        right: 0;
        background: #333; /* Dark background */
        color: #fff; /* White text */
        width: 250px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border-radius: 5px;
        z-index: 1031;
    }

    .notification-dropdown .dropdown-menu ul {
        list-style: none;
        margin: 0;
        padding: 10px;
        max-height: 200px;
        overflow-y: auto; /* Scrollable if too many notifications */
    }

    .notification-dropdown .dropdown-menu ul li {
        padding: 10px;
        border-bottom: 1px solid #444; /* Subtle divider */
    }

    .notification-dropdown .dropdown-menu ul li:last-child {
        border-bottom: none;
    }

    .notification-dropdown .dropdown-menu ul li:hover {
        background: #444; /* Subtle hover effect */
        cursor: pointer;
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

    .notification-dropdown:hover .dropdown-menu {
        display: block;
    }
</style>

<!-- JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const notificationIcon = document.getElementById("notification-icon");
        const notificationBadge = document.getElementById("notification-badge");
        const notificationList = document.getElementById("notification-list");
        const dropdownMenu = document.getElementById("notification-dropdown");
        const LOCAL_STORAGE_KEY = "lastSeenTimestamp"; // Key for local storage

        let isDropdownOpen = false;

        // Retrieve the last seen timestamp from local storage (default to 0 if not found)
        let lastSeenTimestamp = localStorage.getItem(LOCAL_STORAGE_KEY) || 0;

        async function fetchNotifications() {
            try {
                const response = await fetch(`../admin/get_notificationlogs.php?last_seen=${lastSeenTimestamp}`);
                const logs = await response.json();

                if (logs.error) {
                    console.error("Error:", logs.error);
                    return;
                }

                notificationList.innerHTML = ""; // Clear current notifications

                if (logs.message) {
                    notificationList.innerHTML = `<li>${logs.message}</li>`;
                } else {
                    logs.forEach(log => {
                        const li = document.createElement("li");
                        li.textContent = `${log.action}: ${log.details} (${new Date(log.timestamp * 1000).toLocaleString()})`;
                        notificationList.appendChild(li);
                    });

                    // Update the last seen timestamp to the most recent log
                    if (logs.length > 0) {
                        lastSeenTimestamp = logs[0].timestamp;
                    }
                }

                // Update badge count
                const newNotificationCount = logs.length;
                if (newNotificationCount > 0) {
                    notificationBadge.textContent = newNotificationCount;
                    notificationBadge.style.display = "block";
                } else {
                    notificationBadge.style.display = "none";
                }
            } catch (error) {
                console.error("Error fetching notifications:", error);
            }
        }

        // Add click event listener to bell icon
        notificationIcon.addEventListener("click", function () {
            isDropdownOpen = !isDropdownOpen;
            dropdownMenu.style.display = isDropdownOpen ? "block" : "none";

            if (isDropdownOpen) {
                // Clear the notification badge
                notificationBadge.style.display = "none";

                // Save the last seen timestamp to local storage
                localStorage.setItem(LOCAL_STORAGE_KEY, lastSeenTimestamp);
            }
        });

        // Close dropdown if clicked outside
        document.addEventListener("click", function (event) {
            if (!notificationIcon.contains(event.target) && !dropdownMenu.contains(event.target)) {
                dropdownMenu.style.display = "none";
                isDropdownOpen = false;
            }
        });

        // Fetch notifications on page load
        fetchNotifications();

        // Periodically fetch new notifications
        setInterval(fetchNotifications, 30000); // Every 30 seconds
    });
</script>