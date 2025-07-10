@extends('layouts.master', ['activePage' => 'qr-checkin', 'titlePage' => 'QR Check-in'])

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">Member /</span> QR Check-in
    </h4>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header text-center">
                    <h5 class="mb-0">Generate QR Code for Check-in</h5>
                </div>
                <div class="card-body text-center">
                    <div id="qr-container" style="display: none;">
                        <div id="qr-code-display" class="mb-3"></div>
                        <p class="text-muted">Show this QR code at the gym entrance</p>
                        <div class="alert alert-info">
                            <i class="bx bx-time me-1"></i>
                            QR code expires in <span id="countdown">5:00</span>
                        </div>
                    </div>
                    
                    <div id="generate-container">
                        <button id="generate-qr" class="btn btn-primary btn-lg">
                            <i class="bx bx-qr me-1"></i> Generate QR Code
                        </button>
                    </div>
                    
                    <div id="loading" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Generating QR code...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Alternative Check-in Methods</h5>
                </div>
                <div class="card-body">
                    <!-- Photo Check-in -->
                    <div class="mb-4">
                        <h6><i class="bx bx-camera me-1"></i> Photo Verification</h6>
                        <p class="text-muted small">Take a selfie for verification</p>
                        <input type="file" id="photo-input" accept="image/*" capture="user" style="display: none;">
                        <button id="photo-checkin" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-camera me-1"></i> Take Photo
                        </button>
                    </div>
                    
                    <!-- Location Check-in -->
                    <div class="mb-4">
                        <h6><i class="bx bx-location-plus me-1"></i> Location Verification</h6>
                        <p class="text-muted small">Check-in using your location</p>
                        <button id="location-checkin" class="btn btn-outline-success btn-sm">
                            <i class="bx bx-location-plus me-1"></i> Use Location
                        </button>
                    </div>
                    
                    <!-- Manual Check-in -->
                    <div>
                        <h6><i class="bx bx-user me-1"></i> Manual Check-in</h6>
                        <p class="text-muted small">Ask staff for manual check-in</p>
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                            <i class="bx bx-user me-1"></i> Contact Staff
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let countdownInterval;

document.getElementById('generate-qr').addEventListener('click', function() {
    generateQRCode();
});

document.getElementById('photo-checkin').addEventListener('click', function() {
    document.getElementById('photo-input').click();
});

document.getElementById('photo-input').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        photoCheckIn(e.target.files[0]);
    }
});

document.getElementById('location-checkin').addEventListener('click', function() {
    locationCheckIn();
});

function generateQRCode() {
    document.getElementById('generate-container').style.display = 'none';
    document.getElementById('loading').style.display = 'block';
    
    // Get current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            generateQRWithLocation(position.coords.latitude, position.coords.longitude);
        }, function() {
            generateQRWithLocation(null, null);
        });
    } else {
        generateQRWithLocation(null, null);
    }
}

function generateQRWithLocation(lat, lng) {
    $.ajax({
        url: '/member/qr-code/generate',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            location_lat: lat,
            location_lng: lng
        },
        success: function(response) {
            if (response.success) {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('qr-code-display').innerHTML = response.qr_code;
                document.getElementById('qr-container').style.display = 'block';
                
                // Start countdown
                startCountdown(300); // 5 minutes
            } else {
                alert(response.message);
                resetUI();
            }
        },
        error: function() {
            alert('Error generating QR code');
            resetUI();
        }
    });
}

function photoCheckIn(file) {
    if (!file) return;
    
    const formData = new FormData();
    formData.append('photo', file);
    formData.append('_token', '{{ csrf_token() }}');
    
    // Get location if available
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            formData.append('location_lat', position.coords.latitude);
            formData.append('location_lng', position.coords.longitude);
            submitPhotoCheckIn(formData);
        }, function() {
            submitPhotoCheckIn(formData);
        });
    } else {
        submitPhotoCheckIn(formData);
    }
}

function submitPhotoCheckIn(formData) {
    $.ajax({
        url: '/member/checkin/photo',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert(response.message);
            if (response.success) {
                // Redirect to attendance page or refresh
                window.location.href = '/member/attendance';
            }
        },
        error: function() {
            alert('Error processing photo check-in');
        }
    });
}

function locationCheckIn() {
    if (!navigator.geolocation) {
        alert('Geolocation is not supported by this browser.');
        return;
    }
    
    navigator.geolocation.getCurrentPosition(function(position) {
        $.ajax({
            url: '/member/checkin/location',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                location_lat: position.coords.latitude,
                location_lng: position.coords.longitude
            },
            success: function(response) {
                alert(response.message);
                if (response.success) {
                    window.location.href = '/member/attendance';
                }
            },
            error: function() {
                alert('Error processing location check-in');
            }
        });
    }, function() {
        alert('Unable to retrieve your location. Please try another method.');
    });
}

function startCountdown(seconds) {
    let remaining = seconds;
    
    countdownInterval = setInterval(function() {
        const minutes = Math.floor(remaining / 60);
        const secs = remaining % 60;
        
        document.getElementById('countdown').textContent = 
            minutes + ':' + (secs < 10 ? '0' : '') + secs;
        
        remaining--;
        
        if (remaining < 0) {
            clearInterval(countdownInterval);
            alert('QR code has expired. Please generate a new one.');
            resetUI();
        }
    }, 1000);
}

function resetUI() {
    document.getElementById('loading').style.display = 'none';
    document.getElementById('qr-container').style.display = 'none';
    document.getElementById('generate-container').style.display = 'block';
    
    if (countdownInterval) {
        clearInterval(countdownInterval);
    }
}
</script>
@endsection
