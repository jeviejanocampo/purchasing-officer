function updateBudgetInput() {
    const budgetSelector = document.getElementById('budget-selector');
    const selectedOption = budgetSelector.options[budgetSelector.selectedIndex];
    const budgetInput = selectedOption.getAttribute('data-input-budget');
    
    // Update the displayed budget input
    document.getElementById('input-budget').innerText = budgetInput ? '₱' + budgetInput : '₱0.00';
    
    // Update the remaining balance display
    const totalBudget = parseFloat(budgetInput.replace(/,/g, '').replace('₱', ''));
    document.getElementById('updated-remaining-balance-display').innerText = '₱' + totalBudget.toFixed(2);
}

function calculateStocks() {
    const unitCost = parseFloat(document.getElementById('unit-cost').value) || 0;
    const piecesPerSet = parseFloat(document.getElementById('pieces-per-set').value) || 0;
    const inputBudget = parseFloat(document.getElementById('input-budget').innerText.replace(/₱/g, '').replace(/,/g, '')) || 0;

    // Calculate total cost for one set
    const totalCost = unitCost * piecesPerSet;

    // Calculate how many sets can be bought with the input budget
    const totalSets = Math.floor(inputBudget / totalCost);
    
    // Update the stocks per set input with the calculated value
    document.getElementById('stocks-per-set').value = totalSets;

    // Optionally update remaining balance display
    const remainingBalance = inputBudget - (totalSets * totalCost);
    document.getElementById('updated-remaining-balance-display').innerText = '₱' + remainingBalance.toFixed(2);
}

function filterInventory() {
    const searchInput = document.getElementById('budget-search').value.toLowerCase();
    const startDate = document.getElementById('start-date-filter').value;
    const endDate = document.getElementById('end-date-filter').value;
    const table = document.getElementById('inventory-table');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let row of rows) {
        const budgetIdentifier = row.cells[0].innerText.toLowerCase();
        const createdAt = row.cells[5].innerText; // Adjust according to the correct date format

        // Convert createdAt to a Date object for comparison
        const createdAtDate = new Date(createdAt);

        // Check if createdAtDate is within the specified range
        const isWithinDateRange = (!startDate || createdAtDate >= new Date(startDate)) &&
                                  (!endDate || createdAtDate <= new Date(endDate));

        const matchesSearch = budgetIdentifier.includes(searchInput);
        const matchesDate = isWithinDateRange;

        row.style.display = matchesSearch && matchesDate ? '' : 'none';
    }
}

function formatBudget(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // Add comma for every three digits
    if (value.length >= 6) {
        value = Number(value).toLocaleString(); // Converts the number to a string with commas
    }
    
    input.value = value; // Update the input's value
}

function formatBudget(input) {
    // Remove all non-digit characters
    let value = input.value.replace(/\D/g, '');
    
    // Add comma for every three digits
    if (value.length >= 6) {
        value = Number(value).toLocaleString(); // Converts the number to a string with commas
    }
    
    input.value = value; // Update the input's value
}



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


function searchBudgets() {
    const input = document.getElementById('searchBudget');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('budgetTableBody');
    const tr = table.getElementsByTagName('tr');

    for (let i = 0; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td')[0]; // Get the first cell (Budget ID)
        if (td) {
            const txtValue = td.textContent || td.innerText;
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}

function filterInventory() {
    // Get inventory items from your data source, then reverse the order
    const inventoryItems = [...document.querySelectorAll('#inventory-section tbody tr')];
    inventoryItems.reverse().forEach(item => {
        item.parentNode.appendChild(item); // Reattach in reversed order
    });
    // Additional filtering logic here...
}