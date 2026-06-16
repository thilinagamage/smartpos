<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedUsers();
        $this->seedCategories();
        $this->seedBrands();
        $this->seedSuppliers();
        $this->seedCustomers();
        $this->seedProducts();
        $this->seedSettings();
    }

    protected function seedPermissions(): void
    {
        $permissions = Permission::getPermissionsList();
        
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }

    protected function seedRoles(): void
    {
        $superAdmin = Role::create(['name' => 'Super Admin', 'description' => 'Full system access']);
        $admin = Role::create(['name' => 'Admin', 'description' => 'Admin access without user management']);
        $cashier = Role::create(['name' => 'Cashier', 'description' => 'POS and sales only']);

        $allPermissions = Permission::pluck('slug')->toArray();
        
        $adminPermissions = array_filter($allPermissions, function($perm) {
            return !in_array($perm, ['users.view', 'users.create', 'users.edit', 'users.delete', 'users.roles']);
        });
        
        $cashierPermissions = [
            'sales.view', 'sales.create', 'sales.print',
            'customers.view', 'customers.create',
            'products.view',
            'reports.view',
        ];

        $superAdmin->permissions()->sync(Permission::pluck('id'));
        $admin->permissions()->sync(Permission::whereIn('slug', array_keys($adminPermissions))->pluck('id'));
        $cashier->permissions()->sync(Permission::whereIn('slug', $cashierPermissions)->pluck('id'));
    }

    protected function seedUsers(): void
    {
        $adminRole = Role::where('name', 'Super Admin')->first();
        $adminRole2 = Role::where('name', 'Admin')->first();
        $cashierRole = Role::where('name', 'Cashier')->first();

        User::create([
            'role_id' => $adminRole->id,
            'name' => 'Super Admin',
            'email' => 'admin@smartpos.com',
            'phone' => '0771234567',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        User::create([
            'role_id' => $adminRole2->id,
            'name' => 'Admin User',
            'email' => 'admin2@smartpos.com',
            'phone' => '0771234568',
            'password' => Hash::make('password'),
            'status' => true,
        ]);

        User::create([
            'role_id' => $cashierRole->id,
            'name' => 'Cashier',
            'email' => 'cashier@smartpos.com',
            'phone' => '0771234569',
            'password' => Hash::make('password'),
            'status' => true,
        ]);
    }

    protected function seedCategories(): void
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Electronic devices and accessories'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Fashion and apparel'],
            ['name' => 'Food & Beverages', 'slug' => 'food-beverages', 'description' => 'Food items and drinks'],
            ['name' => 'Home & Garden', 'slug' => 'home-garden', 'description' => 'Home improvement and garden'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports equipment and gear'],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Books and publications'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }

    protected function seedBrands(): void
    {
        $brands = [
            ['name' => 'Samsung', 'slug' => 'samsung'],
            ['name' => 'Apple', 'slug' => 'apple'],
            ['name' => 'Sony', 'slug' => 'sony'],
            ['name' => 'Nike', 'slug' => 'nike'],
            ['name' => 'Adidas', 'slug' => 'adidas'],
            ['name' => 'Generic', 'slug' => 'generic'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }

    protected function seedSuppliers(): void
    {
        $suppliers = [
            ['name' => 'Tech Supplies Ltd', 'email' => 'tech@supplier.com', 'phone' => '0111234567', 'address' => 'Colombo 01', 'contact_person' => 'John Doe'],
            ['name' => 'Fashion Distributors', 'email' => 'fashion@supplier.com', 'phone' => '0112345678', 'address' => 'Colombo 02', 'contact_person' => 'Jane Smith'],
            ['name' => 'Wholesale Foods', 'email' => 'foods@supplier.com', 'phone' => '0113456789', 'address' => 'Colombo 03', 'contact_person' => 'Bob Wilson'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }

    protected function seedCustomers(): void
    {
        $customers = [
            ['name' => 'John Doe', 'email' => 'john@example.com', 'phone' => '0711234567', 'address' => 'Colombo 05', 'credit_limit' => 50000],
            ['name' => 'Jane Smith', 'email' => 'jane@example.com', 'phone' => '0712234567', 'address' => 'Colombo 06', 'credit_limit' => 30000],
            ['name' => 'Walk-in Customer', 'email' => null, 'phone' => '0000000000', 'address' => null, 'credit_limit' => 0],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }

    protected function seedProducts(): void
    {
        $electronics = Category::where('slug', 'electronics')->first();
        $clothing = Category::where('slug', 'clothing')->first();

        $samsung = Brand::where('slug', 'samsung')->first();
        $apple = Brand::where('slug', 'apple')->first();
        $sony = Brand::where('slug', 'sony')->first();
        $nike = Brand::where('slug', 'nike')->first();
        $adidas = Brand::where('slug', 'adidas')->first();
        $generic = Brand::where('slug', 'generic')->first();

        $products = [
            [
                'name' => 'Samsung Galaxy A54',
                'category_id' => $electronics->id,
                'brand_id' => $samsung->id,
                'sku' => 'SAM-A54-001',
                'barcode' => '1234567890123',
                'cost_price' => 45000,
                'selling_price' => 55000,
                'stock_quantity' => 25,
                'reorder_level' => 5,
                'warranty_days' => 365,
            ],
            [
                'name' => 'iPhone 14',
                'category_id' => $electronics->id,
                'brand_id' => $apple->id,
                'sku' => 'APL-IP14-001',
                'barcode' => '1234567890124',
                'cost_price' => 150000,
                'selling_price' => 175000,
                'stock_quantity' => 10,
                'reorder_level' => 3,
                'warranty_days' => 365,
            ],
            [
                'name' => 'Sony Headphones',
                'category_id' => $electronics->id,
                'brand_id' => $sony->id,
                'sku' => 'SNY-HP001',
                'barcode' => '1234567890125',
                'cost_price' => 8000,
                'selling_price' => 12000,
                'stock_quantity' => 30,
                'reorder_level' => 10,
                'warranty_days' => 180,
            ],
            [
                'name' => 'Nike Running Shoes',
                'category_id' => $clothing->id,
                'brand_id' => $nike->id,
                'sku' => 'NIK-RN001',
                'barcode' => '1234567890126',
                'cost_price' => 5500,
                'selling_price' => 8500,
                'stock_quantity' => 50,
                'reorder_level' => 15,
                'warranty_days' => 0,
            ],
            [
                'name' => 'Adidas T-Shirt',
                'category_id' => $clothing->id,
                'brand_id' => $adidas->id,
                'sku' => 'ADI-TS001',
                'barcode' => '1234567890127',
                'cost_price' => 1500,
                'selling_price' => 2500,
                'stock_quantity' => 100,
                'reorder_level' => 20,
                'warranty_days' => 0,
            ],
            [
                'name' => 'USB Cable',
                'category_id' => $electronics->id,
                'brand_id' => $generic->id,
                'sku' => 'GEN-USB001',
                'barcode' => '1234567890128',
                'cost_price' => 200,
                'selling_price' => 500,
                'stock_quantity' => 200,
                'reorder_level' => 50,
                'warranty_days' => 30,
            ],
            [
                'name' => 'Phone Charger',
                'category_id' => $electronics->id,
                'brand_id' => $generic->id,
                'sku' => 'GEN-CHG001',
                'barcode' => '1234567890129',
                'cost_price' => 350,
                'selling_price' => 800,
                'stock_quantity' => 100,
                'reorder_level' => 30,
                'warranty_days' => 90,
            ],
            [
                'name' => 'Power Bank 10000mAh',
                'category_id' => $electronics->id,
                'brand_id' => $generic->id,
                'sku' => 'GEN-PB001',
                'barcode' => '1234567890130',
                'cost_price' => 1800,
                'selling_price' => 3500,
                'stock_quantity' => 45,
                'reorder_level' => 15,
                'warranty_days' => 180,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }

    protected function seedSettings(): void
    {
        $settings = [
            ['key' => 'shop_name', 'value' => 'SmartPOS Store'],
            ['key' => 'shop_email', 'value' => 'contact@smartpos.com'],
            ['key' => 'shop_phone', 'value' => '011-1234567'],
            ['key' => 'shop_address', 'value' => '123 Main Street, Colombo, Sri Lanka'],
            ['key' => 'tax_percentage', 'value' => '10'],
            ['key' => 'currency', 'value' => 'LKR'],
            ['key' => 'receipt_footer', 'value' => 'Thank you for shopping with us!'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
