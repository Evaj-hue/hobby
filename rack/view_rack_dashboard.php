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
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


  <style>
    body {
      background-color: #253529;
      color: #E2E8F0;
      font-family: 'Segoe UI', sans-serif;
      margin-left: 200px; /* space for sidebar */
    }

    /* Widgets */
    .widget-container {
      display: flex;
      justify-content: space-between;
      margin: 40px 20px;
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

    /* Charts */
    .charts-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin: 20px 20px;
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

    /* Tables */
    .table-container {
      width: calc(100% - 220px);
      margin: 30px auto;
    }

    .table {
      background-color: #3d4f40;
      color: #E2E8F0;
      border-radius: 10px;
      overflow: hidden;
    }

    .table th {
      background-color: #50624e;
      color: #ECC94B;
    }

    .table tbody tr:hover {
      background-color: #5a6b58;
    }

    /* DataTables - Dark Theme */
    .dataTables_wrapper {
      color: #E2E8F0;
    }

    .dataTables_filter input {
      background-color: #50624e;
      color: #E2E8F0;
      border: 1px solid #7a8c74;
    }

    .dataTables_length select {
      background-color: #50624e;
      color: #E2E8F0;
      border: 1px solid #7a8c74;
    }

    .dataTables_paginate .paginate_button {
      background-color: #50624e;
      color: #E2E8F0 !important;
      border: 1px solid #7a8c74;
    }

    .dataTables_paginate .paginate_button:hover {
      background-color: #5a6b58;
      color: #ECC94B !important;
    }

    .dataTables_info {
      color: #E2E8F0;
    }

    .table-warning {
      background-color: #f8d7da !important;
      color: #721c24;
    }
  </style>
</head>
<body>
<?php include("../partials/sidebar.php"); ?>

<h2 class="text-center mt-4">📦 CozyRack - Real-Time Load Cell Monitor</h2>

<!-- Widgets Section -->
<div class="widget-container">
    <div class="widget bg-success text-white">
        <h5>Real-Time Weight</h5>
        <p id="weight">0.00 kg</p>
    </div>
    <div class="widget bg-primary text-white">
        <h5>Total Items</h5>
        <p id="items">0</p>
    </div>
</div>

<!-- Graphs Section -->
<h1 class="text-center mb-4">Real-Time Data</h1>
<div class="charts-grid">
    <div class="chart-container">
        <div class="chart-header">
            <h3>Weight</h3>
        </div>
        <canvas id="weightChart"></canvas>
    </div>
    <div class="chart-container">
        <div class="chart-header">
            <h3>Item Count</h3>
        </div>
        <canvas id="itemChart"></canvas>
    </div>
</div>

<!-- Weight Changes Table -->
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

<!-- Warnings Table -->
<div class="table-container">
  <h3 class="text-center text-danger">⚠️ Unrecognized Weight Warnings</h3>
  <table id="warningsTable" class="table table-bordered table-sm table-warning">
    <thead>
      <tr>
        <th>ID</th>
        <th>Weight (kg)</th>
        <th>Message</th>
        <th>Time</th>
        <th>Date</th>
      </tr>
    </thead>
    <tbody>
      <!-- Data from AJAX will go here -->
    </tbody>
  </table>
</div>

<script>
  $(document).ready(function() {
    // Initialize DataTables with dark theme
    $('#weightChangesTable').DataTable({
      "searching": true,
      "paging": true,
      "pageLength": 5,
      "lengthMenu": [5, 10, 20]
    });

    $('#warningsTable').DataTable({
      "searching": true,
      "paging": true,
      "pageLength": 5,
      "lengthMenu": [5, 10, 20]
    });
  });

  let lastWeight = 0;
  let lastItemCount = 0;
  const weightData = [], itemData = [], labels = [];

  const weightChart = new Chart(document.getElementById('weightChart'), {
    type: 'line',
    data: { labels, datasets: [{ label: 'Weight (kg)', data: weightData, borderColor: 'orange', fill: false }] },
    options: { responsive: true }
  });

  const itemChart = new Chart(document.getElementById('itemChart'), {
    type: 'line',
    data: { labels, datasets: [{ label: 'Item Count', data: itemData, borderColor: 'green', fill: false }] },
    options: { responsive: true }
  });

  function refreshDisplay() {
    $.get('getvalue.php', function(res) {
      const parsed = JSON.parse(res);
      const newWeight = parseFloat(parsed.weight);
      const newItem = parseInt(parsed.item_count);

      $('#weight').text(parseFloat(parsed.weight).toFixed(2) + ' kg');
      $('#items').text(parsed.item_count);

      const timestamp = new Date().toLocaleTimeString();

      if (Math.abs(newWeight - lastWeight) > 0.1 || newItem !== lastItemCount) {
        labels.push(timestamp);
        weightData.push(newWeight);
        itemData.push(newItem);

        if (labels.length > 10) {
          labels.shift();
          weightData.shift();
          itemData.shift();
        }

        weightChart.update();
        itemChart.update();

        lastWeight = newWeight;
        lastItemCount = newItem;
      }
    });

    // Update Weight Changes Table
    $.get('fetch_weight_changes.php', function(data) {
      $('#weightChangesTable').DataTable().clear().draw();
      $('#weightChangesTable').DataTable().rows.add($(data)).draw();
    });
  }

  function refreshWarnings() {
    $.get('fetch_warnings.php', function(data) {
      $('#warningTableBody').html(data);
      $('#warningsTable').DataTable().clear().draw();
      $('#warningsTable').DataTable().rows.add($(data)).draw();
    });
  }

  setInterval(refreshDisplay, 5000);
  setInterval(refreshWarnings, 5000);
  refreshDisplay();
  refreshWarnings();

  function downloadCSV() {
    let csv = 'Timestamp,Weight,Item Count\n';
    for (let i = 0; i < labels.length; i++) {
      csv += `${labels[i]},${weightData[i]},${itemData[i]}\n`;
    }
    const blob = new Blob([csv], { type: 'text/csv' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `rack_data_${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
  }

  function downloadPNG() {
    const today = new Date().toISOString().slice(0, 10);
    const weightLink = document.createElement('a');
    weightLink.download = `rack_weight_graph_${today}.png`;
    weightLink.href = weightChart.toBase64Image();
    weightLink.click();

    const itemLink = document.createElement('a');
    itemLink.download = `rack_item_graph_${today}.png`;
    itemLink.href = itemChart.toBase64Image();
    itemLink.click();
  }

  function resetGraphs() {
    labels.length = weightData.length = itemData.length = 0;
    weightChart.update();
    itemChart.update();
  }
</script>
</body>
</html>