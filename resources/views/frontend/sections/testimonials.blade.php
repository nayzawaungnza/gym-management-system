<section id="testimonials" class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">What Our Members Say</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Real stories from real people who've transformed their lives at FitZone.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            @php
                $testimonials = [
                    [
                        'name' => 'Jennifer Martinez',
                        'role' => 'Marketing Manager',
                        'content' => 'FitZone has completely transformed my fitness journey. The trainers are incredibly knowledgeable and the community is so supportive. I\'ve never felt stronger!',
                        'rating' => 5,
                        'image' => 'https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=80&q=80'
                    ],
                    [
                        'name' => 'Robert Kim',
                        'role' => 'Software Engineer',
                        'content' => 'The 24/7 access is perfect for my schedule. I can work out early morning or late night. The equipment is top-notch and always well-maintained.',
                        'rating' => 5,
                        'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=80&q=80'
                    ],
                    [
                        'name' => 'Maria Santos',
                        'role' => 'Teacher',
                        'content' => 'I love the variety of classes offered. From yoga to HIIT, there\'s something for everyone. The instructors make every session engaging and fun.',
                        'rating' => 5,
                        'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=80&q=80'
                    ]
                ];
            @endphp

            @foreach($testimonials as $testimonial)
                <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-lg transition-shadow duration-300 relative">
                    <!-- Quote Icon -->
                    <div class="absolute top-6 right-6 text-blue-100">
                        <i data-lucide="quote" class="h-8 w-8"></i>
                    </div>
                    
                    <!-- Rating -->
                    <div class="flex mb-4">
                        @for($i = 0; $i < $testimonial['rating']; $i++)
                            <i data-lucide="star" class="h-5 w-5 text-yellow-400 fill-current"></i>
                        @endfor
                    </div>
                    
                    <!-- Content -->
                    <p class="text-gray-600 mb-6 italic leading-relaxed">"{{ $testimonial['content'] }}"</p>
                    
                    <!-- Author -->
                    <div class="flex items-center">
                        <img src="{{ $testimonial['image'] }}" alt="{{ $testimonial['name'] }}" class="w-12 h-12 rounded-full mr-4 object-cover">
                        <div>
                            <div class="font-semibold text-gray-900">{{ $testimonial['name'] }}</div>
                            <div class="text-sm text-gray-600">{{ $testimonial['role'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
