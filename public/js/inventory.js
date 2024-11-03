// public/js/inventory.js
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
        tbody.append(`
            <tr>
                <td class="border px-2 py-2 flex items-center">
                    ${inventory.budget_identifier}
                    <button class="ml-2 bg-blue-500 text-white rounded-md px-2 py-1" onclick="fetchBudgetDetails(${inventory.budget_identifier})">View</button>
                </td>
                <td class="border px-2 py-2">${inventory.product_name}</td>
                <td class="border px-2 py-2">₱${parseFloat(inventory.unit_cost).toFixed(2)}</td>
                <td class="border px-2 py-2">${inventory.pieces_per_set}</td>
                <td class="border px-2 py-2">${inventory.stocks_per_set}</td>
                <td class="border px-2 py-2">${inventory.created_at}</td>
                <td class="border px-2 py-2">${inventory.updated_at}</td>
                <td class="border px-2 py-2">${inventory.exp_date}</td>
                <td class="border px-2 py-2">
                    ${inventory.remarks ? `
                        <button class="bg-blue-500 text-white rounded-md px-3 py-1 hover:bg-blue-600 transition duration-200" onclick="openViewRemarksModal('${inventory.remarks}')">View</button>
                        <button class="ml-2 bg-yellow-500 text-white rounded-md px-2 py-1" onclick="openEditRemarksModal('${inventory.budget_identifier}', '${inventory.remarks}')">Edit</button>
                    ` : `
                        <button class="ml-2 bg-green-500 text-white rounded-md px-2 py-1" onclick="openRemarksModal('${inventory.budget_identifier}')">Add Remarks</button>
                    `}
                </td>
            </tr>
        `);
    });
}

// Fetch inventory data every 5 seconds
setInterval(fetchInventory, 5000);
