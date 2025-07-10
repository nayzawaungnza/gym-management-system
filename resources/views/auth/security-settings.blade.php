@extends('layouts.master', ['activePage' => 'security', 'titlePage' => 'Security Settings'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Account /</span> Security Settings
    </h4>

    <div class="row">
        <!-- Two-Factor Authentication -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Two-Factor Authentication</h5>
                </div>
                <div class="card-body">
                    @if(auth()->user()->two_factor_enabled)
                    <div class="alert alert-success">
                        <i class="bx bx-check-circle me-2"></i>
                        Two-factor authentication is <strong>enabled</strong>
                    </div>
                    
                    <form method="POST" action="{{ route('2fa.disable') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="password" class="form-label">Enter your password to disable 2FA</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-danger">Disable 2FA</button>
                    </form>
                    @else
                    <div class="alert alert-warning">
                        <i class="bx bx-shield-x me-2"></i>
                        Two-factor authentication is <strong>disabled</strong>
                    </div>
                    
                    <p class="mb-3">
                        Add an extra layer of security to your account by enabling two-factor authentication.
                    </p>
                    
                    <a href="{{ route('2fa.show') }}" class="btn btn-primary">
                        <i class="bx bx-shield-alt-2 me-1"></i>
                        Enable 2FA
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Password Change -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Change Password</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('password.change') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" name="new_password" required>
                            @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" 
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Password</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Login History -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Login Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>IP Address</th>
                                    <th>Location</th>
                                    <th>Device</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(auth()->user()->loginHistory()->latest()->take(10)->get() as $login)
                                <tr>
                                    <td>{{ $login->login_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $login->ip_address }}</td>
                                    <td>
                                        @if($login->location_city && $login->location_country)
                                        {{ $login->location_city }}, {{ $login->location_country }}
                                        @else
                                        <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($login->device_type && $login->browser)
                                        {{ $login->device_type }} - {{ $login->browser }}
                                        @else
                                        <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($login->is_successful)
                                        <span class="badge bg-success">Success</span>
                                        @else
                                        <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No login history available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Events -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Security Events</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Event Type</th>
                                    <th>Risk Level</th>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(auth()->user()->securityEvents()->latest()->take(10)->get() as $event)
                                <tr>
                                    <td>{{ $event->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</td>
                                    <td>
                                        @php
                                            $riskClass = match($event->risk_level) {
                                                'low' => 'success',
                                                'medium' => 'warning',
                                                'high' => 'danger',
                                                'critical' => 'dark',
                                                default => 'secondary'
                                            };
                                        @endphp
                                        <span class="badge bg-{{ $riskClass }}">{{ ucfirst($event->risk_level) }}</span>
                                    </td>
                                    <td>{{ $event->ip_address ?? 'N/A' }}</td>
                                    <td>
                                        @if($event->is_resolved)
                                        <span class="badge bg-success">Resolved</span>
                                        @else
                                        <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No security events</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
