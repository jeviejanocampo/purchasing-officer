<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="bg-white mt-6">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
       <div class="p-4 bg-white rounded-lg shadow">
           <h3 class="text-xl font-semibold mb-4">Retailed Price of each Products</h3>
           <canvas id="productCostChart" width="400" height="400"></canvas>
       </div>

        <!-- Right Column: Budget vs Remaining Balance Bar Chart -->
        <div class="p-4 bg-white rounded-lg shadow">
            <h3 class="text-xl font-semibold mb-4">Budget vs Remaining Balance</h3>
            <canvas id="budgetBarChart" width="400" height="400"></canvas>
        </div>
    </div>
</div>

    <script>
        // Ensure productCosts is passed correctly from the controller
        const productCosts = @json($productCosts); // Data passed from the controller

        // Group costs by product name
        const groupedData = productCosts.reduce((acc, item) => {
            if (acc[item.name]) {
                acc[item.name] = item.cost; // Add cost if name exists
            } else {
                acc[item.name] = item.cost; // Initialize if name does not exist
            }
            return acc;
        }, {});

        // Extract grouped data into labels and values
        const labels = Object.keys(groupedData);  // Product names
        const data = Object.values(groupedData); // Summed costs

        // Product Cost Pie Chart
        const ctxPieChart = document.getElementById('productCostChart').getContext('2d');

        const pieChart = new Chart(ctxPieChart, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Product Cost Breakdown',
                    data: data,
                    backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#FF9F40', '#C9CBCF'],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return tooltipItem.label + ': ₱' + tooltipItem.raw.toFixed(2);
                            }
                        }
                    }
                }
            }
        });

        // Budget vs Remaining Balance Bar Chart
        const totalBudgetAllocated = @json($totalBudgetAllocated);  // Total budget from the controller
        const remainingBalance = @json($remainingBalance);  // Remaining balance from the controller

        const ctxBarChart = document.getElementById('budgetBarChart').getContext('2d');

        const barChart = new Chart(ctxBarChart, {
            type: 'bar',
            data: {
                labels: ['Total Budget Allocated', 'Remaining Balance'],
                datasets: [{
                    label: 'Amount (₱)',
                    data: [totalBudgetAllocated, remainingBalance],
                    backgroundColor: ['#4BC0C0', '#FF9F40'],
                    borderColor: '#fff',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 150000,  // Set the maximum value for the Y-axis to 150,000.00
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toFixed(2);
                            }
                        }
                    }
                }
            }
        });
    </script>

