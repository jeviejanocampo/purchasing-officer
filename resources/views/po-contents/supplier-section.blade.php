    <div id="suppliers-section" class="section bg-white rounded-lg shadow-md p-5">
        <!-- Supplier Table -->
        <h2 class="text-xl font-bold mb-4">Supplier Details</h2>
        <div class="bg-green-100 text-green-800 p-3 rounded-md mb-4">
            <strong>Note:</strong> To add supplier, click here
            <!-- Add the button below the note -->
            <button 
                class="ml-4 bg-blue-500 text-white rounded-md px-4 py-2 hover:bg-blue-600"
                onclick="document.getElementById('section-selector').value = 'add-supplier'; toggleSections();"
            >
            Add Supplier
            </button>
        </div>
        <table class="min-w-full bg-white border border-gray-300 rounded-md">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Supplier Name</th>
                    <th class="px-4 py-2 text-left">Phone Number</th>
                    <th class="px-4 py-2 text-left">Product Type</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Edit Status</th>
                    <th class="px-4 py-2 text-left">View</th>
                </tr>
            </thead>
            <tbody>
                @foreach($suppliers as $supplier)
                    <tr id="supplier-row-{{ $supplier->id }}">
                        <td class="px-4 py-2">{{ $supplier->id }}</td>
                        <td class="px-4 py-2">{{ $supplier->supplier_name }}</td>
                        <td class="px-4 py-2">{{ $supplier->phone_number }}</td>
                        <td class="px-4 py-2">{{ $supplier->product_type }}</td>
                        <td class="px-4 py-2" id="supplier-status-{{ $supplier->id }}">
                            <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $supplier->status == 'Active' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                                {{ $supplier->status }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <!-- Edit Status Form -->
                            <form 
                                onsubmit="updateSupplierStatus(event, {{ $supplier->id }})"
                                class="inline-block">
                                @csrf
                                <select name="status" id="status-select-{{ $supplier->id }}" class="bg-gray-100 border border-gray-300 rounded-md p-2">
                                    <option value="Active" {{ $supplier->status == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ $supplier->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                <button type="submit" class="bg-yellow-500 text-white px-3 py-1 rounded-md">
                                    Update
                                </button>
                            </form>
                        </td>
                        <td class="px-4 py-2">
                            <button 
                                class="bg-blue-500 text-white px-3 py-1 rounded-md" 
                                onclick="openModalSupplier({{ $supplier->id }})">
                                View Details
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Modal for View Supplier Details -->
    <div id="supplier-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-96">
            <h3 class="text-xl font-semibold mb-4">Supplier Details</h3>
            <div id="supplier-modal-content">
                <!-- Dynamic content will be loaded here -->
            </div>
            <button 
                class="bg-red-500 text-white px-4 py-2 rounded-md mt-4" 
                onclick="closeModalSupplier()">Close</button>
        </div>
    </div>

<script>

    let currentSupplierId = null;

    // Function to open modal and show supplier details
    function openModalSupplier(supplierId) {
        currentSupplierId = supplierId;
        fetch(`/supplier/${supplierId}`)
            .then(response => response.json())
            .then(data => {
                const modalContent = `
                    <p><strong>Supplier Name:</strong> ${data.supplier_name}</p>
                    <p><strong>Contact Person:</strong> ${data.contact_person}</p>
                    <p><strong>Phone Number:</strong> ${data.phone_number}</p>
                    <p><strong>Email:</strong> ${data.email}</p>
                    <p><strong>Address:</strong> ${data.address}</p>
                    <p><strong>Product Type:</strong> ${data.product_type}</p>
                    <p><strong>Status:</strong> ${data.status}</p>
                    <p><strong>Created At:</strong> ${data.created_at}</p>
                    <p><strong>Updated At:</strong> ${data.updated_at}</p>
                `;
                document.getElementById('supplier-modal-content').innerHTML = modalContent;
                document.getElementById('supplier-modal').classList.remove('hidden');
            });
    }

    function closeModalSupplier() {
        document.getElementById('supplier-modal').classList.add('hidden');
    }




    // Function to update supplier status via AJAX
    function updateSupplierStatus(event, supplierId) {
        event.preventDefault(); // Prevent form submission and page reload

        const status = document.getElementById(`status-select-${supplierId}`).value;

        fetch(`/supplier/${supplierId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);

                // Update the status on the table without reloading the page
                const statusCell = document.getElementById(`supplier-status-${supplierId}`);
                statusCell.innerHTML = `
                    <span class="inline-block px-2 py-1 text-xs font-semibold rounded-full 
                        ${status === 'Active' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800'}">
                        ${status}
                    </span>
                `;
            } else {
                alert('Failed to update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the status.');
        });
    }
</script>
