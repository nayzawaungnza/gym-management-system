<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="{{url('/')}}" target="_blank" class="app-brand-link">
              
              <span class="app-brand-text text-center demo menu-text fw-bold">GYM Management <br> System</span>
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
              <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
              <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
            </a>
          </div>

          

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
    

            @include('layouts/menu')
          </ul>
        </aside>