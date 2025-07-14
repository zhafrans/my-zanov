@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Transaction Management</h2>
        <button onclick="openModal('createTransactionModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Transaction
        </button>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('transactions.index') }}" class="mb-4 bg-white p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Search Field -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Invoice</label>
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        id="search"
                        placeholder="Search by invoice..."
                        value="{{ request('search') }}"
                        class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Customer Filter -->
            <div>
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                <select 
                    name="customer_id" 
                    id="customer_id"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Product Variant Filter -->
            <div>
                <label for="product_variant_id" class="block text-sm font-medium text-gray-700 mb-1">Product Variant</label>
                <select 
                    name="product_variant_id" 
                    id="product_variant_id"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Variants</option>
                    @foreach($productVariants as $variant)
                        <option value="{{ $variant->id }}" {{ request('product_variant_id') == $variant->id ? 'selected' : '' }}>
                            {{ $variant->code }} ({{ $variant->product->name ?? '' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Seller Filter -->
            <div>
                <label for="seller_id" class="block text-sm font-medium text-gray-700 mb-1">Seller</label>
                <select 
                    name="seller_id" 
                    id="seller_id"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Sellers</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                            {{ $seller->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Payment Type Filter -->
            <div>
                <label for="payment_type" class="block text-sm font-medium text-gray-700 mb-1">Payment Type</label>
                <select 
                    name="payment_type" 
                    id="payment_type"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Types</option>
                    <option value="credit" {{ request('payment_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                    <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select 
                    name="status" 
                    id="status"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Statuses</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="installment" {{ request('status') == 'installment' ? 'selected' : '' }}>Installment</option>
                </select>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-2 mt-4">
            <button 
                type="submit" 
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition"
            >
                <i class="fas fa-filter mr-2"></i>Apply Filters
            </button>
            
            @if(request()->hasAny(['search', 'customer_id', 'product_id', 'seller_id', 'payment_type', 'status']))
                <a 
                    href="{{ route('transactions.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition"
                >
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </a>
            @endif
        </div>
    </form>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $transaction->invoice }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->customer->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->productVariant->code }} - {{ $transaction->productVariant->product->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->seller->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->payment_type === 'credit' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                            {{ ucfirst($transaction->payment_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('transactions.show', $transaction->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick='editTransaction(@json($transaction))' class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick='deleteTransaction(@json($transaction))' class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $transactions->links() }}
    </div>

    <!-- Create Transaction Modal -->
    <div id="createTransactionModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Create New Transaction</h3>
                <button onclick="closeModal('createTransactionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createTransactionForm" method="POST" action="{{ route('transactions.store') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Invoice -->
                    <div class="col-span-1">
                        <label for="invoice" class="block text-sm font-medium text-gray-700">Invoice Number</label>
                        <input type="text" name="invoice" id="invoice" value="{{ old('invoice') }}"
                            class="mt-1 block w-full border {{ $errors->has('invoice') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('invoice')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Customer -->
                    <div class="col-span-1">
                        <label for="customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" id="customer_id"
                            class="mt-1 block w-full border {{ $errors->has('customer_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Product Variant -->
                    <div class="col-span-1">
                        <label for="product_variant_id" class="block text-sm font-medium text-gray-700">Product Variant</label>
                        <select name="product_variant_id" id="product_variant_id"
                            class="mt-1 block w-full border {{ $errors->has('product_variant_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Product Variant --</option>
                            @foreach($productVariants as $variant)
                                <option value="{{ $variant->id }}" {{ old('product_variant_id') == $variant->id ? 'selected' : '' }}>
                                    {{ $variant->code }} ({{ $variant->product->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_variant_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seller -->
                    <div class="col-span-1">
                        <label for="seller_id" class="block text-sm font-medium text-gray-700">Seller</label>
                        <select name="seller_id" id="seller_id"
                            class="mt-1 block w-full border {{ $errors->has('seller_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Seller --</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('seller_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Type -->
                    <div class="col-span-1">
                        <label for="payment_type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                        <select name="payment_type" id="payment_type"
                            class="mt-1 block w-full border {{ $errors->has('payment_type') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Payment Type --</option>
                            <option value="credit" {{ old('payment_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                            <option value="cash" {{ old('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                        </select>
                        @error('payment_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="col-span-1">
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status"
                            class="mt-1 block w-full border {{ $errors->has('status') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Status --</option>
                            <option value="paid" {{ old('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="installment" {{ old('status') == 'installment' ? 'selected' : '' }}>Installment</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Installment Amount (conditional) -->
                    <div id="installmentAmountField" class="col-span-1 hidden">
                        <label for="installment_amount" class="block text-sm font-medium text-gray-700">Installment Amount</label>
                        <input type="number" name="installment_amount" id="installment_amount" value="{{ old('installment_amount') }}"
                            class="mt-1 block w-full border {{ $errors->has('installment_amount') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('installment_amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Transaction Items -->
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Items</label>
                        <div id="transactionItemsContainer" class="mt-2 space-y-2">
                            <!-- Items will be added here dynamically -->
                        </div>
                        <button type="button" onclick="addTransactionItem()" class="mt-2 inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('createTransactionModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Transaction
                    </button>
                </div>
            </form>
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
                    <!-- Invoice -->
                    <div class="col-span-1">
                        <label for="edit_invoice" class="block text-sm font-medium text-gray-700">Invoice Number</label>
                        <input type="text" name="invoice" id="edit_invoice"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Customer -->
                    <div class="col-span-1">
                        <label for="edit_customer_id" class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer_id" id="edit_customer_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Customer --</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product -->
                    <div class="col-span-1">
                        <label for="edit_product_id" class="block text-sm font-medium text-gray-700">Product</label>
                        <select name="product_id" id="edit_product_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Product --</option>
                            @foreach($productVariants as $variant)
                                <option value="{{ $variant->id }}">{{ $variant->code }} ({{ $variant->product->name ?? '' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Seller -->
                    <div class="col-span-1">
                        <label for="edit_seller_id" class="block text-sm font-medium text-gray-700">Seller</label>
                        <select name="seller_id" id="edit_seller_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Seller --</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Payment Type -->
                    <div class="col-span-1">
                        <label for="edit_payment_type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                        <select name="payment_type" id="edit_payment_type"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="credit">Credit</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div class="col-span-1">
                        <label for="edit_status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="edit_status"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="paid">Paid</option>
                            <option value="installment">Installment</option>
                        </select>
                    </div>

                    <!-- Installment Amount (conditional) -->
                    <div id="editInstallmentAmountField" class="col-span-1 hidden">
                        <label for="edit_installment_amount" class="block text-sm font-medium text-gray-700">Installment Amount</label>
                        <input type="number" name="installment_amount" id="edit_installment_amount"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
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
            <p class="text-gray-800">Are you sure you want to delete transaction <strong id="deleteTransactionInvoice"></strong>?</p>
            <form id="deleteTransactionForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('deleteTransactionModal')" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Show/hide installment amount field based on status selection
    document.getElementById('status').addEventListener('change', function() {
        const installmentField = document.getElementById('installmentAmountField');
        if (this.value === 'installment') {
            installmentField.classList.remove('hidden');
        } else {
            installmentField.classList.add('hidden');
        }
    });

    // Edit version of the above
    document.getElementById('edit_status').addEventListener('change', function() {
        const installmentField = document.getElementById('editInstallmentAmountField');
        if (this.value === 'installment') {
            installmentField.classList.remove('hidden');
        } else {
            installmentField.classList.add('hidden');
        }
    });

    // Modal functions
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Edit transaction
    function editTransaction(transaction) {
        document.getElementById('editTransactionForm').action = `/transactions/${transaction.id}`;
        document.getElementById('edit_invoice').value = transaction.invoice;
        document.getElementById('edit_customer_id').value = transaction.customer_id;
        document.getElementById('edit_product_id').value = transaction.product_id;
        document.getElementById('edit_seller_id').value = transaction.seller_id;
        document.getElementById('edit_payment_type').value = transaction.payment_type;
        document.getElementById('edit_status').value = transaction.status;
        
        // Trigger change event to show/hide installment field
        document.getElementById('edit_status').dispatchEvent(new Event('change'));
        
        if (transaction.status === 'installment' && transaction.installments) {
            document.getElementById('edit_installment_amount').value = transaction.installments[0].installment_amount;
        }
        
        openModal('editTransactionModal');
    }

    // Delete transaction
    function deleteTransaction(transaction) {
        document.getElementById('deleteTransactionForm').action = `/transactions/${transaction.id}`;
        document.getElementById('deleteTransactionInvoice').textContent = transaction.invoice;
        openModal('deleteTransactionModal');
    }

    // Transaction items management
    let itemCounter = 0;
    
    let itemCounter = 0;

    function addTransactionItem() {
        const container = document.getElementById('transactionItemsContainer');
        const newItem = document.createElement('div');
        newItem.className = 'flex items-center space-x-2';
        newItem.innerHTML = `
            <select name="items[${itemCounter}][product_variant_id]" class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                <option value="">Select Variant</option>
                @foreach($productVariants as $variant)
                    <option value="{{ $variant->id }}">{{ $variant->code }} ({{ $variant->product->name ?? '' }})</option>
                @endforeach
            </select>
            <input type="number" name="items[${itemCounter}][quantity]" min="1" value="1" class="w-20 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
            <button type="button" onclick="this.parentNode.remove()" class="text-red-500 hover:text-red-700">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newItem);
        itemCounter++;
    }

    // Initialize with one item if there are validation errors
    @if($errors->any() && old('items'))
        @foreach(old('items') as $key => $item)
            const container = document.getElementById('transactionItemsContainer');
            const newItem = document.createElement('div');
            newItem.className = 'flex items-center space-x-2';
            newItem.innerHTML = `
                <select name="items[${key}][product_variant_id]" class="flex-1 border ${ $errors->has("items.${key}.product_variant_id") ? 'border-red-500' : 'border-gray-300' } rounded-md px-3 py-2 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select Variant</option>
                    @foreach($productVariants as $variant)
                        <option value="{{ $variant->id }}" {{ old("items.${key}.product_variant_id") == $variant->id ? 'selected' : '' }}>
                            {{ $variant->code }} ({{ $variant->product->name ?? '' }})
                        </option>
                    @endforeach
                </select>
                <input type="number" name="items[${key}][quantity]" min="1" value="{{ $item['quantity'] }}" class="w-20 border ${ $errors->has("items.${key}.quantity") ? 'border-red-500' : 'border-gray-300' } rounded-md px-3 py-2 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                <button type="button" onclick="this.parentNode.remove()" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            container.appendChild(newItem);
            itemCounter++;
        @endforeach
    @else
        // Add one empty item by default when opening create modal
        document.addEventListener('DOMContentLoaded', function() {
            addTransactionItem();
        });
    @endif
</script>
@endsection