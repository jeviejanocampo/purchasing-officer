<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-6">
    <!-- Card 1 -->
    <div class="bg-gray-800 text-white p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Inventory In Stock</h3>
        <p class="text-2xl">{{ $inventoryCount }}</p> <!-- Display the inventory count -->
        <p>Date: {{ now()->format('Y-m-d') }}</p> <!-- Dynamic date -->
    </div>

    <!-- Card 2 -->
    <div class="bg-gray-700 text-white p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Total Products</h3>
        <p class="text-2xl">8</p>
        <p>Date: {{ now()->format('Y-m-d') }}</p> <!-- Dynamic date -->
    </div>

    <!-- Card 3 -->
    <div class="bg-gray-700 text-white p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Total Budget Allocated</h3>
        <p class="text-2xl">₱ {{ number_format($totalBudgetAllocated, 2) }}</p> <!-- Display total budget allocated -->
        <p>Date: {{ now()->format('Y-m-d') }}</p> <!-- Dynamic date -->
    </div>

    <!-- Card 4 -->
    <!-- <div class="bg-gray-500 text-white p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Products Expiring Soon</h3>
        <p class="text-2xl">2</p>
        <p>Date: {{ now()->format('Y-m-d') }}</p> <!-- Dynamic date -->
    </div> -->

    <!-- Optional Cards (Uncomment to use) -->
    <!-- Card 5 -->
    <!-- <div class="bg-gray-400 text-white p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Low Stock Products</h3>
        <p class="text-2xl">3</p>
        <p>Date: {{ now()->format('Y-m-d') }}</p> 
    </div>

    <div class="bg-gray-300 text-gray-800 p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Total Inventory Value</h3>
        <p class="text-2xl">$1,200.00</p>
        <p>Date: {{ now()->format('Y-m-d') }}</p>
    </div>

    <div class="bg-gray-200 text-gray-800 p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Active Products</h3>
        <p class="text-2xl">6</p>
        <p>Date: {{ now()->format('Y-m-d') }}</p> 
    </div>

    <div class="bg-gray-100 text-gray-800 p-4 rounded-lg shadow">
        <h3 class="font-bold text-lg">Products with Remarks</h3>
        <p class="text-2xl">4</p>
        <p>Date: {{ now()->format('Y-m-d') }}</p> 
    </div>   -->
</div>
