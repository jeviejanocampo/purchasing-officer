function openSetStatusModal(productId, currentStatus, productDescription, productStocks) {
    // Set the hidden input for product_id
    document.getElementById('modal-product-id').value = productId;

    // Set the current status in the select dropdown
    document.getElementById('modal-product-status').value = currentStatus;

    // Disable 'In Stock' option if either product_description or product_stocks is 'TO BE DEFINED'
    const statusSelect = document.getElementById('modal-product-status');
    const inStockOption = statusSelect.querySelector('option[value="In Stock"]');

    // Check if either product_description or product_stocks is 'TO BE DEFINED'
    if (productDescription === 'TO BE DEFINED' || productStocks === 'TO BE DEFINED') {
        inStockOption.disabled = true;  // Disable the 'In Stock' option
    } else {
        inStockOption.disabled = false;  // Enable the 'In Stock' option
    }

    // Dynamically set the form action with the product_id
    document.getElementById('status-form').action = '/product/update-status/' + productId;

    // Show the modal
    document.getElementById('set-status-modal').classList.remove('hidden');
}

$(document).ready(function() {
    $('#status-form').on('submit', function(event) {
        event.preventDefault();

        const productId = $('#modal-product-id').val();
        const productDescription = $('#product-description-' + productId).text(); // Get the product description
        const productStocks = $('#product-stocks-' + productId).text(); // Get the product stocks

        // Prevent updating to 'In Stock' if either product_description or product_stocks is 'TO BE DEFINED'
        const selectedStatus = $('#modal-product-status').val();
        if (selectedStatus === 'In Stock' && (productDescription === 'TO BE DEFINED' || productStocks === 'TO BE DEFINED')) {
            swal("Cannot Update", "Cannot update status to 'In Stock' because the product details are undefined.", "error");
            return; // Prevent the form from submitting
        }

        const formData = $(this).serializeArray();
        let details = 'Please confirm your details:\n\n';

        formData.forEach(function(field) {
            details += `${field.name}: ${field.value}\n`;
        });

        swal({
            title: "Confirm Status Change",
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
                // Submit the form using AJAX
                $.ajax({
                    url: $(this).attr('action'),  // Use the dynamically set URL
                    type: "PUT",
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            swal({
                                title: "Success",
                                text: response.message,
                                icon: "success"
                            });

                            // Update the status on the page
                            $('#status-' + response.product_id).text(response.product_status);
                        } else {
                            // Show error message
                            swal({
                                title: "Error",
                                text: response.message,
                                icon: "error"
                            });
                        }
                    },
                    error: function(xhr) {
                        // Show error message if AJAX fails
                        swal({
                            title: "Error",
                            text: xhr.responseJSON ? xhr.responseJSON.message : "An error occurred. Please try again.",
                            icon: "error"
                        });
                    }
                });
            } else {
                // Optional feedback if canceled
                swal("Status change canceled", "You can edit the status and try again.", "info");
            }
        });
    });
});
