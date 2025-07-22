<section id="trainers" class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Meet Our Trainers</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Our certified professionals are here to guide you on your fitness journey.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @if(isset($trainers) && $trainers->count() > 0)
                @foreach($trainers as $trainer)
                    <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
                        <div class="mx-auto mb-4">
                            <img src="{{ $trainer->profile_photo ? asset('storage/' . $trainer->profile_photo) : 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80' }}" 
                                 alt="{{ $trainer->first_name }} {{ $trainer->last_name }}" 
                                 class="w-32 h-32 rounded-full object-cover mx-auto">
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $trainer->first_name }} {{ $trainer->last_name }}</h3>
                        <p class="text-blue-600 font-medium mb-2">{{ $trainer->specialization ?? 'Fitness Trainer' }}</p>
                        <div class="text-sm text-gray-600 mb-4">
                            @if($trainer->hire_date)
                                {{ $trainer->hire_date->diffForHumans() }}
                            @else
                                New trainer
                            @endif
                        </div>
                        
                        @if($trainer->certifications && is_array($trainer->certifications) && count($trainer->certifications) > 0)
                            <div class="mb-4">
                                <div class="flex flex-wrap gap-1 justify-center">
                                    @foreach($trainer->certifications as $cert)
                                        @if(is_string($cert))
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">{{ $cert }}</span>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <p class="text-sm text-gray-600">
                            @if($trainer->bio)
                                {{ Str::limit($trainer->bio, 100) }}
                            @else
                                Experienced fitness professional dedicated to helping you achieve your goals.
                            @endif
                        </p>
                    </div>
                @endforeach
            @else
                @php
                    $defaultTrainers = [
                        [
                            'name' => 'Sarah Johnson',
                            'specialization' => 'HIIT & Strength Training',
                            'experience' => '8 years',
                            'certifications' => ['NASM-CPT', 'HIIT Specialist'],
                            'bio' => 'Passionate about helping clients achieve their strength and conditioning goals through high-intensity training.',
                            'image' => 'https://images.unsplash.com/photo-1594736797933-d0501ba2fe65?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'
                        ],
                        [
                            'name' => 'Mike Chen',
                            'specialization' => 'Yoga & Flexibility',
                            'experience' => '6 years',
                            'certifications' => ['RYT-500', 'Yin Yoga Certified'],
                            'bio' => 'Dedicated to promoting mindfulness and flexibility through various yoga practices and meditation.',
                            'image' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'
                        ],
                        [
                            'name' => 'David Wilson',
                            'specialization' => 'Powerlifting & Bodybuilding',
                            'experience' => '10 years',
                            'certifications' => ['CSCS', 'Powerlifting Coach'],
                            'bio' => 'Former competitive powerlifter specializing in strength development and muscle building techniques.',
                            'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'
                        ],
                        [
                            'name' => 'Emma Davis',
                            'specialization' => 'Pilates & Rehabilitation',
                            'experience' => '7 years',
                            'certifications' => ['PMA-CPT', 'Physical Therapy Assistant'],
                            'bio' => 'Focuses on corrective exercise and rehabilitation through Pilates and functional movement patterns.',
                            'image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80'
                        ]
                    ];
                @endphp

                @foreach($defaultTrainers as $trainer)
                    <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-lg transition-shadow">
                        <div class="mx-auto mb-4">
                            <img src="{{ $trainer['image'] }}" alt="{{ $trainer['name'] }}" class="w-32 h-32 rounded-full object-cover mx-auto">
                        </div>
                        <h3 class="text-xl font-bold mb-2">{{ $trainer['name'] }}</h3>
                        <p class="text-blue-600 font-medium mb-2">{{ $trainer['specialization'] }}</p>
                        <div class="text-sm text-gray-600 mb-4">{{ $trainer['experience'] }} experience</div>
                        
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-1 justify-center">
                                @foreach($trainer['certifications'] as $cert)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">{{ $cert }}</span>
                                @endforeach
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-600">{{ $trainer['bio'] }}</p>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</section>
