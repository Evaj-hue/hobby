// Initialize global variables for weight and item charts
let lastWeight = 0;
let lastItemCount = 0;
const weightData = [], itemData = [], labels = [];

// Initialize the weight chart
const weightChart = new Chart(document.getElementById('weightChart'), {
  type: 'line',
  data: {
    labels,
    datasets: [{
      label: 'Weight (kg)',
      data: weightData,
      borderColor: '#ECC94B', // Gold color
      borderWidth: 2,
      tension: 0.3,
      fill: false,
      pointBackgroundColor: '#ECC94B',
      pointBorderColor: '#ECC94B',
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        labels: {
          color: '#E2E8F0' // Text color
        }
      }
    },
    scales: {
      x: {
        grid: {
          color: '#50624e' // Gridline color
        },
        ticks: {
          color: '#E2E8F0' // Text color
        }
      },
      y: {
        grid: {
          color: '#50624e' // Gridline color
        },
        ticks: {
          color: '#E2E8F0' // Text color
        }
      }
    }
  }
});

// Initialize the item count chart
const itemChart = new Chart(document.getElementById('itemChart'), {
  type: 'line',
  data: {
    labels,
    datasets: [{
      label: 'Item Count',
      data: itemData,
      borderColor: '#48BB78', // Green color
      borderWidth: 2,
      tension: 0.3,
      fill: false,
      pointBackgroundColor: '#48BB78',
      pointBorderColor: '#48BB78',
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        display: true,
        labels: {
          color: '#E2E8F0' // Text color
        }
      }
    },
    scales: {
      x: {
        grid: {
          color: '#50624e' // Gridline color
        },
        ticks: {
          color: '#E2E8F0' // Text color
        }
      },
      y: {
        grid: {
          color: '#50624e' // Gridline color
        },
        ticks: {
          color: '#E2E8F0' // Text color
        }
      }
    }
  }
});

// Function to refresh data in the charts
function refreshDisplay() {
  $.get('getvalue.php', function(res) {
    const parsed = JSON.parse(res);
    const newWeight = parseFloat(parsed.weight);
    const newItem = parseInt(parsed.item_count);

    $('#weight').text(newWeight.toFixed(2) + ' kg');
    $('#items').text(newItem);

    const timestamp = new Date().toLocaleTimeString();

    if (Math.abs(newWeight - lastWeight) > 0.1 || newItem !== lastItemCount) {
      labels.push(timestamp);
      weightData.push(newWeight);
      itemData.push(newItem);

      // Limit the number of data points to 10
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
}

// Function to download the graph data as a CSV file
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

// Function to download the graphs as PNG images
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

// Function to reset the graphs
function resetGraphs() {
  labels.length = weightData.length = itemData.length = 0;
  weightChart.update();
  itemChart.update();
}

// Set intervals to refresh data automatically every 5 seconds
setInterval(refreshDisplay, 5000);
refreshDisplay();