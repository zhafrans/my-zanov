@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Customer Details</h2>
        <a href="{{ route('customers.index') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Customers
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="flex items-center space-x-4 mb-6">
                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-primary-100 flex items-center justify-center">
                    <i class="fas fa-user text-primary-700"></i>
                </div>
                <div>
                    <h4 class="text-lg font-medium text-gray-900">{{ $customer->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $customer->code }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h3>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Address</p>
                        <p class="text-sm text-gray-900 mt-1">{{ $customer->address }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->email ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Location Info -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Location Information</h3>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Province</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->province->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">City</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->city->name ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Subdistrict</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->subdistrict->name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Village</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->village->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Timestamps -->
                <div class="col-span-2 space-y-4 border-t pt-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created At</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->created_at->format('d M Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Last Updated</p>
                            <p class="text-sm text-gray-900 mt-1">{{ $customer->updated_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 pt-6">
                <a href="{{ route('customers.edit', $customer->id) }}" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Edit Customer
                </a>
                <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" onclick="return confirm('Are you sure you want to delete this customer?')">
                        Delete Customer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection