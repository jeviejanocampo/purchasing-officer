$(document).ready(function() {
    // Handle form submission for adding a budget
    $('#budget-form').on('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Gather form data
        const formData = $(this).serializeArray();
        let details = 'Please confirm your details:\n\n';
        
        // Append form data to details string
        formData.forEach(function(field) {
            details += `${field.name}: ${field.value}\n`;
        });

        // Show confirmation dialog
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
                // Proceed with AJAX request if confirmed
                $.ajax({
                    url: "{{ route('budget.store') }}", // Using the route for storing budget
                    type: "POST",
                    data: $(this).serialize(), // Use the original form data
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
