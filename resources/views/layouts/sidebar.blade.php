<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="{{url('/')}}" target="_blank" class="app-brand-link">
              <span class="app-brand-logo demo">
                <img src="../assets/frontend/images/SA_Icon.png" alt class="h-auto rounded site-icon" />
              </span>
              <span class="app-brand-text demo menu-text fw-bold">Venom Hub</span>
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