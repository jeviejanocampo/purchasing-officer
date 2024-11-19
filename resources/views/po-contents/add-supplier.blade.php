<div id="add-supplier-section" class="section bg-white rounded-lg shadow-md p-5">
    <h2 class="text-lg font-semibold mb-4">Add Supplier</h2>
    <form id="add-supplier-form" method="POST" action="{{ route('suppliers.store') }}">
        @csrf
        <div class="grid grid-cols-2 gap-4">
            <!-- Supplier Name -->
            <div>
                <label for="supplier_name" class="block font-medium">Supplier Name</label>
                <input type="text" name="supplier_name" id="supplier_name" class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Contact Person -->
            <div>
                <label for="contact_person" class="block font-medium">Contact Person</label>
                <input type="text" name="contact_person" id="contact_person" class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block font-medium">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number" class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block font-medium">Email (Optional)</label>
                <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2">
            </div>

            <!-- Product Type -->
            <div>
                <label for="product_type" class="block font-medium">Product Type</label>
                <input type="text" name="product_type" id="product_type" class="w-full border rounded px-3 py-2" required>
            </div>

            <!-- Status -->
            <div>
                <label for="status" class="block font-medium">Status</label>
                <select name="status" id="status" class="w-full border rounded px-3 py-2">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="button" id="confirm-button" class="bg-blue-500 text-white px-4 py-2 rounded">Add Supplier</button>
        </div>
    </form>
</div>

<script>
    document.getElementById('confirm-button').addEventListener('click', function (e) {
        e.preventDefault(); // Prevent form submission
        
        swal({
            title: "Confirm Supplier Addition",
            text: "Are you sure you want to add this supplier?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Add Supplier",
        }).then((isConfirm) => {
            if (isConfirm) {
                const form = document.getElementById('add-supplier-form');
                const formData = new FormData(form);

                // Perform AJAX request
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        swal("Success", data.message, "success");
                        form.reset(); // Reset the form
                    } else {
                        swal("Error", "Something went wrong. Please try again.", "error");
                    }
                })
                .catch(error => {
                    swal("Error", "Failed to add supplier. Please try again.", "error");
                    console.error('Error:', error);
                });
            }
        });
    });
</script>

