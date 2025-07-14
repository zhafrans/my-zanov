@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary-800">Transaction Details</h2>
        <div class="flex space-x-2">
            <a href="{{ route('transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md transition">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
            <button onclick="window.print()" class="bg-blue-100 hover:bg-blue-200 text-blue-800 px-4 py-2 rounded-md transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <!-- Transaction Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-lg font-semibold text-gray-800">Invoice #{{ $transaction->invoice }}</h3>
                    <p class="text-sm text-gray-500">
                        Date: {{ $transaction->created_at->format('d M Y H:i') }}
                    </p>
                </div>
                <div class="flex flex-col items-end">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold 
                        {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                    <span class="mt-1 px-3 py-1 rounded-full text-sm font-semibold 
                        {{ $transaction->payment_type === 'credit' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                        {{ ucfirst($transaction->payment_type) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Customer and Seller Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-2">Customer Information</h4>
                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="font-semibold">{{ $transaction->customer->name }}</p>
                    <p class="text-sm text-gray-600">Customer ID: {{ $transaction->customer->id }}</p>
                    <!-- Add more customer details if available -->
                </div>
            </div>
            
            <div>
                <h4 class="text-md font-medium text-gray-700 mb-2">Seller Information</h4>
                <div class="bg-gray-50 p-4 rounded-md">
                    <p class="font-semibold">{{ $transaction->seller->name }}</p>
                    <p class="text-sm text-gray-600">Seller ID: {{ $transaction->seller->id }}</p>
                </div>
            </div>
        </div>

        <!-- Transaction Items -->
        <div class="px-6 py-4">
            <h4 class="text-md font-medium text-gray-700 mb-3">Items Purchased</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Variant</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $item->variant->product->name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $item->variant->code ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $item->snapshot_name ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($item->snapshot_price, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $item->quantity }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($item->snapshot_price * $item->quantity, 2) }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-end">
                <div class="w-full md:w-1/3">
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <span class="font-medium">Subtotal:</span>
                        <span class="text-right">
                            {{ number_format($transaction->items->sum(function($item) {
                                return $item->snapshot_price * $item->quantity;
                            }), 2) }}
                        </span>
                    </div>
                    
                    @if($transaction->payment_type === 'credit')
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <span class="font-medium">Payment Method:</span>
                        <span class="text-right">Credit</span>
                    </div>
                    @endif
                    
                    @if($transaction->status === 'installment' && $transaction->installments->isNotEmpty())
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <span class="font-medium">Installment Amount:</span>
                        <span class="text-right">
                            {{ number_format($transaction->installments->first()->installment_amount, 2) }}
                        </span>
                    </div>
                    @endif
                    
                    @if($transaction->outstanding)
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <span class="font-medium">Outstanding Amount:</span>
                        <span class="text-right">
                            {{ number_format($transaction->outstanding->outstanding_amount, 2) }}
                        </span>
                    </div>
                    @endif
                    
                    <div class="grid grid-cols-2 gap-2 mt-4 pt-2 border-t border-gray-200">
                        <span class="font-semibold">Total Amount:</span>
                        <span class="text-right font-semibold">
                            {{ number_format($transaction->items->sum(function($item) {
                                return $item->snapshot_price * $item->quantity;
                            }), 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Installment History (if applicable) -->
    @if($transaction->status === 'installment' && $transaction->installments->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-700">Installment History</h4>
        </div>
        <div class="px-6 py-4">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($transaction->installments as $installment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $installment->created_at->format('d M Y H:i') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ number_format($installment->installment_amount, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $installment->status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($installment->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $installment->notes ?? '-' }}
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Transaction Notes -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h4 class="text-md font-medium text-gray-700">Additional Notes</h4>
        </div>
        <div class="px-6 py-4">
            <p class="text-gray-700">
                {{ $transaction->notes ?? 'No additional notes for this transaction.' }}
            </p>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        body * {
            visibility: hidden;
        }
        .container, .container * {
            visibility: visible;
        }
        .container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection