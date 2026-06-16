<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
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
        <h2>Inventory Report</h2>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <div>
        <h4>Summary</h4>
        <p><strong>Total Products:</strong> {{ $report['total_products'] }}</p>
        <p><strong>Stock Value (Cost):</strong> {{ number_format($report['total_stock_value'], 2) }}</p>
        <p><strong>Retail Value:</strong> {{ number_format($report['total_retail_value'], 2) }}</p>
        <p><strong>Potential Profit:</strong> {{ number_format($report['potential_profit'], 2) }}</p>
        <p><strong>Low Stock Items:</strong> {{ $report['low_stock_count'] }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Cost</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['products'] as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td>{{ $product->brand->name ?? '-' }}</td>
                <td>{{ number_format($product->cost_price, 2) }}</td>
                <td>{{ number_format($product->selling_price, 2) }}</td>
                <td>{{ $product->stock_quantity }}</td>
                <td>{{ number_format($product->cost_price * $product->stock_quantity, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
