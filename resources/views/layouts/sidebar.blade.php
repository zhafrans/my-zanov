<!-- Sidebar -->
<div class="sidebar bg-primary-800 text-white shadow-lg">
    <div class="p-4 flex items-center space-x-2 border-b border-primary-700">
        <div class="bg-primary-600 p-2 rounded-lg">
            <i class="fas fa-shoe-prints text-white text-xl"></i>
        </div>
        <span class="logo-text text-xl font-bold">MyZANOV</span>
    </div>
    
    <div class="p-4">
        <div class="flex items-center space-x-3 mb-6">
            <div class="w-10 h-10 rounded-full bg-primary-600 flex items-center justify-center">
                <i class="fas fa-user text-white"></i>
            </div>
            <div class="sidebar-text">
                <p class="font-medium">{{ Auth::user()->name }}</p>
                <p class="text-primary-300 text-sm">{{ Auth::user()->role->name }}</p>
            </div>
        </div>
        
        <nav class="space-y-1">
            <!-- Dashboard (no grouping) -->
            <a href="/dashboard"
                class="nav-item flex items-center space-x-3 p-3 rounded-lg
                        {{ Request::is('dashboard') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                <i class="fas fa-tachometer-alt"></i>
                <span class="sidebar-text">Dashboard</span>
            </a>

            <!-- User Management Group -->
            <div class="sidebar-group">
                <div class="group-header flex items-center justify-between p-3 rounded-lg cursor-pointer hover:bg-primary-700"
                    onclick="toggleGroup('user-management')">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-users-cog"></i>
                        <span class="sidebar-text font-medium">User Management</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="user-management-arrow"></i>
                </div>
                <div class="group-items pl-8" id="user-management-group">
                    <a href="/users"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('users*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'user-management')">
                        <i class="fas fa-user text-sm"></i>
                        <span class="sidebar-text text-sm">Users</span>
                    </a>
                </div>
            </div>

            <!-- Master Data Group -->
            <div class="sidebar-group">
                <div class="group-header flex items-center justify-between p-3 rounded-lg cursor-pointer hover:bg-primary-700"
                    onclick="toggleGroup('master-data')">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-database"></i>
                        <span class="sidebar-text font-medium">Master Data</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="master-data-arrow"></i>
                </div>
                <div class="group-items pl-8" id="master-data-group">
                    <a href="/warehouses"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('warehouses*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'master-data')">
                        <i class="fas fa-warehouse text-sm"></i>
                        <span class="sidebar-text text-sm">Warehouses</span>
                    </a>
                    <a href="/vehicles"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('vehicles*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'master-data')">
                        <i class="fas fa-car text-sm"></i>
                        <span class="sidebar-text text-sm">Vehicles</span>
                    </a>
                    <a href="/sizes"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('sizes*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'master-data')">
                        <i class="fas fa-ruler-combined text-sm"></i>
                        <span class="sidebar-text text-sm">Sizes</span>
                    </a>
                    <a href="/colors"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('colors*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'master-data')">
                        <i class="fas fa-palette text-sm"></i>
                        <span class="sidebar-text text-sm">Colors</span>
                    </a>
                    <a href="/products"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('products*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'master-data')">
                        <i class="fas fa-shoe-prints text-sm"></i>
                        <span class="sidebar-text text-sm">Product Models</span>
                    </a>
                    <a href="/heels"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('heels*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'master-data')">
                        <i class="fas fa-shapes text-sm"></i>
                        <span class="sidebar-text text-sm">Heels</span>
                    </a>
                </div>
            </div>

            <!-- Catalogue Group -->
            <div class="sidebar-group">
                <div class="group-header flex items-center justify-between p-3 rounded-lg cursor-pointer hover:bg-primary-700"
                    onclick="toggleGroup('catalogue')">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-box-open"></i>
                        <span class="sidebar-text font-medium">Catalogue</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="catalogue-arrow"></i>
                </div>
                <div class="group-items pl-8" id="catalogue-group">
                    <a href="/product-variants"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('product-variants*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'catalogue')">
                        <i class="fas fa-shopping-bag text-sm"></i>
                        <span class="sidebar-text text-sm">Product Variants</span>
                    </a>
                </div>
            </div>

            <!-- Stocks Group -->
            <div class="sidebar-group">
                <div class="group-header flex items-center justify-between p-3 rounded-lg cursor-pointer hover:bg-primary-700"
                    onclick="toggleGroup('stocks')">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-boxes"></i>
                        <span class="sidebar-text font-medium">Stocks</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="stocks-arrow"></i>
                </div>
                <div class="group-items pl-8" id="stocks-group">
                    <a href="/stock-amounts"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('stock-amounts*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'stocks')">
                        <i class="fas fa-box text-sm"></i>
                        <span class="sidebar-text text-sm">Stock Amounts</span>
                    </a>
                    <a href="/stock-transactions"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('stock-transactions*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'stocks')">
                        <i class="fas fa-arrows-rotate text-sm"></i>
                        <span class="sidebar-text text-sm">Stock Transactions</span>
                    </a>
                </div>
            </div>

            <!-- Sales Group -->
            <div class="sidebar-group">
                <div class="group-header flex items-center justify-between p-3 rounded-lg cursor-pointer hover:bg-primary-700"
                    onclick="toggleGroup('sales')">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="sidebar-text font-medium">Sales</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="sales-arrow"></i>
                </div>
                <div class="group-items pl-8" id="sales-group">
                    <a href="/customers"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                                {{ Request::is('customers*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'sales')">
                        <i class="fas fa-users text-sm"></i>
                        <span class="sidebar-text text-sm">Customers</span>
                    </a>
                    <a href="/transactions"
                        class="nav-item flex items-center space-x-3 p-2 rounded-lg
                            {{ Request::is('transactions*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}"
                        onclick="keepGroupOpen(event, 'sales')">
                        <i class="fas fa-file-invoice-dollar text-sm"></i>
                        <span class="sidebar-text text-sm">Transactions</span>
                    </a>
                </div>
            </div>
        </nav>
    </div>
    
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-primary-700">
        <button onclick="toggleSidebar()" class="flex items-center space-x-3 p-2 rounded-lg text-primary-200 hover:bg-primary-700 hover:text-white w-full">
            <i class="fas fa-chevron-left"></i>
            <span class="sidebar-text">Collapse</span>
        </button>
    </div>
</div>

<script>
    // Toggle sidebar groups
    function toggleGroup(groupId) {
        const group = document.getElementById(`${groupId}-group`);
        const arrow = document.getElementById(`${groupId}-arrow`);
        
        group.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    // Keep group open when clicking on menu items
    function keepGroupOpen(event, groupId) {
        // Prevent the default behavior (just in case)
        event.preventDefault();
        
        // Make sure the group is open
        const group = document.getElementById(`${groupId}-group`);
        const arrow = document.getElementById(`${groupId}-arrow`);
        
        group.classList.remove('hidden');
        arrow.classList.add('rotate-180');
        
        // Then proceed with the navigation
        window.location.href = event.currentTarget.href;
    }

    // Initialize groups - you can set some to be open by default
    document.addEventListener('DOMContentLoaded', function() {
        // Example: Open User Management by default
        // document.getElementById('user-management-group').classList.remove('hidden');
        // document.getElementById('user-management-arrow').classList.add('rotate-180');
        
        // Or keep all closed by default
        document.querySelectorAll('.group-items').forEach(group => {
            group.classList.add('hidden');
        });
    });

    // Toggle sidebar collapse (existing function)
    function toggleSidebar() {
        // Your existing sidebar toggle logic
    }
</script>

<style>
    .rotate-180 {
        transform: rotate(180deg);
    }
    .group-items {
        transition: all 0.3s ease;
        overflow: hidden;
    }
</style>