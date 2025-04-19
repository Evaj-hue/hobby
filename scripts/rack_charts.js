document.addEventListener("DOMContentLoaded", function () {
    let lastWeight = 0;
    let lastItemCount = 0;
    const weightData = [], itemData = [], labels = [];
  
    const chartOptionsDark = {
      responsive: true,
      plugins: {
        legend: {
          labels: { color: 'white' }
        }
      },
      scales: {
        x: { ticks: { color: 'white' }, grid: { color: '#444' } },
        y: { ticks: { color: 'white' }, grid: { color: '#444' } }
      }
    };
  
    const weightCanvas = document.getElementById('weightChart');
    const itemCanvas = document.getElementById('itemChart');
  
    if (!weightCanvas || !itemCanvas) {
      console.error("Canvas elements not found.");
      return;
    }
  
    const weightChart = new Chart(weightCanvas, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Weight (kg)',
          data: weightData,
          borderColor: 'orange',
          backgroundColor: 'rgba(255,165,0,0.1)',
          fill: true
        }]
      },
      options: chartOptionsDark
    });
  
    const itemChart = new Chart(itemCanvas, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Item Count',
          data: itemData,
          borderColor: '#00ff99',
          backgroundColor: 'rgba(0,255,153,0.1)',
          fill: true
        }]
      },
      options: chartOptionsDark
    });
  
    function updateGraphs() {
      $.get('../rack/getvalue.php', function(res) {
        try {
          const parsed = JSON.parse(res);
          const newWeight = parseFloat(parsed.weight);
          const newItem = parseInt(parsed.item_count);
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
        } catch (e) {
          console.error("Error parsing response:", res);
        }
      });
    }
  
    setInterval(updateGraphs, 5000);
    updateGraphs();
  });
  