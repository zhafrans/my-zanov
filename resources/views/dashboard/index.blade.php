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
                            <h3 class="text-2xl font-bold text-primary-800">$24,780</h3>
                            <p class="text-green-500 text-sm mt-1">+12% from last month</p>
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
                            <h3 class="text-2xl font-bold text-primary-800">1,245</h3>
                            <p class="text-green-500 text-sm mt-1">+8% from last month</p>
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
                            <h3 class="text-2xl font-bold text-primary-800">86</h3>
                            <p class="text-green-500 text-sm mt-1">+5% from last month</p>
                        </div>
                        <div class="bg-primary-100 p-3 rounded-lg">
                            <i class="fas fa-users text-primary-700 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-gray-500">Production Rate</p>
                            <h3 class="text-2xl font-bold text-primary-800">78%</h3>
                            <p class="text-red-500 text-sm mt-1">-3% from last month</p>
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
                        <select class="bg-gray-100 border border-gray-200 rounded px-3 py-1 text-sm">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option selected>Last 90 Days</option>
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
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shoe-prints text-primary-700"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium">ZANOV Runner Pro</p>
                                <p class="text-sm text-gray-500">Athletic Shoes</p>
                            </div>
                            <div class="text-primary-700 font-bold">$89.99</div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shoe-prints text-primary-700"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium">ZANOV Classic</p>
                                <p class="text-sm text-gray-500">Casual Shoes</p>
                            </div>
                            <div class="text-primary-700 font-bold">$69.99</div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shoe-prints text-primary-700"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium">ZANOV Trail Master</p>
                                <p class="text-sm text-gray-500">Hiking Shoes</p>
                            </div>
                            <div class="text-primary-700 font-bold">$109.99</div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shoe-prints text-primary-700"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium">ZANOV Urban</p>
                                <p class="text-sm text-gray-500">Streetwear</p>
                            </div>
                            <div class="text-primary-700 font-bold">$79.99</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders and Production Status -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-primary-800">Recent Orders</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-700">#ORD-1001</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John Smith</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$129.98</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-700">#ORD-1002</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Sarah Johnson</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Processing</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$89.99</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-700">#ORD-1003</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Michael Brown</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Shipped</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$219.97</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-primary-700">#ORD-1004</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Emily Davis</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$69.99</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Production Status -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-lg font-semibold text-primary-800">Production Status</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-primary-800">ZANOV Runner Pro</span>
                                <span class="text-sm font-medium text-primary-800">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-primary-600 h-2.5 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-primary-800">ZANOV Classic</span>
                                <span class="text-sm font-medium text-primary-800">90%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-primary-600 h-2.5 rounded-full" style="width: 90%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-primary-800">ZANOV Trail Master</span>
                                <span class="text-sm font-medium text-primary-800">60%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-primary-600 h-2.5 rounded-full" style="width: 60%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-primary-800">ZANOV Urban</span>
                                <span class="text-sm font-medium text-primary-800">45%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-primary-600 h-2.5 rounded-full" style="width: 45%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        
@endsection