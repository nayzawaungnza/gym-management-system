@extends('layouts.backend')

@section('title', 'Equipment Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Equipment Details</h1>
            <p class="mb-0 text-muted">{{ $equipment->name }}</p>
        </div>
        <div class="d-flex gap-2">
            @can('edit equipment')
            <a href="{{ route('backend.equipment.edit', $equipment) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Equipment
            </a>
            @endcan
            <button type="button" class="btn btn-success" onclick="scheduleMaintenance()">
                <i class="fas fa-calendar-plus"></i> Schedule Maintenance
            </button>
            <a href="{{ route('backend.equipment.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Equipment
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Equipment Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Equipment Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            @if($equipment->image)
                            <img src="{{ Storage::url($equipment->image) }}" alt="{{ $equipment->name }}" 
                                 class="img-fluid rounded shadow">
                            @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" 
                                 style="height: 200px;">
                                <i class="fas fa-dumbbell fa-4x text-muted"></i>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Name:</strong></td>
                                    <td>{{ $equipment->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td><span class="badge badge-secondary">{{ ucfirst($equipment->category) }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Brand:</strong></td>
                                    <td>{{ $equipment->brand ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Model:</strong></td>
                                    <td>{{ $equipment->model ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Serial Number:</strong></td>
                                    <td>{{ $equipment->serial_number ?? 'Not specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'operational' => 'success',
                                                'maintenance' => 'warning',
                                                'out_of_order' => 'danger',
                                                'retired' => 'secondary'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusColors[$equipment->status] ?? 'secondary' }}">
                                            {{ ucfirst(str_replace('_', ' ', $equipment->status)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Location:</strong></td>
                                    <td>{{ $equipment->location ?? 'Not specified' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($equipment->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $equipment->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Purchase Information -->
            @if($equipment->purchase_date || $equipment->purchase_price || $equipment->warranty_expiry_date)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Purchase Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Purchase Date:</strong><br>
                            {{ $equipment->purchase_date ? $equipment->purchase_date->format('M d, Y') : 'Not specified' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Purchase Price:</strong><br>
                            {{ $equipment->purchase_price ? '$' . number_format($equipment->purchase_price, 2) : 'Not specified' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Warranty Status:</strong><br>
                            @if($equipment->warranty_expiry_date)
                                @if($warrantyStatus === 'active')
                                    <span class="badge badge-success">Active</span><br>
                                    <small class="text-muted">Expires {{ $equipment->warranty_expiry_date->format('M d, Y') }}</small>
                                @elseif($warrantyStatus === 'expiring_soon')
                                    <span class="badge badge-warning">Expiring Soon</span><br>
                                    <small class="text-muted">Expires {{ $equipment->warranty_expiry_date->format('M d, Y') }}</small>
                                @else
                                    <span class="badge badge-danger">Expired</span><br>
                                    <small class="text-muted">Expired {{ $equipment->warranty_expiry_date->format('M d, Y') }}</small>
                                @endif
                            @else
                                <span class="text-muted">Not specified</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Maintenance Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Maintenance Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Maintenance Status:</strong><br>
                            @if($maintenanceStatus === 'up_to_date')
                                <span class="badge badge-success"><i class="fas fa-check"></i> Up to Date</span>
                            @elseif($maintenanceStatus === 'due_soon')
                                <span class="badge badge-warning"><i class="fas fa-clock"></i> Due Soon</span>
                            @else
                                <span class="badge badge-danger"><i class="fas fa-exclamation-triangle"></i> Overdue</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <strong>Last Maintenance:</strong><br>
                            {{ $equipment->last_maintenance_date ? $equipment->last_maintenance_date->format('M d, Y') : 'Never' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Next Maintenance:</strong><br>
                            {{ $equipment->next_maintenance_date ? $equipment->next_maintenance_date->format('M d, Y') : 'Not scheduled' }}
                        </div>
                    </div>

                    @if($equipment->maintenance_interval_days)
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <strong>Maintenance Interval:</strong><br>
                            Every {{ $equipment->maintenance_interval_days }} days
                        </div>
                    </div>
                    @endif

                    @if($equipment->maintenance_notes)
                    <div class="mt-3">
                        <strong>Maintenance Notes:</strong>
                        <p class="text-muted">{{ $equipment->maintenance_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Technical Specifications -->
            @if($equipment->specifications)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Technical Specifications</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            @foreach($equipment->specifications as $key => $value)
                            <tr>
                                <td><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong></td>
                                <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @can('edit equipment')
                        <a href="{{ route('backend.equipment.edit', $equipment) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit Equipment
                        </a>
                        @endcan
                        
                        <button type="button" class="btn btn-success btn-sm" onclick="scheduleMaintenance()">
                            <i class="fas fa-calendar-plus"></i> Schedule Maintenance
                        </button>
                        
                        <button type="button" class="btn btn-info btn-sm" onclick="window.print()">
                            <i class="fas fa-print"></i> Print Details
                        </button>
                        
                        @can('delete equipment')
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteEquipment()">
                            <i class="fas fa-trash"></i> Delete Equipment
                        </button>
                        @endcan
                    </div>
                </div>
            </div>

            <!-- Equipment Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Equipment Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Age:</strong><br>
                        @if($equipment->purchase_date)
                            {{ $equipment->purchase_date->diffForHumans() }}
                        @else
                            Unknown
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>Days Since Last Maintenance:</strong><br>
                        @if($equipment->last_maintenance_date)
                            {{ $equipment->last_maintenance_date->diffInDays(now()) }} days
                        @else
                            Never maintained
                        @endif
                    </div>
                    
                    @if($equipment->next_maintenance_date)
                    <div class="mb-3">
                        <strong>Days Until Next Maintenance:</strong><br>
                        @if($equipment->next_maintenance_date > now())
                            {{ now()->diffInDays($equipment->next_maintenance_date) }} days
                        @else
                            <span class="text-danger">{{ $equipment->next_maintenance_date->diffInDays(now()) }} days overdue</span>
                        @endif
                    </div>
                    @endif

                    <div class="mb-3">
                        <strong>Created:</strong><br>
                        {{ $equipment->created_at->format('M d, Y g:i A') }}
                    </div>
                    
                    <div class="mb-3">
                        <strong>Last Updated:</strong><br>
                        {{ $equipment->updated_at->format('M d, Y g:i A') }}
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Activity</h6>
                </div>
                <div class="card-body">
                    @if($equipment->activities && $equipment->activities->count() > 0)
                        @foreach($equipment->activities->take(5) as $activity)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-circle text-primary" style="font-size: 0.5rem;"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="small">
                                    <strong>{{ $activity->description }}</strong><br>
                                    <span class="text-muted">{{ $activity->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted small">No recent activity recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Schedule Maintenance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="maintenanceForm">
                    <div class="mb-3">
                        <label for="maintenance_date" class="form-label">Maintenance Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="maintenance_date" name="maintenance_date" 
                               min="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="maintenance_notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="maintenance_notes" name="maintenance_notes" 
                                  rows="3" placeholder="Enter maintenance notes..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="confirmScheduleMaintenance()">Schedule Maintenance</button>
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
                <p><strong>{{ $equipment->name }}</strong></p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('backend.equipment.destroy', $equipment) }}" method="POST" style="display: inline;">
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
function scheduleMaintenance() {
    $('#maintenanceModal').modal('show');
}

function confirmScheduleMaintenance() {
    const date = $('#maintenance_date').val();
    const notes = $('#maintenance_notes').val();
    
    if (!date) {
        alert('Please select a maintenance date.');
        return;
    }

    $.ajax({
        url: '{{ route("backend.equipment.schedule-maintenance", $equipment) }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            maintenance_date: date,
            maintenance_notes: notes
        },
        success: function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('Error scheduling maintenance.');
        }
    });
}

function deleteEquipment() {
    $('#deleteModal').modal('show');
}

// Print styles
window.addEventListener('beforeprint', function() {
    document.body.classList.add('printing');
});

window.addEventListener('afterprint', function() {
    document.body.classList.remove('printing');
});
</script>

<style>
@media print {
    .btn, .card-header, .modal, .sidebar {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .container-fluid {
        padding: 0 !important;
    }
}
</style>
@endpush
