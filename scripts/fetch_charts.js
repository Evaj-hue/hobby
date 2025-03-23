document.addEventListener("DOMContentLoaded", function () {
    const container = document.getElementById('charts-container');
    let charts = {}; // Store chart instances

    function fetchDataAndUpdateCharts() {
        fetch("../admin/get_product_data.php")
            .then(response => response.json())
            .then(data => {
                console.log("Fetched Product Data:", data);

                if (!data || Object.keys(data).length === 0) {
                    container.innerHTML = "<p style='color: red;'>No product data found.</p>";
                    return;
                }

                Object.keys(data).forEach((category, index) => {
                    let chartId = `chart-${index}`;
                    let canvasId = `chartCanvas-${index}`;

                    if (!document.getElementById(chartId)) {
                        const section = document.createElement('div');
                        section.classList.add('chart-card');

                        section.innerHTML = `
                            <div class="chart-header">
                                <h2>${category}</h2>
                            </div>
                            <div class="chart-content" id="${chartId}">
                                <canvas id="${canvasId}"></canvas>
                            </div>
                        `;
                        container.appendChild(section);
                    }

                    let ctx = document.getElementById(canvasId)?.getContext('2d');
                    if (!ctx) {
                        console.error("Canvas not found:", canvasId);
                        return;
                    }

                    if (!charts[chartId]) {
                        charts[chartId] = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: data[category]["products"],
                                datasets: [{
                                    label: `Stock Levels - ${category}`,
                                    data: data[category]["stocks"],
                                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: { y: { beginAtZero: true } }
                            }
                        });
                    } else {
                        charts[chartId].data.labels = data[category]["products"];
                        charts[chartId].data.datasets[0].data = data[category]["stocks"];
                        charts[chartId].update();
                    }
                });
            })
            .catch(error => console.error("Error fetching product data:", error));
    }

    fetchDataAndUpdateCharts();
});
