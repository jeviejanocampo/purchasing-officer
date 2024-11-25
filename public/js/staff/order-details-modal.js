// Global variables to store the order_id and checkout_id
let currentOrderId = null;
let currentCheckoutId = null;

// Function to open the modal and fetch order details
function viewOrderDetails(orderId, checkoutId, userId) {
    // Redirect to the order details page
    window.location.href = `/staff/order-details/${checkoutId}`;
}


// Function to close the modal
function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}
