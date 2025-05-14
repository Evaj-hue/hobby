document.addEventListener("DOMContentLoaded", function () {
    const notificationIcon = document.getElementById("notification-icon");
    const notificationCount = document.getElementById("notification-count");
    const notificationDropdown = document.getElementById("notification-dropdown");
    const notificationList = document.getElementById("notification-list");
    const notificationToggle = document.getElementById("notification-toggle");
    const clearAllButton = document.getElementById("clear-all-button");

    let notifications = []; // Store fetched notifications

    // Fetch latest merch and product logs
    function fetchNotifications() {
        Promise.all([
            fetch("../admin/get_merch_logs.php").then(res => res.json()),
            fetch("../admin/get_product_logs.php").then(res => res.json())
        ])
        .then(([merchLogs, productLogs]) => {
            notifications = []; // Reset notifications array

            // Process merch logs
            Object.keys(merchLogs).forEach(category => {
                merchLogs[category].products.forEach((product, index) => {
                    notifications.push({
                        source: "Merch",
                        action: "Removed",
                        name: product,
                        id: merchLogs[category].stocks[index],
                        redirect: `merch_detail.php?id=${merchLogs[category].stocks[index]}`
                    });
                });
            });

            // Process product logs
            Object.keys(productLogs).forEach(category => {
                productLogs[category].products.forEach((product, index) => {
                    notifications.push({
                        source: "Product",
                        action: "Added",
                        name: product,
                        id: productLogs[category].stocks[index],
                        redirect: `product_detail.php?id=${productLogs[category].stocks[index]}`
                    });
                });
            });

            // Sort notifications by most recent (if needed; assuming this is based on data order)
            notifications.reverse();

            // Limit to the 5 most recent notifications
            const recentNotifications = notifications.slice(0, 5);

            // Update notification count
            if (recentNotifications.length > 0) {
                notificationCount.textContent = recentNotifications.length;
                notificationCount.style.display = "block";
            } else {
                notificationCount.style.display = "none";
            }

            // Update notification list
            notificationList.innerHTML = recentNotifications.length
                ? recentNotifications
                    .map(
                        notif =>
                            `<li data-redirect="${notif.redirect}" data-id="${notif.id}">
                                <span>${notif.source}: ${notif.name}</span>
                                <span>${notif.action}</span>
                             </li>`
                    )
                    .join("")
                : "<li>No new notifications</li>";
        })
        .catch(error => console.error("Error fetching notifications:", error));
    }

    // Clear all notifications
    clearAllButton.addEventListener("click", function () {
        notifications = []; // Clear notifications array
        notificationList.innerHTML = "<li>No new notifications</li>";
        notificationCount.style.display = "none"; // Hide notification count
    });

    // Clear a specific notification and redirect
    notificationList.addEventListener("click", function (e) {
        const li = e.target.closest("li");
        if (li) {
            const redirectUrl = li.getAttribute("data-redirect");
            const notifId = li.getAttribute("data-id");

            // Remove the clicked notification
            notifications = notifications.filter(notif => notif.id !== notifId);
            fetchNotifications(); // Refresh the list

            // Redirect to the specified URL
            if (redirectUrl) {
                window.location.href = redirectUrl;
            }
        }
    });

    // Toggle dropdown visibility
    notificationToggle.addEventListener("click", function (e) {
        e.preventDefault();
        notificationIcon.classList.toggle("show");
    });

    // Fetch notifications every 5 seconds
    fetchNotifications();
    setInterval(fetchNotifications, 5000);
});