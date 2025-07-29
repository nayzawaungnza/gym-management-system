<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Verify Email - Gym Management System</title>
    <meta name="description" content="Verify your email address" />
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon/favicon.ico" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="/assets/vendor/fonts/boxicons.css" />
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="/assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="/assets/css/demo.css" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    
    <!-- Page CSS -->
    <link rel="stylesheet" href="/assets/vendor/css/pages/page-auth.css" />
    
    <!-- Helpers -->
    <script src="/assets/vendor/js/helpers.js"></script>
    <script src="/assets/js/config.js"></script>

    <style>
        .verification-code-input {
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5rem;
            font-weight: bold;
        }
        .code-timer {
            font-size: 0.875rem;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Verify Email -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center mb-4">
                            <a href="/" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <i class="bx bx-dumbbell text-primary" style="font-size: 2rem;"></i>
                                </span>
                                <span class="app-brand-text demo text-body fw-bolder">GymMS</span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        
                        <h4 class="mb-2">Verify your email ✉️</h4>
                        <p class="mb-4">
                            We've sent a 6-digit verification code to: 
                            <strong>{{ auth()->user()->email }}</strong>
                        </p>

                        @if (session('status') == 'verification-code-sent')
                        <div class="alert alert-success alert-dismissible" role="alert">
                            A new verification code has been sent to your email address.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        @if (session('status') == 'verification-link-sent')
                        <div class="alert alert-success alert-dismissible" role="alert">
                            A new verification link has been sent to your email address.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="text-center">
                            <div class="mb-4">
                                <i class="bx bx-envelope display-4 text-primary"></i>
                            </div>
                            
                            <!-- Verification Code Form -->
                            <form method="POST" action="{{ route('verification.verify.code') }}" class="mb-4">
                                @csrf
                                <div class="mb-3">
                                    <label for="verification_code" class="form-label">Enter Verification Code</label>
                                    <input 
                                        type="text" 
                                        class="form-control verification-code-input @error('verification_code') is-invalid @enderror" 
                                        id="verification_code" 
                                        name="verification_code" 
                                        maxlength="6" 
                                        placeholder="000000"
                                        autocomplete="off"
                                        required
                                    >
                                    @error('verification_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    <i class="bx bx-check me-1"></i>
                                    Verify Code
                                </button>
                            </form>

                            <p class="mb-4 code-timer">
                                Code expires in <span id="timer">10:00</span> minutes
                            </p>
                            
                            <p class="mb-4">
                                Didn't get the code? Check your spam folder or click below to resend.
                            </p>
                            
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                <form method="POST" action="{{ route('verification.resend.code') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-primary">
                                        <i class="bx bx-refresh me-1"></i>
                                        Resend Code
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary">
                                        <i class="bx bx-link me-1"></i>
                                        Send Link Instead
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bx bx-log-out me-1"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Verify Email -->
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- Core JS -->
    <script src="/assets/vendor/libs/jquery/jquery.js"></script>
    <script src="/assets/vendor/libs/popper/popper.js"></script>
    <script src="/assets/vendor/js/bootstrap.js"></script>
    <script src="/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="/assets/vendor/js/menu.js"></script>
    
    <!-- Main JS -->
    <script src="/assets/js/main.js"></script>

    <script>
        // Auto-format verification code input
        document.getElementById('verification_code').addEventListener('input', function(e) {
            // Remove any non-numeric characters
            this.value = this.value.replace(/\D/g, '');
            
            // Limit to 6 digits
            if (this.value.length > 6) {
                this.value = this.value.slice(0, 6);
            }
        });

        // Countdown timer (optional - you can remove this if not needed)
        let timeLeft = 600; // 10 minutes in seconds
        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft > 0) {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            } else {
                timerElement.textContent = 'Expired';
                timerElement.style.color = '#dc3545';
            }
        }
        
        updateTimer();
    </script>
</body>
</html>