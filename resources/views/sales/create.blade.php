@extends('layouts.main')

@section('title', 'POS - SmartPOS')
@section('page-title', 'Point of Sale')

@section('content')
<style>
    @media (max-width: 991.98px) {
        .pos-layout {
            flex-direction: column !important;
        }
        .pos-products {
            order: 1;
        }
        .pos-cart {
            order: 2;
        }
    }
</style>

<div class="pos-layout">
    <div class="pos-products">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-md-4">
                        <input type="text" id="productSearch" class="form-control" placeholder="Search products...">
                    </div>
                    <div class="col-md-3">
                        <select id="categoryFilter" class="form-select">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode..." autofocus>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="product-grid" id="productGrid">
            @foreach($products as $product)
            <div class="product-card" onclick="addToCart({{ $product->id }})">
                <div class="fw-bold">{{ $product->name }}</div>
                <div class="text-muted small">{{ $product->sku }}</div>
                <div class="text-primary fw-bold">{{ number_format($product->selling_price, 2) }}</div>
                <div class="small text-{{ $product->stock_quantity <= $product->reorder_level ? 'danger' : 'success' }}">
                    Stock: {{ $product->stock_quantity }}
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <div class="pos-cart">
        <div class="card h-100">
            <div class="card-header">
                <div class="row g-2">
                    <div class="col-8">
                        <select id="customerSelect" class="form-select form-select-sm">
                            <option value="">Walk-in Customer</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <button class="btn btn-sm btn-outline-secondary w-100" onclick="clearCart()">Clear</button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 pos-cart-items">
                <table class="table table-sm mb-0" id="cartTable">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="cartItems">
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="row mb-2">
                    <div class="col-6">Subtotal</div>
                    <div class="col-6 text-end" id="subtotalDisplay">0.00</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">Tax (%)</div>
                    <div class="col-4">
                        <input type="number" id="taxRate" class="form-control form-control-sm" value="0" min="0" max="100">
                    </div>
                    <div class="col-2 text-end" id="taxDisplay">0.00</div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">Discount</div>
                    <div class="col-6">
                        <input type="number" id="orderDiscount" class="form-control form-control-sm" value="0" min="0" step="0.01">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6"><strong>Total</strong></div>
                    <div class="col-6 text-end"><strong id="totalDisplay">0.00</strong></div>
                </div>
                <div class="row mb-2">
                    <div class="col-6">Payment Method</div>
                    <div class="col-6">
                        <select id="paymentMethod" class="form-select form-select-sm">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">Paid Amount</div>
                    <div class="col-6">
                        <input type="number" id="paidAmount" class="form-control form-select-sm" value="0" min="0" step="0.01">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-6">Change</div>
                    <div class="col-6 text-end" id="changeDisplay">0.00</div>
                </div>
                <button class="btn btn-primary w-100 btn-lg" onclick="completeSale()" id="completeSaleBtn" disabled>
                    Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>

<form id="saleForm" method="POST" action="{{ route('sales.store') }}">
    @csrf
    <input type="hidden" name="customer_id" id="customerId">
    <input type="hidden" name="subtotal" id="subtotalInput">
    <input type="hidden" name="tax_amount" id="taxInput">
    <input type="hidden" name="order_discount" id="discountInput">
    <input type="hidden" name="total_amount" id="totalInput">
    <input type="hidden" name="paid_amount" id="paidInput">
    <input type="hidden" name="payment_method" id="paymentMethodInput">
    <input type="hidden" name="items" id="itemsInput">
</form>
@endsection

