<section class="py-20 bg-blue-600 text-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="bg-white/10 rounded-lg p-6 text-center">
                <i data-lucide="users" class="h-8 w-8 mx-auto mb-4"></i>
                <div class="text-3xl md:text-4xl font-bold mb-2">
                    @if(isset($stats) && isset($stats['members']))
                        {{ $stats['members'] }}
                    @else
                        500+
                    @endif
                </div>
                <div class="text-lg font-semibold mb-1">Happy Members</div>
                <div class="text-sm opacity-80">Active community</div>
            </div>
            <div class="bg-white/10 rounded-lg p-6 text-center">
                <i data-lucide="trophy" class="h-8 w-8 mx-auto mb-4"></i>
                <div class="text-3xl md:text-4xl font-bold mb-2">
                    @if(isset($stats) && isset($stats['weekly_classes']))
                        {{ $stats['weekly_classes'] }}
                    @else
                        50+
                    @endif
                </div>
                <div class="text-lg font-semibold mb-1">Classes Weekly</div>
                <div class="text-sm opacity-80">Diverse programs</div>
            </div>
            <div class="bg-white/10 rounded-lg p-6 text-center">
                <i data-lucide="clock" class="h-8 w-8 mx-auto mb-4"></i>
                <div class="text-3xl md:text-4xl font-bold mb-2">24/7</div>
                <div class="text-lg font-semibold mb-1">Access Hours</div>
                <div class="text-sm opacity-80">Always open</div>
            </div>
            <div class="bg-white/10 rounded-lg p-6 text-center">
                <i data-lucide="star" class="h-8 w-8 mx-auto mb-4"></i>
                <div class="text-3xl md:text-4xl font-bold mb-2">4.9</div>
                <div class="text-lg font-semibold mb-1">Rating</div>
                <div class="text-sm opacity-80">Member satisfaction</div>
            </div>
        </div>
    </div>
</section>
