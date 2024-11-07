// public/js/notifications.js
document.addEventListener('DOMContentLoaded', function() {
    // Check for success message
    if (window.successMessage) {
        alert(window.successMessage);
    }

    // Check for error messages
    if (window.errorMessage) {
        alert(window.errorMessage);
    }
});
