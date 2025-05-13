document.addEventListener("DOMContentLoaded", function () {
    const chartsContainer = document.getElementById("charts-container");

    function fetchCharts() {
        // Clear the container before appending new charts
        chartsContainer.innerHTML = "";

        fetch("../admin/get_product_logs.php")
            .then((response) => response.json())
            .then((data) => {
                // Iterate over the fetched data and create chart containers
                Object.entries(data).forEach(([category, details]) => {
                    const chartContainer = document.createElement("div");
                    chartContainer.classList.add("chart-container");

                    chartContainer.innerHTML = `
                        <div class="chart-header">
                            <h3>${category}</h3>
                        </div>
                        <canvas id="chart-${category}"></canvas>
                    `;

                    chartsContainer.appendChild(chartContainer);

                    // Render the chart with axis labels
                    const ctx = document.getElementById(`chart-${category}`).getContext("2d");
                    new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: details.products,
                            datasets: [{
                                label: "Stock Levels",
                                data: details.stocks,
                                backgroundColor: "rgba(72, 187, 120, 0.2)",
                                borderColor: "rgba(72, 187, 120, 1)",
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: "#ECC94B" // Customize legend text color
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    title: {
                                        display: true,
                                        text: "Products", // X-axis label
                                        color: "#ECC94B", // Customize label color
                                        font: {
                                            size: 14,
                                            weight: "bold"
                                        }
                                    },
                                    ticks: {
                                        color: "#E2E8F0", // Customize tick text color
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                y: {
                                    title: {
                                        display: true,
                                        text: "Stock", // Y-axis label
                                        color: "#ECC94B", // Customize label color
                                        font: {
                                            size: 14,
                                            weight: "bold"
                                        }
                                    },
                                    ticks: {
                                        color: "#E2E8F0", // Customize tick text color
                                        font: {
                                            size: 12
                                        }
                                    },
                                    beginAtZero: true // Ensure Y-axis starts at zero
                                }
                            }
                        }
                    });
                });
            })
            .catch((error) => console.error("Error fetching data:", error));
    }

    // Fetch charts once when the page loads
    fetchCharts();
});