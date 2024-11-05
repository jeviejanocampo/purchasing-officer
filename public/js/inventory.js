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
                    ${lowStockMessage}  <!-- Display low stock message if applicable -->
                </td>
                <td class="border px-1 py-1">${inventory.created_at}</td>
                <td class="border px-1 py-1">${inventory.exp_date}</td>
                <td class="border px-1 py-1" id="set-status-${inventory.id}">
                    ${inventory.set_status}
                    <button class="ml-1 bg-yellow-500 text-white rounded-md px-1 py-1 text-sm" onclick="openSetStatusModal(${inventory.id}, '${inventory.set_status}')">Edit</button>
                </td>   
                <td class="border px-1 py-1">
                    ${inventory.remarks ? 
                        `<button class="bg-blue-500 text-white rounded-md px-2 py-1 text-sm hover:bg-blue-600 transition duration-200" onclick="openViewRemarksModal('${inventory.remarks}')">View</button>
                        <button class="ml-1 bg-yellow-500 text-white rounded-md px-1 py-1 text-sm" onclick="openEditRemarksModal('${inventory.budget_identifier}', '${inventory.remarks}')">Edit</button>` :
                        `<button class="ml-1 bg-green-500 text-white rounded-md px-1 py-1 text-sm" onclick="openRemarksModal('${inventory.budget_identifier}')">Add Remarks</button>`
                    }
                </td>
                <td class="border px-1 py-1">
                    <a 
                        href="/inventory/${inventory.id}" 
                        class="bg-blue-500 text-white rounded-md px-2 py-1 text-sm hover:bg-blue-600 transition duration-200">
                        View Details
                    </a>
                </td>
            </tr>`
        );
        
        // Call checkLowStock function for each inventory item
        // checkLowStock(inventory.stocks_per_set, inventory.product_name);
    });
}


// Fetch inventory data every 5 seconds
setInterval(fetchInventory, 5000);

let selectedInventoryId = null;

function openSetStatusModal(inventoryId, currentStatus) {
    selectedInventoryId = inventoryId;
    document.getElementById('status-select').value = currentStatus;
    document.getElementById('set-status-modal').classList.remove('hidden');
}

function closeSetStatusModal() {
    document.getElementById('set-status-modal').classList.add('hidden');
    selectedInventoryId = null;
}

async function saveStatus() {
    const newStatus = document.getElementById('status-select').value;
    if (!selectedInventoryId) {
        alert("No inventory item selected for updating status.");
        return;
    }

    // Confirmation dialog
    const proceed = confirm("Are you sure you want to change the status to '" + newStatus + "'?");
    if (!proceed) {
        return; // User canceled, exit the function
    }

    try {
        // Retrieve the CSRF token from the meta tag
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const response = await fetch(`/inventory/${selectedInventoryId}/update-status`, {
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
            document.getElementById(`set-status-${selectedInventoryId}`).innerHTML = `
                ${newStatus}
                <button class="ml-1 bg-yellow-500 text-white rounded-md px-1 py-1 text-sm" onclick="openSetStatusModal(${selectedInventoryId}, '${newStatus}')">Edit</button>
            `;
            closeSetStatusModal();
        } else {
            alert("Error updating status. Please try again.");
        }
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred. Please check the console for details.");
    }
}
