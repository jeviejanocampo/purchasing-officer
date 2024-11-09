$(document).ready(function() {
$('#add-product-details-section form').on('submit', function(event) {
event.preventDefault(); // Prevent form from submitting immediately

const formData = new FormData(this);
let details = 'Please confirm your details:\n\n';

// Loop through form fields and build details string
formData.forEach(function(value, key) {
    details += `${key}: ${value}\n`;
});

swal({
    title: "Confirm Product Details",
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
        // Proceed with form submission via AJAX
        $.ajax({
            url: $(this).attr('action'),  // URL from the form's action attribute
            type: "POST",  // Assuming it's a POST request
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    swal({
                        title: "Success",
                        text: response.message,
                        icon: "success"
                    });

                    // Optionally, reset the form or update the UI here
                    $('#add-product-details-section form')[0].reset();
                } else {
                    // Show error message if the response indicates failure
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
        // Optional feedback if the user cancels the action
        swal("Action canceled", "You can review your details and try again.", "info");
    }
});
});
});
