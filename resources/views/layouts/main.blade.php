<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SmartPOS')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <style>
        :root {
            --primary-color: #4f46e5;
            --secondary-color: #64748b;
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 70px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }
        
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: white;
            overflow-x: hidden;
            overflow-y: auto;
            z-index: 1000;
            transition: all 0.3s ease;
            padding-right: 10px;
            box-sizing: border-box;
        }
        
        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }
        
        .sidebar.collapsed .sidebar-brand-text,
        .sidebar.collapsed .sidebar-menu a span,
        .sidebar.collapsed .sidebar-menu .text-muted,
        .sidebar.collapsed .sidebar-menu .badge {
            display: none;
        }
        
        .sidebar.collapsed .sidebar-menu a {
            justify-content: center;
            padding: 0.75rem;
        }
        
        .sidebar.collapsed .sidebar-menu i {
            margin-right: 0;
            font-size: 1.25rem;
        }
        
        .sidebar-toggle {
            background: rgba(255,255,255,0.1);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            cursor: pointer;
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        .sidebar-toggle:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .sidebar-toggle i {
            font-size: 0.9rem;
        }
        
        .sidebar.collapsed .sidebar-toggle i {
            transform: rotate(180deg);
        }
        
        .sidebar-brand {
            padding: 1rem 1.5rem;
            font-size: 1.25rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .sidebar-brand-text {
            white-space: nowrap;
        }
        
        .sidebar-menu {
            padding: 1rem 0;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left-color: var(--primary-color);
        }
        
        .sidebar-menu i {
            width: 2rem;
            margin-right: 0.5rem;
        }
        
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            transition: margin-left 0.3s ease;
        }
        
        body.sidebar-collapsed .main-content {
            margin-left: var(--sidebar-collapsed-width);
        }
        
        .top-bar {
            background: white;
            padding: 1rem 1.5rem;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mobile-toggle {
            display: none;
        }
        
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
            border-color: #4338ca;
        }
        
        .stat-card {
            border-radius: 0.5rem;
            padding: 1.5rem;
            color: white;
        }
        
        .stat-card.primary { background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); }
        .stat-card.success { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-card.warning { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stat-card.danger { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
        
        .table th {
            font-weight: 600;
            color: #374151;
            background: #f9fafb;
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f1f5f9;
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        .dropdown-menu {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .alert {
            border: none;
            border-radius: 0.5rem;
        }
        
        .pos-layout {
            display: flex;
            gap: 1rem;
            height: calc(100vh - 200px);
        }
        
        .pos-products {
            flex: 2;
            overflow-y: auto;
        }
        
        .pos-cart {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .pos-cart-items {
            flex: 1;
            overflow-y: auto;
        }
        
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 1rem;
        }
        
        .product-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .product-card:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .receipt {
            max-width: 80mm;
            margin: 0 auto;
            padding: 1rem;
            background: white;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
        }
        
        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #000;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .receipt-items {
            border-bottom: 1px dashed #000;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
        
        .receipt-total {
            font-weight: bold;
            font-size: 14px;
        }
        
        .sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .mobile-toggle {
                display: block;
            }
            
            .sidebar-backdrop.show {
                display: block;
            }
            
            .top-bar {
                padding: 0.75rem 1rem;
                margin: -1rem -1rem 1rem -1rem;
            }
            
            .main-content {
                padding: 1rem;
            }
            
            .pos-layout {
                flex-direction: column;
                height: auto;
            }
            
            .pos-products, .pos-cart {
                flex: none;
                width: 100%;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
            
            .stat-card {
                padding: 1rem;
            }
            
            .stat-card h4 {
                font-size: 1.25rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .page-title {
                font-size: 1.25rem;
            }
            
            .card-header {
                padding: 0.75rem 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                padding: 0.375rem 0.75rem;
                font-size: 0.875rem;
            }
            
            .form-control, .form-select {
                font-size: 0.875rem;
            }
            
            .table {
                font-size: 0.875rem;
            }
            
            .product-card {
                padding: 0.5rem;
            }
            
            .product-card h6 {
                font-size: 0.75rem;
            }
        }
        
        @media print {
            .sidebar, .top-bar, .btn-print-hide { display: none !important; }
            .main-content { margin-left: 0 !important; }
            body { background: white !important; }
            .card { box-shadow: none !important; }
        }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <div class="sidebar-backdrop" onclick="toggleSidebar()"></div>
    
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <span class="sidebar-brand-text">
                <i class="fas fa-cash-register"></i> SmartPOS
            </span>
            <button class="sidebar-toggle" onclick="toggleSidebarCollapse()">
                <i class="fas fa-chevron-left"></i>
            </button>
        </div>
        <div class="sidebar-menu">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> <span>Dashboard</span>
            </a>
            <a href="{{ route('pos.create') }}" class="{{ request()->routeIs('pos.create') ? 'active' : '' }}">
                <i class="fas fa-calculator"></i> <span>POS Screen</span>
            </a>
            <div class="text-uppercase text-muted small px-3 mt-3 mb-1">Inventory</div>
            <a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">
                <i class="fas fa-box"></i> <span>Products</span>
            </a>
            <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> <span>Categories</span>
            </a>
            <a href="{{ route('brands.index') }}" class="{{ request()->routeIs('brands.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> <span>Brands</span>
            </a>
            <a href="{{ route('stock.index') }}" class="{{ request()->routeIs('stock.*') ? 'active' : '' }}">
                <i class="fas fa-warehouse"></i> <span>Stock</span>
            </a>
            <div class="text-uppercase text-muted small px-3 mt-3 mb-1">People</div>
            <a href="{{ route('customers.index') }}" class="{{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> <span>Customers</span>
            </a>
            <a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                <i class="fas fa-truck"></i> <span>Suppliers</span>
            </a>
            <div class="text-uppercase text-muted small px-3 mt-3 mb-1">Sales</div>
            <a href="{{ route('sales.index') }}" class="{{ request()->routeIs('sales.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-cart"></i> <span>Sales</span>
            </a>
            <a href="{{ route('purchases.index') }}" class="{{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                <i class="fas fa-shopping-bag"></i> <span>Purchases</span>
            </a>
            <div class="text-uppercase text-muted small px-3 mt-3 mb-1">Reports</div>
            <a href="{{ route('reports.daily-sales') }}" class="{{ request()->routeIs('reports.daily-sales') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> <span>Daily Sales</span>
            </a>
            <a href="{{ route('reports.monthly-sales') }}" class="{{ request()->routeIs('reports.monthly-sales') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> <span>Monthly Sales</span>
            </a>
            <a href="{{ route('reports.profit') }}" class="{{ request()->routeIs('reports.profit') ? 'active' : '' }}">
                <i class="fas fa-coins"></i> <span>Profit Report</span>
            </a>
            <a href="{{ route('reports.inventory') }}" class="{{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list"></i> <span>Inventory</span>
            </a>
            <a href="{{ route('reports.warranty') }}" class="{{ request()->routeIs('reports.warranty*') ? 'active' : '' }}">
                <i class="fas fa-shield-alt"></i> <span>Warranty</span>
            </a>
            <div class="text-uppercase text-muted small px-3 mt-3 mb-1">System</div>
            @if(auth()->user()->hasPermission('users.view'))
            <a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fas fa-user"></i> <span>Users</span>
            </a>
            <a href="{{ route('roles.index') }}" class="{{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> <span>Roles</span>
            </a>
            @endif
            <a href="{{ route('settings.index') }}" class="{{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> <span>Settings</span>
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    
    <div class="main-content">
        <div class="top-bar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-outline-dark mobile-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="mb-0 page-title">@yield('page-title', 'Dashboard')</h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted d-none d-md-inline">{{ auth()->user()->name }}</span>
                <span class="badge bg-primary">{{ auth()->user()->role->name ?? 'User' }}</span>
            </div>
        </div>
        
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @yield('content')
    </div>
    @else
    @yield('content')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
            document.querySelector('.sidebar-backdrop').classList.toggle('show');
        }
        
        function toggleSidebarCollapse() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        }
        
        // Restore sidebar state on page load
        document.addEventListener('DOMContentLoaded', function() {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                document.getElementById('sidebar').classList.add('collapsed');
                document.body.classList.add('sidebar-collapsed');
            }
        });
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        $(document).ready(function() {
            $('.datatable').each(function() {
                if ($(this).find('tbody tr').length > 0) {
                    $(this).DataTable({
                        ordering: true,
                        paging: true,
                        info: true,
                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        responsive: true,
                        destroy: true
                    });
                }
            });
        });
    </script>
    @yield('scripts')
</body>
</html>
