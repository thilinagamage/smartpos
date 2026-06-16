<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4f46e5; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Sales Report</h2>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Due</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->invoice_no }}</td>
                <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                <td>{{ $sale->sale_date->format('Y-m-d H:i') }}</td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
                <td>{{ number_format($sale->paid_amount, 2) }}</td>
                <td>{{ number_format($sale->due_amount, 2) }}</td>
                <td>{{ ucfirst($sale->payment_method) }}</td>
                <td>{{ ucfirst($sale->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="summary">
        <h4>Summary</h4>
        <p><strong>Total Revenue:</strong> {{ number_format($totalRevenue, 2) }}</p>
        <p><strong>Total Paid:</strong> {{ number_format($totalPaid, 2) }}</p>
    </div>
</body>
</html>
