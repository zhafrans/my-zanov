@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Create New Transaction</h2>
        <a href="{{ route('transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
            <i class="fas fa-arrow-left mr-2"></i>Back to Transactions
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form method="POST" action="{{ route('transactions.store') }}" class="p-6" id="transactionForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Invoice Number -->
                <div>
                    <label for="invoice" class="block text-sm font-medium text-gray-700 mb-1">Invoice Number*</label>
                    <input 
                        type="text" 
                        id="invoice" 
                        name="invoice" 
                        value="{{ old('invoice') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                        required
                    >
                    @error('invoice')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deal Price -->
                <div>
                    <label for="deal_price" class="block text-sm font-medium text-gray-700 mb-1">Deal Price*</label>
                    <input 
                        type="number" 
                        id="deal_price" 
                        name="deal_price" 
                        value="{{ old('deal_price') }}"
                        min="0" 
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                        required
                    >
                    @error('deal_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Customer Selection -->
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Customer*</label>
                    <select 
                        id="customer_id" 
                        name="customer_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                        required
                    >
                        <option value="">Select Customer</option>
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

                <!-- Seller Selection -->
                <div>
                    <label for="seller_id" class="block text-sm font-medium text-gray-700 mb-1">Seller*</label>
                    <select 
                        id="seller_id" 
                        name="seller_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                        required
                    >
                        <option value="">Select Seller</option>
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
            </div>

             <!-- Transaction Date -->
            <div class="mb-6">
                <label for="transaction_date" class="block text-sm font-medium text-gray-700 mb-1">Transaction Date*</label>
                <div class="flex items-center space-x-4">
                    <input 
                        type="date" 
                        id="transaction_date" 
                        name="transaction_date" 
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                        value=""
                        required
                    >
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="today_checkbox" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                        >
                        <label for="today_checkbox" class="ml-2 text-sm text-gray-700">Today</label>
                    </div>
                </div>
                @error('transaction_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Product Items Section -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Product Items*</label>
                <div id="productItemsContainer">
                    <!-- Product Variant Selection -->
                    <div class="mb-4">
                        <label for="product_variant_search" class="block text-sm font-medium text-gray-700 mb-1">Search Product Variant</label>
                        <select id="product_variant_search" class="w-full">
                            <option value="">Search product variant...</option>
                            @foreach($products as $variant)
                                <option value="{{ $variant->id }}" 
                                    data-product-name="{{ $variant->product->name ?? 'N/A' }}"
                                    data-variant-code="{{ $variant->code }}"
                                    data-price="{{ $variant->price }}">
                                    {{ $variant->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Selected Products Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variant Code</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody id="selectedProductsTable" class="divide-y divide-gray-200">
                                <!-- Products will be added here dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                @error('items')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Payment Type -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Type*</label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input 
                            type="radio" 
                            name="payment_type" 
                            value="cash" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 payment-type-radio"
                            {{ old('payment_type', 'cash') == 'cash' ? 'checked' : '' }}
                        >
                        <span class="ml-2 text-gray-700">Cash</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input 
                            type="radio" 
                            name="payment_type" 
                            value="installment" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 payment-type-radio"
                            {{ old('payment_type') == 'installment' ? 'checked' : '' }}
                        >
                        <span class="ml-2 text-gray-700">Credit (Installment)</span>
                    </label>
                </div>
                @error('payment_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tempo Option -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input 
                            type="radio" 
                            name="is_tempo" 
                            value="0" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 tempo-radio"
                            {{ old('is_tempo', '0') == '0' ? 'checked' : '' }}
                        >
                        <span class="ml-2 text-gray-700">Regular</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input 
                            type="radio" 
                            name="is_tempo" 
                            value="1" 
                            class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 tempo-radio"
                            {{ old('is_tempo') == '1' ? 'checked' : '' }}
                        >
                        <span class="ml-2 text-gray-700">Tempo (1 Month)</span>
                    </label>
                </div>
                @error('is_tempo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- DP Section - Only shown when payment type is installment -->
            <div id="dpSection" class="mb-6 hidden">
                <div class="flex items-center mb-2">
                    <input 
                        type="checkbox" 
                        id="is_dp" 
                        name="is_dp" 
                        value="1"
                        class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded"
                        {{ old('is_dp') ? 'checked' : '' }}
                    >
                    <label for="is_dp" class="ml-2 text-sm font-medium text-gray-700">Down Payment (DP)</label>
                </div>
                
                <div id="dpAmountContainer" class="hidden">
                    <label for="dp_amount" class="block text-sm font-medium text-gray-700 mb-1">DP Amount*</label>
                    <input 
                        type="number" 
                        id="dp_amount" 
                        name="dp_amount" 
                        value="{{ old('dp_amount') }}"
                        min="0" 
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                    @error('dp_amount')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end">
                <button 
                    type="submit" 
                    class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-md transition"
                >
                    <i class="fas fa-save mr-2"></i>Create Transaction
                </button>
            </div>
        </form>
    </div>
</div>

@if ($errors->any())
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: `{!! implode('<br>', $errors->all()) !!}`,
            confirmButtonColor: '#d33',
        });
    });
</script>
@endif

<!-- SlimSelect JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.css" rel="stylesheet"></link>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize SlimSelect for product variant search
        new SlimSelect({
            select: '#product_variant_search',
            placeholder: 'Search product variant...',
            searchPlaceholder: 'Search...',
            onChange: (info) => {
                if (info.value) {
                    addProductToTable(info);
                }
            }
        });

        // Initialize SlimSelect for customer and seller
        new SlimSelect({
            select: '#customer_id',
            placeholder: 'Select customer...'
        });

        new SlimSelect({
            select: '#seller_id',
            placeholder: 'Select seller...'
        });

        // Load any previously selected items (for form validation errors)
        @if(old('items'))
            @foreach(old('items') as $item)
                const variant = document.querySelector(`#product_variant_search option[value="{{ $item['product_variant_id'] }}"]`);
                if (variant) {
                    addProductToTable({
                        value: "{{ $item['product_variant_id'] }}",
                        text: variant.text,
                        data: {
                            productName: variant.dataset.productName,
                            variantCode: variant.dataset.variantCode,
                            price: variant.dataset.price
                        }
                    }, "{{ $item['quantity'] }}");
                }
            @endforeach
        @endif
    });

    function addProductToTable(info, quantity = 1) {
        const table = document.getElementById('selectedProductsTable');
        const productId = info.value;
        
        // Check if product already exists in table
        if (document.querySelector(`input[name="items[${productId}][product_variant_id]"]`)) {
            return;
        }
        
        const row = document.createElement('tr');
        row.className = 'product-item';
        row.dataset.productId = productId;
        row.innerHTML = `
            <td class="px-4 py-2">
                ${info.data.productName}
                <input type="hidden" name="items[${productId}][product_variant_id]" value="${productId}">
            </td>
            <td class="px-4 py-2">${info.data.variantCode}</td>
            <td class="px-4 py-2">
                <input type="number" 
                       name="items[${productId}][quantity]" 
                       value="${quantity}"
                       min="1" 
                       class="w-20 px-2 py-1 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 quantity-input"
                       required
            </td>
            <td class="px-4 py-2">
                <button type="button" 
                        class="text-red-600 hover:text-red-800 remove-product"
                        onclick="removeProduct('${productId}')">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        table.appendChild(row);
        document.getElementById('product_variant_search').value = '';
    }

    function removeProduct(productId) {
        const row = document.querySelector(`tr[data-product-id="${productId}"]`);
        if (row) {
            row.remove();
        }
    }

    // Today checkbox functionality
    const todayCheckbox = document.getElementById('today_checkbox');
    const transactionDateInput = document.getElementById('transaction_date');

    todayCheckbox.addEventListener('change', function() {
        if (this.checked) {
            // Set to today's date in YYYY-MM-DD format hanya ketika checkbox dicentang
            const today = new Date();
            const formattedDate = today.toISOString().split('T')[0];
            transactionDateInput.value = formattedDate;
        } else {
            // Kosongkan nilai jika checkbox tidak dicentang
            transactionDateInput.value = '';
        }
    });


    // Payment type radio buttons change handler
    document.querySelectorAll('.payment-type-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const dpSection = document.getElementById('dpSection');
            if (this.value === 'installment') {
                dpSection.classList.remove('hidden');
            } else {
                dpSection.classList.add('hidden');
                document.getElementById('is_dp').checked = false;
                document.getElementById('dpAmountContainer').classList.add('hidden');
            }
        });
    });

    // DP checkbox change handler
    document.getElementById('is_dp').addEventListener('change', function() {
        const dpAmountContainer = document.getElementById('dpAmountContainer');
        if (this.checked) {
            dpAmountContainer.classList.remove('hidden');
        } else {
            dpAmountContainer.classList.add('hidden');
        }
    });

    // Initialize visibility on page load
    document.addEventListener('DOMContentLoaded', function() {
        const paymentType = document.querySelector('input[name="payment_type"]:checked').value;
        const dpSection = document.getElementById('dpSection');
        
        if (paymentType === 'installment') {
            dpSection.classList.remove('hidden');
            if (document.getElementById('is_dp').checked) {
                document.getElementById('dpAmountContainer').classList.remove('hidden');
            }
        }
    });

</script>
@endsection