@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Transaction Details</h2>
        <a href="{{ route('transactions.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-1"></i> Back to Transactions
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Transaction Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Invoice #{{ $transaction->invoice }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('l, d F Y') }}
                    </p>
                </div>
                <div class="flex space-x-2">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $transaction->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $transaction->payment_type == 'cash' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ ucfirst($transaction->payment_type) }}
                    </span>
                     @if($transaction->is_tempo == 1)
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                            Cash Tempo
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Transaction Details -->
        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-500">Customer</h4>
                <p class="mt-1 text-sm text-gray-900">{{ $transaction->customer->name }}</p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Seller</h4>
                <p class="mt-1 text-sm text-gray-900">{{ $transaction->seller->name }}</p>
                <strong><p class="mt-1 text-sm text-gray-900">{{ $transaction->seller->vehicle->name ?? '-'}}</p></strong>
            </div>
        <div>
                <h4 class="text-sm font-medium text-gray-500">Address</h4>
                <p class="mt-1 text-sm text-gray-900">
                    {{ $transaction->customer->address }},
                    {{ $transaction->customer->subdistrict->name ?? '' }},
                    {{ $transaction->customer->city->name ?? '' }}
                </p>
            </div>
            <div>
                <h4 class="text-sm font-medium text-gray-500">Total Amount</h4>
                <p class="mt-1 text-sm text-gray-900">{{ number_format($transaction->deal_price, 2) }}</p>
            </div>
           <div>
                <h4 class="text-sm font-medium text-gray-500">Total Quantity</h4>
                <p class="mt-1 text-sm text-gray-900">
                    {{ $transaction->items->sum('quantity') }}
                </p>
            </div>
            @if($transaction->payment_type == 'credit')
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Installment Amount</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($transaction->installments->first()->installment_amount, 2) }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Outstanding Amount</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($transaction->outstanding->outstanding_amount, 2) }}</p>
                </div>
            @endif
        </div>

        @if($transaction->payment_type == 'installment' || $transaction->is_tempo)
        <div class="px-6 py-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-500 mb-3">Installment Information</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Original Amount</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($transaction->deal_price, 2) }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Outstanding Amount</h4>
                    <p class="mt-1 text-sm text-gray-900">{{ number_format($transaction->outstanding->outstanding_amount, 2) }}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Total Paid</h4>
                    <p class="mt-1 text-sm text-gray-900">
                        {{ number_format($transaction->deal_price - $transaction->outstanding->outstanding_amount, 2) }}
                    </p>
                </div>
            </div>

            <!-- Pay Installment Button -->
            <button type="button" onclick="openPaymentModal('{{ $transaction->id }}')" 
                class="mb-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <i class="fas fa-money-bill-wave mr-2"></i> Record Payment
            </button>

            <!-- Payment History -->
            <h5 class="text-sm font-medium text-gray-500 mb-2">Payment History</h5>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->installments as $installment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($transaction->transaction_date)->translatedFormat('l, d F Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($installment->installment_amount, 2) }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Modal (updated with today checkbox) -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-green-700">Pay Installment</h3>
                <button onclick="closeModal('paymentModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="paymentForm" method="POST" action="">
                @csrf
                <div class="mb-4">
                    <label for="payment_amount" class="block text-sm font-medium text-gray-700">Payment Amount</label>
                    <input type="number" step="0.01" name="payment_amount" id="payment_amount" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        max="{{ $transaction->outstanding->outstanding_amount }}">
                </div>
                
                <!-- Modified Payment Date Section with Today Checkbox -->
                <div class="mb-4">
                    <label for="payment_date" class="block text-sm font-medium text-gray-700">Payment Date</label>
                    <div class="flex items-center space-x-4 mt-1">
                        <input 
                            type="date" 
                            name="payment_date" 
                            id="payment_date" 
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm"
                        >
                        <div class="flex items-center">
                            <input 
                                type="checkbox" 
                                id="payment_today_checkbox" 
                                class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                            >
                            <label for="payment_today_checkbox" class="ml-2 text-sm text-gray-700">Today</label>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('paymentModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

        <script>
            function openPaymentModal(transactionId) {
                document.getElementById('paymentForm').action = `/transactions/${transactionId}/pay-installment`;
                document.getElementById('paymentModal').classList.remove('hidden');
            }
        </script>
        @endif

        <!-- Transaction Items -->
        <div class="px-6 py-4 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-500 mb-3">Items</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->productVariant->code }}</div>
                                <div class="text-sm text-gray-500">{{ $item->productVariant->product->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteConfirmationModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-bold text-red-700">Delete Confirmation</h3>
            <button onclick="closeModal('deleteConfirmationModal')" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="flex items-start">
            <div class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div class="ml-4">
                <h4 class="text-lg font-medium text-gray-900">Delete Transaction</h4>
                <p class="text-sm text-gray-500">Are you sure you want to delete transaction #<span id="transactionInvoiceToDelete" class="font-semibold"></span>? This action cannot be undone.</p>
            </div>
        </div>
        
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end pt-5 space-x-3">
                <button type="button" onclick="closeModal('deleteConfirmationModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                    Delete Transaction
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function confirmDelete(transactionId, transactionInvoice) {
        document.getElementById('transactionInvoiceToDelete').textContent = transactionInvoice;
        document.getElementById('deleteForm').action = `/transactions/${transactionId}`;
        document.getElementById('deleteConfirmationModal').classList.remove('hidden');
    }

    function openPaymentModal(transactionId) {
        // Reset form and set action
        document.getElementById('paymentForm').action = `/transactions/${transactionId}/pay-installment`;
        document.getElementById('paymentForm').reset();
        
        // Get elements
        const paymentModal = document.getElementById('paymentModal');
        const paymentDateInput = document.getElementById('payment_date');
        const paymentTodayCheckbox = document.getElementById('payment_today_checkbox');
        
        // Today checkbox functionality
        paymentTodayCheckbox.addEventListener('change', function() {
            if (this.checked) {
                // Set to today's date in YYYY-MM-DD format
                const today = new Date();
                const formattedDate = today.toISOString().split('T')[0];
                paymentDateInput.value = formattedDate;
            } else {
                // Clear the date if checkbox is unchecked
                paymentDateInput.value = '';
            }
        });
        
        // Show modal
        paymentModal.classList.remove('hidden');
        
        // Clean up event listener when modal is closed
        const cleanUp = function() {
            paymentTodayCheckbox.removeEventListener('change', this);
            paymentModal.removeEventListener('hidden', cleanUp);
        };
        paymentModal.addEventListener('hidden', cleanUp);
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

</script>
@endsection