<div id="budget-allocation-section" class="section hidden bg-white rounded-lg shadow-md p-5">
    <h1 class="text-xl font-bold mb-4">Budget Allocation Details for Product Purchase</h1>

    <!-- Search Bar -->
    <div class="mb-4 flex">
        <input 
            type="text" 
            id="budget-search" 
            placeholder="Search by Budget ID" 
            class="w-1/4 p-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200"
            onkeyup="searchBudget()"
        />
    </div>

    <button 
        onclick="location.reload()" 
        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors duration-300 mb-4">
        Refresh
    </button>

    <div class="overflow-y-auto h-200">
        <table class="min-w-full border-collapse border border-gray-300 mt-4 text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-1 py-1">Budget ID</th>
                    <th class="border px-1 py-1">Balance</th>
                    <th class="border px-1 py-1">Status</th>
                    <th class="border px-1 py-1">Action</th> 
                </tr>
            </thead>
            <tbody id="budgetTableBody">
                @php
                    $totalBalance = 0;
                @endphp
                @foreach ($budgets as $budget)
                    <tr>
                        <td class="border px-1 py-1">{{ $budget->id }}</td> 
                        <td class="border px-1 py-1">
                            @if($budget->remaining_balance == 0)
                                <span class="text-red-500 font-semibold">Budget not used yet</span>
                            @else
                                ₱{{ number_format($budget->remaining_balance, 2) }}
                                @php
                                    $totalBalance += $budget->remaining_balance;
                                @endphp
                            @endif
                        </td>
                        <td class="border px-1 py-1">{{ $budget->budget_status }}</td> 
                        <td class="border px-1 py-1">
                            <button 
                                onclick="openModal({{ json_encode($budget) }})" 
                                class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition-colors duration-300 text-xs">
                                View
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    function searchBudget() {
        const input = document.getElementById('budget-search').value.toLowerCase();
        const tableRows = document.querySelectorAll('#budgetTableBody tr');
        
        tableRows.forEach(row => {
            const budgetIdCell = row.cells[0]; // Budget ID cell
            const budgetId = budgetIdCell ? budgetIdCell.textContent || budgetIdCell.innerText : '';
            
            if (budgetId.toLowerCase().includes(input)) {
                row.style.display = ''; // Show row if it matches the search
            } else {
                row.style.display = 'none'; // Hide row if it doesn't match
            }
        });
    }
</script>
