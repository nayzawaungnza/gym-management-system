<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="/assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Two-Factor Authentication - Gym Management System</title>
    <meta name="description" content="Enter your two-factor authentication code" />
    
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
            <div class="authentication-inner py-4">
                <!-- Two Factor Auth -->
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
                        
                        <h4 class="mb-2">Two-Step Verification 💬</h4>
                        <p class="mb-4">
                            We sent a verification code to your mobile. Enter the code from the mobile in the field below.
                        </p>
                        <p class="mb-0 fw-semibold">******1234</p>

                        @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible mt-3" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <form id="twoFactorForm" class="mb-3" action="{{ route('2fa.verify') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="code" class="form-label">Type your 6 digit security code</label>
                                <div class="auth-input-wrapper d-flex align-items-center justify-content-sm-between numeral-mask-wrapper">
                                    <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" 
                                           maxlength="1" autofocus />
                                    <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" 
                                           maxlength="1" />
                                    <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" 
                                           maxlength="1" />
                                    <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" 
                                           maxlength="1" />
                                    <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" 
                                           maxlength="1" />
                                    <input type="text" class="form-control auth-input h-px-50 text-center numeral-mask text-center h-px-50 mx-1 my-2" 
                                           maxlength="1" />
                                </div>
                                <input type="hidden" name="code" id="verification-code" />
                            </div>
                            
                            <button class="btn btn-primary d-grid w-100" type="submit">Verify my account</button>
                        </form>
                        
                        <div class="text-center">
                            <span>Didn't get the code?</span>
                            <a href="javascript:void(0);" onclick="resendCode()">Resend</a>
                        </div>
                    </div>
                </div>
                <!-- /Two Factor Auth -->
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
            const inputs = document.querySelectorAll('.auth-input');
            const hiddenInput = document.getElementById('verification-code');
            
            inputs.forEach((input, index) => {
                input.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');
                    
                    // Move to next input
                    if (this.value.length === 1 && index < inputs.length - 1) {
                        inputs[index + 1].focus();
                    }
                    
                    // Update hidden input
                    updateHiddenInput();
                });
                
                input.addEventListener('keydown', function(e) {
                    // Move to previous input on backspace
                    if (e.key === 'Backspace' && this.value === '' && index > 0) {
                        inputs[index - 1].focus();
                    }
                });
                
                input.addEventListener('paste', function(e) {
                    e.preventDefault();
                    const paste = (e.clipboardData || window.clipboardData).getData('text');
                    const numbers = paste.replace(/[^0-9]/g, '').slice(0, 6);
                    
                    for (let i = 0; i < numbers.length && i < inputs.length; i++) {
                        inputs[i].value = numbers[i];
                    }
                    
                    updateHiddenInput();
                    
                    // Focus on the next empty input or the last one
                    const nextEmpty = Array.from(inputs).findIndex(input => input.value === '');
                    if (nextEmpty !== -1) {
                        inputs[nextEmpty].focus();
                    } else {
                        inputs[inputs.length - 1].focus();
                    }
                });
            });
            
            function updateHiddenInput() {
                const code = Array.from(inputs).map(input => input.value).join('');
                hiddenInput.value = code;
                
                // Auto-submit when all 6 digits are entered
                if (code.length === 6) {
                    setTimeout(() => {
                        document.getElementById('twoFactorForm').submit();
                    }, 500);
                }
            }
        });
        
        function resendCode() {
            // Implement resend functionality
            alert('Verification code resent!');
        }
    </script>
</body>
</html>
