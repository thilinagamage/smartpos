@extends('layouts.main')

@section('title', 'New Purchase - SmartPOS')

@section('content')
<div class="card">
    <div class="card-header">
        <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('purchases.store') }}">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Supplier *</label>
                        <select name="supplier_id" class="form-select" required>
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Purchase Date</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>
            
            <h5>Purchase Items</h5>
            <div id="purchaseItems">
                <div class="row g-3 mb-3 item-row">
                    <div class="col-md-4">
                        <select name="items[0][product_id]" class="form-select product-select" required>
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->cost_price }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][quantity]" class="form-control" placeholder="Qty" min="1" value="1" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][unit_cost]" class="form-control" placeholder="Cost" step="0.01" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="items[0][selling_price]" class="form-control" placeholder="Price" step="0.01">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger remove-item"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
            
            <button type="button" class="btn btn-secondary mb-3" onclick="addItem()">Add Item</button>
            
            <hr>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Total Amount</label>
                        <input type="number" name="total_amount" class="form-control" step="0.01" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Paid Amount</label>
                        <input type="number" name="paid_amount" class="form-control" step="0.01" value="0">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Purchase</button>
        </form>
    </div>
</div>

<script>
let itemCount = 1;

function addItem() {
    const html = `
        <div class="row g-3 mb-3 item-row">
            <div class="col-md-4">
                <select name="items[${itemCount}][product_id]" class="form-select product-select" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->cost_price }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][quantity]" class="form-control" placeholder="Qty" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][unit_cost]" class="form-control" placeholder="Cost" step="0.01" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${itemCount}][selling_price]" class="form-control" placeholder="Price" step="0.01">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger remove-item"><i class="fas fa-times"></i></button>
            </div>
        </div>
    `;
    document.getElementById('purchaseItems').insertAdjacentHTML('beforeend', html);
    itemCount++;
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            e.target.closest('.item-row').remove();
        }
    }
});
</script>
@endsection
