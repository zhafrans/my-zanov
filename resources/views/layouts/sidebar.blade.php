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
            
            <nav class="space-y-2">
                        <a href="/dashboard"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('dashboard') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-tachometer-alt"></i>
                            <span class="sidebar-text">Dashboard</span>
                        </a>

                        <a href="/sales"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('sales*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-chart-line"></i>
                            <span class="sidebar-text">Sales</span>
                        </a>
                        
                        <a href="/products"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('products*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-shopping-bag"></i>
                            <span class="sidebar-text">Products</span>
                        </a>

                        <a href="/inventory"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('inventory*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-warehouse"></i>
                            <span class="sidebar-text">Inventory</span>
                        </a>

                        <a href="/customers"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('customers*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-users"></i>
                            <span class="sidebar-text">Customers</span>
                        </a>


                        <a href="/production"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('production*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-cogs"></i>
                            <span class="sidebar-text">Production</span>
                        </a>

                        <a href="/settings"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('settings*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-cog"></i>
                            <span class="sidebar-text">Settings</span>
                        </a>

                        <a href="/users"
                        class="nav-item flex items-center space-x-3 p-3 rounded-lg
                                {{ Request::is('users*') ? 'bg-primary-700 text-white' : 'text-primary-200 hover:bg-primary-700 hover:text-white' }}">
                            <i class="fas fa-user"></i>
                            <span class="sidebar-text">User</span>
                        </a>

            </nav>
        </div>
        
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-primary-700">
            <button onclick="toggleSidebar()" class="flex items-center space-x-3 p-2 rounded-lg text-primary-200 hover:bg-primary-700 hover:text-white w-full">
                <i class="fas fa-chevron-left"></i>
                <span class="sidebar-text">Collapse</span>
            </button>
        </div>
    </div>