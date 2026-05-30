<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'group'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public static function getGroups(): array
    {
        return [
            'products' => 'Products',
            'categories' => 'Categories',
            'brands' => 'Brands',
            'customers' => 'Customers',
            'suppliers' => 'Suppliers',
            'sales' => 'Sales',
            'purchases' => 'Purchases',
            'stock' => 'Stock',
            'reports' => 'Reports',
            'settings' => 'Settings',
            'users' => 'Users',
        ];
    }

    public static function getPermissionsList(): array
    {
        return [
            // Products
            ['name' => 'View Products', 'slug' => 'products.view', 'group' => 'products'],
            ['name' => 'Create Products', 'slug' => 'products.create', 'group' => 'products'],
            ['name' => 'Edit Products', 'slug' => 'products.edit', 'group' => 'products'],
            ['name' => 'Delete Products', 'slug' => 'products.delete', 'group' => 'products'],
            ['name' => 'Import Products', 'slug' => 'products.import', 'group' => 'products'],
            ['name' => 'Export Products', 'slug' => 'products.export', 'group' => 'products'],

            // Categories
            ['name' => 'View Categories', 'slug' => 'categories.view', 'group' => 'categories'],
            ['name' => 'Create Categories', 'slug' => 'categories.create', 'group' => 'categories'],
            ['name' => 'Edit Categories', 'slug' => 'categories.edit', 'group' => 'categories'],
            ['name' => 'Delete Categories', 'slug' => 'categories.delete', 'group' => 'categories'],

            // Brands
            ['name' => 'View Brands', 'slug' => 'brands.view', 'group' => 'brands'],
            ['name' => 'Create Brands', 'slug' => 'brands.create', 'group' => 'brands'],
            ['name' => 'Edit Brands', 'slug' => 'brands.edit', 'group' => 'brands'],
            ['name' => 'Delete Brands', 'slug' => 'brands.delete', 'group' => 'brands'],

            // Customers
            ['name' => 'View Customers', 'slug' => 'customers.view', 'group' => 'customers'],
            ['name' => 'Create Customers', 'slug' => 'customers.create', 'group' => 'customers'],
            ['name' => 'Edit Customers', 'slug' => 'customers.edit', 'group' => 'customers'],
            ['name' => 'Delete Customers', 'slug' => 'customers.delete', 'group' => 'customers'],

            // Suppliers
            ['name' => 'View Suppliers', 'slug' => 'suppliers.view', 'group' => 'suppliers'],
            ['name' => 'Create Suppliers', 'slug' => 'suppliers.create', 'group' => 'suppliers'],
            ['name' => 'Edit Suppliers', 'slug' => 'suppliers.edit', 'group' => 'suppliers'],
            ['name' => 'Delete Suppliers', 'slug' => 'suppliers.delete', 'group' => 'suppliers'],

            // Sales
            ['name' => 'View Sales', 'slug' => 'sales.view', 'group' => 'sales'],
            ['name' => 'Create Sales', 'slug' => 'sales.create', 'group' => 'sales'],
            ['name' => 'Edit Sales', 'slug' => 'sales.edit', 'group' => 'sales'],
            ['name' => 'Delete Sales', 'slug' => 'sales.delete', 'group' => 'sales'],
            ['name' => 'Refund Sales', 'slug' => 'sales.refund', 'group' => 'sales'],
            ['name' => 'Print Receipt', 'slug' => 'sales.print', 'group' => 'sales'],

            // Purchases
            ['name' => 'View Purchases', 'slug' => 'purchases.view', 'group' => 'purchases'],
            ['name' => 'Create Purchases', 'slug' => 'purchases.create', 'group' => 'purchases'],
            ['name' => 'Edit Purchases', 'slug' => 'purchases.edit', 'group' => 'purchases'],
            ['name' => 'Delete Purchases', 'slug' => 'purchases.delete', 'group' => 'purchases'],

            // Stock
            ['name' => 'View Stock', 'slug' => 'stock.view', 'group' => 'stock'],
            ['name' => 'Manage Stock', 'slug' => 'stock.manage', 'group' => 'stock'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'reports.view', 'group' => 'reports'],
            ['name' => 'Export Reports', 'slug' => 'reports.export', 'group' => 'reports'],

            // Settings
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'settings'],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'settings'],

            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'group' => 'users'],
            ['name' => 'Manage Roles', 'slug' => 'users.roles', 'group' => 'users'],
        ];
    }
}
