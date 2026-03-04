<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::getGroups();
        $permissionList = Permission::getPermissionsList();
        return view('roles.create', compact('permissions', 'permissionList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $permissions = Permission::whereIn('slug', $request->permissions)->get();
        $role->permissions()->sync($permissions->pluck('id'));

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function show(string $id)
    {
        $role = Role::with('permissions', 'users')->findOrFail($id);
        return view('roles.show', compact('role'));
    }

    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        $permissions = Permission::getGroups();
        $permissionList = Permission::getPermissionsList();
        $rolePermissions = $role->permissions->pluck('slug')->toArray();
        return view('roles.edit', compact('role', 'permissions', 'permissionList', 'rolePermissions'));
    }

    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array',
        ]);

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        $permissions = Permission::whereIn('slug', $request->permissions)->get();
        $role->permissions()->sync($permissions->pluck('id'));

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete role with assigned users.');
        }

        if (in_array($role->name, ['Super Admin', 'Admin', 'Cashier'])) {
            return redirect()->route('roles.index')->with('error', 'Cannot delete default role.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role deleted successfully.');
    }
}
