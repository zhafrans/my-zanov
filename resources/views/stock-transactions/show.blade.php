@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Transaction Details</h2>
        <a href="{{ route('stock-transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
            Back to Transactions
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="flex items-start space-x-6">
                <div class="flex-shrink-0 h-16 w-16 rounded-full bg-primary-100 flex items-center justify-center">
                    <i class="fas fa-exchange-alt text-primary-700 text-xl"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">{{ $transaction->stockAmount->name }}</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->type === 'IN' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $transaction->type }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        Created: {{ $transaction->created_at->format('d M Y H:i') }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Last Updated: {{ $transaction->updated_at->format('d M Y H:i') }}
                    </p>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Quantity</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $transaction->quantity }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Adjustment</h4>
                    <p class="mt-1 text-sm text-gray-900">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->is_adjustment ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $transaction->is_adjustment ? 'Yes' : 'No' }}
                        </span>
                    </p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Warehouse</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $transaction->warehouse->name }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Destination Warehouse</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $transaction->toWarehouse->name ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Destination</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ $transaction->destination ?? '-' }}</p>
                </div>
            </div>

            <div class="mt-6">
                <h4 class="text-sm font-medium text-gray-500">Note</h4>
                <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $transaction->note ?? '-' }}</p>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-3 flex justify-end space-x-3">
            <button onclick="editTransaction(@json($transaction))" class="text-primary-600 hover:text-primary-900 px-4 py-2 rounded-md">
                <i class="fas fa-edit mr-2"></i>Edit
            </button>
            <button onclick="deleteTransaction(@json($transaction))" class="text-red-600 hover:text-red-900 px-4 py-2 rounded-md">
                <i class="fas fa-trash mr-2"></i>Delete
            </button>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div id="editTransactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-primary-800">Edit Transaction</h3>
            <button onclick="closeModal('editTransactionModal')" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="editTransactionForm" method="POST" action="">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Stock Amount -->
                <div class="col-span-2">
                    <label for="edit_stock_amount_id" class="block text-sm font-medium text-gray-700">Stock Item</label>
                    <select name="stock_amount_id" id="edit_stock_amount_id" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Stock Item --</option>
                        @foreach(Warehouse::with('stockAmounts')->get() as $warehouse)
                            @if($warehouse->stockAmounts->count())
                                <optgroup label="{{ $warehouse->name }}">
                                    @foreach($warehouse->stockAmounts as $stockAmount)
                                        <option value="{{ $stockAmount->id }}">
                                            {{ $stockAmount->name }} ({{ $warehouse->name }} - {{ $stockAmount->amount }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                </div>

                <!-- Type -->
                <div>
                    <label for="edit_type" class="block text-sm font-medium text-gray-700">Type</label>
                    <select name="type" id="edit_type" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Type --</option>
                        <option value="IN">IN</option>
                        <option value="OUT">OUT</option>
                    </select>
                </div>

                <!-- Quantity -->
                <div>
                    <label for="edit_quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="quantity" id="edit_quantity" min="1" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Warehouse -->
                <div>
                    <label for="edit_warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse</label>
                    <select name="warehouse_id" id="edit_warehouse_id" required
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Warehouse --</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- To Warehouse -->
                <div>
                    <label for="edit_to_warehouse_id" class="block text-sm font-medium text-gray-700">Destination Warehouse</label>
                    <select name="to_warehouse_id" id="edit_to_warehouse_id"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Destination --</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Destination -->
                <div>
                    <label for="edit_destination" class="block text-sm font-medium text-gray-700">Destination (if not warehouse)</label>
                    <input type="text" name="destination" id="edit_destination"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                </div>

                <!-- Is Adjustment -->
                <div>
                    <label for="edit_is_adjustment" class="block text-sm font-medium text-gray-700">Is Adjustment?</label>
                    <select name="is_adjustment" id="edit_is_adjustment"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <!-- Note -->
                <div class="col-span-2">
                    <label for="edit_note" class="block text-sm font-medium text-gray-700">Note</label>
                    <textarea name="note" id="edit_note" rows="3"
                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-5">
                <button type="button" onclick="closeModal('editTransactionModal')"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </button>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Update Transaction
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Transaction Modal -->
<div id="deleteTransactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-red-700">Delete Confirmation</h3>
            <button onclick="closeModal('deleteTransactionModal')" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <p class="text-gray-800">Are you sure you want to delete this transaction?</p>
        <form id="deleteTransactionForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end pt-5 space-x-3">
                <button type="button" onclick="closeModal('deleteTransactionModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-md">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                    Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function editTransaction(transaction) {
        // Set form action URL
        document.getElementById('editTransactionForm').action = `/stock-transactions/${transaction.id}`;
        
        // Fill form fields
        document.getElementById('edit_stock_amount_id').value = transaction.stock_amount_id;
        document.getElementById('edit_type').value = transaction.type;
        document.getElementById('edit_quantity').value = transaction.quantity;
        document.getElementById('edit_warehouse_id').value = transaction.warehouse_id;
        document.getElementById('edit_to_warehouse_id').value = transaction.to_warehouse_id;
        document.getElementById('edit_destination').value = transaction.destination;
        document.getElementById('edit_is_adjustment').value = transaction.is_adjustment ? '1' : '0';
        document.getElementById('edit_note').value = transaction.note;
        
        openModal('editTransactionModal');
    }

    function deleteTransaction(transaction) {
        document.getElementById('deleteTransactionForm').action = `/stock-transactions/${transaction.id}`;
        openModal('deleteTransactionModal');
    }

    // Modal functions
    function closeAllModals() {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }

    function openModal(modalId) {
        closeAllModals();
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection