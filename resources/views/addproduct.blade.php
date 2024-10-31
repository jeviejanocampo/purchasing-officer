<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
</head>
<body class="bg-gray-100">

@extends('main.main') 

@section('content')
    <div class="mb-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 m-7">
        <div class="rounded-lg shadow-md p-1 bg-cover bg-center" 
             style="background-image: url('{{ asset('images/card-background.png') }}');">
            <div class="bg-opacity-60 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray">Remaining Balance</h2>
                <p class="text-xl font-bold text-gray">
                    ₱0.00 <!-- Placeholder for Remaining Balance -->
                </p>
                <p class="text-sm text-gray">
                    Date: 2024-10-23 to 2024-11-22 <!-- Static date range -->
                </p>
            </div>
        </div> 
        <div class="rounded-lg shadow-md p-1 bg-cover bg-center" 
             style="background-image: url('{{ asset('images/card-background.png') }}');">
            <div class="bg-opacity-60 rounded-lg p-4">
                <h2 class="text-lg font-semibold text-gray">Count of Products in Inventory</h2>
                <p class="text-xl font-bold text-gray">
                    0 <!-- Placeholder for Inventory Count -->
                </p>
                <p class="text-sm text-gray">
                    Date: 2024-10-23 to 2024-11-22 <!-- Static date range -->
                </p>
            </div>
        </div> 
    </div>

    <div class="max-w-10xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-5">
        <!-- Left Column -->
        <div class="bg-white rounded-lg shadow-md p-5">
            <h1 class="text-xl font-bold mb-4">Add Budget</h1>
            <form action="/budget/store" method="POST" id="budget-form">
                <!-- CSRF protection placeholder -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="mb-4">
                    <label for="budget" class="block text-sm font-medium text-gray-700">Input Budget:</label>
                    <input 
                        type="text" 
                        id="budget" 
                        name="budget" 
                        placeholder="Input budget here in pesos" 
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                    />
                </div>

                <div class="mb-4">
                    <label for="product-name-to-buy" class="block text-sm font-medium text-gray-700">Product Name to Buy:</label>
                    <input 
                        type="text" 
                        id="product-name-to-buy" 
                        name="product_to_buy"  
                        placeholder="Enter product name to buy" 
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                    />
                </div>

                <div class="mb-4">
                    <label for="budget-identifier" class="block text-sm font-medium text-gray-700">Budget Identifier:</label>
                    <input 
                        type="text" 
                        id="budget-identifier" 
                        name="budget-identifier" 
                        placeholder="e.g., AB12" 
                        class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                    />
                    <button type="submit" class="mt-2 bg-green-500 text-white rounded-md p-2">Confirm Budget Identifier</button>
                </div>
            </form>
        </div>

        <!-- Right Column -->
        <div class="bg-white rounded-lg shadow-md p-5">
            <h1 class="text-xl font-bold mb-4">Add Product</h1>
            <form action="/product/store" method="POST">
                <!-- CSRF token placeholder -->
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Input Budget:</label>
                    <span id="input-budget-display" class="block mt-1 text-sm text-gray-800">₱0.00</span>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Remaining Balance:</label>
                    <span id="updated-remaining-balance-display" class="block mt-1 text-sm text-gray-800">₱0.00</span>
                </div>

                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div>
                        <label for="budget-selector" class="block text-sm font-medium text-gray-700">Select Budget Identifier:</label>
                        <select id="budget-selector" name="budget_identifier" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200">
                            <option value="" disabled selected>Select a budget identifier</option>
                            <!-- Static example options -->
                            <option value="1">AB12</option>
                            <option value="2">CD34</option>
                        </select>
                    </div>

                    <div>
                        <label for="product-selector" class="block text-sm font-medium text-gray-700">Select Product to Buy:</label>
                        <select id="product-selector" name="product_name" class="mt-1 block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200">
                            <option value="" disabled selected>Select a product</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="mt-4 bg-blue-500 text-white rounded-md p-2">Add Product</button>
            </form>
        </div>

        <!-- Middle Column -->
        <div class="bg-white rounded-lg shadow-md p-5 col-span-1 md:col-span-2 lg:col-span-1">
            <h1 class="text-xl font-bold mb-4">Budget Allocation Details for Product Purchase</h1>
            <table class="min-w-full border-collapse border border-gray-300 mt-4">
                <thead>
                    <tr>
                        <th class="border px-2 py-2">ID</th>
                        <th class="border px-2 py-2">Reference Code</th>
                        <th class="border px-2 py-2">Product to Buy</th>
                        <th class="border px-2 py-2">Input Budget</th>
                        <th class="border px-2 py-2">Remaining Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Static example rows -->
                    <tr>
                        <td class="border px-2 py-2">1</td>
                        <td class="border px-2 py-2">AB12</td>
                        <td class="border px-2 py-2">Sample Product</td>
                        <td class="border px-2 py-2">₱1,000.00</td>
                        <td class="border px-2 py-2">₱500.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

    <div id="modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-5 w-1/3 mx-auto">
            <h2 class="text-xl font-bold mb-4">Budget Details</h2>
            <p id="modal-product-name" class="mb-2"></p>
            <p id="modal-input-budget" class="mb-2"></p>
            <p id="modal-remaining-balance" class="mb-2"></p>
            <p id="modal-created-at" class="mb-2"></p>
            <button class="mt-4 bg-red-500 text-white rounded-md p-2" onclick="closeModal()">Close</button>
        </div>
    </div>

    <script src="{{ asset('js/product.js') }}"></script>
    <script src="{{ asset('js/calculation.js') }}"></script>

    

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Check for success message
        @if(session('success'))
            alert("{{ addslashes(session('success')) }}");
        @endif

        // Check for error messages
        @if($errors->any())
            alert("{{ addslashes($errors->first()) }}");
        @endif
    });
</script>


</body>
</html>
