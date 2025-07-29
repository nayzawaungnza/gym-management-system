@extends('layouts.master', ['activePage' => 'membershiptypes', 'titlePage' => 'Membership Type Details'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Membership Types /</span> Membership Type Details
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Membership Type Information</h5>
                    <div class="d-flex">
                        <a href="{{ route('membershiptypes.edit', $membershiptype->id) }}" class="btn btn-sm btn-primary me-2">
                            <i class="ti ti-edit me-1"></i> Edit
                        </a>
                        <form action="{{ route('membershiptypes.destroy', $membershiptype->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this membership type?')">
                                <i class="ti ti-trash me-1"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Type Name</label>
                            <p class="form-control-static">{{ $membershiptype->type_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Duration (Months)</label>
                            <p class="form-control-static">{{ $membershiptype->duration_months }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <p class="form-control-static">${{ number_format($membershiptype->price, 2) }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <p class="form-control-static">
                                <span class="badge bg-{{ $membershiptype->is_active ? 'success' : 'secondary' }}">
                                    {{ $membershiptype->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Description</label>
                            <div class="border p-3 rounded">
                                {!! nl2br(e($membershiptype->description)) ?? 'No description available' !!}
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created At</label>
                            <p class="form-control-static">{{ $membershiptype->created_at->format('M d, Y H:i A') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Updated At</label>
                            <p class="form-control-static">{{ $membershiptype->updated_at->format('M d, Y H:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
