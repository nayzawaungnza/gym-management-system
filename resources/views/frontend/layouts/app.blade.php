<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'GYM - Transform Your Body & Mind')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        html {
            scroll-behavior: smooth;
        }
        
        /* Active nav link styling */
        .nav-link.active {
            color: #2563eb;
            position: relative;
        }
        
        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 6px;
            height: 6px;
            background-color: #2563eb;
            border-radius: 50%;
        }
        
        /* Header background transition */
        .header-scrolled {
            background-color: rgba(255, 255, 255, 0.98);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Section padding for fixed header */
        section {
            scroll-margin-top: 80px;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-white">
    @include('frontend.partials.header')
    
    <main class="pt-8">
        @yield('content')
    </main>
    
    @include('frontend.partials.footer')
    
    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition-all duration-300 opacity-0 invisible">
        <i data-lucide="arrow-up" class="h-5 w-5"></i>
    </button>
    
    @stack('scripts')
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('.scroll-link').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                if (targetSection) {
                    const headerHeight = 80;
                    const targetPosition = targetSection.offsetTop - headerHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
        
        // Header scroll effect and active nav highlighting
        window.addEventListener('scroll', function() {
            const header = document.getElementById('main-header');
            const backToTop = document.getElementById('back-to-top');
            const scrollPosition = window.scrollY;
            
            // Header background effect
            if (scrollPosition > 100) {
                header.classList.add('header-scrolled');
            } else {
                header.classList.remove('header-scrolled');
            }
            
            // Back to top button
            if (scrollPosition > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
            }
            
            // Active navigation highlighting
            const sections = document.querySelectorAll('section[id]');
            const navLinks = document.querySelectorAll('.nav-link');
            
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 120;
                const sectionHeight = section.offsetHeight;
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    current = section.getAttribute('id');
                }
            });
            
            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
        
        // Back to top functionality
        document.getElementById('back-to-top').addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = event.target.closest('button');
            
            if (!mobileMenu.contains(event.target) && !menuButton) {
                if (!mobileMenu.classList.contains('hidden')) {
                    toggleMobileMenu();
                }
            }
        });
    </script>
</body>
</html>
