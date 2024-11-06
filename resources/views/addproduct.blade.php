<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <style>
        body {
            font-size: 14px;
        }
        .hidden {
            display: none;
        }

        #budget-modal:target {
            display: flex;
        }
        .stocks-low-animation {
            animation: beat 0.5s infinite; /* Infinite beat animation */
        }

        @keyframes beat {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2); /* Scale up */
            }
            100% {
                transform: scale(1); /* Scale back to original size */
            }
        }

    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-gray-100" style="font-family: 'Roboto', sans-serif;">

    @extends('main.main') 

    @section('content')
    
    <h1 class="text-2xl font-bold text-gray-800 mb-4">Stock Procurement</h1>
    @if(session('user_id'))
                <p class="hidden" id="user-id">Your User ID: {{ session('user_id') }}</p>
            @else
                <p class="hidden">You are not logged in.</p>
    @endif  
    <div class="mb-4">
        <label for="section-selector" class="block text-sm font-medium text-gray-700">Select Section</label>
        <select id="section-selector" class="block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" onchange="toggleSections()">
            <optgroup label="Restock Budget Management">
                <option value="add-budget">Add Budget for Restocking</option>
            </optgroup>
            <optgroup label="Product Management">
            <option value="add-product-details">Add Product Details for Restocking</option> 
                <option value="add-product">Add Product To The Inventory</option>
            </optgroup>
            <optgroup label="Inventory Management">
                <option value="product-details">Product Details</option>
                <option value="inventory" selected>Inventory</option>  
                <option value="budget-allocation">Restocking Budget Allocation</option>
            </optgroup>
        </select>
    </div>


    <div id="product-details-section" class="section hidden bg-white rounded-lg shadow-md p-5">
    <h1 class="text-xl font-bold mb-4">Product Details</h1>

    <table class="min-w-full table-auto text-sm">
        <thead>
            <tr>
                <th class="px-2 py-1 border-b">Product ID</th>
                <th class="px-2 py-1 border-b">Product Name</th>
                <th class="px-2 py-1 border-b">Product Image</th>
                <th class="px-2 py-1 border-b">Created At</th>
                <th class="px-2 py-1 border-b">Product Status</th>
                <th class="px-2 py-1 border-b">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
                <tr>
                    <td class="px-2 py-1 border-b">{{ $product->product_id }}</td>
                    <td class="px-2 py-1 border-b">{{ $product->product_name }}</td>
                    <td class="px-2 py-1 border-b">
                        <img src="{{ asset('storage/' . $product->product_image) }}" alt="{{ $product->product_name }}" class="w-12 h-12 object-cover">
                    </td>
                    <td class="px-2 py-1 border-b">{{ $product->created_at }}</td>
                    <td class="px-2 py-1 border-b">{{ $product->product_status }}</td>
                    <td class="px-2 py-1 border-b">
                        <a href="{{ route('product.details', $product->product_id) }}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors duration-300">Edit</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>



    <div id="add-budget-section" class="section bg-white rounded-lg shadow-md p-5">
        <h1 class="text-xl font-bold mb-4">Add Budget</h1>
        <form action="{{ route('budget.store') }}" method="POST" id="budget-form">
            @csrf 
            <div class="mb-4">
                <label for="input_budget" class="block text-sm font-medium text-gray-700">Input Budget:</label>
                <input 
                    type="text" 
                    id="input_budget" 
                    name="input_budget" 
                    placeholder="Input budget here in pesos" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                />
            </div>

            <div class="mb-4">
                <label for="product_to_buy" class="block text-sm font-medium text-gray-700">Product Name to Buy:</label>
                <input 
                    type="text" 
                    id="product_to_buy" 
                    name="product_to_buy"  
                    placeholder="Enter product name to buy" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                />
            </div>

            <div class="mb-4">
                <label for="reference_code" class="block text-sm font-medium text-gray-700">Budget Identifier:</label>
                <input 
                    type="text" 
                    id="reference_code" 
                    name="reference_code" 
                    placeholder="e.g., AB12" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                />
            </div>

            <button type="submit" class="mt-2 bg-green-500 text-white rounded-md p-2">Confirm Budget</button>
        </form>
    </div>

    <div id="add-product-section" class="section hidden bg-white rounded-lg shadow-md p-5">
        <h1 class="text-xl font-bold mb-4">Add Product</h1>
        <form id="add-product-form" action="/product/store" method="POST">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Budget Inputted:</label>
            <h3 id="input-budget" class="mt-1 text-sm text-gray-800">₱0.00</h3>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Remaining Balance:</label>
            <span id="updated-remaining-balance-display" class="block mt-1 text-sm text-gray-800">₱0.00</span>
        </div>

        <div class="mb-4 grid grid-cols-2 gap-4">
            <div>
                <label for="budget-selector" class="block text-sm font-medium text-gray-700">Select Budget ID:</label>
                <select id="budget-selector" name="budget_identifier" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" onchange="updateBudgetInput()">
                    <option value="" disabled selected>Select a budget identifier</option>
                    @foreach ($budgets as $budget)
                        <option value="{{ $budget->id }}" data-input-budget="{{ number_format($budget->input_budget, 2) }}" data-product="{{ $budget->product_to_buy }}">{{ $budget->id }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="product-selector" class="block text-sm font-medium text-gray-700">Select Product to Buy:</label>
                <select id="product-selector" name="product_name" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200">
                    <option value="" disabled selected>Select a product</option>
                </select>
            </div>
        </div>

        <div class="mb-4 grid grid-cols-2 gap-4">
        <div>
            <label for="product-id-selector" class="block text-sm font-medium text-gray-700">Select Product ID:</label>
            <select id="product-id-selector" name="product_id" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200">
                <!-- Default option with value 0 -->
                <option value="0" selected>0 - Temporary Option</option>
                
                <!-- Dynamically populated product options -->
                @foreach ($productIds as $product)
                    <option value="{{ $product->product_id }}">
                        {{ $product->product_id }} - {{ $product->product_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-4 grid grid-cols-3 gap-4">
            <div>
                <label for="unit-cost" class="block text-sm font-medium text-gray-700">Unit Cost:</label>
                <input type="number" id="unit-cost" name="unit_cost" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" placeholder="₱0.00" step="0.01" required onchange="calculateStocks()" onkeyup="calculateStocks()">
            </div>
            <div>
                <label for="pieces-per-set" class="block text-sm font-medium text-gray-700">Pieces per Set:</label>
                <input type="number" id="pieces-per-set" name="pieces_per_set" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" placeholder="0" required onchange="calculateStocks()" onkeyup="calculateStocks()">
            </div>
            <div>
                <label for="stocks-per-set" class="block text-sm font-medium text-gray-700">Stocks per Set:</label>
                <input type="number" id="stocks-per-set" name="stocks_per_set" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" placeholder="0" required>
            </div>
        </div>

        <div class="mb-4">
            <label for="expiration-date" class="block text-sm font-medium text-gray-700">Expiration Date:</label>
                <input type="date" id="expiration-date" name="expiration_date" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" required>
            </div>

            <button type="submit" class="mt-4 bg-blue-500 text-white rounded-md p-2">Add Product</button>
        </form>
    </div>

    <div id="add-product-details-section" class="section hidden bg-white rounded-lg shadow-md p-5">
        <h1 class="text-xl font-bold mb-4">Budget Details</h1>

        <button 
            onclick="location.reload()" 
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors duration-300 mb-4">
            Refresh
        </button>

        <div class="mt-4">
            <input 
                type="text" 
                placeholder="Search by Budget ID..." 
                class="border rounded-lg px-3 py-2 w-full lg:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                id="searchBudget"
                onkeyup="searchBudgets()"
            />
        </div>

        <div class="overflow-y-auto h-200">
            <table class="min-w-full border-collapse border border-gray-300 mt-4 text-sm"> <!-- Reduced font size -->
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border px-1 py-1">Budget ID</th> <!-- Reduced padding -->
                        <th class="border px-1 py-1">Balance</th> <!-- Reduced padding -->
                        <th class="border px-1 py-1">Product To Buy</th> <!-- Reduced padding -->
                        <th class="border px-1 py-1">Action</th> <!-- Reduced padding -->
                    </tr>
                </thead>
                <tbody id="budgetTableBody">
                    @php
                        $totalBalance = 0; // Initialize total balance variable
                    @endphp
                    @foreach ($budgets as $budget)
                        <!-- Check if the remaining_balance is zero (Budget not used yet) -->
                        @if($budget->remaining_balance == 0)
                            <tr>
                                <td class="border px-1 py-1">{{ $budget->id }}</td> <!-- Reduced padding -->
                                <td class="border px-1 py-1">
                                    <span class="text-red-500 font-semibold">Budget not used yet</span>
                                </td>
                                <td class="border px-1 py-1">{{ $budget->product_to_buy }}</td> <!-- Product To Buy -->
                                <td class="border px-1 py-1">
                                    <button 
                                        onclick="openModal({{ json_encode($budget) }})" 
                                        class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition-colors duration-300 text-xs"> <!-- Reduced button size -->
                                        View
                                    </button>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        

        <h1 class="text-xl font-bold mb-4 mt-10">Add Product Details for Restocking</h1>
        <form action="{{ route('product.addRestockDetails') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">

                <!-- Product Name -->
                <div class="mt-4 mb-4">
                    <label for="product_name" class="block text-sm font-medium text-gray-700">Product Name</label>
                    <select id="product_name" name="product_name" class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Product</option>
                        @foreach($productNames as $product)
                            <option value="{{ $product->product_to_buy }}">{{ $product->product_to_buy }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Product Image -->
                <div class="mt-4 mb-4">
                    <label for="product_image" class="block text-sm font-medium text-gray-700">Product Image</label>
                    <input type="file" id="product_image" name="product_image" class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>

                <!-- Product Status -->
                <div class="mt-4 mb-4">
                    <label for="product_status" class="block text-sm font-medium text-gray-700">Product Status</label>
                    <select id="product_status" name="product_status" class="border rounded-lg px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="Ordered">Ordered</option>
                        <option value="Back-Ordered">Back-Ordered</option>
                        <option value="In Stock">In Stock</option>
                        <option value="Not Available">Not Available</option>
                        <option value="Out of Stock">Out of Stock</option>
                    </select>
                </div>

            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors duration-300 mt-4">
                Add Product
            </button>
        </form>
    </div>


    @if(session('success'))
        <script>
            swal({
                title: "Success!",
                text: "{{ session('success') }}",
                type: "success",
                confirmButtonText: "OK"
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            swal({
                title: "Error!",
                text: "{{ session('error') }}",
                type: "error",
                confirmButtonText: "Try Again"
            });
        </script>
    @endif

    <div id="budget-allocation-section" class="section hidden bg-white rounded-lg shadow-md p-5">
        <h1 class="text-xl font-bold mb-4">Budget Allocation Details for Product Purchase</h1>

        <button 
            onclick="location.reload()" 
            class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition-colors duration-300 mb-4">
            Refresh
        </button>

        <div class="mt-4">
            <input 
                type="text" 
                placeholder="Search by Budget ID..." 
                class="border rounded-lg px-3 py-2 w-full lg:w-1/3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                id="searchBudget"
                onkeyup="searchBudgets()"
            />
        </div>

    <div class="overflow-y-auto h-200">
        <table class="min-w-full border-collapse border border-gray-300 mt-4 text-sm"> <!-- Reduced font size -->
            <thead>
                <tr class="bg-gray-100">
                    <th class="border px-1 py-1">Budget ID</th> <!-- Reduced padding -->
                    <th class="border px-1 py-1">Balance</th> <!-- Reduced padding -->
                    <th class="border px-1 py-1">Action</th> <!-- Reduced padding -->
                </tr>
            </thead>
            <tbody id="budgetTableBody">
                @php
                    $totalBalance = 0; // Initialize total balance variable
                @endphp
                @foreach ($budgets as $budget)
                    <tr>
                        <td class="border px-1 py-1">{{ $budget->id }}</td> <!-- Reduced padding -->
                        <td class="border px-1 py-1">
                            @if($budget->remaining_balance == 0)
                                <span class="text-red-500 font-semibold">Budget not used yet</span>
                            @else
                                ₱{{ number_format($budget->remaining_balance, 2) }}
                                @php
                                    $totalBalance += $budget->remaining_balance; // Add to total balance
                                @endphp
                            @endif
                        </td>
                        <td class="border px-1 py-1">
                            <button 
                                onclick="openModal({{ json_encode($budget) }})" 
                                class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600 transition-colors duration-300 text-xs"> <!-- Reduced button size -->
                                View
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- <div class="mt-4">
            <h2 class="text-lg font-semibold">Total Remaining Balance: ₱{{ number_format($totalBalance, 2) }}</h2>
        </div> -->
    </div>
    </div>

        
    <div id="inventory-section" class="section hidden bg-white rounded-lg shadow-md p-3 mb-5">
        <h1 class="text-xl font-bold mb-4">Inventory</h1>
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
                        <th class="border px-1 py-1">Status</th> 
                        <th class="border px-1 py-1">Remarks</th> 
                        <th class="border px-1 py-1">View Details</th> 
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
                                <span id="set-status-{{ $inventory->id }}">{{ $inventory->set_status }}</span>
                                <button 
                                    class="ml-2 bg-yellow-500 text-white rounded-md px-1 py-1" 
                                    onclick="openSetStatusModal({{ $inventory->id }}, '{{ $inventory->set_status }}')"
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
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
    </div>

    <div id="set-status-modal" class="hidden fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-md p-5">
                <h2 class="text-lg font-bold mb-4">Edit Set Status</h2>
                <select id="status-select" class="block w-full border border-gray-300 rounded-md p-2 mb-4">
                    <option value="In Stock">In Stock</option>
                    <option value="On Order">On Order</option>
                    <option value="Discontinued">Discontinued</option>
                    <option value="Expired">Expired</option>
                    <option value="Damaged">Damaged</option>
                </select>
                <div class="flex justify-end">
                    <button id="save-status-button" class="bg-blue-500 text-white rounded-md px-4 py-2" onclick="saveStatus()">Save</button>
                    <button class="ml-2 bg-red-500 text-white rounded-md px-4 py-2" onclick="closeSetStatusModal()">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="remarks-modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-md p-5 w-1/3">
            <h2 class="text-lg font-bold mb-4">Add/Edit Remarks</h2>
            <textarea 
                id="remarks-input" 
                rows="4" 
                class="block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                placeholder="Enter your remarks here..."
            ></textarea>
            <div class="flex justify-end mt-4">
                <button 
                    class="bg-blue-500 text-white rounded-md px-4 py-2" 
                    onclick="saveRemarks()"
                >
                    Save
                </button>
                <button 
                    class="ml-2 bg-red-500 text-white rounded-md px-4 py-2" 
                    onclick="closeRemarksModal()"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <div id="view-remarks-modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-md p-5 w-1/3">
            <h2 class="text-lg font-bold mb-4">View Remarks</h2>
            <p id="view-remarks-text" class="text-gray-800"></p>
            <div class="flex justify-end mt-4">
                <button 
                    class="bg-red-500 text-white rounded-md px-4 py-2" 
                    onclick="closeViewRemarksModal()"
                >
                    Close
                </button>
            </div>
        </div>
    </div>


    <div id="budget-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-md p-5 w-11/12 md:w-1/3 relative">
            <div id="modal-content"></div>
            <a href="#" class="absolute top-3 right-3 text-xl text-gray-500 hover:text-gray-800">&times;</a>
        </div>
    </div>

    <script>
        let selectedBudgetIdentifier;

        function openRemarksModal(budgetIdentifier) {
            selectedBudgetIdentifier = budgetIdentifier;
            document.getElementById('remarks-input').value = ''; // Clear input for adding remarks
            document.getElementById('remarks-modal').classList.remove('hidden');
        }

        function openEditRemarksModal(budgetIdentifier, existingRemarks) {
            selectedBudgetIdentifier = budgetIdentifier;
            document.getElementById('remarks-input').value = existingRemarks; // Set existing remarks
            document.getElementById('remarks-modal').classList.remove('hidden');
        }

        function closeRemarksModal() {
            document.getElementById('remarks-modal').classList.add('hidden');
            document.getElementById('remarks-input').value = ''; // Clear the input
        }

        function saveRemarks() {
            const remarks = document.getElementById('remarks-input').value;

            if (!remarks) {
                alert('Please enter remarks.');
                return;
            }

            // AJAX request to save the remarks in the database
            fetch(`/inventory/${selectedBudgetIdentifier}/remarks`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token
                },
                body: JSON.stringify({ remarks })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the table with the new remarks
                    updateRemarksInTable(selectedBudgetIdentifier, remarks);
                    closeRemarksModal();
                } else {
                    alert('Error saving remarks.');
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function updateRemarksInTable(budgetIdentifier, remarks) {
            // Find the correct row in the inventory table and update the remarks cell
            const rows = document.querySelectorAll('#inventory-table tbody tr');
            rows.forEach(row => {
                const budgetIdCell = row.querySelector('td:first-child');
                if (budgetIdCell.textContent.trim() === budgetIdentifier) {
                    const remarksCell = row.querySelector('td:last-child'); // Assuming remarks is the last cell
                    remarksCell.querySelector('span').textContent = remarks; // Update remarks
                }
            });
        }

        function openViewRemarksModal(remarks) {
            document.getElementById('view-remarks-text').textContent = remarks; // Set the remarks in the modal
            document.getElementById('view-remarks-modal').classList.remove('hidden'); // Show the modal
        }

        function closeViewRemarksModal() {
            document.getElementById('view-remarks-modal').classList.add('hidden'); // Hide the modal
        }
    </script>

    @include('components.budget-modal')
    @endsection

    <script src="{{ asset('js/alert-stocks.js') }}"></script> 
    <script src="{{ asset('js/inventory.js') }}"></script> 
    <script src="{{ asset('js/product.js') }}"></script>
    <script src="{{ asset('js/selection.js') }}"></script>
    <script src="{{ asset('js/calculationproduct.js') }}"></script>
    <script src="{{ asset('js/calculation.js') }}"></script>
    <script src="{{ asset('js/budgetjs.js') }}"></script>
    <script src="{{ asset('js/nof.js') }}"></script> 
    <script src="{{ asset('js/form-data.js') }}"></script> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
     const budgetStoreRoute = "{{ route('budget.store') }}";
    </script>
    <script>
        $(document).ready(function() {
            // Function to check if budget ID is already used
            $('#budget-selector').on('change', function() {
                const budgetId = $(this).val();

                if (budgetId) {
                    $.ajax({
                        url: "{{ route('product.checkBudget') }}",
                        type: "POST",
                        data: {
                            budget_identifier: budgetId,
                            _token: "{{ csrf_token() }}" // CSRF token for security
                        },
                        success: function(response) {
                            if (response.is_used) {
                                // Show an alert if the budget ID is already used
                                swal({
                                    title: "Warning",
                                    text: "Budget ID is already used in inventory.",
                                    type: "warning"
                                });

                                // Optionally clear the selection
                                $('#budget-selector').val('');
                            } else {
                                // Update the displayed budget input
                                const inputBudget = $('#budget-selector option:selected').data('input-budget');
                                $('#input-budget').text(inputBudget);
                            }
                        },
                        error: function(xhr) {
                            swal({
                                title: "Error",
                                text: "An error occurred while checking the budget ID.",
                                type: "error"
                            });
                        }
                    });
                }
            });
        });
    </script>

</body>
</html>
