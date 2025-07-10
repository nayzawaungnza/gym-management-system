@extends('layouts.master', ['activePage' => 'schedule', 'titlePage' => 'Class Schedule'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Member /</span> Class Schedule
    </h4>

    <div class="row">
        @foreach($upcomingClasses as $class)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $class->class_name }}</h5>
                            <p class="text-muted mb-0">
                                <i class="bx bx-user me-1"></i>
                                {{ $class->trainer?->full_name ?? 'No Trainer' }}
                            </p>
                        </div>
                        @if(in_array($class->id, $registeredClassIds))
                        <span class="badge bg-success">Registered</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <p class="mb-1">
                            <i class="bx bx-calendar me-1"></i>
                            {{ $class->schedule->format('M d, Y') }}
                        </p>
                        <p class="mb-1">
                            <i class="bx bx-time me-1"></i>
                            {{ $class->schedule->format('H:i') }} 
                            ({{ $class->duration_minutes }} min)
                        </p>
                        <p class="mb-0">
                            <i class="bx bx-group me-1"></i>
                            {{ $class->current_capacity }}/{{ $class->max_capacity }} spots
                        </p>
                    </div>

                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ ($class->current_capacity / $class->max_capacity) * 100 }}%">
                        </div>
                    </div>

                    @if(in_array($class->id, $registeredClassIds))
                    <button class="btn btn-outline-danger btn-sm w-100" 
                            onclick="cancelRegistration('{{ $class->id }}')">
                        <i class="bx bx-x me-1"></i> Cancel Registration
                    </button>
                    @elseif($class->isFull())
                    <button class="btn btn-secondary btn-sm w-100" disabled>
                        <i class="bx bx-x me-1"></i> Class Full
                    </button>
                    @else
                    <button class="btn btn-primary btn-sm w-100" 
                            onclick="registerForClass('{{ $class->id }}')">
                        <i class="bx bx-plus me-1"></i> Register
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($upcomingClasses->isEmpty())
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bx bx-calendar-x display-4 text-muted mb-3"></i>
            <h5 class="text-muted">No Upcoming Classes</h5>
            <p class="text-muted">There are no classes scheduled at the moment.</p>
        </div>
    </div>
    @endif
</div>

<script>
function registerForClass(classId) {
    if (confirm('Are you sure you want to register for this class?')) {
        $.ajax({
            url: '/member/classes/' + classId + '/register',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error registering for class');
            }
        });
    }
}

function cancelRegistration(classId) {
    if (confirm('Are you sure you want to cancel your registration?')) {
        // Find the registration ID (you might need to pass this differently)
        $.ajax({
            url: '/member/classes/registration/cancel',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                class_id: classId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr) {
                alert('Error cancelling registration');
            }
        });
    }
}
</script>
@endsection
