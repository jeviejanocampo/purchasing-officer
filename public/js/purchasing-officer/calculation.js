document.addEventListener('DOMContentLoaded', function () {
    const budgetSelector = document.getElementById('budget-selector');
    const inputBudgetDisplay = document.getElementById('input-budget-display');
    const remainingBalanceDisplay = document.getElementById('remaining-balance-display');
    const updatedRemainingBalanceDisplay = document.getElementById('updated-remaining-balance-display');
    const unitCostInput = document.getElementById('unit-cost');
    const piecesInput = document.getElementById('pieces-input');
    const stocksPerSetInput = document.getElementById('stocks-per-set');

    // Function to update budget displays
    function updateProductSelector() {
        // Check if a budget is selected
        if (budgetSelector.selectedIndex === 0) {
            inputBudgetDisplay.textContent = '₱0.00';
            remainingBalanceDisplay.textContent = '₱0.00';
            updatedRemainingBalanceDisplay.textContent = '₱0.00'; // Reset updated balance if no budget is selected
            stocksPerSetInput.value = '0'; // Reset stocks if no budget is selected
            return;
        }

        // Get the selected option
        const selectedOption = budgetSelector.options[budgetSelector.selectedIndex];

        // Update input budget and remaining balance displays
        const inputBudget = parseFloat(selectedOption.getAttribute('data-input-budget'));
        const remainingBalance = parseFloat(selectedOption.getAttribute('data-remaining-balance'));

        inputBudgetDisplay.textContent = '₱' + inputBudget.toFixed(2);
        remainingBalanceDisplay.textContent = '₱' + remainingBalance.toFixed(2);

        // Recalculate stocks when budget changes
        calculateStocks();
    }

    // Function to calculate stocks and update remaining balance based on user input
    function calculateStocks() {
        const unitCost = parseFloat(unitCostInput.value);
        const piecesPerSet = parseInt(piecesInput.value);

        // Check if a budget identifier is selected
        if (budgetSelector.selectedIndex === 0) {
            stocksPerSetInput.value = '0'; // Default to 0 if no budget is selected
            updatedRemainingBalanceDisplay.textContent = '₱0.00'; // Reset updated balance
            return; // Exit the function
        }

        const selectedOption = budgetSelector.options[budgetSelector.selectedIndex];
        const inputBudget = parseFloat(selectedOption.getAttribute('data-input-budget'));

        // Calculate stocks per set as a string
        if (!isNaN(unitCost) && !isNaN(piecesPerSet) && !isNaN(inputBudget) && piecesPerSet > 0) {
            const totalCostPerSet = unitCost * piecesPerSet; // Total cost for one set
            const stocksPerSet = Math.floor(inputBudget / totalCostPerSet); // Calculate how many sets can be bought
            stocksPerSetInput.value = stocksPerSet.toString(); // Set value as string
            
            // Calculate updated remaining balance
            const totalCostForStocks = totalCostPerSet * stocksPerSet; // Total cost for the stocks
            const updatedRemainingBalance = inputBudget - totalCostForStocks; // Updated remaining balance

            // Prevent displaying negative balance
            if (updatedRemainingBalance < 0) {
                updatedRemainingBalanceDisplay.textContent = '₱0.00'; // Reset to 0 if negative
            } else {
                updatedRemainingBalanceDisplay.textContent = '₱' + updatedRemainingBalance.toFixed(2); // Update remaining balance display
            }
        } else {
            stocksPerSetInput.value = '0'; // Default to 0 if input is invalid
            updatedRemainingBalanceDisplay.textContent = '₱' + inputBudget.toFixed(2); // Show original input budget as remaining
        }
    }

    // Add event listeners
    budgetSelector.addEventListener('change', updateProductSelector);
    unitCostInput.addEventListener('input', calculateStocks);
    piecesInput.addEventListener('input', calculateStocks);
});

