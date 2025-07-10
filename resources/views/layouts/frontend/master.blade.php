<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title> {{ $settings->site_name ?? config('app.name') }}</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{Storage::url($settings->favicon)}}">

    @include('layouts/frontend/sections/styles')
</head> 
<body class="dark-theme">
    

<!-- ==================== Mobile Menu Start Here ==================== -->
{{-- @include('layouts/frontend/mobilemenu') --}}
<!-- ==================== Mobile Menu End Here ==================== -->


    <!-- ==================== Header Start Here ==================== -->
    @include('layouts/frontend/menu')
    <!-- ==================== Header End Here ==================== -->

   @yield('content')  
    
<!-- ==================== Footer Start Here ==================== -->
@include('layouts/frontend/footer')
<!-- ==================== Footer End Here ==================== -->
  

        
@include('layouts/frontend/sections/scripts')
   
    </body>
</html>