<footer class="bg-white border-t">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand -->
            <div class="space-y-4">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <i data-lucide="dumbbell" class="h-8 w-8 text-blue-600"></i>
                    <span class="text-2xl font-bold">GYM</span>
                </a>
                <p class="text-gray-600">
                    Transform your body and mind with our state-of-the-art facilities and expert guidance.
                </p>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors">
                        <i data-lucide="facebook" class="h-5 w-5"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors">
                        <i data-lucide="twitter" class="h-5 w-5"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors">
                        <i data-lucide="instagram" class="h-5 w-5"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-blue-600 transition-colors">
                        <i data-lucide="youtube" class="h-5 w-5"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="font-semibold mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="#home" class="text-gray-600 hover:text-blue-600 transition-colors">Home</a></li>
                    <li><a href="#services" class="text-gray-600 hover:text-blue-600 transition-colors">Services</a></li>
                    <li><a href="#membership" class="text-gray-600 hover:text-blue-600 transition-colors">Membership</a></li>
                    <li><a href="#classes" class="text-gray-600 hover:text-blue-600 transition-colors">Classes</a></li>
                    <li><a href="#trainers" class="text-gray-600 hover:text-blue-600 transition-colors">Trainers</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h3 class="font-semibold mb-4">Services</h3>
                <ul class="space-y-2">
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition-colors">Personal Training</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition-colors">Group Classes</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition-colors">Nutrition Coaching</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition-colors">Health Assessment</a></li>
                    <li><a href="#" class="text-gray-600 hover:text-blue-600 transition-colors">Massage Therapy</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 class="font-semibold mb-4">Contact Info</h3>
                <ul class="space-y-2 text-gray-600">
                    <li>123 Fitness Street</li>
                    <li>Downtown, NY 10001</li>
                    <li>(555) 123-4567</li>
                    <li>info@fitzone.com</li>
                </ul>
            </div>
        </div>

        <div class="border-t mt-8 pt-8 text-center text-gray-600">
            <p>&copy; {{ date('Y') }} FitZone. All rights reserved.</p>
        </div>
    </div>
</footer>
