<section id="home" class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" 
             style="background-image: url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/70"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 container mx-auto px-4 text-center text-white">
        <div class="max-w-4xl mx-auto">
            <!-- Main Heading -->
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-bold mb-6 leading-tight">
                Transform Your
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-blue-600">
                    Body & Mind
                </span>
            </h1>
            
            <!-- Subtitle -->
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto text-gray-200 leading-relaxed">
                Join FitZone and discover the perfect blend of cutting-edge equipment, expert trainers, and a supportive
                community to achieve your fitness goals.
            </p>

            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-16">
                <a href="{{ route('register') }}" class="group inline-flex items-center px-8 py-4 bg-blue-600 text-white text-lg font-medium rounded-full hover:bg-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    Start Your Journey
                    <i data-lucide="arrow-right" class="ml-2 h-5 w-5 group-hover:translate-x-1 transition-transform duration-300"></i>
                </a>
                <button class="group inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 text-white text-lg font-medium rounded-full hover:bg-white/20 transition-all duration-300">
                    <i data-lucide="play" class="mr-2 h-5 w-5 group-hover:scale-110 transition-transform duration-300"></i>
                    Watch Tour
                </button>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 max-w-4xl mx-auto">
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-400 mb-2">
                        @if(isset($stats) && isset($stats['members']))
                            {{ $stats['members'] }}
                        @else
                            500+
                        @endif
                    </div>
                    <div class="text-sm md:text-base text-gray-300">Active Members</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-400 mb-2">
                        @if(isset($stats) && isset($stats['trainers']))
                            {{ $stats['trainers'] }}
                        @else
                            15+
                        @endif
                    </div>
                    <div class="text-sm md:text-base text-gray-300">Expert Trainers</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-400 mb-2">
                        @if(isset($stats) && isset($stats['classes']))
                            {{ $stats['classes'] }}
                        @else
                            50+
                        @endif
                    </div>
                    <div class="text-sm md:text-base text-gray-300">Classes Weekly</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl md:text-4xl font-bold text-blue-400 mb-2">5</div>
                    <div class="text-sm md:text-base text-gray-300">Years Experience</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
        <a href="#about" class="scroll-link">
            <i data-lucide="chevron-down" class="h-8 w-8"></i>
        </a>
    </div>
</section>
