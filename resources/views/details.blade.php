@extends('main.main')

@section('content')
<div class="p-5 font-roboto">
    <a 
        href="{{ route('calculation') }}" 
        class="inline-block bg-gray-300 text-gray-800 px-3 py-1 rounded mb-4 hover:bg-gray-400 transition duration-200"
    >
        &larr; Back to Inventory
    </a>

    <!-- Print Button -->
    <button 
        onclick="window.print()" 
        class="print-button text-white bg-blue-600 px-3 py-1 rounded mb-4 hover:bg-blue-700 transition duration-200"
    >
        Print
    </button>

    <h1 class="text-2xl font-bold mb-4">Product Inventory Record</h1>
    <div class="border rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h2 class="text-lg font-bold mb-3">Inventory Details</h2>
                <p><strong>Budget Identifier:</strong> {{ $inventory->budget_identifier }}</p>
                <p><strong>Product Name:</strong> {{ $inventory->product_name }}</p>
                <p><strong>Unit Cost:</strong> ₱{{ number_format($inventory->unit_cost, 2) }}</p>
                <p><strong>Pieces per Set:</strong> {{ $inventory->pieces_per_set }}</p>
                <p><strong>Stocks per Set:</strong> {{ $inventory->stocks_per_set }}</p>
                <p><strong>Created At:</strong> {{ $inventory->created_at }}</p>
                <!-- <p><strong>Updated At:</strong> {{ $inventory->updated_at }}</p> -->
                <p><strong>Expiration Date:</strong> {{ $inventory->exp_date }}</p>
                <p><strong>Remarks:</strong> {{ $inventory->remarks ?: 'No remarks available' }}</p>
            </div>
            <div>
                <h2 class="text-lg font-bold">Budget Details</h2>
                <div id="budget-details" class="border rounded-lg p-4 shadow-md bg-gray-50 mt-4"></div>
            </div>
        </div>

        <!-- Calculations Section -->
        <!-- <h2 class="text-lg font-semibold mt-4 mb-2">Calculations</h2> -->
        <div class="p-1 border border-gray-200 rounded-lg mt-2">
            <table class="w-full text-left">
                <thead>
                    <tr class="border-b">
                        <th>Calculation</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-1">Unit Cost per Set</td>
                        <td class="py-1">₱{{ number_format($inventory->unit_cost, 2) }} x {{ $inventory->pieces_per_set }} = ₱<span id="unit-cost-per-set"></span></td>
                    </tr>
                    <tr>
                        <td class="py-1">Total Cost of Pieces per Set</td>
                        <td class="py-1">₱<span id="total-cost-pieces"></span> = ₱<span id="calculation-pieces"></span></td>
                    </tr>
                    <tr>
                        <td class="py-1">Total Cost of Sets</td>
                        <td class="py-1">₱<span id="total-cost-pieces"></span> x {{ $inventory->stocks_per_set }} = ₱<span id="total-cost-stocks"></span></td>
                    </tr>
                    <td class="py-1">Unit Cost of Single Product X Pieces Per Set = Stocks Per Set X Unit Cost per Set - Inputted Budget</td>
                    <td class="py-1">Remaining Balance</td>
                    <tr>
                        <td class="py-1">{{ number_format($inventory->unit_cost, 2) }} X {{ $inventory->pieces_per_set }} = {{ $inventory->stocks_per_set }} X Unit Cost per Set </td>
                        <td class="py-1">₱<span id="input-budget"></span> - ₱<span id="total-cost-stocks"></span> = ₱<span id="remaining-balance"></span></td>
                    </tr>
                    <!-- <tr>
                        <td class="py-1">Final Calculation (Unit Cost per Set x Stocks per Set - Input Budget)</td>
                        <td class="py-1">(₱<span id="calculation-pieces"></span> x {{ $inventory->stocks_per_set }}) - ₱<span id="input-budget"></span> = ₱<span id="final-calculation"></span></td>
                    </tr> -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    .font-roboto {
        font-family: 'Roboto', sans-serif;
    }
    @media print {
        /* Hide the sidebar and header when printing */
        .sidebar,
        header {
            display: none;
        }

        /* Expand the main content to full width */
        .main-content {
            width: 100%;
            margin: 0;
        }

        /* Hide the back button and print button on print */
        .print-button,
        a.inline-block {
            display: none;
        }
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const budgetId = {{ $inventory->budget_identifier }};
        fetchBudgetDetails(budgetId);
    });

    function fetchBudgetDetails(budgetId) {
        fetch(`/budgets/${budgetId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const budgetDetailsContainer = document.getElementById('budget-details');
                    budgetDetailsContainer.innerHTML = `
                        <p><strong>ID:</strong> ${data.data.id}</p>
                        <p><strong>Reference Code:</strong> ${data.data.reference_code}</p>
                        <p><strong>Product to Buy:</strong> ${data.data.product_to_buy}</p>
                        <p><strong>Input Budget:</strong> ₱${parseFloat(data.data.input_budget).toFixed(2)}</p>
                        <p><strong>Remaining Balance:</strong> ₱${parseFloat(data.data.remaining_balance).toFixed(2)}</p>
                        <p><strong>Created At:</strong> ${data.data.created_at}</p>
                    `;

                    const unitCost = {{ $inventory->unit_cost }};
                    const piecesPerSet = {{ $inventory->pieces_per_set }};
                    const stocksPerSet = {{ $inventory->stocks_per_set }};
                    const inputBudget = parseFloat(data.data.input_budget);

                    const unitCostPerSet = (unitCost * piecesPerSet).toFixed(2);
                    const totalCostStocks = (unitCostPerSet * stocksPerSet).toFixed(2);
                    const remainingBalance = (inputBudget - totalCostStocks).toFixed(2);
                    const finalCalculation = (totalCostStocks - inputBudget).toFixed(2);

                    document.getElementById('unit-cost-per-set').innerText = unitCostPerSet;
                    document.getElementById('total-cost-pieces').innerText = unitCostPerSet;
                    document.getElementById('calculation-pieces').innerText = unitCostPerSet;
                    document.getElementById('total-cost-stocks').innerText = totalCostStocks;
                    document.getElementById('input-budget').innerText = inputBudget.toFixed(2);
                    document.getElementById('remaining-balance').innerText = remainingBalance;
                    document.getElementById('final-calculation').innerText = finalCalculation;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching budget details:', error);
            });
    }
</script>
@endsection
<!-- <p><strong>Updated At:</strong> ${data.data.updated_at}</p> -->
<!-- <p><strong>Balance:</strong> ₱${parseFloat(data.data.balance).toFixed(2)}</p> -->

