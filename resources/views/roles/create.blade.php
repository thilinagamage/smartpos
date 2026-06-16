@extends('layouts.main')

@section('title', 'Create Role - SmartPOS')
@section('page-title', 'Create Role')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Create New Role</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Role Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Permissions</label>
                @foreach($permissions as $group => $label)
                <div class="mt-3">
                    <h6>{{ $label }}</h6>
                    <div class="row">
                        @php
                        $groupPermissions = array_filter($permissionList, fn($p) => $p['group'] === $group);
                        @endphp
                        @foreach($groupPermissions as $perm)
                        <div class="col-md-3 col-6">
                            <div class="form-check">
                                <input type="checkbox" name="permissions[]" class="form-check-input" value="{{ $perm['slug'] }}" id="perm_{{ $perm['slug'] }}">
                                <label class="form-check-label" for="perm_{{ $perm['slug'] }}">{{ $perm['name'] }}</label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            
            <button type="submit" class="btn btn-primary">Create Role</button>
            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
@endsection
