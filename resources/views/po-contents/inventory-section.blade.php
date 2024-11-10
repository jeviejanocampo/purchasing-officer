<div id="inventory-section" class="section hidden bg-white rounded-lg shadow-md p-3 mb-5">
    <h1 class="text-xl font-bold mb-4">Inventory</h1>
    <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
        <strong>Note:</strong> When the product is already in the inventory, add details for 'Ready for Sale'
        <!-- Add the button below the note -->
        <button 
            class="ml-4 bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600"
            onclick="document.getElementById('section-selector').value = 'product-details'; toggleSections();"
        >
            Go to Product Details
        </button>
    </div>


    <div class="flex justify-between mb-4">
        <div class="w-1/4">
            <input 
                type="text" 
                id="budget-search" 
                placeholder="Search by Budget Identifier" 
                class="block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200"
                onkeyup="filterInventory()"
            />
        </div>

        <!-- Date Range Filter -->
        <div class="w-1/4 flex space-x-2">
            <input 
                type="date" 
                id="start-date-filter" 
                class="block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200"
                onchange="filterInventory()"
            />
            <input 
                type="date" 
                id="end-date-filter" 
                class="block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200"
                onchange="filterInventory()"
            />
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border-collapse border border-gray-300 mt-4" id="inventory-table">
            <thead>
                <tr>
                    <th class="border px-1 py-1">Inventory ID</th>
                    <th class="border px-1 py-1">Budget ID</th>
                    <th class="border px-1 py-1">Product Name</th>
                    <th class="border px-1 py-1">Unit Cost</th>
                    <th class="border px-1 py-1">Pieces per Set</th>
                    <th class="border px-1 py-1">Stocks per Set</th>
                    <th class="border px-1 py-1">Created At</th>
                    <th class="border px-1 py-1">Expiration Date</th>
                    <th class="border px-1 py-1">Edit Quantity</th>
                    <th class="border px-1 py-1">Status</th> 
                    <th class="border px-1 py-1">Remarks</th> 
                    <th class="border px-1 py-1">Reports</th> 
                </tr>
            </thead>
            <tbody>
                @foreach ($inventories as $inventory)
                    <tr>
                        <td class="border px-1 py-1">{{ $inventory->id }}</td>
                        <td class="border px-2 py-2 flex items-center">
                            {{ $inventory->budget_identifier }}
                            <button 
                                class="ml-2 bg-blue-500 text-white rounded-md px-2 py-1" 
                                onclick="fetchBudgetDetails({{ $inventory->budget_identifier }})"
                            >
                                View
                            </button>
                        </td>
                        <td class="border px-1 py-1">{{ $inventory->product_name }}</td>
                        <td class="border px-1 py-1">₱{{ number_format($inventory->unit_cost, 2) }}</td>
                        <td class="border px-1 py-1">{{ $inventory->pieces_per_set }}</td>
                        <td class="border px-1 py-1 relative">
                            {{ $inventory->stocks_per_set }}
                            @if ($inventory->stocks_per_set <= 10)
                                <span class="absolute right-0 top-0 bg-red-800 text-white text-xs px-2 py-1 rounded-full stocks-low-animation">
                                    Stocks Low
                                </span>
                            @endif
                            <script>
                                checkLowStock({{ $inventory->stocks_per_set }}, '{{ $inventory->product_name }}');
                            </script>
                        </td>
                        <td class="border px-1 py-1">{{ $inventory->created_at }}</td>
                        <td class="border px-1 py-1">{{ $inventory->exp_date }}</td>
                        <td class="border px-1 py-1">
                                <!-- Edit quantity button -->
                                <button class="ml-1 bg-yellow-500 text-white rounded-md px-1 py-1 text-sm" onclick="openEditQuantityModal(${inventory.id}, ${inventory.stocks_per_set})">Edit Quantity</button>
                            </td>
                        <td class="border px-1 py-1">
                            <span id="set-status-{{ $inventory->id }}">{{ $inventory->set_status }}</span>
                            <button 
                                class="ml-2 bg-yellow-500 text-white rounded-md px-1 py-1" 
                                onclick="openSetStatusModalForInventory({{ $inventory->id }}, '{{ $inventory->set_status }}')"
                            >
                                Edit
                            </button>
                        </td>
                        <td class="border px-1 py-1">
                            @if($inventory->remarks)
                                <button 
                                    class="bg-blue-700 text-white rounded-md px-3 py-1 hover:bg-blue-800 transition duration-200" 
                                    onclick="openViewRemarksModal('{{ $inventory->remarks }}')"
                                >
                                    View
                                </button>
                                <button 
                                    class="ml-2 bg-yellow-600 text-white rounded-md px-1 py-1 hover:bg-yellow-700" 
                                    onclick="openEditRemarksModal('{{ $inventory->budget_identifier }}', '{{ $inventory->remarks }}')"
                                >
                                    Edit
                                </button>
                            @else
                                <button 
                                    class="ml-2 bg-green-600 text-white rounded-md px-1 py-1 hover:bg-green-700" 
                                    onclick="openRemarksModal('{{ $inventory->budget_identifier }}')"
                                >
                                    Add Remarks
                                </button>
                            @endif
                        </td>
                        <td class="border px-2 py-2">
                            <a 
                                href="{{ route('inventory.details', ['id' => $inventory->id]) }}" 
                                class="bg-blue-700 text-white rounded-md px-1 py-1 hover:bg-blue-800 transition duration-200">
                                View
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>