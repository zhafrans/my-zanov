@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Create New Customer</h2>
        <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Customers
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <form method="POST" action="{{ route('customers.store') }}" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Code -->
                <div class="col-span-1">
                    <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}"
                        class="mt-1 block w-full border {{ $errors->has('code') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div class="col-span-1">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="mt-1 block w-full border {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                    <textarea name="address" id="address" rows="3"
                        class="mt-1 block w-full border {{ $errors->has('address') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location Fields -->
                <div class="col-span-1">
                    <label for="province_id" class="block text-sm font-medium text-gray-700">Province</label>
                    <select name="province_id" id="province_id" onchange="getCities(this.value)"
                        class="mt-1 block w-full border {{ $errors->has('province_id') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Province --</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}" {{ old('province_id') == $province->id ? 'selected' : '' }}>{{ $province->name }}</option>
                        @endforeach
                    </select>
                    @error('province_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1">
                    <label for="city_id" class="block text-sm font-medium text-gray-700">City</label>
                    <select name="city_id" id="city_id" onchange="getSubdistricts(this.value)"
                        class="mt-1 block w-full border {{ $errors->has('city_id') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select City --</option>
                        @if(old('province_id'))
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>{{ $city->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('city_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1">
                    <label for="subdistrict_id" class="block text-sm font-medium text-gray-700">Subdistrict</label>
                    <select name="subdistrict_id" id="subdistrict_id" onchange="getVillages(this.value)"
                        class="mt-1 block w-full border {{ $errors->has('subdistrict_id') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Subdistrict --</option>
                        @if(old('city_id'))
                            @foreach($subdistricts as $subdistrict)
                                <option value="{{ $subdistrict->id }}" {{ old('subdistrict_id') == $subdistrict->id ? 'selected' : '' }}>{{ $subdistrict->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('subdistrict_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-1">
                    <label for="village_id" class="block text-sm font-medium text-gray-700">Village</label>
                    <select name="village_id" id="village_id"
                        class="mt-1 block w-full border {{ $errors->has('village_id') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Select Village --</option>
                        @if(old('subdistrict_id'))
                            @foreach($villages as $village)
                                <option value="{{ $village->id }}" {{ old('village_id') == $village->id ? 'selected' : '' }}>{{ $village->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    @error('village_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Contact Fields -->
                <div class="col-span-1">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                        class="mt-1 block w-full border {{ $errors->has('phone') ? 'border-red-500' : 'border-gray-300' }} 
                            rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('customers.index') }}"
                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Cancel
                </a>
                <button type="submit"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Save Customer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// AJAX functions for location dropdowns
async function getCities(provinceId) {
    if (!provinceId) {
        document.getElementById('city_id').innerHTML = '<option value="">-- Select City --</option>';
        document.getElementById('subdistrict_id').innerHTML = '<option value="">-- Select Subdistrict --</option>';
        document.getElementById('village_id').innerHTML = '<option value="">-- Select Village --</option>';
        return;
    }

    try {
        const response = await fetch(`/api/cities?province_id=${provinceId}`);
        const data = await response.json();
        
        let options = '<option value="">-- Select City --</option>';
        data.forEach(city => {
            options += `<option value="${city.id}">${city.name}</option>`;
        });
        document.getElementById('city_id').innerHTML = options;
        
        // Reset dependent dropdowns
        document.getElementById('subdistrict_id').innerHTML = '<option value="">-- Select Subdistrict --</option>';
        document.getElementById('village_id').innerHTML = '<option value="">-- Select Village --</option>';
    } catch (error) {
        console.error('Error loading cities:', error);
    }
}

async function getSubdistricts(cityId) {
    if (!cityId) {
        document.getElementById('subdistrict_id').innerHTML = '<option value="">-- Select Subdistrict --</option>';
        document.getElementById('village_id').innerHTML = '<option value="">-- Select Village --</option>';
        return;
    }

    try {
        const response = await fetch(`/api/subdistricts?city_id=${cityId}`);
        const data = await response.json();
        
        let options = '<option value="">-- Select Subdistrict --</option>';
        data.forEach(subdistrict => {
            options += `<option value="${subdistrict.id}">${subdistrict.name}</option>`;
        });
        document.getElementById('subdistrict_id').innerHTML = options;
        
        // Reset dependent dropdown
        document.getElementById('village_id').innerHTML = '<option value="">-- Select Village --</option>';
    } catch (error) {
        console.error('Error loading subdistricts:', error);
    }
}

async function getVillages(subdistrictId) {
    if (!subdistrictId) {
        document.getElementById('village_id').innerHTML = '<option value="">-- Select Village --</option>';
        return;
    }

    try {
        const response = await fetch(`/api/villages?subdistrict_id=${subdistrictId}`);
        const data = await response.json();
        
        let options = '<option value="">-- Select Village --</option>';
        data.forEach(village => {
            options += `<option value="${village.id}">${village.name}</option>`;
        });
        document.getElementById('village_id').innerHTML = options;
    } catch (error) {
        console.error('Error loading villages:', error);
    }
}

// Initialize dropdowns if there are old values
document.addEventListener('DOMContentLoaded', function() {
    @if(old('province_id'))
        getCities({{ old('province_id') }}).then(() => {
            @if(old('city_id'))
                document.getElementById('city_id').value = {{ old('city_id') }};
                getSubdistricts({{ old('city_id') }}).then(() => {
                    @if(old('subdistrict_id'))
                        document.getElementById('subdistrict_id').value = {{ old('subdistrict_id') }};
                        getVillages({{ old('subdistrict_id') }}).then(() => {
                            @if(old('village_id'))
                                document.getElementById('village_id').value = {{ old('village_id') }};
                            @endif
                        });
                    @endif
                });
            @endif
        });
    @endif
});
</script>
@endsection