<!-- Start Footer Area -->
        <div class="footer-area">
            <div class="footer-top-info">
                <div class="container">
                    <div class="row g-0 align-items-center">
                        <div class="col-lg-6 col-md-6 col-12">
                            <p class="mb-0 text-white">Drop us on Email</p>
                            @if($settings->contact_email)
                            <a class="footer-email text-white" href="mailto:{{$settings->contact_email}}">{{$settings->contact_email}}</a>
                            @else
                                <a class="footer-email text-white" href="mailto:hello@website.com">hello@website.com</a>
                            @endif
                            
                        </div>
                        <div class="col-lg-6 col-md-6 col-12">
                            <div class="content">
                                @if($settings->facebook_url || $settings->twitter_url || $settings->instagram_url || $settings->linkedin_url || $settings->youtube_url || $settings->whatsapp_url || $settings->tiktok_url || $settings->viber)
                                <ul>
                                    {{-- @if($settings->viber)
                                        <li><a href="{{$settings->viber}}" target="_blank"><i class='bx bxl-viber'></i></a></li>
                                    @endif --}}
                                    @if($settings->facebook_url)
                                        <li><a href="{{$settings->facebook_url}}" target="_blank"><i class='bx bxl-facebook'></i></a></li>
                                    @endif
                                    @if($settings->twitter_url)
                                        <li><a href="{{$settings->twitter_url}}" target="_blank"><i class='bx bxl-twitter'></i></a></li>
                                    @endif
                                    @if($settings->instagram_url)
                                        <li><a href="{{$settings->instagram_url}}" target="_blank"><i class='bx bxl-instagram'></i></a></li>
                                    @endif
                                    @if($settings->linkedin_url)
                                        <li><a href="{{$settings->linkedin_url}}" target="_blank"><i class='bx bxl-linkedin-square'></i></a></li>
                                    @endif
                                    @if($settings->youtube_url)
                                        <li><a href="{{$settings->youtube_url}}" target="_blank"><i class='bx bxl-youtube'></i></a></li>
                                    @endif
                                    @if($settings->whatsapp_url)
                                        <li><a href="{{$settings->whatsapp_url}}" target="_blank"><i class='bx bxl-whatsapp'></i></a></li>
                                    @endif
                                    {{-- @if($settings->tiktok_url)
                                        <li><a href="{{$settings->tiktok_url}}" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"></path></svg></a></li>
                                    @endif --}}

                                    
                                </ul>
                                @endif
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer-widget-info">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-5 col-sm-12 col-md-12">
                            <div class="subscribe-area">
                                <h4>Subscribe To Social Ads!</h4>
                                
                                <div class="subscribe-wrapper"> 
                                    <div class="subscribe-box"> 
                                        <form id="subscribe-form" action="{{ route('website.subscribe') }}" method="POST">
                                            @csrf
                                            <div class="row align-items-center"> 
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" placeholder="Your email address @" name="email" required autocomplete="off">
                                                </div> 
                                                <div class="col-lg-4"> 
                                                    <button type="submit" class="btn">
                                                        <span id="btn-text">Subscribe</span>
                                                        <span id="btn-spinner" class="spinner-border spinner-border-sm d-none"></span>
                                                    </button>
                                                </div> 
                                            </div>
                                            
                                        </form>
                                    </div>
                                    <div id="subscribe-message" class="mt-2"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-sm-6 col-md-4">
                            <div class="footer-widget">
                                <h4>Our Services</h4>
                                <ul>
                                    @foreach($services_menu as $service)
                                        <li><a href="{{route('website.service-detail', $service->slug)}}" target="_blank">{{$service->name}}</a></li>
                                    @endforeach
                                    
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 col-md-4">
                            <div class="footer-widget">
                                <h4>Social Ads</h4>
                                <ul>
                                    <li><a href="{{route('website.about-us')}}" targrt="_blank">About Us</a></li>
                                    <li><a href="#" targrt="_blank">Careers</a></li>
                                    <li><a href="#" targrt="_blank">Why Choose Us</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-2 col-sm-6 col-md-4">
                            <div class="footer-widget">
                                <h4>Resources</h4>
                                <ul>
                                    <li><a href="#">Blog</a></li>
                                    <li><a href="#">Case Studies</a></li>
                                    <li><a href="#">Digital Marketing Tips</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="copy-right-area">
                <div class="container">
                    <div class="row"> 
                        <div class="col-xl-12 col-lg-12">
                            <div class="text-center">
                                @if($settings->copyright_text)
                                    {!!$settings->copyright_text!!}
                                @else
                                    <p>Copyright © {{ date('Y') }} {{$settings->site_name}}. All rights reserved.</p>
                                @endif
                                
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <!-- End Footer Area -->

        <div class="go-top active">
            <i class="bx bx-up-arrow-alt"></i>
        </div>