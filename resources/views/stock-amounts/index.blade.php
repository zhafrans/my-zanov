@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Stock Amount Management</h2>
        <button onclick="openModal('createStockAmountModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Stock Amount
        </button>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('stock-amounts.index') }}" class="mb-4 flex items-center space-x-4">
        <!-- Search Field -->
        <div class="flex items-center space-x-2">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by amount..." 
                    value="{{ request('search') }}"
                    class="w-64 pl-4 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Warehouse Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="warehouse_id" class="text-sm font-medium text-gray-700">Warehouse</label>
            <div class="relative">
                <select 
                    name="warehouse_id" 
                    id="warehouse_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- Stock Type Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="stock_type_id" class="text-sm font-medium text-gray-700">Stock Type</label>
            <div class="relative">
                <select 
                    name="stock_type_id" 
                    id="stock_type_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Stock Types</option>
                    @foreach($stockTypes as $stockType)
                        <option value="{{ $stockType->id }}" {{ request('stock_type_id') == $stockType->id ? 'selected' : '' }}>{{ $stockType->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
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
            
            @if(request()->has('search') || request()->has('warehouse_id') || request()->has('stock_type_id'))
                <a 
                    href="{{ route('stock-amounts.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition"
                >
                    Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Stock Amounts Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                    {{-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock Types</th> --}}
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($stockAmounts as $stockAmount)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $stockAmount->warehouse->name }}</div>
                    </td>
                    {{-- <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            @foreach($stockAmount->items as $item)
                                <span class="inline-block bg-gray-100 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">
                                    {{ $item->stockType->name }}: {{ $item->amount }}
                                </span>
                            @endforeach
                        </div>
                    </td> --}}
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $stockAmount->total_amount }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='showStockAmount(@json($stockAmount))' class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick='editStockAmount(@json($stockAmount))' class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick='deleteStockAmount(@json($stockAmount))' class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Info and Navigation -->
    <div class="mt-4">
        {{ $stockAmounts->links() }}
    </div>

    <!-- Create Stock Amount Modal -->
    <div id="createStockAmountModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Create New Stock Amount</h3>
                <button onclick="closeModal('createStockAmountModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createStockAmountForm" method="POST" action="{{ route('stock-amounts.store') }}">
                @csrf
                <div class="space-y-4">
                    <!-- Warehouse -->
                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse *</label>
                        <select name="warehouse_id" id="warehouse_id" required
                            class="mt-1 block w-full border {{ $errors->has('warehouse_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Warehouse --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Stock Items -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stock Items *</label>
                        <div id="stockItemsContainer" class="mt-2 space-y-3">
                            @if(old('items'))
                                @foreach(old('items') as $index => $item)
                                    <div class="flex items-end space-x-2 stock-item">
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-500">Stock Type</label>
                                            <select name="items[{{ $index }}][stock_type_id]" required
                                                class="mt-1 block w-full border {{ $errors->has('items.'.$index.'.stock_type_id') ? 'border-red-500' : 'border-gray-300' }} 
                                                    rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                                <option value="">-- Select Stock Type --</option>
                                                @foreach($stockTypes as $stockType)
                                                    <option value="{{ $stockType->id }}" {{ $item['stock_type_id'] == $stockType->id ? 'selected' : '' }}>{{ $stockType->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('items.'.$index.'.stock_type_id')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-xs font-medium text-gray-500">Amount</label>
                                            <input type="number" name="items[{{ $index }}][amount]" min="0" value="{{ $item['amount'] }}" required
                                                class="mt-1 block w-full border {{ $errors->has('items.'.$index.'.amount') ? 'border-red-500' : 'border-gray-300' }} 
                                                    rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                            @error('items.'.$index.'.amount')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <button type="button" onclick="removeStockItem(this)" class="mb-1 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex items-end space-x-2 stock-item">
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500">Stock Type</label>
                                        <select name="items[0][stock_type_id]" required
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                            <option value="">-- Select Stock Type --</option>
                                            @foreach($stockTypes as $stockType)
                                                <option value="{{ $stockType->id }}">{{ $stockType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-xs font-medium text-gray-500">Amount</label>
                                        <input type="number" name="items[0][amount]" min="0" value="0" required
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                                    </div>
                                    <button type="button" onclick="removeStockItem(this)" class="mb-1 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="addStockItem()" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                        @error('items')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('createStockAmountModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Stock Amount
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Show Stock Amount Modal -->
    <div id="showStockAmountModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Stock Amount Details</h3>
                <button onclick="closeModal('showStockAmountModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-boxes text-primary-700"></i>
                    </div>
                    <div>
                        <h4 id="show_warehouse" class="text-lg font-medium text-gray-900"></h4>
                        <p id="show_total_amount" class="text-sm text-gray-500"></p>
                    </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Stock Items:</h5>
                    <div id="show_items" class="space-y-2"></div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Created At</p>
                        <p id="show_created_at" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Last Updated</p>
                        <p id="show_updated_at" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end pt-5">
                <button onclick="closeModal('showStockAmountModal')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Stock Amount Modal -->
    <div id="deleteStockAmountModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-red-700">Delete Confirmation</h3>
                <button onclick="closeModal('deleteStockAmountModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-800">Are you sure you want to delete this stock amount record?</p>
            <form id="deleteStockAmountForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('deleteStockAmountModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Stock Amount Modal -->
    <div id="editStockAmountModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Edit Stock Amount</h3>
                <button onclick="closeModal('editStockAmountModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editStockAmountForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Warehouse -->
                    <div>
                        <label for="edit_warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse *</label>
                        <select name="warehouse_id" id="edit_warehouse_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Warehouse --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Stock Items -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stock Items *</label>
                        <div id="editStockItemsContainer" class="mt-2 space-y-3">
                            <!-- Items will be added dynamically via JavaScript -->
                        </div>
                        <button type="button" onclick="addEditStockItem()" class="mt-2 inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-primary-700 bg-primary-100 hover:bg-primary-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('editStockAmountModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Update Stock Amount
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Stock Item Management for Create Form
    let stockItemCount = {{ old('items') ? count(old('items')) : 1 }};
    
    function addStockItem() {
        const container = document.getElementById('stockItemsContainer');
        const newItem = document.createElement('div');
        newItem.className = 'flex items-end space-x-2 stock-item';
        newItem.innerHTML = `
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500">Stock Type</label>
                <select name="items[${stockItemCount}][stock_type_id]" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Select Stock Type --</option>
                    @foreach($stockTypes as $stockType)
                        <option value="{{ $stockType->id }}">{{ $stockType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500">Amount</label>
                <input type="number" name="items[${stockItemCount}][amount]" min="0" value="0" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="button" onclick="removeStockItem(this)" class="mb-1 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newItem);
        stockItemCount++;
    }
    
    function removeStockItem(button) {
        const item = button.closest('.stock-item');
        item.remove();
    }

    // Stock Item Management for Edit Form
    function addEditStockItem(stockTypeId = '', amount = 0) {
        const container = document.getElementById('editStockItemsContainer');
        const itemCount = container.querySelectorAll('.stock-item').length;
        const newItem = document.createElement('div');
        newItem.className = 'flex items-end space-x-2 stock-item';
        newItem.innerHTML = `
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500">Stock Type</label>
                <select name="items[${itemCount}][stock_type_id]" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    <option value="">-- Select Stock Type --</option>
                    @foreach($stockTypes as $stockType)
                        <option value="{{ $stockType->id }}" ${stockTypeId == '{{ $stockType->id }}' ? 'selected' : ''}>{{ $stockType->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-xs font-medium text-gray-500">Amount</label>
                <input type="number" name="items[${itemCount}][amount]" min="0" value="${amount}" required
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="button" onclick="removeStockItem(this)" class="mb-1 px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(newItem);
    }

    // Modal Functions
    function showStockAmount(stockAmount) {
        document.getElementById('show_warehouse').textContent = stockAmount.warehouse.name;
        document.getElementById('show_total_amount').textContent = `Total: ${stockAmount.total_amount}`;
        
        const itemsContainer = document.getElementById('show_items');
        itemsContainer.innerHTML = '';
        
        stockAmount.items.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.className = 'flex justify-between items-center bg-gray-50 p-2 rounded';
            itemElement.innerHTML = `
                <span class="text-sm font-medium">${item.stock_type.name}</span>
                <span class="text-sm font-bold">${item.amount}</span>
            `;
            itemsContainer.appendChild(itemElement);
        });
        
        document.getElementById('show_created_at').textContent = new Date(stockAmount.created_at).toLocaleString();
        document.getElementById('show_updated_at').textContent = new Date(stockAmount.updated_at).toLocaleString();
        
        openModal('showStockAmountModal');
    }
    
    function editStockAmount(stockAmount) {
        // Set form action URL
        document.getElementById('editStockAmountForm').action = `/stock-amounts/${stockAmount.id}`;
        
        // Fill warehouse
        document.getElementById('edit_warehouse_id').value = stockAmount.warehouse_id;
        
        // Clear existing items
        const container = document.getElementById('editStockItemsContainer');
        container.innerHTML = '';
        
        // Add items
        stockAmount.items.forEach(item => {
            addEditStockItem(item.stock_type_id, item.amount);
        });
        
        openModal('editStockAmountModal');
    }
    
    function deleteStockAmount(stockAmount) {
        document.getElementById('deleteStockAmountForm').action = `/stock-amounts/${stockAmount.id}`;
        openModal('deleteStockAmountModal');
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

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal('createStockAmountModal');
    });
</script>
@endif

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