@section('scripts')
<script>
    let cart = [];
    let products = @json($products->keyBy('id'));

    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const barcode = this.value;
            const product = Object.values(products).find(p => p.barcode == barcode);
            if (product) {
                addToCart(product.id);
                this.value = '';
            }
        }
    });

    document.getElementById('productSearch').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('.product-card').forEach(card => {
            const name = card.querySelector('.fw-bold').textContent.toLowerCase();
            card.style.display = name.includes(search) ? 'block' : 'none';
        });
    });

    document.getElementById('taxRate').addEventListener('input', updateTotals);
    document.getElementById('orderDiscount').addEventListener('input', updateTotals);
    document.getElementById('paidAmount').addEventListener('input', updateTotals);

    function addToCart(productId) {
        const product = products[productId];
        if (!product) return;
        
        if (product.stock_quantity <= 0) {
            alert('Product out of stock');
            return;
        }

        const existing = cart.find(item => item.product_id === productId);
        if (existing) {
            if (existing.quantity >= product.stock_quantity) {
                alert('Insufficient stock');
                return;
            }
            existing.quantity++;
            existing.total = existing.quantity * existing.unit_price;
        } else {
            cart.push({
                product_id: productId,
                product_name: product.name,
                quantity: 1,
                unit_price: parseFloat(product.selling_price),
                unit_discount: 0,
                tax_amount: 0,
                total: parseFloat(product.selling_price)
            });
        }

        renderCart();
    }

    function updateQuantity(productId, newQty) {
        const product = products[productId];
        const item = cart.find(i => i.product_id === productId);
        
        if (newQty <= 0) {
            removeFromCart(productId);
            return;
        }
        
        if (newQty > product.stock_quantity) {
            alert('Insufficient stock');
            return;
        }

        item.quantity = newQty;
        item.total = item.quantity * item.unit_price;
        renderCart();
    }

    function removeFromCart(productId) {
        cart = cart.filter(item => item.product_id !== productId);
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cartItems');
        tbody.innerHTML = '';

        cart.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.product_name}</td>
                <td>
                    <input type="number" class="form-control form-control-sm" style="width: 60px" 
                        value="${item.quantity}" min="1" 
                        onchange="updateQuantity(${item.product_id}, parseInt(this.value))">
                </td>
                <td>${item.unit_price.toFixed(2)}</td>
                <td>${item.total.toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeFromCart(${item.product_id})">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        updateTotals();
    }

    function updateTotals() {
        const subtotal = cart.reduce((sum, item) => sum + item.total, 0);
        const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
        const discount = parseFloat(document.getElementById('orderDiscount').value) || 0;
        
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax - discount;
        
        let paid = parseFloat(document.getElementById('paidAmount').value);
        if (isNaN(paid) || paid === 0) {
            paid = total;
            document.getElementById('paidAmount').value = total.toFixed(2);
        }
        
        const change = Math.max(0, paid - total);

        document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
        document.getElementById('taxDisplay').textContent = tax.toFixed(2);
        document.getElementById('totalDisplay').textContent = total.toFixed(2);
        document.getElementById('changeDisplay').textContent = change.toFixed(2);

        document.getElementById('completeSaleBtn').disabled = cart.length === 0 || total <= 0;
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function completeSale() {
        if (cart.length === 0) return;

        const total = parseFloat(document.getElementById('totalDisplay').textContent);
        const paidInput = document.getElementById('paidAmount');
        let paid = parseFloat(paidInput.value);
        
        if (paid === 0 || isNaN(paid)) {
            paid = total;
            paidInput.value = total;
        }

        document.getElementById('customerId').value = document.getElementById('customerSelect').value;
        document.getElementById('subtotalInput').value = cart.reduce((sum, item) => sum + item.total, 0);
        document.getElementById('taxInput').value = document.getElementById('taxDisplay').textContent;
        document.getElementById('discountInput').value = document.getElementById('orderDiscount').value;
        document.getElementById('totalInput').value = total;
        document.getElementById('paidInput').value = paid;
        document.getElementById('paymentMethodInput').value = document.getElementById('paymentMethod').value;
        document.getElementById('itemsInput').value = JSON.stringify(cart);

        document.getElementById('saleForm').submit();
    }

    $(document).ready(function() {
        $('#customerSelect').select2({
            ajax: {
                url: '{{ route("customers.search") }}',
                processResults: function(data) {
                    return {
                        results: data.map(c => ({ id: c.id, text: c.name + ' (' + c.phone + ')' }))
                    };
                }
            },
            minimumInputLength: 1
        });
    });
</script>
@endsection
