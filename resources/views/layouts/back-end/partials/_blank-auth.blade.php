<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{Session::get('direction')}}"
      style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="nofollow, noindex ">
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    {{-- Hardcoded link to the favicon in the public folder to prevent errors --}}
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    
    {{-- All the necessary CSS from your main admin layout --}}
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/vendor.min.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/google-fonts.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/custom.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/vendor/icon-set/style.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/theme.minc619.css?v=1.0')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/style.css')}}">
    <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/toastr.css')}}">
    @if(Session::get('direction') === "rtl")
        <link rel="stylesheet" href="{{dynamicAsset(path: 'public/assets/back-end/css/menurtl.css')}}">
    @endif
    @stack('css_or_js')
</head>

<body>
    {{-- A container to center the content vertically and horizontally --}}
    <main class="main bg-soft-light" id="content" role="main">
        <div class="container">
            <div class="row vh-100 d-flex align-items-center justify-content-center">
                <div class="col-md-7 col-lg-5">
                     <div class="card">
                        <div class="card-body">
                            {{-- The content from the OTP page will be injected here --}}
                            @yield('content')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- All the necessary JS from your main admin layout --}}
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/vendor.min.js')}}"></script>
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/theme.min.js')}}"></script>
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/bootstrap.min.js')}}"></script>
    <script src="{{dynamicAsset(path: 'public/assets/back-end/js/toastr.js')}}"></script>
    {!! Toastr::message() !!}
    
    {{-- The script stack for the OTP input functionality --}}
    @stack('script')

</body>
</html>