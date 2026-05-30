<!DOCTYPE html>
<html>
<head>
    <title>Low Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #dc2626; color: white; }
        .header { text-align: center; margin-bottom: 20px; }
        .low-stock { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Low Stock Report</h2>
        <p>Generated on: {{ date('Y-m-d H:i:s') }}</p>
    </div>
    
    <div>
        <h4>Summary</h4>
        <p><strong>Total Low Stock Products:</strong> {{ count($report['products']) }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Current Stock</th>
                <th>Reorder Level</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['products'] as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? '-' }}</td>
                <td>{{ $product->brand->name ?? '-' }}</td>
                <td class="low-stock">{{ $product->stock_quantity }}</td>
                <td>{{ $product->reorder_level }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
