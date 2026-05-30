<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::middleware(['role:Super Admin,Admin'])->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('roles', RoleController::class);
    });
    
    Route::get('/pos', [SaleController::class, 'create'])->name('pos.create');
    
    Route::resource('categories', CategoryController::class);
    Route::resource('brands', BrandController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    Route::resource('products', ProductController::class);
    Route::resource('sales', SaleController::class);
    Route::resource('purchases', PurchaseController::class);
    
    Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/history', [StockController::class, 'history'])->name('stock.history');
    Route::get('/stock/adjust/{product}', [StockController::class, 'adjustForm'])->name('stock.adjust');
    Route::post('/stock/adjust/{product}', [StockController::class, 'adjust'])->name('stock.adjust.store');
    Route::get('/stock/low-stock', [StockController::class, 'lowStock'])->name('stock.low-stock');
    
    Route::get('/reports/daily-sales', [ReportController::class, 'dailySales'])->name('reports.daily-sales');
    Route::get('/reports/monthly-sales', [ReportController::class, 'monthlySales'])->name('reports.monthly-sales');
    Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/low-stock', [ReportController::class, 'lowStock'])->name('reports.low-stock');
    Route::get('/reports/warranty', [ReportController::class, 'warranty'])->name('reports.warranty');
    Route::get('/reports/warranty-lookup', [ReportController::class, 'warrantyLookup'])->name('reports.warranty-lookup');
    
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    Route::post('/sales/{sale}/refund', [SaleController::class, 'refund'])->name('sales.refund');
    Route::get('/sales/{sale}/print', [SaleController::class, 'print'])->name('sales.print');
    
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
    Route::post('/products/import', [ProductController::class, 'import'])->name('products.import');
    Route::get('/products/export', [ProductController::class, 'export'])->name('products.export');
    Route::get('/products/{product}/barcode', [ProductController::class, 'generateBarcode'])->name('products.barcode');
    
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
    
    Route::get('/exports/sales/excel', [ExportController::class, 'exportSalesExcel'])->name('exports.sales.excel');
    Route::get('/exports/sales/pdf', [ExportController::class, 'exportSalesPdf'])->name('exports.sales.pdf');
    Route::get('/exports/daily-sales/excel', [ExportController::class, 'exportDailySalesExcel'])->name('exports.daily-sales.excel');
    Route::get('/exports/daily-sales/pdf', [ExportController::class, 'exportDailySalesPdf'])->name('exports.daily-sales.pdf');
    Route::get('/exports/monthly-sales/excel', [ExportController::class, 'exportMonthlySalesExcel'])->name('exports.monthly-sales.excel');
    Route::get('/exports/monthly-sales/pdf', [ExportController::class, 'exportMonthlySalesPdf'])->name('exports.monthly-sales.pdf');
    Route::get('/exports/profit/excel', [ExportController::class, 'exportProfitExcel'])->name('exports.profit.excel');
    Route::get('/exports/profit/pdf', [ExportController::class, 'exportProfitPdf'])->name('exports.profit.pdf');
    Route::get('/exports/inventory/excel', [ExportController::class, 'exportInventoryExcel'])->name('exports.inventory.excel');
    Route::get('/exports/inventory/pdf', [ExportController::class, 'exportInventoryPdf'])->name('exports.inventory.pdf');
    Route::get('/exports/low-stock/excel', [ExportController::class, 'exportLowStockExcel'])->name('exports.low-stock.excel');
    Route::get('/exports/low-stock/pdf', [ExportController::class, 'exportLowStockPdf'])->name('exports.low-stock.pdf');
});
