@extends('layouts.master', ['activePage' => 'attendance-verification', 'titlePage' => 'Attendance Verification Logs'])

@section('vendor-style')
<link rel="stylesheet" href="{{ url('/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Attendance /</span> Verification Logs
    </h4>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Verification Method</label>
                    <select name="verification_method" class="form-select">
                        <option value="">All Methods</option>
                        <option value="qr_code" {{ request('verification_method') == 'qr_code' ? 'selected' : '' }}>QR Code</option>
                        <option value="photo" {{ request('verification_method') == 'photo' ? 'selected' : '' }}>Photo</option>
                        <option value="biometric" {{ request('verification_method') == 'biometric' ? 'selected' : '' }}>Biometric</option>
                        <option value="rfid" {{ request('verification_method') == 'rfid' ? 'selected' : '' }}>RFID</option>
                        <option value="manual" {{ request('verification_method') == 'manual' ? 'selected' : '' }}>Manual</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Member ID</label>
                    <input type="text" name="member_id" class="form-control" value="{{ request('member_id') }}" placeholder="Enter member ID">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('attendance.verification-logs') }}" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Verification Logs -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Verification Logs</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Member</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Confidence</th>
                            <th>Location</th>
                            <th>Flags</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr class="{{ $log->is_flagged ? 'table-warning' : '' }}">
                            <td>{{ $log->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                <div>
                                    <strong>{{ $log->member?->full_name ?? 'Unknown' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $log->member?->email }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $log->verification_method)) }}</span>
                            </td>
                            <td>
                                @php
                                    $statusClass = match($log->verification_status) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'pending' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($log->verification_status) }}</span>
                            </td>
                            <td>
                                @if($log->confidence_score)
                                <div class="progress" style="width: 60px; height: 20px;">
                                    <div class="progress-bar" style="width: {{ $log->confidence_score }}%">
                                        {{ $log->confidence_score }}%
                                    </div>
                                </div>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($log->location_lat && $log->location_lng)
                                <small>
                                    {{ number_format($log->location_lat, 4) }},<br>
                                    {{ number_format($log->location_lng, 4) }}
                                </small>
                                @else
                                <span class="text-muted">No location</span>
                                @endif
                            </td>
                            <td>
                                @if($log->is_flagged)
                                <span class="badge bg-warning">
                                    <i class="bx bx-flag"></i> Flagged
                                </span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        Actions
                                    </button>
                                    <ul class="dropdown-menu">
                                        @if($log->photo_path)
                                        <li>
                                            <a class="dropdown-item" href="{{ Storage::url($log->photo_path) }}" target="_blank">
                                                <i class="bx bx-image me-1"></i> View Photo
                                            </a>
                                        </li>
                                        @endif
                                        @if(!$log->is_flagged && $log->verification_status === 'approved')
                                        <li>
                                            <a class="dropdown-item text-warning" href="javascript:void(0);" onclick="flagSuspicious('{{ $log->id }}')">
                                                <i class="bx bx-flag me-1"></i> Flag as Suspicious
                                            </a>
                                        </li>
                                        @endif
                                        @if($log->is_flagged)
                                        <li>
                                            <a class="dropdown-item text-success" href="javascript:void(0);" onclick="approveFlagged('{{ $log->id }}')">
                                                <i class="bx bx-check me-1"></i> Approve
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bx bx-search display-4 text-muted"></i>
                                <p class="text-muted mt-2">No verification logs found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{ $logs->links() }}
        </div>
    </div>
</div>

<script>
function flagSuspicious(id) {
    if (confirm('Are you sure you want to flag this attendance as suspicious?')) {
        $.ajax({
            url: '/admin/attendance/verification/' + id + '/flag',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }
}

function approveFlagged(id) {
    if (confirm('Are you sure you want to approve this flagged attendance?')) {
        $.ajax({
            url: '/admin/attendance/verification/' + id + '/approve',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }
}
</script>
@endsection
