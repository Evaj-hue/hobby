<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: index.html');
    exit();
}

// Include database connection
include '../includes/db.php';

// Fetch total activity logs
$totalActivitiesSql = "SELECT COUNT(*) as total FROM activity_log";
$totalActivitiesResult = $conn->query($totalActivitiesSql);
$totalActivities = $totalActivitiesResult->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch total merch activity logs
$totalMerchSql = "SELECT COUNT(*) as total FROM activity_log WHERE action LIKE '%merch%'";
$totalMerchResult = $conn->query($totalMerchSql);
$totalMerch = $totalMerchResult->fetch(PDO::FETCH_ASSOC)['total'];

// Fetch total product activity logs
$totalProductSql = "SELECT COUNT(*) as total FROM activity_log WHERE action LIKE '%product%'";
$totalProductResult = $conn->query($totalProductSql);
$totalProduct = $totalProductResult->fetch(PDO::FETCH_ASSOC)['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Admin Dashboard</title>
    <style>
        body {
            background-color: #253529;
            color: #E2E8F0;
            font-family: Arial, sans-serif;
        }

        .content-container {
            margin-left: 200px;
            margin-top: 60px;
            padding: 20px;
        }

        .divider {
            height: 4px;
            background-color: #ECC94B;
            margin: 20px 0;
            border-radius: 2px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-header h1 {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .section-header.products h1 {
            color: #ED7117; /* Orange for Product Stock Levels */
        }

        .section-header.merch h1 {
            color: #3AB54A; /* Green for Merch Stock Levels */
        }

        .section-header .toggle-icon {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ECC94B;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .chart-container {
            background-color: #2D3748;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 20px;
            text-align: center;
        }

        .chart-header h3 {
            color: #ECC94B;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        canvas {
            display: block;
            max-width: 100%;
            max-height: 300px;
        }

        /* Widget CSS */
        .widget-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
        }

        .widget {
            flex: 1;
            margin: 0 10px;
            padding: 20px;
            background-color: #2D3748;
            border-radius: 10px;
            text-align: center;
            color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .widget:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .widget h5 {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .widget p {
            font-size: 2.5rem;
            margin: 0;
        }
    </style>
</head>
<body>

<?php include("../partials/sidebar.php"); ?>
<?php include("../partials/navbar.php"); ?>

<div class="content-container">
    <!-- Widgets Section -->
    <div class="widget-container">
        <a href="activity_logs.php?filter=all" class="widget bg-success text-white">
            <h5>Total Activity Logs</h5>
            <p><?php echo $totalActivities; ?></p>
        </a>
        <a href="activity_logs.php?filter=merch" class="widget bg-primary text-white">
            <h5>Merch Activity Logs</h5>
            <p><?php echo $totalMerch; ?></p>
        </a>
        <a href="activity_logs.php?filter=product" class="widget bg-warning text-dark">
            <h5>Product Activity Logs</h5>
            <p><?php echo $totalProduct; ?></p>
        </a>
    </div>

    <!-- Show All Button -->
    <button id="show-all" class="btn btn-warning mb-4">Show All Charts</button>

    <!-- Product Stock Levels Section -->
    <div class="section-header products">
        <h1>Product Stock Levels</h1>
        <span class="toggle-icon" onclick="toggleSection('product-charts-container')">▼</span>
    </div>
    <div class="divider"></div>
    <div class="charts-grid product-charts-container cards-container" id="product-charts-container"></div>

    <!-- Divider above Merch Stock Levels -->
    <div class="divider"></div>

    <!-- Merch Stock Levels Section -->
    <div class="section-header merch">
        <h1>Merch Stock Levels</h1>
        <span class="toggle-icon" onclick="toggleSection('merch-charts-container')">▼</span>
    </div>
    <div class="divider"></div>
    <div class="charts-grid merch-charts-container cards-container" id="merch-charts-container"></div>
</div>

<script>
    // Function to toggle the visibility of a section
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const icon = document.querySelector(`.toggle-icon[onclick="toggleSection('${sectionId}')"]`);

        if (section.style.display === "none" || !section.style.display) {
            section.style.display = "grid"; // Use "grid" to retain the layout
            icon.textContent = "▲"; // Update icon to up arrow
        } else {
            section.style.display = "none"; // Hide the section
            icon.textContent = "▼"; // Update icon to down arrow
        }
    }

    // Function to show all charts
    document.getElementById("show-all").addEventListener("click", () => {
        document.getElementById("product-charts-container").style.display = "grid";
        document.getElementById("merch-charts-container").style.display = "grid";

        // Update icons to the "up arrow"
        const icons = document.querySelectorAll(".toggle-icon");
        icons.forEach(icon => {
            icon.textContent = "▲";
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
        const productChartsContainer = document.getElementById("product-charts-container");
        const merchChartsContainer = document.getElementById("merch-charts-container");

        // Fetch and render product charts
        function fetchProductCharts() {
            productChartsContainer.innerHTML = "";

            fetch("../admin/get_product_logs.php")
                .then(response => response.json())
                .then(data => {
                    Object.entries(data).forEach(([category, details]) => {
                        const chartContainer = document.createElement("div");
                        chartContainer.classList.add("chart-container");

                        chartContainer.innerHTML = `
                            <div class="chart-header">
                                <h3>${category}</h3>
                            </div>
                            <canvas id="product-chart-${category}"></canvas>
                        `;

                        productChartsContainer.appendChild(chartContainer);

                        const ctx = document.getElementById(`product-chart-${category}`).getContext("2d");
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
                                maintainAspectRatio: true,
                                animation: { // Restore animations
                                    duration: 1000,
                                    easing: "easeOutBounce"
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        labels: {
                                            color: "#ECC94B"
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: "Products",
                                            color: "#ECC94B",
                                            font: {
                                                size: 14,
                                                weight: "bold"
                                            }
                                        },
                                        ticks: {
                                            color: "#E2E8F0",
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: "Stock",
                                            color: "#ECC94B",
                                            font: {
                                                size: 14,
                                                weight: "bold"
                                            }
                                        },
                                        ticks: {
                                            color: "#E2E8F0",
                                            font: {
                                                size: 12
                                            }
                                        },
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    });
                })
                .catch(error => console.error("Error fetching product data:", error));
        }

        // Fetch and render merch charts
        function fetchMerchCharts() {
            merchChartsContainer.innerHTML = "";

            fetch("../admin/get_merch_logs.php")
                .then(response => response.json())
                .then(data => {
                    Object.entries(data).forEach(([category, details]) => {
                        const chartContainer = document.createElement("div");
                        chartContainer.classList.add("chart-container");

                        chartContainer.innerHTML = `
                            <div class="chart-header">
                                <h3>${category}</h3>
                            </div>
                            <canvas id="merch-chart-${category}"></canvas>
                        `;

                        merchChartsContainer.appendChild(chartContainer);

                        const ctx = document.getElementById(`merch-chart-${category}`).getContext("2d");
                        new Chart(ctx, {
                            type: "bar",
                            data: {
                                labels: details.products,
                                datasets: [{
                                    label: "Stock Levels",
                                    data: details.stocks,
                                    backgroundColor: "rgba(75, 192, 192, 0.2)",
                                    borderColor: "rgba(75, 192, 192, 1)",
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                animation: { // Restore animations
                                    duration: 1000,
                                    easing: "easeOutBounce"
                                },
                                plugins: {
                                    legend: {
                                        display: true,
                                        labels: {
                                            color: "#ECC94B"
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        title: {
                                            display: true,
                                            text: "Merch Items",
                                            color: "#ECC94B",
                                            font: {
                                                size: 14,
                                                weight: "bold"
                                            }
                                        },
                                        ticks: {
                                            color: "#E2E8F0",
                                            font: {
                                                size: 12
                                            }
                                        }
                                    },
                                    y: {
                                        title: {
                                            display: true,
                                            text: "Stock",
                                            color: "#ECC94B",
                                            font: {
                                                size: 14,
                                                weight: "bold"
                                            }
                                        },
                                        ticks: {
                                            color: "#E2E8F0",
                                            font: {
                                                size: 12
                                            }
                                        },
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    });
                })
                .catch(error => console.error("Error fetching merch data:", error));
        }

        // Fetch both product and merch charts
        fetchProductCharts();
        fetchMerchCharts();
    });
</script>
</body>
</html>