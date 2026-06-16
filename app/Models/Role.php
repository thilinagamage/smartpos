<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function hasPermission(string $slug): bool
    {
        return $this->permissions->contains('slug', $slug);
    }

    public function givePermission(Permission $permission)
    {
        return $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    public function givePermissions(array $permissions)
    {
        $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id');
        return $this->permissions()->syncWithoutDetaching($permissionIds);
    }

    public function revokePermission(Permission $permission)
    {
        return $this->permissions()->detach($permission->id);
    }

    public function syncPermissions(array $permissions)
    {
        $permissionIds = Permission::whereIn('slug', $permissions)->pluck('id');
        return $this->permissions()->sync($permissionIds);
    }
}
