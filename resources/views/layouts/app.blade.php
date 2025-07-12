<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyZANOV - Shoe Management Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar {
            transition: all 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 250px;
            overflow-y: auto;
            z-index: 1000;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        .sidebar.collapsed .logo-text {
            display: none;
        }
        .sidebar.collapsed .nav-item {
            justify-content: center;
        }
        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        .main-content.collapsed {
            margin-left: 70px;
        }
        .chart-container {
            height: 300px;
        }
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }
            .sidebar.show {
                left: 0;
            }
            .sidebar.collapsed {
                left: -70px;
            }
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100">
    
    
    <!-- Main Content -->
    <div class="main-content">
        
        @include('layouts.sidebar')

        @include('layouts.navbar')
        
        @yield('content')
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const collapseIcon = document.querySelector('.collapse-icon');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');  // Ini yang penting!
            
            // Rotasi icon dan resize chart
            if (sidebar.classList.contains('collapsed')) {
                collapseIcon.style.transform = 'rotate(180deg)';
            } else {
                collapseIcon.style.transform = 'rotate(0deg)';
            }
            
            setTimeout(() => {
                if (window.salesChart) {
                    window.salesChart.resize();
                }
            }, 300);
        }
        
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');

            // Jika sedang collapsed, hilangkan dulu collapsed supaya tampil di mobile
            if (window.innerWidth <= 768 && sidebar.classList.contains('collapsed')) {
                sidebar.classList.remove('collapsed');
            }

            sidebar.classList.toggle('show');
        });
        
        // Sales Chart
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                datasets: [{
                    label: 'Sales',
                    data: [6500, 5900, 8000, 8100, 8600, 9250, 10000, 11000, 12000],
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderColor: 'rgba(249, 115, 22, 1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        
        // Resize chart on sidebar toggle
        function resizeChart() {
            salesChart.resize();
        }
        
        // Add event listener for sidebar toggle
        document.querySelectorAll('[onclick="toggleSidebar()"]').forEach(button => {
            button.addEventListener('click', resizeChart);
        });
        
        // Resize chart on window resize
        window.addEventListener('resize', resizeChart);
    </script>
</body>
</html>