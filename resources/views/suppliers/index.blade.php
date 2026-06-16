@extends('layouts.main')

@section('title', 'Suppliers - SmartPOS')
@section('page-title', 'Suppliers')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Suppliers</span>
        <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">
            <i class="fas fa-plus"></i> Add Supplier
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
        <table class="table datatable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Contact Person</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->email }}</td>
                    <td>{{ $supplier->phone }}</td>
                    <td>{{ $supplier->contact_person }}</td>
                    <td>
                        @if($supplier->status)
                        <span class="badge bg-success">Active</span>
                        @else
                        <span class="badge bg-danger">Inactive</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('suppliers.edit', $supplier->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('suppliers.destroy', $supplier->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No suppliers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        {{ $suppliers->links() }}
    </div>
</div>
@endsection
