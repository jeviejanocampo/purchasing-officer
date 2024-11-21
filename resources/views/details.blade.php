@extends('main.main')

@section('content')

<div class="p-5 font-roboto bg-white">
    @php
        $userId = session('user_id');
        $user = \App\Models\User::find($userId); 
    @endphp
    <!-- Back and Print Buttons -->
    <button onclick="history.back()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors duration-300 mb-4">
            Return
    </button>
    <button 
        onclick="window.print()" 
       class="bg-blue-800 text-white px-4 py-2 rounded hover:bg-red-600 transition-colors duration-300 mb-4"
    >
        Print
    </button>

    <!-- Title Section -->
    <h1 class="text-3xl mb-4" style="font-family: 'Lato', sans-serif; font-weight: 200; color: #C96A2A;">
        TinioFoods Inventory and Budget Summary Report
    </h1>
    <p class="mb-2"><strong>Company Name:</strong> MS Tinio Food Products Trading</p>
    <p class="mb-2"><strong>Address:</strong> 967 Highway Mangnao, Dumaguete City 6200, Lamberto Macias Road, Dumaguete, Negros Oriental</p>
    <p class="mb-4"><strong>Phone:</strong> (035) 225 7840</p>

    <div class="p-4 mb-6">
        <h2 class="text-lg font-bold mb-3">Supplier Details</h2>
        <div id="supplier-details"></div>
    </div>

        <!-- Budget Details Section -->
    <div class="p-4 mb-6">
        <h2 class="text-lg font-bold mb-3">Budget Details</h2>
        <div id="budget-details"></div>
    </div>


    <!-- Inventory Details Section -->
    <div class="p-4 mb-6">
        <h2 class="text-lg font-bold mb-3">Product Purchased</h2>
        <table class="table-auto w-full border-collapse">
            <thead>
                <tr>
                    <th class="font-semibold border px-2 py-1">Budget Identifier</th>
                    <th class="font-semibold border px-2 py-1">Product Name</th>
                    <th class="font-semibold border px-2 py-1">Unit Cost</th>
                    <th class="font-semibold border px-2 py-1">Pieces per Set</th>
                    <th class="font-semibold border px-2 py-1">Stocks per Set</th>
                    <th class="font-semibold border px-2 py-1">Created At</th>
                    <th class="font-semibold border px-2 py-1">Expiration Date</th>
                    <th class="font-semibold border px-2 py-1">Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border px-2 py-1">{{ $inventory->budget_identifier }}</td>
                    <td class="border px-2 py-1">{{ $inventory->product_name }}</td>
                    <td class="border px-2 py-1">₱{{ number_format($inventory->unit_cost, 2) }}</td>
                    <td class="border px-2 py-1">{{ $inventory->pieces_per_set }}</td>
                    <td class="border px-2 py-1">{{ $inventory->stocks_per_set }}</td>
                    <td class="border px-2 py-1">{{ $inventory->created_at }}</td>
                    <td class="border px-2 py-1">{{ $inventory->exp_date }}</td>
                    <td class="border px-2 py-1">{{ $inventory->remarks ?: 'No remarks available' }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Calculations Section -->
    <div class="border rounded-lg p-4">
        <h2 class="text-lg font-bold mb-3">Calculation Result</h2>
        <table class="w-full text-left border-t border-b">
            <thead>
                <tr class="border-b bg-gray-100">
                    <th class="py-2">Calculation</th>
                    <th class="py-2">Result</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2">Unit Cost per Set</td>
                    <td class="py-2">₱{{ number_format($inventory->unit_cost, 2) }} x {{ $inventory->pieces_per_set }} = ₱<span id="unit-cost-per-set"></span></td>
                </tr>
                <tr>
                    <td class="py-2">Total Cost of Pieces per Set</td>
                    <td class="py-2">₱<span id="total-cost-pieces"></span> = ₱<span id="calculation-pieces"></span></td>
                </tr>
                <tr>
                    <td class="py-2">Total Cost of Sets</td>
                    <td class="py-2">₱<span id="total-cost-pieces"></span> x {{ $inventory->stocks_per_set }} = ₱<span id="total-cost-stocks"></span></td>
                </tr>
                <tr>
                    <td class="py-1">Unit Cost of Single Product X Pieces Per Set = Stocks Per Set X Unit Cost per Set - Inputted Budget</td>
                    <td class="py-1">Remaining Balance</td>
                </tr>
                <tr>
                    <td class="py-2">Remaining Balance Calculation</td>
                    <td class="py-2">₱<span id="input-budget"></span> - ₱<span id="total-cost-stocks"></span> = ₱<span id="remaining-balance"></span></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="border rounded-lg p-4 mb-6 mt-12">
        <h2 class="text-lg font-bold mb-3">Cost Breakdown</h2>
        <table class="w-full text-left border-t border-b">
            <thead>
                <tr class="border-b bg-gray-100">
                    <th class="py-2">Words</th>
                    <th class="py-2">Number</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="py-2">Unit Cost per Set</td>
                    <td class="py-2">₱<span id="unit-cost-per-set-breakdown"></span></td>
                </tr>
                <tr>
                    <td class="py-2">Total Cost of Pieces per Set</td>
                    <td class="py-2">₱<span id="total-cost-pieces-breakdown"></span></td>
                </tr>
                <tr>
                    <td class="py-2">Budget</td>
                    <td class="py-2">₱<span id="budget-breakdown"></span></td>
                </tr>
                <tr>
                    <td class="py-2">Total Cost of Sets</td>
                    <td class="py-2">₱<span id="total-cost-sets-breakdown"></span></td>
                </tr>
                <tr>
                    <td class="py-2">Remaining Balance</td>
                    <td class="py-2">₱<span id="remaining-balance-breakdown"></span></td>
                </tr>
            </tbody>
        </table>
    </div>


    <h1 class="text-lg  mt-6 text-center">FOR THE OWNER</h1>
    <h3 class="mt-2 text-center">Record By</h3>
    <div class="mt-2 text-center">{{ $user->name ?? 'Guest' }}</div>
    <h3 class="mt-2 text-center">(Assigned Purchasing Officer)</h1>


</div>

<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    .font-roboto {
        font-family: 'Poppins', sans-serif;
    }
    @media print {
        body {
            zoom: 85%;
        }
        .sidebar, header, .print-button, a.inline-block {
            display: none;
        }
        .main-content {
            width: 100%;
            margin: 0;
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
                    // Populate Budget Details
                    const budgetDetailsContainer = document.getElementById('budget-details');
                    budgetDetailsContainer.innerHTML = `
                       <table class="table-auto w-full">
                            <thead>
                                <tr>
                                    <th class="font-semibold border px-2 py-1">ID</th>
                                    <th class="font-semibold border px-2 py-1">Reference Code</th>
                                    <th class="font-semibold border px-2 py-1">Product to Buy</th>
                                    <th class="font-semibold border px-2 py-1">Input Budget</th>
                                    <th class="font-semibold border px-2 py-1">Remaining Balance</th>
                                    <th class="font-semibold border px-2 py-1">Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border px-2 py-1">${data.data.id}</td>
                                    <td class="border px-2 py-1">${data.data.reference_code}</td>
                                    <td class="border px-2 py-1">${data.data.product_to_buy}</td>
                                    <td class="border px-2 py-1">₱${parseFloat(data.data.input_budget).toFixed(2)}</td>
                                    <td class="border px-2 py-1">₱${parseFloat(data.data.remaining_balance).toFixed(2)}</td>
                                    <td class="border px-2 py-1">${data.data.created_at}</td>
                                </tr>
                            </tbody>
                        </table>
                    `;

                    // Populate Supplier Details
                    const supplierDetailsContainer = document.getElementById('supplier-details');
                    const supplier = data.data.supplier;
                    supplierDetailsContainer.innerHTML = `
                   <table class="table-auto w-full border-collapse">
                        <thead>
                            <tr>
                                <th class="font-semibold border px-2 py-1">Supplier Name</th>
                                <th class="font-semibold border px-2 py-1">Phone</th>
                                <th class="font-semibold border px-2 py-1">Email</th>
                                <th class="font-semibold border px-2 py-1">Product Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="border px-2 py-1">${supplier.supplier_name}</td>
                                <td class="border px-2 py-1">${supplier.phone_number}</td>
                                <td class="border px-2 py-1">${supplier.email}</td>
                                <td class="border px-2 py-1">${supplier.product_type}</td>
                            </tr>
                        </tbody>
                    </table>
                    `;
                    // Calculation logic
                    const unitCost = {{ $inventory->unit_cost }};
                    const piecesPerSet = {{ $inventory->pieces_per_set }};
                    const stocksPerSet = {{ $inventory->stocks_per_set }};
                    const inputBudget = parseFloat(data.data.input_budget);

                    const unitCostPerSet = (unitCost * piecesPerSet).toFixed(2);
                    const totalCostStocks = (unitCostPerSet * stocksPerSet).toFixed(2);
                    const remainingBalance = (inputBudget - totalCostStocks).toFixed(2);

                    document.getElementById('unit-cost-per-set').innerText = unitCostPerSet;
                    document.getElementById('total-cost-pieces').innerText = unitCostPerSet;
                    document.getElementById('calculation-pieces').innerText = unitCostPerSet;
                    document.getElementById('total-cost-stocks').innerText = totalCostStocks;
                    document.getElementById('input-budget').innerText = inputBudget.toFixed(2);
                    document.getElementById('remaining-balance').innerText = remainingBalance;

                    // Update Cost Breakdown
                    document.getElementById('unit-cost-per-set-breakdown').innerText = unitCostPerSet;
                    document.getElementById('total-cost-pieces-breakdown').innerText = unitCostPerSet;
                    document.getElementById('budget-breakdown').innerText = inputBudget.toFixed(2); // Display the input budget
                    document.getElementById('total-cost-sets-breakdown').innerText = totalCostStocks;
                    document.getElementById('remaining-balance-breakdown').innerText = remainingBalance;
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