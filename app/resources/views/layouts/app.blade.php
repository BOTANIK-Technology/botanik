<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') . ' — ' . ($buss_name ?? 'LOGIN') }}</title>

    <!-- Scripts -->
    <script>
        const SLUG = '{{ $slug ?? '' }}';
        const CURRENT_URL = '{{url()->current()}}';
    </script>
    @yield('critical-scripts')

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="{{ asset('css/font.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    @phone <link href="{{ asset('css/main-phone.css') }}" rel="stylesheet"> @endphone
    @guest
        <!-- Don't log user... -->
    @else
        <!-- Logged user... -->
        @role('owner')
            @if ($package == 'pro')
                <link href="{{ asset('css/owner.css') }}" rel="stylesheet">
            @else
                <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
            @endif
        @endrole
        @role('admin')
            <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
        @endrole
        @role('master')
            <link href="{{ asset('css/master.css') }}" rel="stylesheet">
        @endrole
    @endguest
    @yield('styles')
</head>
<body>
    <div id="sound"></div>
    @if($errors->any())
        <!-- Show errors -->
        @section('modal')
            @component('modal')
                @slot('class')
                    error
                @endslot

                @slot('header')
                    <span class="error fw500">{{__('Ошибка')}}</span>
                @endslot

                <ul class="error-list">
                    @foreach ($errors->all() as $error)
                        <li class="error">{{ $error }}</li>
                    @endforeach
                </ul>

                @slot('buttons')
                    <button class="btn error" type="button" onclick="closeModal()">{{__('ОК')}}</button>
                @endslot
            @endcomponent
        @endsection
    @endif

    <!-- Modal window -->
    @yield('modal')

    <div id="app">

        @guest
            <!-- Don't logged user... -->
        @else
            <!-- Logged user... -->

            @phone
                <!-- Only for phone user agents... -->
                <ul class="navbar flex justify-content-around align-items-center" id="nav">
                    <li>
                        <a href="{{ route('notice', $slug ?? '') }}">
                            <div class="notice {{ Route::currentRouteNamed('notice') ? 'active': '' }} @notice"></div>
                        </a>
                    </li>
                    <li>
                        <a class="navbar-brand" href="{{ url( Route::getCurrentRoute()->parameter('business')) }}">
                            <img src="{{ asset($logotype) }}">
                        </a>
                    </li>
                    <li>
                        <div id="menu-open" class="menu-icon pointer"></div>
                        <div id="menu-close" class="menu-close-icon pointer hide"></div>
                    </li>

                </ul>

                <div id="menu-main" class="hide">
                    @component('layouts/sidebar') @endcomponent
                </div>
            @else
                <!-- Else user agents... -->
                <nav class="navbar flex justify-content-between align-items-center">

                    <!-- Left Side Of Navbar -->
                    <a class="navbar-brand" href="{{ url( Route::getCurrentRoute()->parameter('business')) }}">
                        <img src="{{ asset($logotype) }}">
                    </a>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav flex justify-content-end">
                        <li class="nav-item">
                            <a href="{{ route('notice', $slug ?? '') }}">
                                <div class="notice {{ Route::currentRouteNamed('notice')? 'active': '' }} @notice"></div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                               href="{{ route('logout', Route::getCurrentRoute()->parameter('business')) }}"
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            >
                                <div class="exit"></div>
                            </a>
                            <form id="logout-form" action="{{ route('logout', Route::getCurrentRoute()->parameter('business')) }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </nav>
            @endphone

        @endguest

        <main @phone id="phone-main" @endphone>
            @yield('content')
        </main>
    </div>
    <footer>
        <!-- Non critical scripts -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="{{asset('js/modal.js')}}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        @phone <script src="{{asset('js/phone-main.js')}}"></script> @endphone
        @yield('scripts')
        @yield('modal-scripts')
    </footer>

    <script>
        function playSound(filename){
            // var mp3Source = '<source src="' + filename + '.mp3" type="audio/mpeg">';
            // var embedSource = '<embed hidden="true" autostart="true" loop="false" src="' + filename +'.mp3">';
            // document.getElementById("sound").innerHTML='<audio autoplay="autoplay">' + mp3Source + embedSource + '</audio>';

            const audio = new Audio(filename+'.mp3');
            audio.play();
        }
    </script>

</body>
</html>
