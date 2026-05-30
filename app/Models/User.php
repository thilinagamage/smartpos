<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'phone',
        'password',
        'image',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->name === 'Super Admin';
    }

    public function isAdmin(): bool
    {
        return $this->role && in_array($this->role->name, ['Super Admin', 'Admin']);
    }

    public function isStaff(): bool
    {
        return $this->role && in_array($this->role->name, ['Cashier', 'Staff']);
    }

    public function canAccessDashboard(): bool
    {
        return $this->role && in_array($this->role->name, ['Super Admin', 'Admin', 'Cashier', 'Staff']);
    }

    public function hasPermission(string $slug): bool
    {
        if (!$this->role) {
            return false;
        }

        if ($this->isSuperAdmin()) {
            return true;
        }

        return $this->role->hasPermission($slug);
    }

    public function hasAnyPermission(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }
        return false;
    }

    public function hasAllPermissions(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if (!$this->hasPermission($slug)) {
                return false;
            }
        }
        return true;
    }

    public function canAccessRoute(string $routeName): bool
    {
        $routePermissions = [
            'dashboard' => 'reports.view',
            'pos.create' => 'sales.create',
            'products.index' => 'products.view',
            'products.create' => 'products.create',
            'products.edit' => 'products.edit',
            'products.destroy' => 'products.delete',
            'categories.index' => 'categories.view',
            'categories.create' => 'categories.create',
            'categories.edit' => 'categories.edit',
            'categories.destroy' => 'categories.delete',
            'brands.index' => 'brands.view',
            'brands.create' => 'brands.create',
            'brands.edit' => 'brands.edit',
            'brands.destroy' => 'brands.delete',
            'customers.index' => 'customers.view',
            'customers.create' => 'customers.create',
            'customers.edit' => 'customers.edit',
            'customers.destroy' => 'customers.delete',
            'suppliers.index' => 'suppliers.view',
            'suppliers.create' => 'suppliers.create',
            'suppliers.edit' => 'suppliers.edit',
            'suppliers.destroy' => 'suppliers.delete',
            'sales.index' => 'sales.view',
            'sales.create' => 'sales.create',
            'sales.edit' => 'sales.edit',
            'sales.destroy' => 'sales.delete',
            'sales.refund' => 'sales.refund',
            'purchases.index' => 'purchases.view',
            'purchases.create' => 'purchases.create',
            'purchases.edit' => 'purchases.edit',
            'purchases.destroy' => 'purchases.delete',
            'stock.index' => 'stock.view',
            'stock.adjust' => 'stock.manage',
            'stock.history' => 'stock.view',
            'stock.low-stock' => 'stock.view',
            'reports.daily-sales' => 'reports.view',
            'reports.monthly-sales' => 'reports.view',
            'reports.profit' => 'reports.view',
            'reports.inventory' => 'reports.view',
            'reports.low-stock' => 'reports.view',
            'reports.warranty' => 'reports.view',
            'settings.index' => 'settings.view',
            'users.index' => 'users.view',
            'users.create' => 'users.create',
            'users.edit' => 'users.edit',
            'users.destroy' => 'users.delete',
            'roles.index' => 'users.roles',
        ];

        $permission = $routePermissions[$routeName] ?? null;
        
        if (!$permission) {
            return true;
        }

        return $this->hasPermission($permission);
    }
}
