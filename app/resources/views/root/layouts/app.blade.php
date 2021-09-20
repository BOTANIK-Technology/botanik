<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @yield('critical-scripts')

    <!-- Fonts -->
    <link href="{{ asset('css/font.css') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/reset.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/root.css') }}" rel="stylesheet">
    @phone
        <link href="{{ asset('css/main-phone.css') }}" rel="stylesheet">
    @endphone
    @yield('styles')
</head>
<body>

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
                        <a class="navbar-brand" href="{{ url( '/') }}">
                            <div class="brand-logo"></div>
                        </a>
                    </li>
                    <li>
                        <div id="menu-open" class="menu-icon pointer"></div>
                        <div id="menu-close" class="menu-close-icon pointer hide"></div>
                    </li>

                </ul>

                <div id="menu-main" class="hide">
                    @component('root.layouts.sidebar') @endcomponent
                </div>

            @else
                <!-- Else user agents... -->
                <nav class="navbar flex justify-content-between align-items-center">

                    <!-- Left Side Of Navbar -->
                    <a class="navbar-brand" href="{{ url( '/a-level') }}">
                        <div class="brand-logo"></div>
                    </a>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav flex justify-content-end">
                        <li class="nav-item">
                            <a
                                    href="{{ route('logout', 'a-level') }}"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            >
                                <div class="exit"></div>
                            </a>
                            <form id="logout-form" action="{{ route('logout', 'a-level') }}" method="POST" class="d-none">
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
        <script src="{{asset('js/modal.js')}}"></script>
        @phone <script src="{{asset('js/phone-main.js')}}"></script> @endphone
        @yield('scripts')
    </footer>
</body>
</html>

