<!DOCTYPE html>
<html>
<head>
    <title>Profit Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4f46e5; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .profit { color: green; }
        .loss { color: red; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Profit Report</h2>
        <p>Date: {{ $date->format('Y-m-d') }}</p>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <div>
        <h4>Summary</h4>
        <p><strong>Total Revenue:</strong> {{ number_format($report['total_revenue'], 2) }}</p>
        <p><strong>Total Cost:</strong> {{ number_format($report['total_cost'], 2) }}</p>
        <p><strong>Total Profit:</strong> {{ number_format($report['total_profit'], 2) }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Invoice No</th>
                <th>Revenue</th>
                <th>Cost</th>
                <th>Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['sales'] as $sale)
            @php
                $cost = $sale->items->sum(fn($item) => $item->quantity * $item->cost_price);
                $profit = $sale->total_amount - $cost;
            @endphp
            <tr>
                <td>{{ $sale->invoice_no }}</td>
                <td>{{ number_format($sale->total_amount, 2) }}</td>
                <td>{{ number_format($cost, 2) }}</td>
                <td class="{{ $profit >= 0 ? 'profit' : 'loss' }}">{{ number_format($profit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
