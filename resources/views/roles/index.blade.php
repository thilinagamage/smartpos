@extends('layouts.main')

@section('title', 'Roles - SmartPOS')
@section('page-title', 'Roles & Permissions')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Roles</span>
        <a href="{{ route('roles.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Add Role
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Permissions</th>
                    <th>Users</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>
                        <span class="badge bg-{{ $role->name == 'Super Admin' ? 'danger' : ($role->name == 'Admin' ? 'warning' : 'info') }}">
                            {{ $role->name }}
                        </span>
                    </td>
                    <td>{{ $role->description ?? '-' }}</td>
                    <td>{{ $role->permissions->count() }}</td>
                    <td>{{ $role->users->count() }}</td>
                    <td>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        @if(!in_array($role->name, ['Super Admin', 'Admin', 'Cashier']))
                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">No roles found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
