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
            <div class="flex">
                <input 
                    type="text" 
                    id="reference_code" 
                    name="reference_code" 
                    placeholder="e.g., AB12" 
                    class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                />
                <button 
                    type="button" 
                    onclick="generateIdentifier()" 
                    class="ml-2 bg-blue-500 text-white rounded-md p-2"
                >
                    Generate
                </button>
            </div>
        </div>

        <!-- Supplier Dropdown -->
        <div class="mb-4">
            <label for="supplier_id" class="block text-sm font-medium text-gray-700">Select Supplier:</label>
            <select 
                id="supplier_id" 
                name="supplier_id" 
                class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200"
            >
                <option value="" disabled selected>Select a Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->supplier_name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="mt-2 bg-green-500 text-white rounded-md p-2">Confirm Budget</button>
    </form>
</div>

<script>
    function generateIdentifier() {
        const letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const numbers = '0123456789';
        
        // Generate two random letters
        let identifier = '';
        for (let i = 0; i < 2; i++) {
            identifier += letters.charAt(Math.floor(Math.random() * letters.length));
        }

        // Generate two random numbers
        for (let i = 0; i < 2; i++) {
            identifier += numbers.charAt(Math.floor(Math.random() * numbers.length));
        }

        // Set the generated identifier to the reference code input
        document.getElementById('reference_code').value = identifier;
    }
</script>
