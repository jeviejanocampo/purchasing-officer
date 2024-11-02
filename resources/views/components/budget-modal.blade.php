<div id="modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-5 w-1/3 mx-auto">
        <h2 class="text-xl font-bold mb-4">Budget Details</h2>
        <p id="modal-reference-code" class="mb-2"></p>
        <p id="modal-product-to-buy" class="mb-2"></p> <!-- New paragraph for product_to_buy -->
        <p id="modal-input-budget" class="mb-2"></p>
        <p id="modal-remaining-balance" class="mb-2"></p>
        <p id="modal-created-at" class="mb-2"></p>
        <p id="modal-updated-at" class="mb-2"></p>
        <button class="mt-4 bg-red-500 text-white rounded-md p-2" onclick="closeModal()">Close</button>
    </div>
</div>

<script>
    function openModal(budget) {
        document.getElementById('modal-reference-code').innerText = 'Reference Code: ' + budget.reference_code;
        document.getElementById('modal-input-budget').innerText = 'Input Budget: ₱' + parseFloat(budget.input_budget).toFixed(2);
        document.getElementById('modal-remaining-balance').innerText = 'Remaining Balance: ₱' + parseFloat(budget.remaining_balance).toFixed(2);
        document.getElementById('modal-created-at').innerText = 'Created At: ' + budget.created_at;
        document.getElementById('modal-updated-at').innerText = 'Updated At: ' + budget.updated_at;
        document.getElementById('modal-product-to-buy').innerText = 'Product to Buy: ' + budget.product_to_buy; // Update for product_to_buy
        
        document.getElementById('modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
</script>
