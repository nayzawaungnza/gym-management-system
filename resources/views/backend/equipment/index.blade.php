@extends('layouts.backend')

@section('title', 'Equipment Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Equipment Management</h1>
            <p class="mb-0 text-muted">Manage gym equipment inventory and maintenance</p>
        </div>
        @can('create equipment')
        <a href="{{ route('backend.equipment.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Equipment
        </a>
        @endcan
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Equipment
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Operational
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['operational'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                In Maintenance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['maintenance'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Overdue Maintenance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['overdue_maintenance'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('backend.equipment.index') }}" id="filterForm">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Name, brand, model, serial...">
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="operational" {{ request('status') == 'operational' ? 'selected' : '' }}>Operational</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            <option value="out_of_order" {{ request('status') == 'out_of_order' ? 'selected' : '' }}>Out of Order</option>
                            <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Retired</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control" id="category" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="maintenance_due" class="form-label">Maintenance</label>
                        <select class="form-control" id="maintenance_due" name="maintenance_due">
                            <option value="">All</option>
                            <option value="overdue" {{ request('maintenance_due') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            <option value="due_soon" {{ request('maintenance_due') == 'due_soon' ? 'selected' : '' }}>Due Soon</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Filter
                            </button>
                            <a href="{{ route('backend.equipment.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Clear
                            </a>
                            <button type="button" class="btn btn-success" onclick="exportEquipment()">
                                <i class="fas fa-download"></i> Export
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Equipment Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Equipment List</h6>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-warning" onclick="bulkUpdateStatus()" id="bulkStatusBtn" style="display: none;">
                    <i class="fas fa-edit"></i> Update Status
                </button>
                @can('delete equipment')
                <button type="button" class="btn btn-sm btn-danger" onclick="bulkDelete()" id="bulkDeleteBtn" style="display: none;">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
                @endcan
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="equipmentTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none">
                                    Name
                                    @if(request('sort_by') == 'name')
                                        <i class="fas fa-sort-{{ request('sort_order') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Brand/Model</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Maintenance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($equipment as $item)
                        <tr>
                            <td>
                                <input type="checkbox" class="equipment-checkbox" value="{{ $item->id }}">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->image)
                                    <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}" 
                                         class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-dumbbell text-muted"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->serial_number)
                                        <br><small class="text-muted">SN: {{ $item->serial_number }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $item->brand }}
                                @if($item->model)
                                <br><small class="text-muted">{{ $item->model }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ ucfirst($item->category) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'operational' => 'success',
                                        'maintenance' => 'warning',
                                        'out_of_order' => 'danger',
                                        'retired' => 'secondary'
                                    ];
                                @endphp
                                <span class="badge badge-{{ $statusColors[$item->status] ?? 'secondary' }}">
                                    {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                                </span>
                            </td>
                            <td>{{ $item->location ?? 'Not specified' }}</td>
                            <td>
                                @if($item->next_maintenance_date)
                                    @if($item->next_maintenance_date < now())
                                        <span class="badge badge-danger">
                                            <i class="fas fa-exclamation-triangle"></i> Overdue
                                        </span>
                                    @elseif($item->next_maintenance_date <= now()->addDays(7))
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> Due Soon
                                        </span>
                                    @else
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> Up to Date
                                        </span>
                                    @endif
                                    <br><small class="text-muted">{{ $item->next_maintenance_date->format('M d, Y') }}</small>
                                @else
                                    <span class="text-muted">Not scheduled</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('backend.equipment.show', $item) }}" 
                                       class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @can('edit equipment')
                                    <a href="{{ route('backend.equipment.edit', $item) }}" 
                                       class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    @can('delete equipment')
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteEquipment({{ $item->id }}, '{{ $item->name }}')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-dumbbell fa-3x mb-3"></i>
                                    <p>No equipment found matching your criteria.</p>
                                    @can('create equipment')
                                    <a href="{{ route('backend.equipment.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Equipment
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($equipment->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Showing {{ $equipment->firstItem() }} to {{ $equipment->lastItem() }} of {{ $equipment->total() }} results
                </div>
                {{ $equipment->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Bulk Status Update Modal -->
<div class="modal fade" id="bulkStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Equipment Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="bulkStatusForm">
                    <div class="mb-3">
                        <label for="bulkStatus" class="form-label">New Status</label>
                        <select class="form-control" id="bulkStatus" name="status" required>
                            <option value="">Select Status</option>
                            <option value="operational">Operational</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="out_of_order">Out of Order</option>
                            <option value="retired">Retired</option>
                        </select>
                    </div>
                    <p class="text-muted">This will update the status for <span id="selectedCount">0</span> selected equipment items.</p>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" onclick="confirmBulkStatusUpdate()">Update Status</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this equipment?</p>
                <p><strong id="equipmentName"></strong></p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Handle select all checkbox
    $('#selectAll').change(function() {
        $('.equipment-checkbox').prop('checked', this.checked);
        toggleBulkActions();
    });

    // Handle individual checkboxes
    $('.equipment-checkbox').change(function() {
        toggleBulkActions();
        
        // Update select all checkbox
        const totalCheckboxes = $('.equipment-checkbox').length;
        const checkedCheckboxes = $('.equipment-checkbox:checked').length;
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);
    });
});

function toggleBulkActions() {
    const checkedCount = $('.equipment-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulkStatusBtn, #bulkDeleteBtn').show();
        $('#selectedCount').text(checkedCount);
    } else {
        $('#bulkStatusBtn, #bulkDeleteBtn').hide();
    }
}

function bulkUpdateStatus() {
    const checkedCount = $('.equipment-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select equipment items to update.');
        return;
    }
    
    $('#selectedCount').text(checkedCount);
    $('#bulkStatusModal').modal('show');
}

function confirmBulkStatusUpdate() {
    const status = $('#bulkStatus').val();
    if (!status) {
        alert('Please select a status.');
        return;
    }

    const equipmentIds = $('.equipment-checkbox:checked').map(function() {
        return this.value;
    }).get();

    $.ajax({
        url: '{{ route("backend.equipment.bulk-update-status") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            equipment_ids: equipmentIds,
            status: status
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error updating equipment status.');
        }
    });
}

function bulkDelete() {
    const checkedCount = $('.equipment-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select equipment items to delete.');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${checkedCount} equipment items? This action cannot be undone.`)) {
        return;
    }

    const equipmentIds = $('.equipment-checkbox:checked').map(function() {
        return this.value;
    }).get();

    $.ajax({
        url: '{{ route("backend.equipment.bulk-delete") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            equipment_ids: equipmentIds
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error deleting equipment.');
        }
    });
}

function deleteEquipment(id, name) {
    $('#equipmentName').text(name);
    $('#deleteForm').attr('action', `/backend/equipment/${id}`);
    $('#deleteModal').modal('show');
}

function exportEquipment() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', '1');
    window.location.href = '{{ route("backend.equipment.export") }}?' + params.toString();
}
</script>
@endpush
