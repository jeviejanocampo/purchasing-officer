<div id="add-budget-section" class="section bg-white rounded-lg shadow-md p-5">
            <h1 class="text-xl font-bold mb-4">Add Budget</h1>
            <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
                <strong>Note:</strong> Add Budget for a certain product to use
            </div>
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