<aside class="sidebar">
    <ul class="nav-menu">
        <li class="{{ Route::currentRouteNamed('root.business')? 'active': '' }}">
            <a href="{{ route('root.business') }}">Создать бизнес</a>
        </li>
        <li class="{{ Route::currentRouteNamed('root.management')? 'active': '' }}">
            <a href="{{ route('root.management') }}">Менеджмент</a>
        </li>
        <li class="{{ Route::currentRouteNamed('root.supports')? 'active': '' }}">
            <a href="{{ route('root.supports') }}">Поддержка</a>
        </li>
        <li class="{{ Route::currentRouteNamed('root.analytic')? 'active': '' }}">
            <a href="{{ route('root.analytic') }}">Аналитика</a>
        </li>
    </ul>
    @if (isset($footer))
        @include('layouts.footer')
    @endif
</aside>