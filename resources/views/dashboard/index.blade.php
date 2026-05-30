@extends('layouts.main')

@section('title', 'Dashboard - SmartPOS')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Today's Sales</h6>
                    <h3 class="mb-0">{{ number_format($todayRevenue, 2) }}</h3>
                    <small>{{ $todaySales->count() }} transactions</small>
                </div>
                <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Monthly Sales</h6>
                    <h3 class="mb-0">{{ number_format($monthlyRevenue, 2) }}</h3>
                    <small>This month</small>
                </div>
                <i class="fas fa-calendar fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Total Products</h6>
                    <h3 class="mb-0">{{ $totalProducts }}</h3>
                    <small>Active products</small>
                </div>
                <i class="fas fa-box fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">Customers</h6>
                    <h3 class="mb-0">{{ $totalCustomers }}</h3>
                    <small>Registered</small>
                </div>
                <i class="fas fa-users fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Revenue Chart (Last 30 Days)</span>
            </div>
            <div class="card-body">
                <canvas id="salesChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Low Stock Alerts</span>
                <span class="badge bg-danger">{{ $lowStockProducts->count() }}</span>
            </div>
            <div class="card-body p-0">
                @if($lowStockProducts->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($lowStockProducts->take(5) as $product)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <br><small class="text-muted">{{ $product->sku }}</small>
                            </div>
                            <span class="badge bg-danger">{{ $product->stock_quantity }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($lowStockProducts->count() > 5)
                <div class="card-body text-center">
                    <a href="{{ route('stock.low-stock') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                @endif
                @else
                <div class="card-body text-center text-muted">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <p class="mb-0">No low stock alerts</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-2">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Recent Sales</div>
            <div class="card-body">
                <div class="table-responsive">
                <table class="table datatable">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($todaySales->take(10) as $sale)
                        <tr>
                            <td><a href="{{ route('sales.show', $sale->id) }}">{{ $sale->invoice_no }}</a></td>
                            <td>{{ $sale->customer->name ?? 'Walk-in Customer' }}</td>
                            <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                            <td>{{ number_format($sale->total_amount, 2) }}</td>
                            <td>
                                @if($sale->status == 'completed')
                                <span class="badge bg-success">Completed</span>
                                @elseif($sale->status == 'refunded')
                                <span class="badge bg-danger">Refunded</span>
                                @else
                                <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesData = {!! json_encode($salesChartData) !!};
    const labels = Object.keys(salesData);
    const data = Object.values(salesData);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Revenue',
                data: data,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
