@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Product Variant Management</h2>
        <button onclick="openModal('createVariantModal')" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition">
            <i class="fas fa-plus mr-2"></i>Create Variant
        </button>
    </div>

    <!-- Search and Filter Section -->
    <form method="GET" action="{{ route('product-variants.index') }}" class="mb-4">
        <div class="flex flex-wrap items-end gap-4">
            <!-- Search Field -->
            <div class="flex-1 min-w-[250px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative flex items-center">
                    <select 
                        name="search_type" 
                        id="search_type"
                        class="appearance-none bg-white border border-gray-300 rounded-l-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        <option value="code" {{ request('search_type', 'code') == 'code' ? 'selected' : '' }}>Code</option>
                        <option value="base_code" {{ request('search_type') == 'base_code' ? 'selected' : '' }}>Base Code</option>
                        <option value="other_code" {{ request('search_type') == 'other_code' ? 'selected' : '' }}>Other Code</option>
                    </select>
                    
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search..." 
                            value="{{ request('search') }}"
                            class="w-full pl-4 pr-10 py-2 border border-l-0 rounded-r-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Filter -->
            <div class="min-w-[200px]">
                <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select 
                    name="product_id" 
                    id="product_id"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Products</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Color Filter -->
            <div class="min-w-[200px]">
                <label for="color_id" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                <select 
                    name="color_id" 
                    id="color_id"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Colors</option>
                    @foreach($colors as $color)
                        <option value="{{ $color->id }}" {{ request('color_id') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Size Filter -->
            <div class="min-w-[200px]">
                <label for="size_id" class="block text-sm font-medium text-gray-700 mb-1">Size</label>
                <select 
                    name="size_id" 
                    id="size_id"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Sizes</option>
                    @foreach($sizes as $size)
                        <option value="{{ $size->id }}" {{ request('size_id') == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Heel Filter -->
            <div class="min-w-[200px]">
                <label for="heel_id" class="block text-sm font-medium text-gray-700 mb-1">Heel</label>
                <select 
                    name="heel_id" 
                    id="heel_id"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Heels</option>
                    @foreach($heels as $heel)
                        <option value="{{ $heel->id }}" {{ request('heel_id') == $heel->id ? 'selected' : '' }}>{{ $heel->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Gender Filter -->
            <div class="min-w-[200px]">
                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                <select 
                    name="gender" 
                    id="gender"
                    class="w-full appearance-none bg-white border border-gray-300 rounded-md pl-3 pr-8 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                >
                    <option value="">All Genders</option>
                    <option value="man" {{ request('gender') == 'man' ? 'selected' : '' }}>Man</option>
                    <option value="woman" {{ request('gender') == 'woman' ? 'selected' : '' }}>Woman</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2">
                <button 
                    type="submit" 
                    class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md transition h-[42px]"
                >
                    Apply
                </button>
                
                @if(request()->has('search') || request()->has('product_id') || request()->has('color_id') || request()->has('size_id') || request()->has('heel_id') || request()->has('gender'))
                    <a 
                        href="{{ route('product-variants.index') }}" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition h-[42px] flex items-center"
                    >
                        Clear
                    </a>
                @endif
            </div>
        </div>
    </form>

    <!-- Variants Table -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Code</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heel</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gender</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($variants as $variant)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-200 flex items-center justify-center">
                            @if($variant->image)
                                <img src="{{ Storage::url($variant->image) }}" alt="{{ $variant->code }}" 
                                    class="h-full w-full object-cover"
                                    onerror="this.onerror=null;this.parentElement.innerHTML=generateInitials('{{ $variant->code }}')">
                            @else
                                {!! generateInitials($variant->code) !!}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $variant->base_code }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $variant->code ?? $variant->other_code ?? '-' }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $variant->product->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $variant->color->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $variant->size->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $variant->heel->name }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            @if($variant->gender == 'man')
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Man</span>
                            @elseif($variant->gender == 'woman')
                                <span class="px-2 py-1 bg-pink-100 text-pink-800 rounded-full text-xs">Woman</span>
                            @else
                                <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Unisex</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Rp {{ number_format($variant->price, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button onclick='showVariant(@json($variant))' class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick='editVariant(@json($variant))' class="text-primary-600 hover:text-primary-900 mr-3">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick='deleteVariant(@json($variant))' class="text-red-600 hover:text-red-900">
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
        {{ $variants->links() }}
    </div>

    <!-- Create Variant Modal -->
    <div id="createVariantModal" class="{{ $errors->any() ? '' : 'hidden' }} fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Create New Product Variant</h3>
                <button onclick="closeModal('createVariantModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="createVariantForm" method="POST" action="{{ route('product-variants.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Product -->
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700">Product *</label>
                        <select name="product_id" id="product_id" required
                            class="mt-1 block w-full border {{ $errors->has('product_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Color -->
                    <div>
                        <label for="color_id" class="block text-sm font-medium text-gray-700">Color *</label>
                        <select name="color_id" id="color_id" required
                            class="mt-1 block w-full border {{ $errors->has('color_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Color --</option>
                            @foreach($colors as $color)
                                <option value="{{ $color->id }}" {{ old('color_id') == $color->id ? 'selected' : '' }}>{{ $color->name }}</option>
                            @endforeach
                        </select>
                        @error('color_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Size -->
                    <div>
                        <label for="size_id" class="block text-sm font-medium text-gray-700">Size *</label>
                        <select name="size_id" id="size_id" required
                            class="mt-1 block w-full border {{ $errors->has('size_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Size --</option>
                            @foreach($sizes as $size)
                                <option value="{{ $size->id }}" {{ old('size_id') == $size->id ? 'selected' : '' }}>{{ $size->name }}</option>
                            @endforeach
                        </select>
                        @error('size_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Heel -->
                    <div>
                        <label for="heel_id" class="block text-sm font-medium text-gray-700">Heel *</label>
                        <select name="heel_id" id="heel_id" required
                            class="mt-1 block w-full border {{ $errors->has('heel_id') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Heel --</option>
                            @foreach($heels as $heel)
                                <option value="{{ $heel->id }}" {{ old('heel_id') == $heel->id ? 'selected' : '' }}>{{ $heel->name }}</option>
                            @endforeach
                        </select>
                        @error('heel_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                        <select name="gender" id="gender" required
                            class="mt-1 block w-full border {{ $errors->has('gender') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">-- Select Gender --</option>
                            <option value="man" {{ old('gender') == 'man' ? 'selected' : '' }}>Man</option>
                            <option value="woman" {{ old('gender') == 'woman' ? 'selected' : '' }}>Woman</option>
                            <option value="unisex" {{ old('gender') == 'unisex' ? 'selected' : '' }}>Unisex</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Other Code -->
                    <div>
                        <label for="other_code" class="block text-sm font-medium text-gray-700">Other Code</label>
                        <input type="text" name="other_code" id="other_code" value="{{ old('other_code') }}"
                            class="mt-1 block w-full border {{ $errors->has('other_code') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('other_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Price *</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" required
                            class="mt-1 block w-full border {{ $errors->has('price') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Installment Price -->
                    <div>
                        <label for="installment_price" class="block text-sm font-medium text-gray-700">Installment Price *</label>
                        <input type="number" name="installment_price" id="installment_price" value="{{ old('installment_price') }}" required
                            class="mt-1 block w-full border {{ $errors->has('installment_price') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('installment_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Image -->
                    <div class="md:col-span-2">
                        <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                        <input type="file" name="image" id="image"
                            class="mt-1 block w-full border {{ $errors->has('image') ? 'border-red-500' : 'border-gray-300' }} 
                                rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('createVariantModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Save Variant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Show Variant Modal -->
    <div id="showVariantModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Variant Details</h3>
                <button onclick="closeModal('showVariantModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4">
                <!-- Image Section -->
                <div class="flex justify-center">
                    <div class="h-64 w-64 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                        <img id="show_image" src="" alt="Variant Image" class="h-full w-full object-contain">
                    </div>
                </div>
                
                <!-- Code Section -->
                <div class="text-center">
                    <h4 id="show_code" class="text-xl font-bold text-gray-900"></h4>
                    <p id="show_base_code" class="text-sm text-gray-500">Base Code: <span class="font-medium"></span></p>
                </div>
                
                <!-- Details Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Product</p>
                        <p id="show_product" class="text-base font-medium text-gray-900"></p>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Color</p>
                        <p id="show_color" class="text-base font-medium text-gray-900"></p>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Size</p>
                        <p id="show_size" class="text-base font-medium text-gray-900"></p>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Heel</p>
                        <p id="show_heel" class="text-base font-medium text-gray-900"></p>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Price</p>
                        <p id="show_price" class="text-base font-medium text-gray-900"></p>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Installment Price</p>
                        <p id="show_installment_price" class="text-base font-medium text-gray-900"></p>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Created At</p>
                        <p id="show_created_at" class="text-sm text-gray-900"></p>
                    </div>
                    
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-gray-500 mb-1">Last Updated</p>
                        <p id="show_updated_at" class="text-sm text-gray-900"></p>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end pt-5">
                <button onclick="closeModal('showVariantModal')" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Close
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Variant Modal -->
    <div id="editVariantModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-primary-800">Edit Variant</h3>
                <button onclick="closeModal('editVariantModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="editVariantForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <!-- Current Image -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Image</label>
                        <div class="mt-1 flex items-center">
                            <img id="current_image" src="" alt="Current Image" class="h-16 w-16 rounded-md object-cover">
                        </div>
                    </div>

                    <!-- New Image -->
                    <div>
                        <label for="edit_image" class="block text-sm font-medium text-gray-700">New Image</label>
                        <input type="file" name="image" id="edit_image"
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="edit_price" class="block text-sm font-medium text-gray-700">Price *</label>
                        <input type="number" name="price" id="edit_price" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>

                    <!-- Installment Price -->
                    <div>
                        <label for="edit_installment_price" class="block text-sm font-medium text-gray-700">Installment Price *</label>
                        <input type="number" name="installment_price" id="edit_installment_price" required
                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-5">
                    <button type="button" onclick="closeModal('editVariantModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        Update Variant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Variant Modal -->
    <div id="deleteVariantModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-lg font-bold text-red-700">Delete Confirmation</h3>
                <button onclick="closeModal('deleteVariantModal')" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p class="text-gray-800">Are you sure you want to delete variant <strong id="deleteVariantCode"></strong>?</p>
            <form id="deleteVariantForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-end pt-5 space-x-3">
                    <button type="button" onclick="closeModal('deleteVariantModal')" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-sm rounded-md">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md">
                        Yes, Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SlimSelect JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.27.1/slimselect.min.css" rel="stylesheet"></link>

<script>
    // Initialize SlimSelect for filter dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize all filter dropdowns
        new SlimSelect({
            select: '#product_id',
            placeholder: 'All Products',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        new SlimSelect({
            select: '#color_id',
            placeholder: 'All Colors',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        new SlimSelect({
            select: '#size_id',
            placeholder: 'All Sizes',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        new SlimSelect({
            select: '#heel_id',
            placeholder: 'All Heels',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        new SlimSelect({
            select: '#gender',
            placeholder: 'All Genders',
            allowDeselect: true,
            deselectLabel: '<span class="text-red-500">×</span>'
        });

        // Initialize search type dropdown (not searchable)
        new SlimSelect({
            select: '#search_type',
            showSearch: false
        });

        // Initialize dropdowns in create modal
        new SlimSelect({
            select: '#createVariantModal #product_id',
            placeholder: 'Select Product'
        });

        new SlimSelect({
            select: '#createVariantModal #color_id',
            placeholder: 'Select Color'
        });

        new SlimSelect({
            select: '#createVariantModal #size_id',
            placeholder: 'Select Size'
        });

        new SlimSelect({
            select: '#createVariantModal #heel_id',
            placeholder: 'Select Heel'
        });

        new SlimSelect({
            select: '#createVariantModal #gender',
            placeholder: 'Select Gender'
        });
    });

    // Function to show variant details
    function showVariant(variant) {
        // Populate the modal with variant data
        document.getElementById('show_code').textContent = variant.code;
        document.getElementById('show_base_code').querySelector('span').textContent = variant.base_code;
        document.getElementById('show_product').textContent = variant.product.name;
        document.getElementById('show_color').textContent = variant.color.name;
        document.getElementById('show_size').textContent = variant.size.name;
        document.getElementById('show_heel').textContent = variant.heel.name;
        document.getElementById('show_price').textContent = 'Rp ' + variant.price.toLocaleString('id-ID');
        document.getElementById('show_installment_price').textContent = 'Rp ' + variant.installment_price.toLocaleString('id-ID');
        document.getElementById('show_created_at').textContent = new Date(variant.created_at).toLocaleString();
        document.getElementById('show_updated_at').textContent = new Date(variant.updated_at).toLocaleString();
        
        // Set image
        const imageUrl = variant.image ? `/storage/${variant.image}` : '/images/default.jpg';
        document.getElementById('show_image').src = imageUrl;
        
        // Open the modal
        openModal('showVariantModal');
    }

    // Function to edit variant
    function editVariant(variant) {
        // Set form action URL
        document.getElementById('editVariantForm').action = `/product-variants/${variant.id}`;
        
        // Fill form fields
        document.getElementById('edit_price').value = variant.price;
        document.getElementById('edit_installment_price').value = variant.installment_price;
        
        // Set current image
        const imageUrl = variant.image ? `/storage/${variant.image}` : '/images/default.jpg';
        document.getElementById('current_image').src = imageUrl;
        
        // Open the modal
        openModal('editVariantModal');
    }

    // Function to delete variant
    function deleteVariant(variant) {
        document.getElementById('deleteVariantCode').textContent = variant.code;
        document.getElementById('deleteVariantForm').action = `/product-variants/${variant.id}`;
        openModal('deleteVariantModal');
    }

    // Modal control functions
    function closeAllModals() {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
            modal.classList.add('hidden');
        });
    }

    function openModal(modalId) {
        closeAllModals();
        document.getElementById(modalId).classList.remove('hidden');
        
        // Add click event to backdrop to close modal
        document.addEventListener('click', function backdropClick(event) {
            if (event.target.id === modalId) {
                closeModal(modalId);
                document.removeEventListener('click', backdropClick);
            }
        });
        
        // Prevent clicks inside modal content from closing the modal
        const modalContent = document.querySelector(`#${modalId} > div`);
        if (modalContent) {
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    // Function to generate initials avatar
    function generateInitials(code) {
        // Get the first 2 characters of the code
        const initials = code.substring(0, 2).toUpperCase();
        // Generate a random background color based on the code
        const colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 'bg-red-500', 'bg-yellow-500', 'bg-indigo-500'];
        const colorIndex = Math.abs(hashCode(code)) % colors.length;
        const color = colors[colorIndex];
        
        return `<span class="h-full w-full flex items-center justify-center text-white font-bold ${color}">${initials}</span>`;
    }

    // Helper function to generate a hash code from a string
    function hashCode(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        return hash;
    }
</script>

@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        openModal('createVariantModal');
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