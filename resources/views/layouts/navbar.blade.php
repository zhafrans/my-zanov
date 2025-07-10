<!-- Top Navigation -->
<header class="bg-white shadow-sm p-4 flex justify-between items-center sticky top-0 z-10">
    <div class="flex items-center space-x-4">
        <button id="mobile-menu-button" class="md:hidden text-primary-700">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h1 class="text-xl font-bold text-primary-800">@yield('pageTitle', 'MyZANOV Dashboard')</h1>
    </div>
    <div class="flex items-center space-x-4">
        {{-- <div class="relative">
            <button class="text-primary-700 hover:text-primary-900">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
            </button>
        </div>
        <div class="relative">
            <button class="text-primary-700 hover:text-primary-900">
                <i class="fas fa-envelope text-xl"></i>
                <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span>
            </button>
        </div> --}}
        <div class="relative">
            <button id="profile-menu-button" class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center hover:bg-primary-200 transition">
                <i class="fas fa-user text-primary-700"></i>
            </button>
            
            <!-- Dropdown Menu (hidden by default) -->
            <div id="profile-menu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20">
                {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">Profile</a> --}}
                {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">Settings</a> --}}
                <form method="GET" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-primary-50">Logout</button>
                </form>
            </div>
        </div>
    </div>
</header>

<script>
    // Toggle profile dropdown
    document.getElementById('profile-menu-button').addEventListener('click', function() {
        const menu = document.getElementById('profile-menu');
        menu.classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const profileButton = document.getElementById('profile-menu-button');
        const profileMenu = document.getElementById('profile-menu');
        
        if (!profileButton.contains(event.target) && !profileMenu.contains(event.target)) {
            profileMenu.classList.add('hidden');
        }
    });
</script>