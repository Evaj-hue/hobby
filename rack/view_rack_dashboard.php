<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Bootstrap + Chart.js -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Add Bootstrap JS Bundle - this is critical for modals -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
      min-width: 220px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card-box:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    }
    .weight-box { background-color: #fff3cd; }
    .item-box { background-color: #d1e7dd; }
    .unrecognized-box { background-color: #f8d7da; }
    .specified-box { 
      background-color: #d0e6ff; 
      background-image: linear-gradient(135deg, #d0e6ff 0%, #b8d9f3 100%);
    }
    .label { 
      font-size: 1.2rem; 
      font-weight: 600; 
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .label i {
      margin-right: 8px;
    }
    .value { 
      font-size: 2.5rem; 
      font-weight: bold; 
      margin-top: 20px;
    }
    .refresh-indicator {
      font-size: 0.8rem;
      color: #6c757d;
      margin-top: 10px;
    }
    .view-only-badge {
      position: fixed;
      top: 70px;
      right: 10px;
      background-color: rgba(13, 110, 253, 0.8);
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      z-index: 1000;
      font-weight: bold;
    }
    .auto-refresh-toggle {
      position: fixed;
      top: 110px;
      right: 10px;
      background-color: rgba(25, 135, 84, 0.8);
      color: white;
      padding: 5px 10px;
      border-radius: 4px;
      z-index: 1000;
      cursor: pointer;
    }
    .auto-refresh-toggle:hover {
      background-color: rgba(25, 135, 84, 1);
    }
    .dataTables_wrapper .dataTables_length, 
    .dataTables_wrapper .dataTables_filter {
      margin-bottom: 15px;
    }
    .dataTables_wrapper {
      padding: 15px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .page-title {
      position: relative;
    }
    .page-title::after {
      content: '';
      display: block;
      width: 100px;
      height: 3px;
      background-color: #0d6efd;
      margin: 10px auto;
    }
    .animate-value {
      animation: pulse 0.5s ease-in-out;
    }
    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body>
  <?php include("../partials/user_navbar.php"); ?>
  <?php include("../partials/user_sidebar.php"); ?> 

  <div class="view-only-badge">
    <i class="fas fa-eye"></i> View Only Mode
  </div>

  <div class="auto-refresh-toggle" id="refreshToggle">
    <i class="fas fa-sync-alt"></i> Auto-Refresh: ON
  </div>

  <div class="container my-4">
    <h2 class="text-center mt-4 page-title">📦 CozyRack - Real-Time Monitoring Dashboard</h2>
    
    <div class="d-flex justify-content-center align-items-center gap-3 mb-4">
      <div class="metric-selection">
        <label for="metric-system" class="form-label fw-bold mb-0">Select Metric System:</label>
        <select id="metric-system" class="form-select d-inline-block w-auto">
          <option value="kg" selected>Kilograms (kg)</option>
          <option value="g">Grams (g)</option>
          <option value="l">Liters (L)</option>
          <option value="ml">Milliliters (ml)</option>
        </select>
      </div>
      <div class="chart-duration">
        <label for="chart-minutes" class="form-label fw-bold mb-0">Chart Duration:</label>
        <select id="chart-minutes" class="form-select d-inline-block w-auto">
          <option value="5">Last 5 minutes</option>
          <option value="10" selected>Last 10 minutes</option>
          <option value="30">Last 30 minutes</option>
          <option value="60">Last hour</option>
        </select>
      </div>
      <button id="manualRefresh" class="btn btn-primary btn-sm">
        <i class="fas fa-sync-alt"></i> Refresh Now
      </button>
    </div>
  
    <!-- Info Boxes -->
    <div class="monitor">
      <div class="card-box weight-box">
        <div class="label"><i class="fas fa-weight"></i> Real-Time Weight</div>
        <div id="weight" class="value">0.00 kg</div>
        <div class="refresh-indicator" id="weightUpdateTime">Last updated: Just now</div>
      </div>
      <div class="card-box item-box">
        <div class="label"><i class="fas fa-cube"></i> Total Items</div>
        <div id="items" class="value">0</div>
        <div class="refresh-indicator" id="itemsUpdateTime">Last updated: Just now</div>
      </div>
      <div class="card-box unrecognized-box">
        <div class="label"><i class="fas fa-exclamation-triangle"></i> Unrecognized Weight</div>
        <div id="unrecognized" class="value">NO</div>
        <div class="refresh-indicator" id="unrecognizedUpdateTime">Last updated: Just now</div>
      </div>
      <div class="card-box specified-box">
        <div class="label"><i class="fas fa-clipboard-check"></i> Specified Weight Items</div>
        <div id="specifiedItems" class="value">0</div>
        <div class="refresh-indicator" id="specifiedUpdateTime">Last updated: Just now</div>
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
      <button class="btn btn-primary" onclick="downloadCSV()">
        <i class="fas fa-file-csv"></i> Export as CSV
      </button>
      <button class="btn btn-secondary" onclick="downloadPNG()">
        <i class="fas fa-image"></i> Download Charts
      </button>
      <button class="btn btn-danger" onclick="resetGraphs()">
        <i class="fas fa-redo-alt"></i> Reset Graphs
      </button>
    </div>

    <!-- Weight changes table -->
    <div class="table-container">
      <h3 class="text-center mb-3">📊 Weight Changes History</h3>
      <div class="table-responsive">
        <table id="weightChangesTable" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>#</th>
              <th>Weight (kg)</th>
              <th>Time</th>
              <th>Date</th>
              <th>Item Count</th>
              <th>Operation</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <!-- Data from AJAX will go here -->
          </tbody>
        </table>
      </div>
    </div>
    
    <div id="warningBox" class="alert alert-warning text-center" style="display: none;"></div>

    <div class="table-container">
      <div class="container">
        <h4 class="text-center text-danger mb-3">⚠️ Unrecognized Weight Warnings</h4>
      </div>
      <div class="table-responsive">
        <table id="warningsTable" class="table table-striped table-hover">
          <thead>
            <tr>
              <th>ID</th>
              <th>Weight (kg)</th>
              <th>Message</th>
              <th>Time</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody id="warningTableBody">
            <!-- Data from AJAX will go here -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

<script>
$(function() {
  // Chart setup with better styling
  let lastWeight = 0;
  let lastItemCount = 0;
  let autoRefresh = true;
  let refreshTimer;
  const refreshInterval = 5000; // 5 seconds
  const weightData = [], itemData = [], labels = [];
  
  // Format for last updated time
  function updateLastRefreshTime() {
    const now = new Date();
    const timeStr = now.toLocaleTimeString();
    $('#weightUpdateTime, #itemsUpdateTime, #unrecognizedUpdateTime, #specifiedUpdateTime').text(`Last updated: ${timeStr}`);
  }
  
  // Better configured chart
  const weightChart = new Chart(document.getElementById('weightChart'), {
    type: 'line', 
    data: { 
      labels, 
      datasets: [{ 
        label: 'Weight (kg)', 
        data: weightData, 
        borderColor: 'orange',
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderWidth: 2,
        tension: 0.4,
        fill: false 
      }] 
    }, 
    options: { 
      responsive: true,
      animation: { duration: 500 },
      plugins: {
        title: {
          display: true,
          text: 'Real-Time Measurement Monitoring',
          font: { size: 16, weight: 'bold' }
        },
        legend: { position: 'top' }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Weight (kg)'
          }
        },
        x: {
          title: {
            display: true,
            text: 'Time'
          }
        }
      }
    }
  });
  
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
      animation: { duration: 500 },
      plugins: {
        title: {
          display: true,
          text: 'Item Count Tracking',
          font: { size: 16, weight: 'bold' }
        },
        legend: { position: 'top' }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: {
            display: true,
            text: 'Number of Items'
          },
          ticks: {
            stepSize: 1,
            precision: 0
          }
        },
        x: {
          title: {
            display: true,
            text: 'Time'
          }
        }
      }
    }
  });

  // Get current metric unit
  function getCurrentUnit() {
    return $('#metric-system').val() || 'kg';
  }

  // Get chart duration in minutes
  function getChartDuration() {
    return parseInt($('#chart-minutes').val()) || 10;
  }

  // Function to animate value changes
  function animateValueChange(elementId) {
    $(`#${elementId}`).addClass('animate-value');
    setTimeout(() => {
      $(`#${elementId}`).removeClass('animate-value');
    }, 500);
  }

  function refreshDisplay() {
    // Add timestamp to prevent caching
    const timestamp = new Date().getTime();
    const url = `getvalue.php?_=${timestamp}`;
    
    $.getJSON(url)
      .done(function(data) {
        if (data) {
          const newWeight = parseFloat(data.weight || 0);
          const newItemCount = parseInt(data.item_count || 0);
          const unrecognized = data.unrecognized === 1 ? "YES" : "NO";
          const specifiedItems = parseInt(data.specified_items || 0);
          const unit = getCurrentUnit();

          // Convert weight based on selected unit
          let displayWeight = newWeight;
          let unitLabel = 'kg';
          
          if (unit === 'g') {
            displayWeight = newWeight * 1000;
            unitLabel = 'g';
          } else if (unit === 'l') {
            unitLabel = 'L';
          } else if (unit === 'ml') {
            displayWeight = newWeight * 1000;
            unitLabel = 'ml';
          }

          // Update widget values with animation if changed
          if (parseFloat($('#weight').text()) !== displayWeight) {
            $('#weight').text(displayWeight.toFixed(2) + ' ' + unitLabel);
            animateValueChange('weight');
          }
          
          if (parseInt($('#items').text()) !== newItemCount) {
            $('#items').text(newItemCount);
            animateValueChange('items');
          }
          
          if ($('#unrecognized').text() !== unrecognized) {
            $('#unrecognized').text(unrecognized);
            animateValueChange('unrecognized');
          }
          
          if (parseInt($('#specifiedItems').text()) !== specifiedItems) {
            $('#specifiedItems').text(specifiedItems);
            animateValueChange('specifiedItems');
          }

          // Update last refresh time
          updateLastRefreshTime();

          const timestamp = new Date().toLocaleTimeString();

          // Update chart if significant change or time threshold met
          if (Math.abs(newWeight - lastWeight) > 0.05 || newItemCount !== lastItemCount) {
            labels.push(timestamp);
            weightData.push(displayWeight);
            itemData.push(newItemCount);

            // Limit chart data points based on selected duration
            const maxPoints = getChartDuration() * 60 / 5; // Assuming refresh every 5 seconds
            
            while (labels.length > maxPoints) {
              labels.shift();
              weightData.shift();
              itemData.shift();
            }

            weightChart.update();
            itemChart.update();

            lastWeight = newWeight;
            lastItemCount = newItemCount;
          }
        }
      })
      .fail(function(jqXHR, textStatus, error) {
        console.error("Failed to get data:", textStatus, error);
      });

    // Update weight changes and warnings tables
    refreshWeightChanges();
    refreshWarnings();
  }

  // Function for refreshing weight changes with better error handling
  function refreshWeightChanges() {
    $.ajax({
      url: 'fetch_weight_changes.php',
      type: 'GET',
      dataType: 'html',
      success: function(data) {
        if ($.fn.DataTable.isDataTable('#weightChangesTable')) {
          $('#weightChangesTable').DataTable().destroy();
        }
        $('#weightChangesTable tbody').html(data);
        $('#weightChangesTable').DataTable({
          "searching": true,
          "paging": true,
          "pageLength": 10,
          "lengthMenu": [5, 10, 25, 50],
          "order": [[3, "desc"], [2, "desc"]], // Sort by date and time descending
          "responsive": true,
          "language": {
            "search": "Search records:",
            "emptyTable": "No weight changes recorded yet"
          }
        });
      },
      error: function(xhr, status, error) {
        console.error("Error fetching weight changes:", error);
      }
    });
  }

  // Function to refresh warnings with better error handling
  function refreshWarnings() {
    $.ajax({
      url: 'fetch_warnings.php',
      type: 'GET',
      dataType: 'html',
      success: function(data) {
        if ($.fn.DataTable.isDataTable('#warningsTable')) {
          $('#warningsTable').DataTable().destroy();
        }
        $('#warningTableBody').html(data);
        
        if ($('#warningTableBody tr').length === 0) {
          $('#warningTableBody').html('<tr><td colspan="5" class="text-center">No unrecognized weight warnings found</td></tr>');
        }
        
        $('#warningsTable').DataTable({
          "searching": true,
          "paging": true,
          "pageLength": 10,
          "lengthMenu": [5, 10, 25, 50],
          "order": [[0, "desc"]], // Sort by ID descending
          "responsive": true,
          "language": {
            "search": "Search warnings:",
            "emptyTable": "No unrecognized weight warnings found"
          }
        });
      },
      error: function(xhr, status, error) {
        console.error("Error fetching warnings:", error);
      }
    });
  }

  // Handle metric system change
  $('#metric-system').change(function() {
    // Update chart label
    weightChart.data.datasets[0].label = 'Weight (' + getCurrentUnit() + ')';
    weightChart.options.scales.y.title.text = 'Weight (' + getCurrentUnit() + ')';
    weightChart.update();
    
    refreshDisplay();
  });

  // Handle chart duration change
  $('#chart-minutes').change(function() {
    // Clear and rebuild chart with new duration
    labels.length = 0;
    weightData.length = 0;
    itemData.length = 0;
    weightChart.update();
    itemChart.update();
    refreshDisplay();
  });
  
  // Toggle auto-refresh
  $('#refreshToggle').click(function() {
    autoRefresh = !autoRefresh;
    
    if (autoRefresh) {
      $(this).html('<i class="fas fa-sync-alt"></i> Auto-Refresh: ON');
      $(this).css('background-color', 'rgba(25, 135, 84, 0.8)');
      refreshTimer = setInterval(refreshDisplay, refreshInterval);
    } else {
      $(this).html('<i class="fas fa-pause"></i> Auto-Refresh: OFF');
      $(this).css('background-color', 'rgba(220, 53, 69, 0.8)');
      clearInterval(refreshTimer);
    }
  });
  
  // Manual refresh button
  $('#manualRefresh').click(function() {
    const btn = $(this);
    btn.prop('disabled', true);
    btn.html('<i class="fas fa-spinner fa-spin"></i> Refreshing...');
    
    refreshDisplay();
    
    setTimeout(function() {
      btn.prop('disabled', false);
      btn.html('<i class="fas fa-sync-alt"></i> Refresh Now');
    }, 1000);
  });
  
  // Start auto-refresh timer
  refreshTimer = setInterval(refreshDisplay, refreshInterval);
  
  // Initial load
  refreshDisplay();

  // Download CSV with improved file format
  window.downloadCSV = function() {
    const date = new Date();
    const formattedDate = date.toISOString().slice(0, 10);
    const formattedTime = date.toTimeString().slice(0, 8).replace(/:/g, '-');
    
    let csv = 'CozyRack Data Export - ' + date.toLocaleString() + '\n';
    csv += 'Timestamp,Weight (' + getCurrentUnit() + '),Item Count\n';
    
    for (let i = 0; i < labels.length; i++) {
      csv += `"${labels[i]}",${weightData[i]},${itemData[i]}\n`;
    }
    
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `rack_data_${formattedDate}_${formattedTime}.csv`;
    link.click();
    
    toastr.success('CSV file downloaded successfully');
  }

  // Download PNG with improved file names
  window.downloadPNG = function() {
    const date = new Date();
    const formattedDate = date.toISOString().slice(0, 10);
    const formattedTime = date.toTimeString().slice(0, 8).replace(/:/g, '-');
    
    const link1 = document.createElement('a');
    link1.download = `rack_weight_${formattedDate}_${formattedTime}.png`;
    link1.href = weightChart.toBase64Image();
    link1.click();

    setTimeout(() => {
      const link2 = document.createElement('a');
      link2.download = `rack_items_${formattedDate}_${formattedTime}.png`;
      link2.href = itemChart.toBase64Image();
      link2.click();
      
      toastr.success('Chart images downloaded successfully');
    }, 200);
  }

  // Reset Graphs with animation
  window.resetGraphs = function() {
    labels.length = 0;
    weightData.length = 0;
    itemData.length = 0;
    
    weightChart.update();
    itemChart.update();
    
    toastr.info('Graphs have been reset');
  }
  
  // Initialize toastr notification settings
  toastr.options = {
    "closeButton": true,
    "positionClass": "toast-bottom-right",
    "timeOut": "2000"
  };
});
</script>
</body>
</html>