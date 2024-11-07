function openModal(id, productName, inputBudget, remainingBalance, createdAt) {
    document.getElementById('modal-product-name').innerText = `Product: ${productName}`;
    document.getElementById('modal-input-budget').innerText = `Input Budget: ${inputBudget}`;
    document.getElementById('modal-remaining-balance').innerText = `Remaining Balance: ${remainingBalance}`;
    document.getElementById('modal-created-at').innerText = `Created At: ${createdAt}`;
    document.getElementById('modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modal').classList.add('hidden');
}

function updateProductSelector() {
    const budgetSelector = document.getElementById('budget-selector');
    const productSelector = document.getElementById('product-selector');

    // Clear existing options in the product selector
    productSelector.innerHTML = '<option value="" disabled selected>Select a product</option>';

    // Get the selected budget identifier's product
    const selectedOption = budgetSelector.options[budgetSelector.selectedIndex];

    if (selectedOption) {
        const productToBuy = selectedOption.getAttribute('data-product');
        // Create a new option for the product selector
        if (productToBuy) {
            const option = document.createElement('option');
            option.value = productToBuy; // Set the value to product name
            option.textContent = productToBuy; // Display the product name
            productSelector.appendChild(option);
        }
    }
}

