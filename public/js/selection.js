   // Function to toggle between different sections based on selection
   function toggleSections() {
    const selectedValue = document.getElementById('section-selector').value;
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('hidden');  // Hide all sections
    });
    document.getElementById(`${selectedValue}-section`).classList.remove('hidden');  // Show the selected section
}

// Function to update the budget display based on the selected budget
function updateBudgetInput() {
    const budgetSelector = document.getElementById('budget-selector');
    const selectedOption = budgetSelector.options[budgetSelector.selectedIndex];
    const inputBudget = selectedOption.getAttribute('data-input-budget');
    document.getElementById('input-budget').innerText = `₱${inputBudget}`;  // Update the budget display
}

// Function to calculate total stocks based on unit cost and pieces per set
function calculateStocks() {
    const unitCost = parseFloat(document.getElementById('unit-cost').value) || 0;
    const piecesPerSet = parseInt(document.getElementById('pieces-per-set').value) || 0;
    const totalCost = unitCost * piecesPerSet;

    // Display the total cost
    document.getElementById('total-cost-display').innerText = `₱${totalCost.toFixed(2)}`;
}

// Function to open modal for budget
function openModal(budget) {
    // Implement modal opening logic here
    console.log('Opening modal for budget:', budget);
}

// Ensure the correct section is displayed on initial load
window.onload = function() {
    toggleSections();
};