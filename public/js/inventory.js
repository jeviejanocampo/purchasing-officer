function fetchInventory() {
    $.ajax({
        url: "/inventory/data", // Use a relative URL
        method: "GET",
        success: function(data) {
            updateInventoryTable(data);
        },
        error: function(xhr, status, error) {
            console.error("Error fetching inventory data:", error);
        }
    });
}

function updateInventoryTable(inventories) {
    const tbody = $('#inventory-table tbody');
    tbody.empty(); // Clear existing rows

    inventories.forEach(inventory => {
        // Check if stocks are low
        const isLowStock = inventory.stocks_per_set <= 10;
        const lowStockMessage = isLowStock ? 
            `<span class="absolute right-0 top-0 bg-red-800 text-white text-xs px-2 py-1 rounded-full">Stocks Low</span>` : '';

        tbody.append(
            `<tr>
                <td class="border px-1 py-1">${inventory.id}</td>
                <td class="border px-1 py-1 flex items-center">
                    ${inventory.budget_identifier}
                    <button class="ml-1 bg-blue-500 text-white rounded-md px-1 py-1 text-sm" onclick="fetchBudgetDetails(${inventory.budget_identifier})">View</button>
                </td>
                <td class="border px-1 py-1">${inventory.product_name}</td>
                <td class="border px-1 py-1">₱${parseFloat(inventory.unit_cost).toFixed(2)}</td>
                <td class="border px-1 py-1">${inventory.pieces_per_set}</td>
                <td class="border px-1 py-1 relative">
                    ${inventory.stocks_per_set} 
                    ${lowStockMessage} <!-- Display low stock message if applicable -->
                </td>
                <td class="border px-1 py-1">${inventory.created_at}</td>
                <td class="border px-1 py-1">${inventory.exp_date}</td>
                <td class="border px-1 py-1">
                    <!-- Edit quantity button -->
                    <button class="ml-1 bg-yellow-500 text-white rounded-md px-1 py-1 text-sm" onclick="openEditQuantityModal(${inventory.id}, ${inventory.stocks_per_set})">✎</button>
                </td>
                <td class="border px-1 py-1" id="set-status-${inventory.id}">
                    ${inventory.set_status}
                    <button class="ml-1 bg-yellow-500 text-white rounded-md px-1 py-1 text-sm" onclick="openSetStatusModalForInventory(${inventory.id}, '${inventory.set_status}')">Edit</button>
                </td>   
                <td class="border px-1 py-1">
                    ${inventory.remarks ? 
                        `<button class="bg-blue-500 text-white rounded-md px-2 py-1 text-sm hover:bg-blue-600 transition duration-200" onclick="openViewRemarksModal('${inventory.remarks}')">View</button>
                        <button class="ml-1 bg-yellow-500 text-white rounded-md px-1 py-1 text-sm" onclick="openEditRemarksModal('${inventory.budget_identifier}', '${inventory.remarks}')">✎</button>` :
                        `<button class="ml-1 bg-green-500 text-white rounded-md px-1 py-1 text-sm" onclick="openRemarksModal('${inventory.budget_identifier}')">Add Remarks</button>`
                    }
                </td>
                <td class="border px-1 py-1">
                    <a 
                        href="/inventory/${inventory.id}" 
                        class="bg-blue-500 text-white rounded-md px-2 py-1 text-sm hover:bg-blue-600 transition duration-200">
                        View
                    </a>
                </td>
            </tr>`
        );
    });
}

// Fetch inventory data every 3 seconds
setInterval(fetchInventory, 5000);

let selectedInventoryId = null;

function openEditQuantityModal(inventoryId, currentQuantity) {
    selectedInventoryId = inventoryId;

    // Create the modal HTML dynamically
    const modalHtml = `
    <div id="edit-quantity-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-600 bg-opacity-50">
        <div class="bg-white p-6 rounded-lg">
            <h3 class="text-xl font-bold mb-4">Edit Quantity</h3>
            <label for="current-quantity" class="block text-sm mb-2">Enter New Quantity</label>
            <input type="number" id="current-quantity" class="border p-2 w-full mb-4" min="0" value="${currentQuantity}">
            <div class="flex justify-end">
                <button class="bg-gray-500 text-white px-4 py-2 rounded-md mr-2" onclick="closeEditQuantityModal()">Cancel</button>
                <button class="bg-blue-500 text-white px-4 py-2 rounded-md" onclick="updateQuantity()">Update</button>
            </div>
        </div>
    </div>`;

    // Append the modal to the body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function closeEditQuantityModal() {
    document.getElementById('edit-quantity-modal').remove(); // Remove modal from the DOM
    selectedInventoryId = null;
}

async function updateQuantity() {
    const newQuantity = document.getElementById('current-quantity').value;
    if (!selectedInventoryId || !newQuantity) {
        alert("Please enter a valid quantity.");
        return;
    }

    // Confirmation dialog
    const proceed = confirm(`Are you sure you want to update the quantity to '${newQuantity}'?`);
    if (!proceed) {
        return; // User canceled, exit the function
    }

    try {
        // Retrieve the CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch(`/inventory/update/${selectedInventoryId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken  // Use CSRF token for security
            },
            body: JSON.stringify({ stocks_per_set: newQuantity })
        });

        const data = await response.json();

        if (data.success) {
            alert("Quantity updated successfully!");
            // Refresh inventory table
            fetchInventory();
            closeEditQuantityModal();
        } else {
            alert("Error updating quantity. Please try again.");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred. Please check the console for details.");
    }
}

// Set status modal integration
let selectedSetStatusInventoryId = null;

function openSetStatusModalForInventory(inventoryId, currentStatus) {
    selectedSetStatusInventoryId = inventoryId;

    // Set the current status as the selected value
    document.getElementById('status-select').value = currentStatus;

    // Show the status modal
    const modal = document.getElementById('set-status-modal-for-inventory');
    modal.classList.remove('hidden');
}


function closeSetStatusModal() {
    document.getElementById('set-status-modal').classList.add('hidden');
    selectedInventoryId = null;
}

function closeSetStatusModalForInventory() {
    document.getElementById('set-status-modal-for-inventory').classList.add('hidden');
    selectedInventoryId = null;
}

async function saveStatus() {
    const newStatus = document.getElementById('status-select').value;
    if (!selectedSetStatusInventoryId || !newStatus) {
        alert("Please select a valid status.");
        return;
    }

    // Confirmation dialog
    const proceed = confirm(`Are you sure you want to change the status to '${newStatus}'?`);
    if (!proceed) {
        return; // User canceled, exit the function
    }

    try {
        // Retrieve the CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch(`/inventory/${selectedSetStatusInventoryId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken  // Use CSRF token for security
            },
            body: JSON.stringify({ set_status: newStatus })
        });

        const data = await response.json();

        if (data.success) {
            alert("Status updated successfully!");
            // Refresh inventory table
            fetchInventory();
            closeSetStatusModalForInventory();
        } else {
            alert("Error updating status. Please try again.");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred. Please check the console for details.");
    }
}




