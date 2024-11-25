<div id="notificationModal" class="modal fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
    <div class="modal-content bg-white p-6 rounded-lg w-11/12 max-w-lg max-h-[70vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">Notifications</h2>
            <button onclick="closeNotificationModal()" class="text-red-500">Close</button>
        </div>
        <ul id="notification-list" class="space-y-2">
            <!-- Notifications will be loaded here dynamically -->
        </ul>
    </div>
</div>

<script>
    function fetchLowStockNotifications() {
        fetch('{{ route("lowStockNotifications") }}')
            .then(response => response.json())
            .then(notifications => {
                const notificationList = document.getElementById('notification-list');
                notificationList.innerHTML = '';  // Clear existing notifications

                notifications.forEach(notification => {
                    const notificationItem = document.createElement('li');
                    notificationItem.className = 'bg-gray-100 p-3 rounded-lg shadow-sm';
                    notificationItem.innerHTML = `
                        <p><strong>${notification.type}:</strong> ${notification.message}</p>
                        <span class="text-xs text-gray-500">${notification.time}</span>
                    `;
                    notificationList.appendChild(notificationItem);
                });
            })
            .catch(error => console.error('Error fetching low stock notifications:', error));
    }

    // Call fetchLowStockNotifications when the page loads or when new stock notifications are needed
    document.addEventListener('DOMContentLoaded', fetchLowStockNotifications);
</script>
