@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Stock Transaction Management</h2>
        <button onclick="openModal('createStockTransactionModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Transaction
        </button>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('stock-transactions.index') }}" class="mb-4 flex flex-wrap items-center gap-4" id="filterForm">
        <!-- Search Field -->
        <div class="flex items-center space-x-2">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by note..." 
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
            <label for="warehouse_id" class="text-sm font-medium text-gray-700">From Warehouse</label>
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

        <!-- To Warehouse Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="to_warehouse_id" class="text-sm font-medium text-gray-700">To Warehouse</label>
            <div class="relative">
                <select 
                    name="to_warehouse_id" 
                    id="to_warehouse_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Warehouses</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ request('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- Destination Filter Dropdown -->
        <div class="flex items-center space-x-2">
            <label for="destination" class="text-sm font-medium text-gray-700">Destination</label>
            <div class="relative">
                <select 
                    name="destination" 
                    id="destination"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Destinations</option>
                    @foreach($destinations as $key => $label)
                        <option value="{{ $key }}" {{ request('destination') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- Adjustment Filter -->
        <div class="flex items-center space-x-2">
            <label for="is_adjustment" class="text-sm font-medium text-gray-700">Adjustment</label>
            <div class="relative">
                <select 
                    name="is_adjustment" 
                    id="is_adjustment"
                    class="w-32 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All</option>
                    <option value="1" {{ request('is_adjustment') === '1' ? 'selected' : '' }}>Yes</option>
                    <option value="0" {{ request('is_adjustment') === '0' ? 'selected' : '' }}>No</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
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
                    class="w-32 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Types</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>
        </div>

        <!-- User Filter -->
        <div class="flex items-center space-x-2">
            <label for="user_id" class="text-sm font-medium text-gray-700">User</label>
            <div class="relative">
                <select 
                    name="user_id" 
                    id="user_id"
                    class="w-48 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
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
            
            @if(request()->anyFilled(['search', 'warehouse_id', 'to_warehouse_id', 'destination', 'is_adjustment', 'user_id']))
                <a 
                    href="{{ route('stock-transactions.index') }}" 
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition"
                >
                    Clear
                </a>
            @endif
        </div>
    </form>

    <script>
        // Hapus parameter kosong sebelum submit form
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.value === '' || input.value === null) {
                    input.disabled = true;
                }
            });
        });

        // Setelah form submit, aktifkan kembali input yang dinonaktifkan
        document.getElementById('filterForm').addEventListener('submit', function() {
            setTimeout(() => {
                const inputs = this.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.disabled = false;
                });
            }, 100);
        });
    </script>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Warehouse</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Warehouse</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adjustment</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Note</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($transactions as $transaction)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->created_at->format('d M Y H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->warehouse->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->toWarehouse->name ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $transaction->quantity }}</div>
                    </td>
                   <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            <span class="inline-flex items-center gap-1 px-2 py-1 text-xs rounded-full 
                                {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                
                                {{-- Ikon --}}
                                <i class="fas {{ $transaction->type === 'in' ? 'fa-arrow-down text-green-600' : 'fa-arrow-up text-red-600' }}"></i>
                                
                                {{-- Label --}}
                                {{ $types[$transaction->type] ?? 'Unknown' }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($transaction->destination)
                                <span class="px-2 py-1 text-xs rounded-full inline-flex items-center space-x-1
                                    {{ $transaction->destination === 'lost' ? 'bg-red-100 text-red-800' : 
                                    ($transaction->destination === 'transfer' ? 'bg-blue-100 text-blue-800' : 
                                    ($transaction->destination === 'sold' ? 'bg-orange-200 text-orange-800' : 'bg-green-100 text-green-800')) }}">
                                    
                                    @if($transaction->destination === 'transfer')
                                        <i class="fas fa-arrows-alt-h"></i>
                                    @endif

                                    <span>{{ $destinations[$transaction->destination] }}</span>
                                </span>
                            @else
                                -
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($transaction->is_adjustment)
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Yes</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">No</span>
                            @endif
                        </div>
                    </td>
                   <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 truncate max-w-xs">
                            {{ \Illuminate\Support\Str::limit($transaction->note, 5, '.....') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='showStockTransaction(@json($transaction))' class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick='editStockTransaction(@json($transaction))' class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick='deleteStockTransaction(@json($transaction))' class="text-red-600 hover:text-red-900">
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
        {{ $transactions->links() }}
    </div>

    <!-- Create Stock Transaction Modal -->
    <div id="createStockTransactionModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Create New Stock Transaction</h3>
                <button onclick="closeModal('createStockTransactionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createStockTransactionForm" method="POST" action="{{ route('stock-transactions.store') }}">
                @csrf
                <div class="space-y-4">
                    <!-- Source Warehouse (combines stock amount and warehouse) -->
                    <div>
                        <label for="source_warehouse_id" class="block text-sm font-medium text-gray-700">Source Warehouse *</label>
                        <select name="stock_amount_id" id="source_warehouse_id" required
                            class="mt-1 block w-full border {{ $errors->has('stock_amount_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Source Warehouse --</option>
                            @foreach($stockAmounts as $stockAmount)
                                <option value="{{ $stockAmount->id }}" {{ old('stock_amount_id') == $stockAmount->id ? 'selected' : '' }}>
                                    {{ $stockAmount->warehouse->name }} - Stock: {{ $stockAmount->total_amount }}
                                </option>
                            @endforeach
                        </select>
                        @error('stock_amount_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity *</label>
                        <input type="number" name="quantity" id="quantity" min="1" value="{{ old('quantity', 1) }}" required
                            class="mt-1 block w-full border {{ $errors->has('quantity') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                     <!-- Adjustment Field -->
                    <div class="flex items-center space-x-2">
                        <label for="is_adjustment" class="text-sm font-medium text-gray-700">Adjustment</label>
                        <div class="relative">
                            <select 
                                name="is_adjustment" 
                                id="is_adjustment"
                                class="w-32 appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                required
                            >
                                <option value="">All</option>
                                <option value="1" {{ request('is_adjustment') === '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ request('is_adjustment') === '0' ? 'selected' : '' }}>No</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Destination -->
                    <div>
                        <label for="destination" class="block text-sm font-medium text-gray-700">Destination</label>
                        <select name="destination" id="destination"
                            class="mt-1 block w-full border {{ $errors->has('destination') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                            onchange="toggleToWarehouse(this.value)">
                            <option value="">-- Select Destination --</option>
                            @foreach($destinations as $key => $label)
                                <option value="{{ $key }}" {{ old('destination') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('destination')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- To Warehouse (conditional) -->
                    <div id="toWarehouseContainer" style="display: none;">
                        <label for="to_warehouse_id" class="block text-sm font-medium text-gray-700">To Warehouse *</label>
                        <select name="to_warehouse_id" id="to_warehouse_id"
                            class="mt-1 block w-full border {{ $errors->has('to_warehouse_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Warehouse --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ old('to_warehouse_id') == $warehouse->id ? 'selected' : '' }}>{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                        @error('to_warehouse_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Note -->
                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700">Note</label>
                        <textarea name="note" id="note" rows="3"
                            class="mt-1 block w-full border {{ $errors->has('note') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('createStockTransactionModal')"
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

    <!-- Show Stock Transaction Modal -->
    <div id="showStockTransactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Stock Transaction Details</h3>
                <button onclick="closeModal('showStockTransactionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-primary-700"></i>
                    </div>
                    <div>
                        <h4 id="show_transaction_date" class="text-lg font-medium text-gray-900"></h4>
                        <p id="show_transaction_type" class="text-sm text-gray-500"></p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Source Warehouse</p>
                        <p id="show_source_warehouse" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Quantity</p>
                        <p id="show_quantity" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Adjustment</p>
                        <p id="show_adjustment" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Type</p>
                        <p id="show_type" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Destination</p>
                        <p id="show_destination" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Quantity Before</p>
                        <p id="show_quantity_before" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Quantity After</p>
                        <p id="show_quantity_after" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">User</p>
                        <p id="show_user" class="text-sm text-gray-900 mt-1"></p>
                    </div>
                  <div id="live_stock_container" class="hidden">
                    <p class="text-sm font-medium text-gray-500 flex items-center space-x-2">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-600"></span>
                        </span>
                        <span>STOCK NOW</span>
                    </p>
                    <p id="show_stock_amount" class="text-sm text-gray-900 mt-1"></p>
                </div>
                </div>
                
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-sm font-medium text-gray-500">Note</p>
                    <p id="show_note" class="text-sm text-gray-900 mt-1"></p>
                </div>
            </div>
            
            <div class="flex justify-end pt-5">
                <button onclick="closeModal('showStockTransactionModal')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Stock Transaction Modal -->
    <div id="deleteStockTransactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-red-700">Delete Confirmation</h3>
                <button onclick="closeModal('deleteStockTransactionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-800">Are you sure you want to delete this stock transaction?</p>
            <form id="deleteStockTransactionForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('deleteStockTransactionModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Stock Transaction Modal -->
    <div id="editStockTransactionModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Edit Stock Transaction</h3>
                <button onclick="closeModal('editStockTransactionModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editStockTransactionForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Source Warehouse (combines stock amount and warehouse) -->
                    <div>
                        <label for="edit_source_warehouse_id" class="block text-sm font-medium text-gray-700">Source Warehouse *</label>
                        <select name="stock_amount_id" id="edit_source_warehouse_id" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            @foreach($stockAmounts as $stockAmount)
                                <option value="{{ $stockAmount->id }}">
                                    {{ $stockAmount->warehouse->name }} - Stock: {{ $stockAmount->total_amount }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type -->
                    <div>
                        <label for="edit_type" class="block text-sm font-medium text-gray-700">Type *</label>
                        <select name="type" id="edit_type" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="edit_quantity" class="block text-sm font-medium text-gray-700">Quantity *</label>
                        <input type="number" name="quantity" id="edit_quantity" min="1" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Adjustment -->
                    <div>
                        <label for="edit_is_adjustment" class="block text-sm font-medium text-gray-700">Adjustment</label>
                        <div class="mt-1">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="is_adjustment" id="edit_is_adjustment" value="1" required
                                    class="rounded border-gray-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2">This is an adjustment transaction</span>
                            </label>
                        </div>
                    </div>

                    <!-- Destination -->
                    <div>
                        <label for="edit_destination" class="block text-sm font-medium text-gray-700">Destination</label>
                        <select name="destination" id="edit_destination"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500"
                            onchange="toggleEditToWarehouse(this.value)">
                            <option value="">-- Select Destination --</option>
                            @foreach($destinations as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- To Warehouse (conditional) -->
                    <div id="editToWarehouseContainer" style="display: none;">
                        <label for="edit_to_warehouse_id" class="block text-sm font-medium text-gray-700">To Warehouse *</label>
                        <select name="to_warehouse_id" id="edit_to_warehouse_id"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Note -->
                    <div>
                        <label for="edit_note" class="block text-sm font-medium text-gray-700">Note</label>
                        <textarea name="note" id="edit_note" rows="3"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('editStockTransactionModal')"
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
</div>

<script>
    // Toggle To Warehouse field based on destination
    function toggleToWarehouse(destination) {
        const container = document.getElementById('toWarehouseContainer');
        if (destination === 'transfer') {
            container.style.display = 'block';
            document.getElementById('to_warehouse_id').setAttribute('required', 'required');
        } else {
            container.style.display = 'none';
            document.getElementById('to_warehouse_id').removeAttribute('required');
        }
    }

    // Toggle To Warehouse field for edit form
    function toggleEditToWarehouse(destination) {
        const container = document.getElementById('editToWarehouseContainer');
        if (destination === 'transfer') {
            container.style.display = 'block';
            document.getElementById('edit_to_warehouse_id').setAttribute('required', 'required');
        } else {
            container.style.display = 'none';
            document.getElementById('edit_to_warehouse_id').removeAttribute('required');
        }
    }

    // Initialize on page load if there are errors
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('destination').value === 'transfer') {
            toggleToWarehouse('transfer');
        }
    });

    // Modal Functions
    function showStockTransaction(transaction) {
    try {
        // Parse jika masih berupa string JSON
        if (typeof transaction === 'string') {
            transaction = JSON.parse(transaction);
        }

        // Format tanggal
        const date = new Date(transaction.created_at);
        const formattedDate = `${date.getDate()} ${date.toLocaleString('default', { month: 'short' })} ${date.getFullYear()} ${date.getHours()}:${date.getMinutes().toString().padStart(2, '0')}`;
        
        // Isi data modal
        document.getElementById('show_transaction_date').textContent = formattedDate;
        let sourceWarehouseText = '-';
        if (transaction.stock_amount) {
            sourceWarehouseText = `${transaction.stock_amount.warehouse?.name || 'Unknown Warehouse'} (Stock: ${transaction.stock_amount.total_amount})`;
        }
        document.getElementById('show_source_warehouse').textContent = sourceWarehouseText;
        document.getElementById('show_quantity').textContent = transaction.quantity || '0';
        document.getElementById('show_adjustment').textContent = transaction.is_adjustment ? 'Yes' : 'No';
        document.getElementById('show_destination').textContent = transaction.destination ? 
            transaction.destination.charAt(0).toUpperCase() + transaction.destination.slice(1) : '-';
        let stockAmountText = '-';
        if (transaction.stock_amount) {
            const warehouseName = transaction.stock_amount.warehouse?.name || 
                                'Unknown Warehouse';
            stockAmountText = `${warehouseName} - Total: ${transaction.stock_amount.total_amount}`;
        }

        document.getElementById('show_stock_amount').textContent = stockAmountText;
        document.getElementById('show_note').textContent = transaction.note || '-';
        document.getElementById('show_quantity_before').textContent = transaction.quantity_before || '0';
        document.getElementById('show_quantity_after').textContent = transaction.quantity_after || '0';
        document.getElementById('show_user').textContent = transaction.user?.name || 'System';
        
        // Tampilkan type dengan styling
        const typeElement = document.getElementById('show_type');
        if (typeElement) {
            // Determine type based on destination
            const type = (transaction.destination === 'add') ? 'in' : 'out';
            typeElement.innerHTML = `
                <span class="px-2 py-1 text-xs rounded-full 
                    ${type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${type.toUpperCase()}
                </span>`;
        }

        // Buka modal
        openModal('showStockTransactionModal');
    } catch (error) {
        console.error('Error showing transaction:', error);
        alert('Error showing transaction details. Please check console for details.');
    }
}

// Pastikan modal bisa dibuka
function openModal(modalId) {
    closeAllModals();
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        modal.style.display = 'block';
    } else {
        console.error('Modal not found:', modalId);
    }
}
    
    function editStockTransaction(transaction) {
        // Set form action URL
        document.getElementById('editStockTransactionForm').action = `/stock-transactions/${transaction.id}`;
        
        // Fill form fields
        document.getElementById('edit_source_warehouse_id').value = transaction.stock_amount_id;
        document.getElementById('edit_quantity').value = transaction.quantity;
        document.getElementById('edit_is_adjustment').checked = transaction.is_adjustment;
        document.getElementById('edit_destination').value = transaction.destination || '';
        document.getElementById('edit_to_warehouse_id').value = transaction.to_warehouse_id || '';
        document.getElementById('edit_note').value = transaction.note || '';
        
        // Toggle to warehouse if needed
        if (transaction.destination === 'transfer') {
            toggleEditToWarehouse('transfer');
        }
        
        openModal('editStockTransactionModal');
    }
    
    function deleteStockTransaction(transaction) {
        document.getElementById('deleteStockTransactionForm').action = `/stock-transactions/${transaction.id}`;
        openModal('deleteStockTransactionModal');
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
        openModal('createStockTransactionModal');
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