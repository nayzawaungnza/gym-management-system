<!-- Start Navbar Area -->
        <div class="navbar-area" id="navbar">
            <div class="container">
                <nav class="navbar navbar-expand-lg">
                    <a class="navbar-brand" href="{{route('website.home')}}">
                        <img src="{{Storage::url($settings->site_logo)}}" class="logo" alt="Logo">
                    </a>
                    <div class="other-all-option">
                        
                        {{-- <div class="other-option d-lg-none">
                            <button data-bs-toggle="offcanvas" data-bs-target="#offcanvasTop" aria-controls="offcanvasTop" class="search-button"><i class='bx bx-search'></i></button>
                        </div> --}}
                        <a class="navbar-toggler" data-bs-toggle="offcanvas" href="#navbarOffcanvas" role="button" aria-controls="navbarOffcanvas">
                            <span class="burger-menu">
                                <span class="top-bar"></span>
                                <span class="middle-bar"></span>
                                <span class="bottom-bar"></span>
                            </span>
                        </a>
                    </div>

                    <div class="collapse navbar-collapse">
                        <ul class="navbar-nav">
                            
                            <li class="nav-item ">
                                <a href="{{route('website.home')}}" class="nav-link {{ $activePage == 'home' ? ' active' : '' }}">
                                    Home
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('website.services')}}" class="nav-link {{ $activePage == 'services' ? ' active' : '' }}">
                                    Services
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('website.portfolio')}}" class="nav-link {{ $activePage == 'portfolio' ? ' active' : '' }}">
                                    Portfolio
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('website.insights')}}" class="nav-link {{ $activePage == 'insights' ? ' active' : '' }}">
                                    Insights
                                </a>
                            </li>
                             <li class="nav-item">
                                <a href="{{route('website.contact-us')}}" class="nav-link {{ $activePage == 'contact' ? ' active' : '' }}">
                                    Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route('website.about-us')}}" class="nav-link {{ $activePage == 'about' ? ' active' : '' }}">
                                    About Us
                                </a>
                            </li>   
                        </ul>
                        <div class="others-option d-flex align-items-center">
                            
                            <div class="option-item">
                                <a href="{{route('website.contact-us')}}" class="social-ads-btn"><span>Start A Project?</span> <i class='bx bx-chevron-right'></i></a>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <!-- End Navbar Area -->

        <!-- Start Responsive Navbar Area -->
        <div class="responsive-navbar offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="navbarOffcanvas">
            <div class="offcanvas-header">
                <a class="logo d-inline-block" href="{{route('website.home')}}">
                   <img src="{{Storage::url($settings->site_logo)}}" class="responsive-logo" alt="Logo">
                </a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="accordion" id="navbarAccordion">
                    <div class="accordion-item">
                        <a class="accordion-link without-icon {{ $activePage == 'home' ? ' active' : '' }}" href="{{route('website.home')}}">
                           Home
                        </a>
                    </div>
                    <div class="accordion-item">
                        <a class="accordion-link without-icon {{ $activePage == 'services' ? ' active' : '' }}" href="{{route('website.services')}}">
                            Services
                        </a>
                    </div>
                    <div class="accordion-item">
                        <a class="accordion-link without-icon {{ $activePage == 'portfolio' ? ' active' : '' }}" href="{{route('website.portfolio')}}">
                            Portfolio
                        </a>
                    </div>
                    <div class="accordion-item">
                        <a class="accordion-link without-icon {{ $activePage == 'insights' ? ' active' : '' }}" href="{{route('website.insights')}}">
                            Insights
                        </a>
                    </div>
                    <div class="accordion-item">
                        <a class="accordion-link without-icon {{ $activePage == 'about' ? ' active' : '' }}" href="{{route('website.about-us')}}">
                            About Us
                        </a>
                    </div>
                
                          
                    <div class="accordion-item">
                        <a class="accordion-link without-icon {{ $activePage == 'contact' ? ' active' : '' }}" href="{{route('website.contact-us')}}">
                            Contact
                        </a>
                    </div>
                </div>
                @if($settings->facebook_url || $settings->twitter_url || $settings->instagram_url || $settings->linkedin_url || $settings->youtube_url)
                    <div class="offcanvas-contact-info">
                    <h4>Follow On</h4>
                    <ul class="social-profile list-style">
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
                            <li><a href="{{$settings->linkedin_url}}" target="_blank"><i class='bx bxl-linkedin'></i></a></li>
                        @endif
                        @if($settings->youtube_url)
                            <li><a href="{{$settings->youtube_url}}" target="_blank"><i class='bx bxl-youtube'></i></a></li>
                        @endif
                        
                    </ul>
                </div>
                @endif
                
            </div>
        </div>
        <!-- End Responsive Navbar Area -->