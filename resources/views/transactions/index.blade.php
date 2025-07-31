@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Transaction Management</h2>
        <a href="{{ route('transactions.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Transaction
        </a>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('transactions.index') }}" class="mb-4 flex flex-wrap items-center gap-4">
        <!-- Search Field -->
        <div class="relative flex items-center">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by invoice..." 
                    value="{{ request('search') }}"
                    class="w-64 pl-4 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Customer Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="customer_id" class="text-sm font-medium text-gray-700">Customer</label>
            <div class="relative">
                <select 
                    name="customer_id" 
                    id="customer_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} - ({{ $customer->code }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Seller Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="seller_id" class="text-sm font-medium text-gray-700">Seller</label>
            <div class="relative">
                <select 
                    name="seller_id" 
                    id="seller_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Sellers</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Product Variant Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="product_variant_id" class="text-sm font-medium text-gray-700">Product</label>
            <div class="relative">
                <select 
                    name="product_variant_id" 
                    id="product_variant_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Products</option>
                    @foreach($productVariants as $variant)
                        <option value="{{ $variant->id }}" {{ request('product_variant_id') == $variant->id ? 'selected' : '' }}>
                            {{ $variant->code }} - {{ $variant->product->name ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Payment Type Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="payment_type" class="text-sm font-medium text-gray-700">Payment Type</label>
            <div class="relative">
                <select 
                    name="payment_type" 
                    id="payment_type"
                    class="w-32 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Types</option>
                    <option value="cash" {{ request('payment_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="installment" {{ request('payment_type') == 'installment' ? 'selected' : '' }}>Installment</option>
                </select>
            </div>
        </div>

        <!-- Status Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="status" class="text-sm font-medium text-gray-700">Status</label>
            <div class="relative">
                <select 
                    name="status" 
                    id="status"
                    class="w-32 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
        </div>

        <!-- Date Range Filter - Larger Version -->
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
                <label for="start_date" class="text-sm font-medium text-gray-700 whitespace-nowrap">From Date</label>
                <div class="relative">
                    <input 
                        type="date" 
                        name="start_date" 
                        id="start_date"
                        value="{{ request('start_date') }}"
                        class="w-40 px-4 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <label for="end_date" class="text-sm font-medium text-gray-700 whitespace-nowrap">To Date</label>
                <div class="relative">
                    <input 
                        type="date" 
                        name="end_date" 
                        id="end_date"
                        value="{{ request('end_date') }}"
                        class="w-40 px-4 py-2 text-base border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                    >
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-2">
            <button 
                type="submit" 
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition"
            >
                Apply
            </button>
            
            @if(request()->anyFilled(['search', 'customer_id', 'seller_id', 'product_variant_id', 'payment_type', 'status', 'start_date', 'end_date']))
            <a 
                href="{{ route('transactions.index') }}" 
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition"
            >
                Clear
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
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Products</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
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
                        <div class="text-sm text-gray-500">{{ $transaction->transaction_date }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-primary-700 font-medium hover:underline">
                            <a href="{{ route('transactions.show', $transaction->id) }}">
                                {{ $transaction->customer->name }}
                            </a>
                        </div>
                        <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($transaction->customer->address, 10) }}</div>
                    </td>

                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->seller->name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @foreach($transaction->items as $item)
                                <div>
                                    {{ $item->quantity }}x {{ $item->productVariant->product->name ?? '' }} ({{ $item->productVariant->code ?? $item->productVariant->other_code }})
                                </div>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ number_format($transaction->deal_price, 2) }}</div>
                        @if($transaction->is_tempo && $transaction->status !== 'paid')
                            @php
                                $isOverdue = \Carbon\Carbon::now()->gt(\Carbon\Carbon::parse($transaction->tempo_at));
                            @endphp

                            <i class="fas fa-clock {{ $isOverdue ? 'text-red-500' : 'text-yellow-500' }} mr-2"></i>
                            <span>{{ \Carbon\Carbon::parse($transaction->tempo_at)->format('d-m-Y') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->payment_type == 'cash' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($transaction->payment_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $transaction->status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        @if(($transaction->payment_type === 'installment' || $transaction->is_tempo) && $transaction->status !== 'paid')
                            <button type="button" onclick="openPaymentModal('{{ $transaction->id }}')" class="text-green-600 hover:text-green-900 mr-3" title="Pay Installment">
                                <i class="fas fa-money-bill-wave"></i>
                            </button>
                        @endif
                        <a href="{{ route('transactions.show', $transaction->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('{{ $transaction->id }}', '{{ $transaction->invoice }}')" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Info and Navigation -->
    <div class="mt-4">
        {{ $transactions->links() }}
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
                    <p class="text-sm text-gray-500">Are you sure you want to delete transaction <span id="transactionInvoiceToDelete" class="font-semibold"></span>? This action cannot be undone.</p>
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

    <!-- Payment Modal -->
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
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm">
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

<!-- SlimSelect JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.css" rel="stylesheet"></link>

<script>
    // Initialize SlimSelect for filter dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        new SlimSelect({
            select: '#customer_id',
            placeholder: 'All Customers',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>',
            searchText: 'No customers found',
            searchPlaceholder: 'Search customers...',
            searchingText: 'Searching...',
            // Tambahkan ini untuk format tampilan di dropdown
            innerText: {
                placeholder: function(placeholder) {
                    return `<div class="text-gray-400">${placeholder}</div>`;
                },
                option: function(option) {
                    // Split teks untuk styling yang berbeda
                    const parts = option.text.split(' - ');
                    return `<div>${parts[0]} <span class="text-gray-500">${parts[1] || ''}</span></div>`;
                }
            }
        });

        new SlimSelect({
            select: '#seller_id',
            placeholder: 'All Sellers',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        new SlimSelect({
            select: '#product_variant_id',
            placeholder: 'All Products',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        new SlimSelect({
            select: '#payment_type',
            placeholder: 'All Types',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        new SlimSelect({
            select: '#status',
            placeholder: 'All Status',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        // Function to open payment modal with today's date checkbox functionality
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

        // Function to open delete confirmation modal
        function confirmDelete(transactionId, transactionInvoice) {
            document.getElementById('transactionInvoiceToDelete').textContent = transactionInvoice;
            document.getElementById('deleteForm').action = `/transactions/${transactionId}`;
            openModal('deleteConfirmationModal');
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

        // Date validation
        document.getElementById('start_date').addEventListener('change', function() {
            const endDate = document.getElementById('end_date');
            if (this.value && endDate.value && this.value > endDate.value) {
                endDate.value = this.value;
            }
        });

        document.getElementById('end_date').addEventListener('change', function() {
            const startDate = document.getElementById('start_date');
            if (this.value && startDate.value && this.value < startDate.value) {
                startDate.value = this.value;
            }
        });
    });
</script>

@if (session('error'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Oops!',
            text: '{{ session('error') }}',
            confirmButtonColor: '#d33',
        });
    });
</script>
@endif

@if (session('success'))
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        Toast.fire({
            icon: 'success',
            title: '{{ session('success') }}'
        });
    });
</script>
@endif
@endsection