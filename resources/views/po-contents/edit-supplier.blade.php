@extends('main.main')

@section('content')
<div class="container mx-auto mt-10">
    <div class="bg-white shadow-md rounded-lg p-6">
        <button onclick="history.back()" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600 transition-colors duration-300 mb-4">
            Return 
        </button>
        <h2 class="text-2xl font-bold mb-6">Edit Supplier</h2>

        <!-- Display Success or Error Messages -->
        @if(session('success'))
            <script>
                alert("{{ session('success') }}");
            </script>
            <div class="bg-green-100 text-green-700 p-4 rounded mb-4">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <script>
                alert("{{ session('error') }}");
            </script>
            <div class="bg-red-100 text-red-700 p-4 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form -->
        <form id="edit-supplier-form" action="{{ route('update-supplier', $supplier->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="supplier_name" class="block text-sm font-medium text-gray-700">Supplier Name</label>
                <input type="text" id="supplier_name" name="supplier_name" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                       value="{{ old('supplier_name', $supplier->supplier_name) }}" required>
            </div>
            <div class="mb-4">
                <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                <input type="text" id="contact_person" name="contact_person" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                       value="{{ old('contact_person', $supplier->contact_person) }}">
            </div>
            <div class="mb-4">
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                       value="{{ old('phone_number', $supplier->phone_number) }}" required>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                       value="{{ old('email', $supplier->email) }}">
            </div>
            <div class="mb-4">
                <label for="product_type" class="block text-sm font-medium text-gray-700">Product Type</label>
                <input type="text" id="product_type" name="product_type" 
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                       value="{{ old('product_type', $supplier->product_type) }}">
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="Active" {{ old('status', $supplier->status) === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status', $supplier->status) === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <button type="submit" 
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                Save Changes
            </button>
        </form>
    </div>
</div>

<!-- JavaScript for Confirmation -->
<script>
    document.getElementById('edit-supplier-form').addEventListener('submit', function (event) {
        const confirmation = confirm('Are you sure you want to save these changes?');
        if (!confirmation) {
            event.preventDefault(); // Prevent form submission
        }
    });
</script>
@endsection
