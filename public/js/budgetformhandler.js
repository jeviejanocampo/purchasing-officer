// budgetFormHandler.js

function handleBudgetFormSubmission(formSelector, url) {
    $(formSelector).on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather form data
        const formData = $(this).serialize();

        // Send AJAX request
        $.ajax({
            url: url, // Your route for storing budget
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
                    $('#input-budget-display').text(`₱${response.data.input_budget}`);
                    $('#updated-remaining-balance-display').text(`₱${response.data.remaining_balance}`);
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
}
