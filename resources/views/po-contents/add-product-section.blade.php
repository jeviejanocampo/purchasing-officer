<div id="add-product-section" class="section hidden bg-white rounded-lg shadow-md p-5">
            <h1 class="text-xl font-bold mb-4">Add Product</h1>
            <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
                <strong>Note:</strong> Make sure to pay attention on details before proceeding
            </div>
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
                            @if ($budget->budget_status === 'PENDING')
                                <option value="{{ $budget->id }}" data-input-budget="{{ number_format($budget->input_budget, 2) }}" data-product="{{ $budget->product_to_buy }}">
                                    {{ $budget->id }} - {{ $budget->product_to_buy }}
                                </option>
                            @endif
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