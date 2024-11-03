$(document).ready(function() {
    $('#budget-form').on('submit', function(event) {
        event.preventDefault(); 

        const formData = $(this).serializeArray();
        let details = 'Please confirm your details:\n\n';

        formData.forEach(function(field) {
            details += `${field.name}: ${field.value}\n`;
        });

        swal({
            title: "Confirm Your Details",
            text: details,
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Cancel",
                    value: null,
                    visible: true,
                    className: "bg-red-500 text-white",
                    closeModal: true,
                },
                confirm: {
                    text: "Confirm",
                    value: true,
                    visible: true,
                    className: "bg-green-500 text-white",
                    closeModal: true,
                }
            },
            dangerMode: true,
        }).then((willProceed) => {
            if (willProceed) {
                $.ajax({
                    url: budgetStoreRoute,
                    type: "POST",
                    data: $(this).serialize(), 
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            swal({
                                title: "Success",
                                text: response.message,
                                icon: "success"
                            });

                            // Update placeholders or perform any additional actions
                            $('#input-budget-display').text(`₱${response.data.input_budget}`);
                            $('#updated-remaining-balance-display').text(`₱${response.data.remaining_balance}`);
                        } else {
                            swal({
                                title: "Error",
                                text: response.message,
                                icon: "error"
                            });
                        }
                    },
                    error: function(xhr) {
                        // Show error message
                        swal({
                            title: "Error",
                            text: xhr.responseJSON ? xhr.responseJSON.message : "An error occurred. Please try again.",
                            icon: "error"
                        });
                    }
                });
            } else {
                // Optional feedback if canceled
                swal("Submission canceled", "You can edit your details and try again.", "info");
            }
        });
    });
});


function fetchBudgetDetails(budgetId) {
    fetch(`/budgets/${budgetId}`) // Ensure this matches your route
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate modal with budget details
                document.getElementById('modal-content').innerHTML = `
                    <h2 class="text-lg font-bold">Budget Details</h2>
                    <p><strong>ID:</strong> ${data.data.id}</p>
                    <p><strong>Reference Code:</strong> ${data.data.reference_code}</p>
                    <p><strong>Product to Buy:</strong> ${data.data.product_to_buy}</p>
                    <p><strong>Input Budget:</strong> ₱${data.data.input_budget}</p>
                    <p><strong>Remaining Balance:</strong> ₱${data.data.remaining_balance}</p>
                    <p><strong>Balance:</strong> ₱${data.data.balance}</p>
                    <p><strong>Created At:</strong> ${data.data.created_at}</p>
                    <p><strong>Updated At:</strong> ${data.data.updated_at}</p>
                `;
                // To show the modal, update the href to the target ID
                window.location.hash = 'budget-modal'; // Open the modal by changing the URL hash
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching budget details:', error);
        });
}


function updateBudgetInput() {
    const budgetSelector = document.getElementById('budget-selector');
    const productSelector = document.getElementById('product-selector');

    // Clear previous product options
    productSelector.innerHTML = '<option value="" disabled selected>Select a product</option>';

    // Get the selected budget
    const selectedOption = budgetSelector.options[budgetSelector.selectedIndex];

    // Get the product from the selected budget
    const product = selectedOption.getAttribute('data-product');

    // If a product exists for the selected budget, create a new option
    if (product) {
        const newOption = document.createElement('option');
        newOption.value = product;
        newOption.textContent = product;
        productSelector.appendChild(newOption);
    }
}



