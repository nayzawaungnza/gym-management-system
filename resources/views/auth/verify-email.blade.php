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
                            Account activation link sent to your email address: 
                            <strong>{{ auth()->user()->email }}</strong>
                            Please follow the link inside to continue.
                        </p>

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
                            
                            <p class="mb-4">
                                Didn't get the email? Check your spam folder or 
                                <form class="d-inline" method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 align-baseline">
                                        click here to resend
                                    </button>
                                </form>
                            </p>
                            
                            <div class="d-flex justify-content-center gap-2">
                                <form method="POST" action="{{ route('verification.send') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bx bx-refresh me-1"></i>
                                        Resend Email
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-secondary">
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
</body>
</html>
