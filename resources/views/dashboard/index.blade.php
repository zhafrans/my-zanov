@extends('layouts.app')

@section('content')
<!-- Dashboard Content -->
<main class="p-4">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500">Total Sales</p>
                    <h3 class="text-2xl font-bold text-primary-800">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
                    <p class="{{ $salesChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                        {{ $salesChange >= 0 ? '+' : '' }}{{ $salesChange }}% from last month
                    </p>
                </div>
                <div class="bg-primary-100 p-3 rounded-lg">
                    <i class="fas fa-dollar-sign text-primary-700 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500">Products Sold</p>
                    <h3 class="text-2xl font-bold text-primary-800">{{ number_format($productsSold) }}</h3>
                    <p class="{{ $productsChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                        {{ $productsChange >= 0 ? '+' : '' }}{{ $productsChange }}% from last month
                    </p>
                </div>
                <div class="bg-primary-100 p-3 rounded-lg">
                    <i class="fas fa-shoe-prints text-primary-700 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500">New Customers</p>
                    <h3 class="text-2xl font-bold text-primary-800">{{ $newCustomers }}</h3>
                    <p class="{{ $customersChange >= 0 ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                        {{ $customersChange >= 0 ? '+' : '' }}{{ $customersChange }}% from last month
                    </p>
                </div>
                <div class="bg-primary-100 p-3 rounded-lg">
                    <i class="fas fa-users text-primary-700 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500">Payment Completion</p>
                    <h3 class="text-2xl font-bold text-primary-800">{{ $productionRate }}%</h3>
                    <p class="{{ $productionRate >= $lastMonthRate ? 'text-green-500' : 'text-red-500' }} text-sm mt-1">
                        {{ $productionRate >= $lastMonthRate ? '+' : '' }}{{ $productionRate - $lastMonthRate }}% from last month
                    </p>
                </div>
                <div class="bg-primary-100 p-3 rounded-lg">
                    <i class="fas fa-cogs text-primary-700 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Sales Chart -->
        <div class="bg-white rounded-lg shadow p-6 lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-primary-800">Sales Overview</h2>
                <select class="bg-gray-100 border border-gray-200 rounded px-3 py-1 text-sm" id="chartRange">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="90" selected>Last 90 Days</option>
                </select>
            </div>
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-primary-800 mb-4">Top Selling Products</h2>
            <div class="space-y-4">
                @foreach($topProducts as $product)
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shoe-prints text-primary-700"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">{{ $product->other_code }}</p>
                        <p class="text-sm text-gray-500">{{ $product->sales_count }} sold</p>
                    </div>
                    <div class="text-primary-700 font-bold">Rp {{ number_format($product->total_sales, 0, ',', '.') }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <!-- Recent Orders and Production Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-primary-800">Recent Transactions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-700">#{{ $transaction->invoice }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->customer->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($transaction->status == 'paid')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                @elseif($transaction->status == 'installment')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Installment</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                Rp {{ number_format($transaction->deal_price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Payment Status -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-lg font-semibold text-primary-800">Payment Status</h2>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-primary-800">Paid Transactions</span>
                        <span class="text-sm font-medium text-primary-800">
                            {{ $productionRate }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $productionRate }}%"></div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-primary-800">Installment Plans</span>
                        <span class="text-sm font-medium text-primary-800">
                            {{ 100 - $productionRate }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                        <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ 100 - $productionRate }}%"></div>
                    </div>
                </div>
                
                <div class="text-sm text-gray-500 mt-6">
                    <p><span class="font-medium">Total Outstanding:</span> Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
                    <p class="mt-2"><span class="font-medium">Total Potential:</span> Rp {{ number_format($totalPotential, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare chart data
        const salesData = @json($salesData);
        
        const dates = salesData.map(item => item.date);
        const amounts = salesData.map(item => item.total);
        
        // Create chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Sales (Rp)',
                    data: amounts,
                    backgroundColor: 'rgba(59, 130, 246, 0.05)',
                    borderColor: 'rgba(59, 130, 246, 0.8)',
                    borderWidth: 2,
                    tension: 0.1,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Rp ' + context.raw.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
        
        // Chart range selector
        document.getElementById('chartRange').addEventListener('change', function() {
            const days = this.value;
            // In a real app, you would fetch new data via AJAX here
            // For now we'll just filter the existing data
            const filteredData = salesData.filter(item => {
                const itemDate = new Date(item.date);
                const cutoffDate = new Date();
                cutoffDate.setDate(cutoffDate.getDate() - days);
                return itemDate >= cutoffDate;
            });
            
            salesChart.data.labels = filteredData.map(item => item.date);
            salesChart.data.datasets[0].data = filteredData.map(item => item.total);
            salesChart.update();
        });
    });
</script>
@endpush
@endsection