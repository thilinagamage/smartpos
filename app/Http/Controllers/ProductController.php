<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorPNG;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->when($request->category_id, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->when($request->brand_id, function ($query, $brandId) {
                $query->where('brand_id', $brandId);
            })
            ->when($request->status !== null, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();

        return view('products.index', compact('products', 'categories', 'brands'));
    }

    public function create()
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();
        
        return view('products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'warranty_days' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ]);

        $data = $request->all();
        
        if (empty($data['sku'])) {
            $data['sku'] = 'SKU-' . strtoupper(Str::random(8));
        }
        
        if (empty($data['barcode'])) {
            $data['barcode'] = random_int(100000000000, 999999999999);
        }

        $product = Product::create($data);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand']);
        
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', true)->get();
        $brands = Brand::where('status', true)->get();
        
        return view('products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'warranty_days' => 'nullable|integer|min:0',
            'status' => 'boolean',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function generateBarcode($barcode)
    {
        $generator = new BarcodeGeneratorPNG();
        $barcodeImage = base64_encode($generator->getBarcode($barcode, $generator::TYPE_CODE_128));
        
        return response()->json([
            'barcode' => $barcodeImage,
        ]);
    }

    public function search(Request $request)
    {
        $search = $request->get('q', '');
        
        $products = Product::with(['category', 'brand'])
            ->where('status', true)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        fgetcsv($handle);

        $categories = Category::where('status', true)->get()->keyBy('name');
        $brands = Brand::where('status', true)->get()->keyBy('name');

        $imported = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            $categoryName = $row[2] ?? null;
            $brandName = $row[3] ?? null;
            
            $categoryId = $categories->get($categoryName)?->id ?? $categories->first()?->id;
            $brandId = $brands->get($brandName)?->id ?? null;

            Product::create([
                'name' => $row[0] ?? 'Unknown Product',
                'sku' => $row[1] ?? 'SKU-' . strtoupper(Str::random(8)),
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'cost_price' => floatval($row[4] ?? 0),
                'selling_price' => floatval($row[5] ?? 0),
                'stock_quantity' => intval($row[6] ?? 0),
                'reorder_level' => intval($row[7] ?? 10),
            ]);
            
            $imported++;
        }

        fclose($handle);

        return redirect()->route('products.index')
            ->with('success', "{$imported} products imported successfully.");
    }

    public function export()
    {
        $products = Product::with(['category', 'brand'])->get();
        
        $filename = 'products_' . date('Y_m_d_H_i_s') . '.csv';
        $handle = fopen('php://output', 'w');
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        fputcsv($handle, ['Name', 'SKU', 'Category', 'Brand', 'Cost Price', 'Selling Price', 'Stock', 'Reorder Level', 'Status']);
        
        foreach ($products as $product) {
            fputcsv($handle, [
                $product->name,
                $product->sku,
                $product->category->name ?? '',
                $product->brand->name ?? '',
                $product->cost_price,
                $product->selling_price,
                $product->stock_quantity,
                $product->reorder_level,
                $product->status ? 'Active' : 'Inactive',
            ]);
        }
        
        fclose($handle);
        exit;
    }
}
