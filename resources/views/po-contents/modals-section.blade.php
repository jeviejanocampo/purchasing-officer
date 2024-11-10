<div id="set-status-modal-for-inventory" class="hidden fixed inset-0 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-md p-5">
                <h2 class="text-lg font-bold mb-4">Edit Set Status</h2>
                <select id="status-select" class="block w-full border border-gray-300 rounded-md p-2 mb-4">
                    <option value="In Stock">In Stock</option>
                    <option value="On Order">On Order</option>
                    <option value="Discontinued">Discontinued</option>
                    <option value="Expired">Expired</option>
                    <option value="Damaged">Damaged</option>
                </select>
                <div class="flex justify-end">
                    <button id="save-status-button" class="bg-blue-500 text-white rounded-md px-4 py-2" onclick="saveStatus()">Save</button>
                    <button class="ml-2 bg-red-500 text-white rounded-md px-4 py-2" onclick="closeSetStatusModalForInventory()">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <div id="remarks-modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-md p-5 w-1/3">
            <h2 class="text-lg font-bold mb-4">Add/Edit Remarks</h2>
            <textarea 
                id="remarks-input" 
                rows="4" 
                class="block w-full border border-gray-300 rounded-md p-2 shadow-sm focus:ring focus:ring-indigo-200" 
                placeholder="Enter your remarks here..."
            ></textarea>
            <div class="flex justify-end mt-4">
                <button 
                    class="bg-blue-500 text-white rounded-md px-4 py-2" 
                    onclick="saveRemarks()"
                >
                    Save
                </button>
                <button 
                    class="ml-2 bg-red-500 text-white rounded-md px-4 py-2" 
                    onclick="closeRemarksModal()"
                >
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <div id="view-remarks-modal" class="fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-md p-5 w-1/3">
            <h2 class="text-lg font-bold mb-4">View Remarks</h2>
            <p id="view-remarks-text" class="text-gray-800"></p>
            <div class="flex justify-end mt-4">
                <button 
                    class="bg-red-500 text-white rounded-md px-4 py-2" 
                    onclick="closeViewRemarksModal()"
                >
                    Close
                </button>
            </div>
        </div>
    </div>


    <div id="budget-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-md p-5 w-11/12 md:w-1/3 relative">
            <div id="modal-content"></div>
            <a href="#" class="absolute top-3 right-3 text-xl text-gray-500 hover:text-gray-800">&times;</a>
        </div>
    </div>

     <!-- Modal for setting status -->
    <div id="set-status-modal" class="fixed inset-0 bg-gray-900 bg-opacity-10 flex justify-center items-start pt-20 hidden">
        <div class="bg-white p-5 rounded-lg shadow-md w-1/3">
            <h2 class="text-xl font-bold mb-4">Update Product Status</h2>
            <form action="{{ route('product.updateStatus', '') }}" method="POST" id="status-form">
                @csrf
                @method('PUT')
                <!-- Hidden input to store product_id -->
                <input type="hidden" name="product_id" id="modal-product-id">
                
                <div class="mb-4">
                    <label for="product_status" class="block text-sm font-medium text-gray-700">Product Status</label>
                    <select name="product_status" id="modal-product-status" class="w-full p-2 border border-gray-300 rounded-md">
                        <option value="In Stock">In Stock</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Expired">Expired</option>
                        <option value="Ordered">Ordered</option>
                        <option value="PENDING">PENDING</option>
                    </select>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">Save</button>
                    <button type="button" onclick="closeSetStatusModal()" class="ml-2 bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">Exit</button>
                </div>
            </form>
        </div>
    </div>
