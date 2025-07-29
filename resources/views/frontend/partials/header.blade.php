<header class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-sm shadow-sm transition-all duration-300" id="main-header">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
           <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center space-x-2">
                    <i data-lucide="dumbbell" class="h-8 w-8 text-blue-600"></i>
                    <span class="text-2xl font-bold text-gray-900">GYM</span>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <nav class="hidden lg:flex items-center space-x-8">
                <a href="{{ url('/') }}" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">
                    Home
                </a>
                <a href="#about" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-link">
                    About
                </a>
                <a href="#services" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-link">
                    Services
                </a>
                <a href="#membership" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-link">
                    Membership
                </a>
                <a href="#classes" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-link">
                    Classes
                </a>
                <a href="#trainers" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-link">
                    Trainers
                </a>
                <a href="#testimonials" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-link">
                    Reviews
                </a>
                <a href="#contact" class="nav-link text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200 scroll-link">
                    Contact
                </a>
            </nav>

            <!-- Auth Buttons -->
            <div class="hidden lg:flex items-center space-x-4">
                @guest
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">
                        Login
                    </a>
                    <a href="{{ route('register') }}" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 transition-colors duration-200 shadow-md hover:shadow-lg">
                        Join Now
                    </a>
                @else
                    <div class="relative group">
                        <button class="flex items-center space-x-2 focus:outline-none">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="h-8 w-8 rounded-full object-cover border-2 border-blue-500">
                            @else
                                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <span class="text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                            <i data-lucide="chevron-down" class="h-4 w-4 text-gray-500"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 border border-gray-200">
                            <div class="px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('member.profile') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="user" class="h-4 w-4 mr-2"></i> Profile
                            </a>
                            <a href="{{ route('member.profile.edit') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="settings" class="h-4 w-4 mr-2"></i> Edit Profile
                            </a>
                            <a href="{{ url('dashboard') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="layout-dashboard" class="h-4 w-4 mr-2"></i> Dashboard
                            </a>
                            <a href="{{ url('attendances') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="layout-list" class="h-4 w-4 mr-2"></i> Attendances
                            </a>
                            <a href="{{ url('my-membership') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i data-lucide="credit-card" class="h-4 w-4 mr-2"></i> My Subscriptions
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i data-lucide="log-out" class="h-4 w-4 mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <button class="lg:hidden p-2 text-gray-700 hover:text-blue-600 transition-colors duration-200" onclick="toggleMobileMenu()">
                <i data-lucide="menu" class="h-6 w-6" id="menu-icon"></i>
                <i data-lucide="x" class="h-6 w-6 hidden" id="close-icon"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="lg:hidden bg-white border-t border-gray-200 hidden">
            <nav class="py-4 space-y-2">
                <a href="{{ url('/') }}" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 mobile-link">
                    Home
                </a>
                <a href="#about" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 scroll-link mobile-link">
                    About
                </a>
                <a href="#services" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 scroll-link mobile-link">
                    Services
                </a>
                <a href="#membership" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 scroll-link mobile-link">
                    Membership
                </a>
                <a href="#classes" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 scroll-link mobile-link">
                    Classes
                </a>
                <a href="#trainers" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 scroll-link mobile-link">
                    Trainers
                </a>
                <a href="#testimonials" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 scroll-link mobile-link">
                    Reviews
                </a>
                <a href="#contact" class="block px-4 py-3 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200 scroll-link mobile-link">
                    Contact
                </a>
                
                <!-- Mobile Auth Buttons -->
                <div class="px-4 py-3 border-t border-gray-200 space-y-2">
                    @guest
                        <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2 text-gray-700 hover:text-blue-600 font-medium transition-colors duration-200">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white font-medium rounded-full hover:bg-blue-700 transition-colors duration-200">
                            Join Now
                        </a>
                    @else
                        <div class="flex items-center space-x-3 mb-3 px-2 py-2 bg-gray-50 rounded-lg">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                                     alt="{{ Auth::user()->name }}" 
                                     class="h-10 w-10 rounded-full object-cover border-2 border-blue-500">
                            @else
                                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                        <a href="{{ route('member.profile') }}" class="flex items-center px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200">
                            <i data-lucide="user" class="h-5 w-5 mr-2"></i> Profile
                        </a>
                        <a href="{{ route('member.profile.edit') }}" class="flex items-center px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200">
                            <i data-lucide="settings" class="h-5 w-5 mr-2"></i> Edit Profile
                        </a>
                        <a href="{{ url('dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200">
                            <i data-lucide="layout-dashboard" class="h-5 w-5 mr-2"></i> Dashboard
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center w-full text-left px-4 py-2 text-gray-700 hover:text-blue-600 hover:bg-gray-50 font-medium transition-colors duration-200">
                                <i data-lucide="log-out" class="h-5 w-5 mr-2"></i> Logout
                            </button>
                        </form>
                    @endguest
                </div>
            </nav>
        </div>
    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');
        
        menu.classList.toggle('hidden');
        menuIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    }

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.mobile-link').forEach(link => {
        link.addEventListener('click', function(e) {
            // Only toggle menu for anchor links, not full URLs
            if (this.getAttribute('href').startsWith('#')) {
                toggleMobileMenu();
            }
            // Let the browser handle normal links
        });
    });

    // Initialize smooth scrolling for anchor links only
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
</script>