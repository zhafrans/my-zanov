@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Vehicle Management</h2>
        <button onclick="openModal('createVehicleModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Vehicle
        </button>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('vehicles.index') }}" class="mb-4 flex items-center space-x-4">
        <!-- Search Field with Dropdown -->
        <div class="flex items-center space-x-2">
            <div class="relative flex items-center">
                <!-- Search Type Dropdown -->
                <select 
                    name="search_type" 
                    class="appearance-none bg-white border border-gray-300 rounded-l-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="name" {{ request('search_type', 'name') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="licence_plate" {{ request('search_type') == 'licence_plate' ? 'selected' : '' }}>License Plate</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <!-- Search Input -->
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="Search..." 
                        value="{{ request('search') }}"
                        class="w-64 pl-4 pr-4 py-2 border border-l-0 rounded-r-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Driver Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="driver_id" class="text-sm font-medium text-gray-700">Driver</label>
            <div class="relative">
                <select 
                    name="driver_id" 
                    id="driver_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Drivers</option>
                    @foreach($drivers as $driver)
                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
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
            
            @if(request()->has('search') || request()->has('driver_id') || request()->has('seller_id') || request()->has('search_type'))
                <a 
                    href="{{ route('vehicles.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition"
                >
                    Clear
                </a>
            @endif
        </div>
    </form>

    <!-- Vehicles Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">License Plate</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Warehouse</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seller</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200" id="vehiclesTableBody">
                @foreach($vehicles as $vehicle)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $vehicle->code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $vehicle->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $vehicle->licence_plate }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $vehicle->warehouse->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $vehicle->driver->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $vehicle->seller->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='showVehicle(@json($vehicle))' class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick='editVehicle(@json($vehicle))' class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick='deleteVehicle(@json($vehicle))' class="text-red-600 hover:text-red-900">
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
        {{ $vehicles->links() }}
    </div>

    <!-- Create Vehicle Modal -->
    <div id="createVehicleModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Create New Vehicle</h3>
                <button onclick="closeModal('createVehicleModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createVehicleForm" method="POST" action="{{ route('vehicles.store') }}">
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

                    <!-- License Plate -->
                    <div>
                        <label for="licence_plate" class="block text-sm font-medium text-gray-700">License Plate</label>
                        <input type="text" name="licence_plate" id="licence_plate" value="{{ old('licence_plate') }}"
                            class="mt-1 block w-full border {{ $errors->has('licence_plate') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('licence_plate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Warehouse -->
                    <div>
                        <label for="warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse</label>
                        <select name="warehouse_id" id="warehouse_id"
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

                    <!-- Driver -->
                    <div>
                        <label for="driver_id" class="block text-sm font-medium text-gray-700">Driver</label>
                        <select name="driver_id" id="driver_id"
                            class="mt-1 block w-full border {{ $errors->has('driver_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                            @endforeach
                        </select>
                        @error('driver_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seller -->
                    <div>
                        <label for="seller_id" class="block text-sm font-medium text-gray-700">Seller</label>
                        <select name="seller_id" id="seller_id"
                            class="mt-1 block w-full border {{ $errors->has('seller_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Seller --</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ old('seller_id') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                            @endforeach
                        </select>
                        @error('seller_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('createVehicleModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Show Vehicle Modal -->
    <div id="showVehicleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Vehicle Details</h3>
                <button onclick="closeModal('showVehicleModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-truck text-primary-700"></i>
                    </div>
                    <div>
                        <h4 id="show_name" class="text-lg font-medium text-gray-900"></h4>
                        <p id="show_code" class="text-sm text-gray-500"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-500">License Plate</p>
                        <p id="show_licence_plate" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Warehouse</p>
                        <p id="show_warehouse" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Driver</p>
                        <p id="show_driver" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Seller</p>
                        <p id="show_seller" class="text-sm text-gray-900 mt-1"></p>
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
                <button onclick="closeModal('showVehicleModal')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Vehicle Modal -->
    <div id="deleteVehicleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-red-700">Delete Confirmation</h3>
                <button onclick="closeModal('deleteVehicleModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-800">Are you sure you want to delete <strong id="deleteVehicleName"></strong>?</p>
            <form id="deleteVehicleForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('deleteVehicleModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Vehicle Modal -->
    <div id="editVehicleModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Edit Vehicle</h3>
                <button onclick="closeModal('editVehicleModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editVehicleForm" method="POST" action="">
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

                    <!-- License Plate -->
                    <div>
                        <label for="edit_licence_plate" class="block text-sm font-medium text-gray-700">License Plate</label>
                        <input type="text" name="licence_plate" id="edit_licence_plate"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Warehouse -->
                    <div>
                        <label for="edit_warehouse_id" class="block text-sm font-medium text-gray-700">Warehouse</label>
                        <select name="warehouse_id" id="edit_warehouse_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Warehouse --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Driver -->
                    <div>
                        <label for="edit_driver_id" class="block text-sm font-medium text-gray-700">Driver</label>
                        <select name="driver_id" id="edit_driver_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Driver --</option>
                            @foreach($drivers as $driver)
                                <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Seller -->
                    <div>
                        <label for="edit_seller_id" class="block text-sm font-medium text-gray-700">Seller</label>
                        <select name="seller_id" id="edit_seller_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Seller --</option>
                            @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('editVehicleModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Update Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editVehicle(vehicle) {
        // Set form action URL
        document.getElementById('editVehicleForm').action = `/vehicles/${vehicle.id}`;
        
        // Fill form fields
        document.getElementById('edit_code').value = vehicle.code;
        document.getElementById('edit_name').value = vehicle.name;
        document.getElementById('edit_licence_plate').value = vehicle.licence_plate;
        document.getElementById('edit_warehouse_id').value = vehicle.warehouse_id;
        document.getElementById('edit_driver_id').value = vehicle.driver_id;
        document.getElementById('edit_seller_id').value = vehicle.seller_id;
        
        openModal('editVehicleModal');
    }

    function deleteVehicle(vehicle) {
        document.getElementById('deleteVehicleName').textContent = vehicle.name;
        document.getElementById('deleteVehicleForm').action = `/vehicles/${vehicle.id}`;
        openModal('deleteVehicleModal');
    }

    function showVehicle(vehicle) {
        document.getElementById('show_name').textContent = vehicle.name;
        document.getElementById('show_code').textContent = vehicle.code;
        document.getElementById('show_licence_plate').textContent = vehicle.licence_plate;
        document.getElementById('show_warehouse').textContent = vehicle.warehouse ? vehicle.warehouse.name : '-';
        document.getElementById('show_driver').textContent = vehicle.driver ? vehicle.driver.name : '-';
        document.getElementById('show_seller').textContent = vehicle.seller ? vehicle.seller.name : '-';
        document.getElementById('show_created_at').textContent = new Date(vehicle.created_at).toLocaleString();
        document.getElementById('show_updated_at').textContent = new Date(vehicle.updated_at).toLocaleString();
        
        openModal('showVehicleModal');
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
        openModal('createVehicleModal');
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
                <h4 class="text-lg font-medium text-gray-900">Delete Customer</h4>
                <p class="text-sm text-gray-500">Are you sure you want to delete <span id="customerNameToDelete" class="font-semibold"></span>? This action cannot be undone.</p>
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
                    Delete Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Function to open delete confirmation modal
    function confirmDelete(customerId, customerName) {
        document.getElementById('customerNameToDelete').textContent = customerName;
        document.getElementById('deleteForm').action = `/customers/${customerId}`;
        openModal('deleteConfirmationModal');
    }

    // Replace the inline delete forms with buttons that trigger the modal
    document.addEventListener('DOMContentLoaded', function() {
        // Find all delete buttons and replace them with modal triggers
        document.querySelectorAll('form[action*="/customers/"]').forEach(form => {
            if (form.querySelector('button[type="submit"][class*="text-red-600"]')) {
                const customerId = form.action.split('/').pop();
                const row = form.closest('tr');
                const customerName = row.querySelector('td:nth-child(2) div').textContent;
                
                // Create new button
                const deleteBtn = document.createElement('button');
                deleteBtn.className = 'text-red-600 hover:text-red-900';
                deleteBtn.innerHTML = '<i class="fas fa-trash"></i>';
                deleteBtn.onclick = (e) => {
                    e.preventDefault();
                    confirmDelete(customerId, customerName);
                };
                
                // Replace form with button
                form.parentNode.replaceChild(deleteBtn, form);
            }
        });
    });

    // Modal functions (you might already have these)
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