<!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->

    <script src="{{url('/assets/vendor/libs/jquery/jquery.js')}}"></script>
    <script src="{{url('/assets/vendor/libs/popper/popper.js')}}"></script>
    <script src="{{url('/assets/vendor/js/bootstrap.js')}}"></script>
    <script src="{{url('/assets/vendor/libs/node-waves/node-waves.js')}}"></script>
    <script src="{{url('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
    <script src="{{url('/assets/vendor/libs/hammer/hammer.js')}}"></script>
    {{-- <script src="{{url('/assets/vendor/libs/i18n/i18n.js')}}"></script> --}}
    <script src="{{url('/assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
    <script src="{{url('/assets/vendor/js/menu.js')}}"></script>
    {{-- <script src="{{url('/js/menu.js')}}"></script> --}}
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
   @yield('vendor-script')

<!-- Vendors JS -->
    <script src="{{url('/assets/vendor/libs/toastr/toastr.js')}}"></script>
    <!-- Main JS -->
    <script src="{{url('/assets/js/main.js')}}"></script>

  

   <!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->
  <!-- Page JS -->
  
    <script src="{{url('/assets/js/ui-toasts.js')}}"></script>