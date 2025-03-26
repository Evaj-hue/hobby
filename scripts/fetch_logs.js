document.addEventListener("DOMContentLoaded", function () {
    const logsContainer = document.getElementById("logs-container");

    function fetchLogCount() {
        fetch("../admin/get_logs.php")
            .then(response => response.json())
            .then(logs => {
                let logCount = logs.length || 0; // Get total number of logs

                logsContainer.innerHTML = `
                    <div class="card text-white bg-success mb-3" style="max-width: 18rem;">
                        <div class="card-header">Activity Logs</div>
                        <div class="card-body">
                            <h5 class="card-title">${logCount} Logs</h5>
                            <p class="card-text">Total recorded activities.</p>
                        </div>
                    </div>
                `;
            })
            .catch(error => console.error("Error fetching logs:", error));
    }

    fetchLogCount();
    setInterval(fetchLogCount, 5000); // Refresh log count every 5 seconds
});
