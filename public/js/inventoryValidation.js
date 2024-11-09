$(document).ready(function() {
    // Handle form submission for adding a product
    $('#add-product-form').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather form data
        const formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            url: "/product/store", // Directly using the POST route for storing the product
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    swal({
                        title: "Success",
                        text: response.message,
                        type: "success"
                    });

                    // Update placeholders or perform any additional actions
                    $('#input-budget').text(`₱${response.data.input_budget}`); // Assuming response has input_budget
                    $('#updated-remaining-balance-display').text(`₱${response.data.remaining_balance}`); // Assuming response has remaining_balance

                    // Optionally reset the form
                    $('#add-product-form')[0].reset();
                } else {
                    swal({
                        title: "Error",
                        text: response.message,
                        type: "error"
                    });
                }
            },
            error: function(xhr) {
                // Show error message
                swal({
                    title: "Error",
                    text: xhr.responseJSON ? xhr.responseJSON.message : "An error occurred. Please try again.",
                    type: "error"
                });
            }
        });
    });
});
