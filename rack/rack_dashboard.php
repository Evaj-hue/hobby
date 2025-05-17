<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>📦 CozyRack - Real-Time Load Cell Monitor</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap + Chart.js -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
         <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

 <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
      margin-left: 200px; /* space for sidebar */
      padding-top: 60px; /* Space for fixed navbar */
    }
    .monitor {
      display: flex;
      gap: 30px;
      justify-content: center;
      margin-top: 40px;
      flex-wrap: wrap;
    }
    .card-box {
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      text-align: center;
      height: 220px;
    }
    .weight-box { background-color: #fff3cd; }
    .item-box { background-color: #d1e7dd; }
    .unrecognized-box { background-color: #f8d7da; }
    .specified-box { 
      background-color: #d0e6ff; 
      background-image: linear-gradient(135deg, #d0e6ff 0%, #b8d9f3 100%);
    }
    .label { font-size: 1.2rem; font-weight: 600; margin-bottom: 10px; }
    .value { font-size: 2.5rem; font-weight: bold; }
    .graph-section {
      display: flex;
      gap: 30px;
      justify-content: center;
      flex-wrap: wrap;
      margin: 50px auto;
      max-width: 1100px;
    }
    .graph-container {
      display: flex;
      justify-content: center;
      gap: 30px;
      margin: 40px auto;
      flex-wrap: wrap;
    }
    .graph-box {
      width: 400px;
    }
    .table-container {
      width: calc(100% - 220px);
      margin: 30px auto;
    }
    .table th {
      background-color: #007bff;
      color: white;
    }
    .btns {
      text-align: center;
      margin-bottom: 20px;
    }
    
    /* Updated styling for navbar integration */
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
      margin-left: 200px; /* space for sidebar */
      padding-top: 60px; /* Space for fixed navbar */
    }
    
    .main-content {
      padding: 20px;
      transition: margin-left 0.3s ease;
    }
    
    @media (max-width: 768px) {
      body {
        margin-left: 0;
      }
      .table-container {
        width: 95%;
      }
    }
    
    /* Dark mode compatibility for navbar */
    .dark-mode-compatible {
      background-color: #253529;
      color: white;
    }
    
    /* Ensure consistent spacing with navbar */
    .container-fluid {
      padding-top: 20px;
    }
  </style>
</head>
<body>
  <?php include("../partials/navbar.php"); ?>
  <?php include("../partials/sidebar.php"); ?> 

  <div class="main-content">
    <h2 class="text-center mt-4">📦 CozyRack - Real-Time Load Cell Monitor</h2>
    <div class="container my-4 d-flex justify-content-between align-items-center">
      <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#rackSettingsModal">
        ⚙️ Rack Settings
      </button>
      
      <div class="d-flex align-items-center">
        <!-- Metric System Dropdown -->
        <div class="metric-selector me-3">
          <label for="metricSystem" class="form-label me-2">Metric System:</label>
          <select id="metricSystem" class="form-select form-select-sm" style="width: 150px; display: inline-block">
            <option value="kg" selected>Kilograms (kg)</option>
            <option value="g">Grams (g)</option>
            <option value="ml">Milliliters (ml)</option>
            <option value="l">Liters (l)</option>
          </select>
        </div>
        
        <!-- Chart Time Range Selector -->
        <div class="time-range-selector">
          <label for="timeRange" class="form-label me-2">Chart Range:</label>
          <select id="timeRange" class="form-select form-select-sm" style="width: 120px; display: inline-block">
            <option value="30">30 seconds</option>
            <option value="60" selected>1 minute</option>
            <option value="300">5 minutes</option>
            <option value="900">15 minutes</option>
            <option value="1800">30 minutes</option>
          </select>
        </div>
      </div>
    </div>
   
    <!-- Info Boxes -->
    <div class="monitor">
      <div class="card-box weight-box">
        <div class="label">Real-Time Weight</div>
        <div id="weight" class="value">0.00 kg</div>
      </div>
      <div class="card-box item-box">
        <div class="label">Total Items</div>
        <div id="items" class="value">0</div>
      </div>
      <div class="card-box unrecognized-box">
        <div class="label">Unrecognized Weight</div>
        <div id="unrecognized" class="value">0</div>
      </div>
      <!-- New Widget: Specified Weight Items -->
      <div class="card-box specified-box">
        <div class="label">Specified Weight Items</div>
        <div id="specifiedItems" class="value">0</div>
        <div class="small text-muted">Maximum number of standard items that can be extracted</div>
      </div>
    </div>
    <!-- Graphs -->
    <div class="graph-container">
      <div class="graph-box">
        <canvas id="weightChart"></canvas>
      </div>
      <div class="graph-box">
        <canvas id="itemChart"></canvas>
      </div>
    </div>

    <!-- Buttons -->
    <div class="btns">
      <button class="btn btn-primary" onclick="downloadCSV()">Export Graph Data as CSV</button>
      <button class="btn btn-secondary" onclick="downloadPNG()">Download Graph as PNG</button>
      <button class="btn btn-danger" onclick="resetGraphs()">Reset Graph</button>
    </div>

    <!-- Weight changes table -->
    <div class="table-container">
      <h3 class="text-center mb-4">📊 Weight Changes History</h3>
      <table id="weightChangesTable" class="table table-bordered text-center">
        <thead>
          <tr>
            <th>#</th>
            <th>Weight (kg)</th>
            <th>Time</th>
            <th>Date</th>
            <th>Item Count</th>
            <th>Operation</th>
          </tr>
        </thead>
        <tbody>
          <!-- Data from AJAX will go here -->
        </tbody>
      </table>
    </div>
    <div id="warningBox" class="alert alert-warning text-center" style="display: none;"></div>

    <div class="table-container">
      <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <a href="all_warnings.php" class="btn btn-outline-danger">
            🔍 View All Warnings</a>
          <h4 class="text-center text-danger">⚠️ Unrecognized Weight Warnings</h4>
          <div></div> <!-- Spacer to keep title centered -->
        </div>
      </div>
      <table id="warningsTable" class="table table-bordered table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Weight (kg)</th>
            <th>Message</th>
            <th>Time</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody id="warningTableBody"></tbody>
      </table>
    </div>
    <!-- Debug links for troubleshooting -->
    <div class="container my-2">
      <div class="text-center">
        <a href="debug_data.php" target="_blank" class="btn btn-sm btn-info">Debug Database</a>
        <a href="getvalue.php" target="_blank" class="btn btn-sm btn-secondary">Test API Response</a>
        <a href="debug_warnings.php" target="_blank" class="btn btn-sm btn-warning">Debug Warnings</a>
        <button id="simulateUnrecognized" class="btn btn-sm btn-danger">Simulate Unrecognized Weight</button>
      </div>
    </div>
  </div>

  <?php include("../modal/rack_settings_modal.php"); ?>

  <!-- Make sure script paths are correct -->
  <script src="../script/rack_settings_modal.js"></script>

  <script>
$(function() {
  // Settings form
  $.get('get_config.php', function(data) {
    try {
      let config = JSON.parse(data);
      $('#itemWeight').val(config.item_weight);
      $('#tolerance').val(config.tolerance);
    } catch (e) {
      console.error("Error parsing config:", e);
    }
  });

  // Time-related variables for chart updates
  const chartUpdateInterval = 3000; // Update interval in milliseconds - CHANGED FROM 1000 to 3000
  let lastChartUpdate = 0;
  let chartUpdateThreshold = 5000; // Only update chart every 5 seconds by default
  let maxDataPoints = 12; // Maximum number of data points to display
  
  // Unit conversion factors and labels
  const unitFactors = {
    'kg': 1,        // Base unit
    'g': 1000,      // 1kg = 1000g
    'ml': 1000,     // Assuming 1kg = 1000ml (water density)
    'l': 1          // Assuming 1kg = 1L (water density)
  };
  
  const unitLabels = {
    'kg': 'Weight (kg)',
    'g': 'Weight (g)',
    'ml': 'Volume (ml)',
    'l': 'Volume (l)'
  };

  let currentUnit = 'kg'; // Default unit
  
  // Function to convert weight to selected unit
  function convertValue(weight, targetUnit) {
    return weight * unitFactors[targetUnit];
  }
  
  // Function to format value with appropriate decimal places
  function formatValue(value, unit) {
    if (unit === 'g' || unit === 'ml') {
      return Math.round(value); // No decimals for g and ml
    } else {
      return value.toFixed(2); // 2 decimals for kg and l
    }
  }

  // Handle metric system change
  $('#metricSystem').change(function() {
    currentUnit = $(this).val();
    
    // Update chart labels
    weightChart.data.datasets[0].label = unitLabels[currentUnit];
    weightChart.options.scales.y.title.text = unitLabels[currentUnit];
    weightChart.update();
    
    // Force refresh display to update widgets
    refreshDisplay();
  });

  // Handle chart time range change - FIXED: Added proper initialization
  $('#timeRange').change(function() {
    const timeRangeSeconds = parseInt($(this).val());
    chartUpdateThreshold = Math.max(1000, Math.floor(timeRangeSeconds * 1000 / maxDataPoints));
    
    // Clear current data when changing time range
    resetGraphs();
    
    console.log(`Chart range changed to ${timeRangeSeconds}s, updates every ${chartUpdateThreshold/1000}s`);
  });

  // Initialize chartUpdateThreshold based on selected timeRange at startup
  const initialTimeRange = parseInt($('#timeRange').val());
  chartUpdateThreshold = Math.max(1000, Math.floor(initialTimeRange * 1000 / maxDataPoints));
  console.log(`Initial chart update interval: ${chartUpdateThreshold/1000}s`);

  // Charts setup
  let lastWeight = 0;
  let lastItemCount = 0;
  const weightData = [], itemData = [], labels = [];
  
  // Weight Chart with enhanced options
  const weightChart = new Chart(document.getElementById('weightChart'), {
    type: 'line', 
    data: { 
      labels, 
      datasets: [{ 
        label: unitLabels['kg'], // Default label
        data: weightData, 
        borderColor: 'orange',
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderWidth: 2,
        tension: 0.4, // Increased for smoother curves
        fill: false 
      }] 
    }, 
    options: { 
      responsive: true,
      animation: {
        duration: 500 // Faster animations
      },
      plugins: {
        title: {
          display: true,
          text: 'Real-Time Measurement Monitoring',
          font: {
            size: 16,
            weight: 'bold'
          },
          padding: {
            top: 10,
            bottom: 20
          }
        },
        legend: {
          position: 'top',
        },
        decimation: {
          enabled: true,
          algorithm: 'min-max'
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Time',
            font: {
              weight: 'bold'
            },
            padding: {top: 10}
          },
          ticks: {
            maxRotation: 0, // Don't rotate labels
            maxTicksLimit: 6 // Limit number of ticks for readability
          }
        },
        y: {
          title: {
            display: true,
            text: unitLabels['kg'], // Default y-axis label
            font: {
              weight: 'bold'
            },
            padding: {right: 10}
          },
          beginAtZero: true
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      }
    }
  });
  
  // Item Count Chart with enhanced options
  const itemChart = new Chart(document.getElementById('itemChart'), {
    type: 'line', 
    data: { 
      labels, 
      datasets: [{ 
        label: 'Item Count', 
        data: itemData, 
        borderColor: 'green',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        borderWidth: 2,
        tension: 0.4,
        fill: false 
      }] 
    }, 
    options: { 
      responsive: true,
      animation: {
        duration: 500
      },
      plugins: {
        title: {
          display: true,
          text: 'Item Count Tracking',
          font: {
            size: 16,
            weight: 'bold'
          },
          padding: {
            top: 10,
            bottom: 20
          }
        },
        legend: {
          position: 'top',
        },
        decimation: {
          enabled: true,
          algorithm: 'min-max'
        }
      },
      scales: {
        x: {
          title: {
            display: true,
            text: 'Time',
            font: {
              weight: 'bold'
            },
            padding: {top: 10}
          },
          ticks: {
            maxRotation: 0,
            maxTicksLimit: 6
          }
        },
        y: {
          title: {
            display: true,
            text: 'Number of Items',
            font: {
              weight: 'bold'
            },
            padding: {right: 10}
          },
          beginAtZero: true,
          // For item count, use integer steps
          ticks: {
            stepSize: 1,
            precision: 0
          }
        }
      },
      interaction: {
        intersect: false,
        mode: 'index'
      }
    }
  });

  // Modified refresh function to handle smarter chart updates
  function refreshDisplay() {
    console.log("Checking for updates...");
    
    // Add timestamp to prevent caching
    const timestamp = new Date().getTime();
    const url = `getvalue.php?_=${timestamp}`;
    
    // Use jQuery's getJSON for cleaner code
    $.getJSON(url)
      .done(function(data) {
        // Only log full data when changes occur
        if (data && (parseFloat(data.weight || 0) !== lastWeight)) {
          console.log("Data changed:", data);
        } else {
          console.log("Poll check - no change");
        }
        
        if (data) {
          // Get base weight in kg - FIXED: Added fallback to 0
          const baseWeight = parseFloat(data.weight || 0);
          // Convert to selected unit
          const convertedWeight = convertValue(baseWeight, currentUnit);
          const formattedWeight = formatValue(convertedWeight, currentUnit);
          
          const itemCount = parseInt(data.item_count || 0);
          const unrecognized = data.unrecognized === 1 ? "YES" : "NO";
          
          // Get specified weight items (items within tolerance of the set weight)
          const specifiedItems = parseInt(data.specified_items || 0);
          
          // Update widget values immediately - ALWAYS update these
          $('#weight').text(formattedWeight + ' ' + currentUnit);
          $('#items').text(itemCount);
          
          // Enhanced unrecognized weight display
          if (data.unrecognized === 1) {
            $('#unrecognized').text("YES");
            
            // If we have detailed unrecognized info, add a tooltip
            if (data.unrecognized_details) {
              const details = data.unrecognized_details;
              const expectedWeight = convertValue(details.expected_weight, currentUnit);
              const formattedExpected = formatValue(expectedWeight, currentUnit);
              const difference = convertValue(details.difference, currentUnit);
              const formattedDiff = formatValue(difference, currentUnit);
              
              // Add a tooltip to show more details
              $('.unrecognized-box').attr('title', 
                `Actual: ${formattedWeight} ${currentUnit}\n` +
                `Expected (${details.closest_match_items} items): ${formattedExpected} ${currentUnit}\n` + 
                `Difference: ${formattedDiff} ${currentUnit}`);
              
              // Add this data as HTML data attributes for potential use
              $('.unrecognized-box').data('actual-weight', formattedWeight);
              $('.unrecognized-box').data('expected-weight', formattedExpected);
              $('.unrecognized-box').data('difference', formattedDiff);
            }
          } else {
            $('#unrecognized').text("NO");
            $('.unrecognized-box').removeAttr('title');
          }
          
          $('#specifiedItems').text(specifiedItems);
          
          // Update for chart display - now with better debugging
          const currentTime = new Date().getTime();
          const timeElapsed = currentTime - lastChartUpdate;
          const significantChange = Math.abs(baseWeight - lastWeight) > 0.05 || itemCount !== lastItemCount;
          
          const shouldUpdateChart = timeElapsed >= chartUpdateThreshold || significantChange;
          console.log(`Time since last chart update: ${timeElapsed/1000}s, threshold: ${chartUpdateThreshold/1000}s, significant change: ${significantChange}`);
          
          if (shouldUpdateChart) {
            console.log("Updating charts");
            const timestamp = new Date().toLocaleTimeString([], {hour: '2-digit', minute:'2-digit', second:'2-digit'});
            
            // Add data points
            labels.push(timestamp);
            weightData.push(convertedWeight);
            itemData.push(itemCount);
            
            // Limit data points to maxDataPoints
            while (labels.length > maxDataPoints) {
              labels.shift();
              weightData.shift();
              itemData.shift();
            }
            
            // Update charts
            weightChart.update();
            itemChart.update();
            
            // Save current time as last update time
            lastChartUpdate = currentTime;
            lastWeight = baseWeight;
            lastItemCount = itemCount;
          }
        } else {
          console.warn("Empty or null data received");
        }
      })
      .fail(function(jqXHR, textStatus, error) {
        console.error("Failed to get data:", textStatus, error);
        // Try a direct AJAX call to see error details
        $.ajax({
          url: url,
          dataType: 'text',
          success: function(rawData) {
            console.log("Raw response:", rawData);
          },
          error: function(xhr, status, error) {
            console.error("Raw error:", error);
          }
        });
      });

    // Update weight changes table
    $.get('fetch_weight_changes.php', function(data) {
      $('#weightChangesTable').DataTable().clear().destroy();
      $('#weightChangesTable tbody').html(data);
      $('#weightChangesTable').DataTable({
        "searching": true,
        "paging": true,
        "pageLength": 5,
        "lengthMenu": [5, 10, 20]
      });
    });
  }

  function refreshWarnings() {
    console.log("Refreshing warnings table...");
    $.ajax({
      url: 'fetch_warnings.php',
      type: 'GET',
      dataType: 'html',
      success: function(data) {
        console.log("Warnings data received");
        // Before destroying the table, check if it exists and is initialized
        if ($.fn.DataTable.isDataTable('#warningsTable')) {
          $('#warningsTable').DataTable().clear().destroy();
        }
        
        $('#warningTableBody').html(data);
        
        if ($('#warningTableBody tr').length === 0) {
          console.log("No warnings found, adding empty message");
          $('#warningTableBody').html('<tr><td colspan="5" class="text-center">No unrecognized weight warnings found</td></tr>');
        }
        
        $('#warningsTable').DataTable({
          "searching": true,
          "paging": true,
          "pageLength": 5,
          "lengthMenu": [5, 10, 20],
          "order": [[0, "desc"]] // Order by ID column descending (most recent first)
        });
      },
      error: function(xhr, status, error) {
        console.error("Error fetching warnings:", error);
        console.log("Status:", status);
        console.log("Response:", xhr.responseText);
        $('#warningTableBody').html('<tr><td colspan="5" class="text-center text-danger">Error loading warnings: ' + error + '</td></tr>');
      }
    });
  }

  // Initial refresh and set intervals
  refreshDisplay();
  refreshWarnings(); // Call this immediately
  setInterval(refreshDisplay, chartUpdateInterval);
  setInterval(refreshWarnings, 15000); // Every 15 seconds

  // Download CSV
  window.downloadCSV = function() {
    let csv = `Timestamp,${unitLabels[currentUnit]},Item Count\n`;
    for (let i = 0; i < labels.length; i++) {
      csv += `${labels[i]},${weightData[i]},${itemData[i]}\n`;
    }
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    const today = new Date().toISOString().slice(0, 10);
    link.download = `rack_data_${currentUnit}_${today}.csv`;
    link.click();
  }

  // Download PNG
  window.downloadPNG = function() {
    const today = new Date().toISOString().slice(0, 10);
    const link1 = document.createElement('a');
    link1.download = `rack_weight_graph_${today}.png`;
    link1.href = weightChart.toBase64Image();
    link1.click();

    const link2 = document.createElement('a');
    link2.download = `rack_item_graph_${today}.png`;
    link2.href = itemChart.toBase64Image();
    link2.click();
  }

  // Reset Graphs
  window.resetGraphs = function() {
    labels.length = 0;
    weightData.length = 0;
    itemData.length = 0;
    lastChartUpdate = 0; // Reset the timer
    weightChart.update();
    itemChart.update();
  }
  
  // Add simulation button functionality
  $('#simulateUnrecognized').click(function() {
    // Generate a random weight between 1.0 and 10.0
    const randomWeight = (Math.random() * 9 + 1).toFixed(3);
    
    $.get('log_unrecognized.php', { weight: randomWeight }, function(data) {
      console.log("Logged unrecognized weight:", data);
      toastr.warning(`Simulated unrecognized weight: ${randomWeight}kg`);
      refreshWarnings(); // Refresh the warnings table immediately
    });
  });
});
</script>
</body>
</html>