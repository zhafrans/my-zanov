@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Stock Amount Management</h2>
        {{-- <button onclick="openModal('createStockAmountModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Stock Amount
        </button> --}}
    </div>

    <!-- Search and Filter Section -->
    {{-- <form method="GET" action="{{ route('stock-amounts.index') }}" class="mb-4 flex items-center space-x-4">
        <!-- Search Field -->
        <div class="flex items-center space-x-2">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by name..." 
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
                    @foreach($allWarehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
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
            
            @if(request()->has('search') || request()->has('warehouse_id'))
                <a 
                    href="{{ route('stock-amounts.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition"
                >
                    Clear
                </a>
            @endif
        </div>
    </form> --}}

    <!-- Stock Amounts Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Items</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($warehouses as $warehouse)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $warehouse->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $warehouse->stockAmounts->count() }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $warehouse->stockAmounts->sum('amount') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick="showWarehouseStock({{ $warehouse->id }})" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye mr-1"></i> Details
                        </button>
                        <button onclick='openCreateModalForWarehouse({{ $warehouse->id }})' 
                           class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination Info and Navigation -->
    <div class="mt-4">
        {{ $warehouses->links() }}
    </div>

    <!-- Show Warehouse Stock Modal -->
    <div id="showWarehouseStockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800" id="warehouseModalTitle">Warehouse Stock Details</h3>
                <button onclick="closeModal('showWarehouseStockModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-warehouse text-primary-700 text-xl"></i>
                    </div>
                    <div>
                        <h4 id="warehouseName" class="text-lg font-medium text-gray-900">Loading...</h4>
                        <div class="flex space-x-4 mt-1">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-boxes mr-1"></i>
                                <span id="totalItems">0</span> items
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-cubes mr-1"></i>
                                Total: <span id="totalAmount">0</span>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <div class="bg-gray-50 px-4 py-3 rounded-t-md border-b border-gray-200">
                        <h4 class="text-sm font-medium text-gray-700">Stock Items</h4>
                    </div>
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-b-md">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="py-3 pl-4 pr-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                    <th scope="col" class="relative py-3 pl-3 pr-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white" id="warehouseStockItems">
                                <tr>
                                    <td colspan="4" class="py-8 text-center text-gray-500">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end pt-5">
                <button onclick="closeModal('showWarehouseStockModal')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Create Stock Amount Modal -->
    <div id="createStockAmountModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Create Stock Amount</h3>
                <button onclick="closeModal('createStockAmountModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createStockAmountForm" method="POST" action="{{ route('stock-amounts.store') }}">
                @csrf
                <div class="space-y-4">
                    <!-- Warehouse (hidden if coming from specific warehouse) -->
                    <div id="warehouseSelectContainer">
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id"
                            class="mt-1 block w-full border {{ $errors->has('warehouse_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select Warehouse</option>
                            @foreach($allWarehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('warehouse_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="mt-1 block w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}"
                            class="mt-1 block w-full border {{ $errors->has('amount') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('amount')
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
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Stock Amount Modal -->
    <div id="editStockAmountModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
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
                        <label for="edit_warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse</label>
                        <select name="warehouse_id" id="edit_warehouse_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            @foreach($allWarehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">Item Name</label>
                        <input type="text" name="name" id="edit_name"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Amount -->
                    <div>
                        <label for="edit_amount" class="block text-sm font-medium text-gray-700">Amount</label>
                        <input type="number" name="amount" id="edit_amount"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('editStockAmountModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Update
                    </button>
                </div>
            </form>
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
                    <h4 class="text-lg font-medium text-gray-900">Delete Stock Item</h4>
                    <p class="text-sm text-gray-500">Are you sure you want to delete <span id="stockItemNameToDelete" class="font-semibold"></span>? This action cannot be undone.</p>
                </div>
            </div>
            
            <form id="deleteStockAmountForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('deleteConfirmationModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 text-sm font-medium rounded-md transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition">
                        Delete Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Function to show warehouse stock details
    async function showWarehouseStock(warehouseId) {
        console.log('Loading warehouse stock for ID:', warehouseId);
        
        try {
            // Show loading state
            const modal = document.getElementById('showWarehouseStockModal');
            openModal('showWarehouseStockModal');
            
            // Show loading state in the table
            const itemsContainer = document.getElementById('warehouseStockItems');
            itemsContainer.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Loading...
                    </td>
                </tr>
            `;
            
            // Fetch warehouse data with stock amounts
            const response = await fetch(`/api/warehouses/${warehouseId}/stock-amounts`);
            
            if (!response.ok) {
                throw new Error('Failed to fetch warehouse data');
            }
            
            const data = await response.json();
            console.log('Received data:', data);
            
            // Update modal with warehouse data
            updateWarehouseModal(data);
            
        } catch (error) {
            console.error('Error loading warehouse stock:', error);
            
            // Show error message
            const itemsContainer = document.getElementById('warehouseStockItems');
            itemsContainer.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center text-red-500">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Error loading data
                    </td>
                </tr>
            `;
        }
    }
    
    // Function to update the modal with warehouse data
    function updateWarehouseModal(warehouse) {
        // Set modal title and basic info
        document.getElementById('warehouseModalTitle').textContent = `Stock Details - ${warehouse.name}`;
        document.getElementById('warehouseName').textContent = warehouse.name;
        document.getElementById('totalItems').textContent = warehouse.stock_amounts ? warehouse.stock_amounts.length : 0;
        
        // Calculate total amount
        const totalAmount = warehouse.stock_amounts ? 
            warehouse.stock_amounts.reduce((sum, item) => sum + parseInt(item.amount || 0), 0) : 0;
        document.getElementById('totalAmount').textContent = totalAmount;
        
        // Populate stock items table
        const itemsContainer = document.getElementById('warehouseStockItems');
        itemsContainer.innerHTML = '';
        
        if (!warehouse.stock_amounts || warehouse.stock_amounts.length === 0) {
            itemsContainer.innerHTML = `
                <tr>
                    <td colspan="4" class="py-8 text-center text-gray-500">
                        <i class="fas fa-box-open mr-2"></i>No stock items found
                    </td>
                </tr>
            `;
            return;
        }
        
        // Add items to table
        warehouse.stock_amounts.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';
            
            row.innerHTML = `
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900">
                    ${item.name || 'N/A'}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    ${item.amount || '0'}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                    ${item.updated_at ? new Date(item.updated_at).toLocaleString() : 'N/A'}
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium">
                    <div class="flex justify-end space-x-2">
                        <button onclick="editStockAmount(${JSON.stringify(item).replace(/"/g, '&quot;')})" 
                                class="text-primary-600 hover:text-primary-900">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteStockAmount(${JSON.stringify(item).replace(/"/g, '&quot;')})" 
                                class="text-red-600 hover:text-red-900">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            
            itemsContainer.appendChild(row);
        });
    }

    function openCreateModalForWarehouse(warehouseId) {
        // Set the warehouse select to the specific warehouse and hide the select
        document.getElementById('warehouse_id').value = warehouseId;
        document.getElementById('warehouseSelectContainer').classList.add('hidden');
        
        openModal('createStockAmountModal');
    }

    function editStockAmount(stockAmount) {
        // Set form action URL
        document.getElementById('editStockAmountForm').action = `/stock-amounts/${stockAmount.id}`;
        
        // Fill form fields
        document.getElementById('edit_name').value = stockAmount.name;
        document.getElementById('edit_warehouse_id').value = stockAmount.warehouse_id;
        document.getElementById('edit_amount').value = stockAmount.amount;
        
        closeModal('showWarehouseStockModal');
        openModal('editStockAmountModal');
    }

    function deleteStockAmount(stockAmount) {
        document.getElementById('stockItemNameToDelete').textContent = stockAmount.name;
        document.getElementById('deleteStockAmountForm').action = `/stock-amounts/${stockAmount.id}`;
        
        closeModal('showWarehouseStockModal');
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

    // Reset warehouse select when create modal is closed
    document.getElementById('createStockAmountModal').addEventListener('click', function(e) {
        if (e.target === this) {
            document.getElementById('warehouseSelectContainer').classList.remove('hidden');
        }
    });
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