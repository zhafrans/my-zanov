@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Warehouse Management</h2>
        <button onclick="openModal('createWarehouseModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Warehouse
        </button>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('warehouses.index') }}" class="mb-4 flex items-center space-x-4">
        <!-- Name Search Field -->
        <div class="flex items-center space-x-2">
            <label for="search" class="text-sm font-medium text-gray-700">Search</label>
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    id="search"
                    placeholder="By name..." 
                    value="{{ request('search') }}"
                    class="w-64 pl-4 pr-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Type Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="type" class="text-sm font-medium text-gray-700">Type</label>
            <div class="relative">
                <select 
                    name="type" 
                    id="type"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Types</option>
                    <option value="WAREHOUSE" {{ request('type') == 'WAREHOUSE' ? 'selected' : '' }}>Warehouse</option>
                    <option value="CAR" {{ request('type') == 'CAR' ? 'selected' : '' }}>Car</option>
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
            
            @if(request()->has('search') || request()->has('type'))
                <a 
                    href="{{ route('warehouses.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition"
                >
                    Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Warehouses Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="warehousesTableBody">
                @foreach($warehouses as $warehouse)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $warehouse->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $warehouse->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            {{ $warehouse->type === 'WAREHOUSE' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ $warehouse->type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='showWarehouse(@json($warehouse))' class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick='editWarehouse(@json($warehouse))' class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick='deleteWarehouse(@json($warehouse))' class="text-red-600 hover:text-red-900">
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
        {{ $warehouses->links() }}
    </div>

    <!-- Create Warehouse Modal -->
    <div id="createWarehouseModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Create New Warehouse</h3>
                <button onclick="closeModal('createWarehouseModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createWarehouseForm" method="POST" action="{{ route('warehouses.store') }}">
                @csrf
                <div class="space-y-4">
                    <!-- Code -->
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                        <input type="text" name="code" id="code" value="{{ old('code') }}"
                            class="mt-1 block w-full border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="mt-1 block w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="type"
                            class="mt-1 block w-full border {{ $errors->has('type') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Type --</option>
                            <option value="WAREHOUSE" {{ old('type') === 'WAREHOUSE' ? 'selected' : '' }}>Warehouse</option>
                            <option value="CAR" {{ old('type') === 'CAR' ? 'selected' : '' }}>Car</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('createWarehouseModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Warehouse
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Show Warehouse Modal -->
    <div id="showWarehouseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Warehouse Details</h3>
                <button onclick="closeModal('showWarehouseModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-warehouse text-primary-700"></i>
                    </div>
                    <div>
                        <h4 id="show_name" class="text-lg font-medium text-gray-900"></h4>
                        <p id="show_code" class="text-sm text-gray-500"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Type</p>
                        <p id="show_type" class="text-sm text-gray-900 mt-1"></p>
                    </div>
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
                <button onclick="closeModal('showWarehouseModal')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Warehouse Modal -->
    <div id="deleteWarehouseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-red-700">Delete Confirmation</h3>
                <button onclick="closeModal('deleteWarehouseModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-800">Are you sure you want to delete <strong id="deleteWarehouseName"></strong>?</p>
            <form id="deleteWarehouseForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('deleteWarehouseModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Warehouse Modal -->
    <div id="editWarehouseModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Edit Warehouse</h3>
                <button onclick="closeModal('editWarehouseModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editWarehouseForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Code -->
                    <div>
                        <label for="edit_code" class="block text-sm font-medium text-gray-700">Code</label>
                        <input type="text" name="code" id="edit_code"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Name -->
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" id="edit_name"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="edit_type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" id="edit_type"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="WAREHOUSE">Warehouse</option>
                            <option value="CAR">Car</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('editWarehouseModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Update Warehouse
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editWarehouse(warehouse) {
        // Set form action URL
        document.getElementById('editWarehouseForm').action = `/warehouses/${warehouse.id}`;
        
        // Fill form fields
        document.getElementById('edit_code').value = warehouse.code;
        document.getElementById('edit_name').value = warehouse.name;
        document.getElementById('edit_type').value = warehouse.type;
        
        openModal('editWarehouseModal');
    }

    function deleteWarehouse(warehouse) {
        document.getElementById('deleteWarehouseName').textContent = warehouse.name;
        document.getElementById('deleteWarehouseForm').action = `/warehouses/${warehouse.id}`;
        openModal('deleteWarehouseModal');
    }

    function showWarehouse(warehouse) {
        document.getElementById('show_name').textContent = warehouse.name;
        document.getElementById('show_code').textContent = warehouse.code;
        document.getElementById('show_type').textContent = warehouse.type;
        document.getElementById('show_created_at').textContent = new Date(warehouse.created_at).toLocaleString();
        document.getElementById('show_updated_at').textContent = new Date(warehouse.updated_at).toLocaleString();
        
        openModal('showWarehouseModal');
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
        openModal('createWarehouseModal');
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