<section id="membership" class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Choose Your Plan</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Select the membership that fits your lifestyle and fitness goals.
            </p>
        </div>

        @if(isset($membershipTypesAvailable) && $membershipTypesAvailable)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                @php
                    $plans = [
                        [
                            'name' => 'Basic',
                            'price' => 29,
                            'description' => 'Perfect for getting started',
                            'features' => [
                                'Gym access during business hours',
                                'Basic equipment usage',
                                'Locker room access',
                                'Free fitness assessment'
                            ],
                            'popular' => false
                        ],
                        [
                            'name' => 'Premium',
                            'price' => 49,
                            'description' => 'Most popular choice',
                            'features' => [
                                '24/7 gym access',
                                'All equipment and facilities',
                                'Group fitness classes',
                                'Personal trainer consultation',
                                'Nutrition guidance',
                                'Guest passes (2/month)'
                            ],
                            'popular' => true
                        ],
                        [
                            'name' => 'Elite',
                            'price' => 79,
                            'description' => 'Ultimate fitness experience',
                            'features' => [
                                'Everything in Premium',
                                'Unlimited personal training',
                                'Priority class booking',
                                'Massage therapy sessions',
                                'Meal planning service',
                                'Unlimited guest passes'
                            ],
                            'popular' => false
                        ]
                    ];
                @endphp

                @foreach($plans as $plan)
                    <div class="relative bg-white rounded-lg shadow-md {{ $plan['popular'] ? 'border-2 border-blue-600 transform scale-105' : 'border border-gray-200' }}">
                        @if($plan['popular'])
                            <div class="absolute -top-3 left-1/2 transform -translate-x-1/2">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-medium">Most Popular</span>
                            </div>
                        @endif
                        
                        <div class="p-6 text-center">
                            <h3 class="text-2xl font-bold mb-2">{{ $plan['name'] }}</h3>
                            <p class="text-gray-600 mb-4">{{ $plan['description'] }}</p>
                            <div class="mb-6">
                                <span class="text-4xl font-bold">${{ $plan['price'] }}</span>
                                <span class="text-gray-600">/month</span>
                            </div>
                            
                            <ul class="space-y-3 mb-6 text-left">
                                @foreach($plan['features'] as $feature)
                                    <li class="flex items-center">
                                        <i data-lucide="check" class="h-5 w-5 text-blue-600 mr-3 flex-shrink-0"></i>
                                        <span class="text-sm">{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            
                            <a href="{{ route('register') }}" class="w-full inline-block px-6 py-3 {{ $plan['popular'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} text-center font-medium rounded-md hover:{{ $plan['popular'] ? 'bg-blue-700' : 'bg-gray-200' }} transition-colors">
                                Get Started
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="max-w-2xl mx-auto bg-yellow-50 border border-yellow-200 rounded-lg p-8 text-center">
                <i data-lucide="alert-triangle" class="h-12 w-12 text-yellow-600 mx-auto mb-4"></i>
                <h3 class="text-xl font-semibold text-yellow-800 mb-2">Membership Plans Coming Soon</h3>
                <p class="text-yellow-700 mb-4">We're currently updating our membership plans. Please contact us directly for membership information.</p>
                <a href="#contact" class="inline-flex items-center px-6 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i data-lucide="phone" class="h-4 w-4 mr-2"></i>
                    Contact Us
                </a>
            </div>
        @endif
    </div>
</section>
