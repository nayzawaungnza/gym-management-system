<section id="contact" class="py-20">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Get In Touch</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Ready to start your fitness journey? Contact us today or visit our gym.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
            <!-- Contact Information -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-2xl font-bold mb-6">Visit Our Gym</h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <i data-lucide="map-pin" class="h-6 w-6 text-blue-600 mr-4 mt-1 flex-shrink-0"></i>
                            <div>
                                <div class="font-semibold">Address</div>
                                <div class="text-gray-600">
                                    123 Fitness Street<br>
                                    Downtown, NY 10001
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="phone" class="h-6 w-6 text-blue-600 mr-4 flex-shrink-0"></i>
                            <div>
                                <div class="font-semibold">Phone</div>
                                <div class="text-gray-600">(555) 123-4567</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <i data-lucide="mail" class="h-6 w-6 text-blue-600 mr-4 flex-shrink-0"></i>
                            <div>
                                <div class="font-semibold">Email</div>
                                <div class="text-gray-600">info@fitzone.com</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <i data-lucide="clock" class="h-6 w-6 text-blue-600 mr-4 mt-1 flex-shrink-0"></i>
                            <div>
                                <div class="font-semibold">Hours</div>
                                <div class="text-gray-600">
                                    Mon-Fri: 5:00 AM - 11:00 PM<br>
                                    Sat-Sun: 6:00 AM - 10:00 PM<br>
                                    <span class="text-blue-600 font-medium">24/7 for Premium Members</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-bold mb-2">Send us a Message</h3>
                <p class="text-gray-600 mb-6">Have questions? We'd love to hear from you.</p>
                
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="text" name="first_name" placeholder="First Name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        <div>
                            <input type="text" name="last_name" placeholder="Last Name" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <input type="email" name="email" placeholder="Email Address" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <input type="tel" name="phone" placeholder="Phone Number"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <textarea name="message" placeholder="Your Message" rows="5" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-3 px-6 rounded-md hover:bg-blue-700 transition-colors font-medium">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
