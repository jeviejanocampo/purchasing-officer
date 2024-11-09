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
                <optgroup label="Inventory Overview and Budget Details">
                <option value="product-details">Product Details</option>
                <option value="inventory" >Inventory</option>  
                <option value="budget-allocation">Restocking Budget Allocation</option>
            </optgroup>
            <optgroup label="Restocking">
                <option value="add-budget">Add Budget for Restocking</option>
            </optgroup>
            <optgroup label="Restocking and Product Entry">
            <option value="add-product-details">Add Product Details for Restocking</option> 
                <option value="add-product">Add Product To The Inventory</option>
            </optgroup>
        </select>
    </div>

    @include('po-contents.product-details-section')

    @include('po-contents.add-budget-section')

    @include('po-contents.add-product-section')

    @include('po-contents.add-product-details-section')

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

    @include('po-contents.add-budget-allocation')

    @include('po-contents.inventory-section')

    @include('po-contents.modals-section    ')

    <script>
                    (function() {
            // Flag to ensure the polling happens only once
            let hasPolled = false; // Change from isPollingActive to hasPolled

            // Function to fetch the updated product list
            function fetchUpdatedProducts() {
                fetch('/products/update')
                    .then(response => response.json())
                    .then(data => {
                        const tableBody = document.getElementById('product-body');
                        tableBody.innerHTML = ''; // Clear existing rows

                        data.forEach(product => {
                            const row = document.createElement('tr');
                            row.setAttribute('data-details-status', product.product_details == 'TO BE DEFINED' || product.product_price == 0 || product.product_price == 0.00 ? 'undefined' : 'defined');

                            row.innerHTML = `
                                <td class="px-2 py-1 border-b">${product.product_id}</td>
                                <td class="px-2 py-1 border-b">${product.product_name}</td>
                                <td class="px-2 py-1 border-b">
                                    <!-- Correct image URL path based on product_image value -->
                                    <img src="/storage/product-images/${product.product_image}" alt="${product.product_name}" class="w-20 h-18 object-cover">
                                </td>
                                <td class="px-2 py-1 border-b">${product.created_at}</td>
                                <td class="border px-1 py-1">
                                    <span id="status-${product.product_id}">${product.product_status}</span>
                                    <button 
                                        class="ml-2 bg-yellow-500 text-white rounded-md px-1 py-1" 
                                        onclick="openSetStatusModal(${product.product_id}, '${product.product_status}')"
                                    >
                                        Edit
                                    </button>
                                </td>
                                <td class="px-2 py-1 border-b">
                                    ${product.product_details == 'TO BE DEFINED' || product.product_price == 0 || product.product_price == 0.00
                                        ? '<span class="text-white bg-red-500 px-3 py-1 rounded">Details undefined, need actions</span>'
                                        : ''}
                                    <a href="{{ route('product.details', '') }}/${product.product_id}" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 transition-colors duration-300 mt-2 heartbeat-animation">
                                        Edit
                                    </a>
                                </td>
                            `;

                            tableBody.appendChild(row);
                        });
                    })
                    .catch(error => console.error('Error fetching product data:', error));
            }

            // Function to trigger polling only once
            function startPollingOnce() {
                if (!hasPolled) {
                    hasPolled = true; // Set flag to prevent further polling
                    fetchUpdatedProducts(); // Fetch updated product data once
                }
            }

            // Use IntersectionObserver to detect if the table section is in view
            const productTableSection = document.getElementById('product-details-section');

            const observer = new IntersectionObserver(entries => {
                // If the table section is in view and has not polled yet
                if (entries[0].isIntersecting && !hasPolled) {
                    startPollingOnce();
                }
            }, { threshold: 0.5 }); // Start polling when 50% of the section is in view

            // Start observing the product table section
            observer.observe(productTableSection);

            // Initial fetch when the page loads
            fetchUpdatedProducts();
        })();
    </script>
    <script src="{{ asset('js/add-product-details-alert.js') }}"></script>
    <script src="{{ asset('js/product-details-alert.js') }}"></script>
    

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
                        alert('Remarks saved successfully!'); // Notify user
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
