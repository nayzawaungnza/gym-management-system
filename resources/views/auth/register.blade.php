<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Register - Gym Management System</title>
    <meta name="description" content="Create your Gym Management System account" />
    
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
</head>

<body>
    <!-- Content -->
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
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
                        
                        <h4 class="mb-2">Adventure starts here 🚀</h4>
                        <p class="mb-4">Make your gym management easy and fun!</p>

                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <form id="formAuthentication" class="mb-3" action="{{ route('register') }}" method="POST">
                            @csrf
                            
                            <!-- Role Selection -->
                            <div class="mb-3">
                                <label class="form-label">I want to join as</label>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-check custom-option custom-option-basic">
                                            <label class="form-check-label custom-option-content" for="role-member">
                                                <input class="form-check-input" type="radio" value="Member" id="role-member" name="role" {{ old('role') == 'Member' ? 'checked' : '' }} required />
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0">Member</span>
                                                    <i class="bx bx-user"></i>
                                                </span>
                                                <span class="custom-option-body">
                                                    <small>Join classes, track workouts</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-check custom-option custom-option-basic">
                                            <label class="form-check-label custom-option-content" for="role-trainer">
                                                <input class="form-check-input" type="radio" value="Trainer" id="role-trainer" name="role" {{ old('role') == 'Trainer' ? 'checked' : '' }} required />
                                                <span class="custom-option-header">
                                                    <span class="h6 mb-0">Trainer</span>
                                                    <i class="bx bx-dumbbell"></i>
                                                </span>
                                                <span class="custom-option-body">
                                                    <small>Conduct classes, manage members</small>
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Personal Information -->
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Enter your full name" autofocus required />
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" 
                                       placeholder="Enter your email" required />
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="Enter your phone number" />
                                @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" 
                                           name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                           aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                                <div class="form-text">
                                    Password must be at least 8 characters long
                                </div>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3 form-password-toggle">
                                <label class="form-label" for="password_confirmation">Confirm Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password_confirmation" class="form-control" 
                                           name="password_confirmation" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" 
                                           required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" 
                                           id="terms" name="terms" required />
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" class="text-primary">Terms & Conditions</a> and 
                                        <a href="#" class="text-primary">Privacy Policy</a>
                                    </label>
                                    @error('terms')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <button class="btn btn-primary d-grid w-100" type="submit">Sign up</button>
                        </form>

                        <p class="text-center">
                            <span>Already have an account?</span>
                            <a href="{{ route('login') }}">
                                <span>Sign in instead</span>
                            </a>
                        </p>

                        <!-- Social Registration -->
                        <div class="divider my-4">
                            <div class="divider-text">or</div>
                        </div>

                        <div class="d-flex justify-content-center gap-2">
                            <a href="#" class="btn btn-icon btn-outline-secondary">
                                <i class="tf-icons bx bxl-google"></i>
                            </a>
                            <a href="#" class="btn btn-icon btn-outline-secondary">
                                <i class="tf-icons bx bxl-facebook"></i>
                            </a>
                            <a href="#" class="btn btn-icon btn-outline-secondary">
                                <i class="tf-icons bx bxl-twitter"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- /Register Card -->
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
    
    <!-- Page JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const passwordToggles = document.querySelectorAll('.form-password-toggle .input-group-text');
            
            passwordToggles.forEach(toggle => {
                toggle.addEventListener('click', function() {
                    const passwordInput = this.parentElement.querySelector('input');
                    const icon = this.querySelector('i');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bx-hide');
                        icon.classList.add('bx-show');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bx-show');
                        icon.classList.add('bx-hide');
                    }
                });
            });

            // Password strength indicator
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('password_confirmation');
            
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                const strength = calculatePasswordStrength(password);
                updatePasswordStrengthIndicator(strength);
            });

            confirmPasswordInput.addEventListener('input', function() {
                const password = passwordInput.value;
                const confirmPassword = this.value;
                
                if (confirmPassword && password !== confirmPassword) {
                    this.setCustomValidity('Passwords do not match');
                } else {
                    this.setCustomValidity('');
                }
            });
        });

        function calculatePasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            return strength;
        }

        function updatePasswordStrengthIndicator(strength) {
            // You can add a visual password strength indicator here
            const strengthTexts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            const strengthColors = ['danger', 'warning', 'info', 'primary', 'success'];
            
            // Implementation depends on your UI requirements
        }
    </script>
</body>
</html>
