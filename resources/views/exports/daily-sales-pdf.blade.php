<!DOCTYPE html>
<html>
<head>
    <title>Daily Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4f46e5; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Daily Sales Report</h2>
        <p>Date: {{ $date->format('Y-m-d') }}</p>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <div>
        <h4>Summary</h4>
        <p><strong>Total Sales:</strong> {{ $report['total_sales'] }}</p>
        <p><strong>Total Revenue:</strong> {{ number_format($report['total_revenue'], 2) }}</p>
        <p><strong>Total Tax:</strong> {{ number_format($report['total_tax'], 2) }}</p>
        <p><strong>Total Profit:</strong> {{ number_format($report['total_profit'], 2) }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Time</th>
                <th>Total</th>
                <th>Payment</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['sales'] as $sale)
            <tr>
                <td>{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                <td>{{ $sale->sale_date->format('H:i') }}</td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
                <td>{{ ucfirst($sale->payment_method) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
