let selectedBudgetIdentifier;

function openRemarksModal(budgetIdentifier) {
    selectedBudgetIdentifier = budgetIdentifier;
    document.getElementById('remarks-input').value = ''; // Clear input for adding remarks
    document.getElementById('remarks-modal').classList.remove('hidden');
}

function openEditRemarksModal(budgetIdentifier, existingRemarks) {
    selectedBudgetIdentifier = budgetIdentifier;
    document.getElementById('remarks-input').value = existingRemarks; // Set existing remarks
    document.getElementById('remarks-modal').classList.remove('hidden');
}

function closeRemarksModal() {
    document.getElementById('remarks-modal').classList.add('hidden');
    document.getElementById('remarks-input').value = ''; // Clear the input
}

function saveRemarks() {
    const remarks = document.getElementById('remarks-input').value;

    if (!remarks) {
        alert('Please enter remarks.');
        return;
    }

    // AJAX request to save the remarks in the database
    fetch(`/inventory/${selectedBudgetIdentifier}/remarks`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token
        },
        body: JSON.stringify({ remarks })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the table with the new remarks
            updateRemarksInTable(selectedBudgetIdentifier, remarks);
            closeRemarksModal();
            alert('Remarks saved successfully!'); // Notify user
        } else {
            alert('Error saving remarks.');
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateRemarksInTable(budgetIdentifier, remarks) {
    // Find the correct row in the inventory table and update the remarks cell
    const rows = document.querySelectorAll('#inventory-table tbody tr');
    rows.forEach(row => {
        const budgetIdCell = row.querySelector('td:first-child');
        if (budgetIdCell.textContent.trim() === budgetIdentifier) {
            const remarksCell = row.querySelector('td:last-child'); // Assuming remarks is the last cell
            remarksCell.querySelector('span').textContent = remarks; // Update remarks
        }
    });
}

function openViewRemarksModal(remarks) {
    document.getElementById('view-remarks-text').textContent = remarks; // Set the remarks in the modal
    document.getElementById('view-remarks-modal').classList.remove('hidden'); // Show the modal
}

function closeViewRemarksModal() {
    document.getElementById('view-remarks-modal').classList.add('hidden'); // Hide the modal
}
