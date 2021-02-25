<aside class="sidebar">
    <ul class="nav-menu">
        <li class="{{ Route::currentRouteNamed('home') ? 'active': '' }}">
            <a href="{{ route('home', $slug) }}">{{__('Главная')}}</a>
        </li>
        @role('owner', 'admin')
            <li class="{{ Route::currentRouteNamed('service') ? 'active': '' }}">
                <a href="{{ route('service', $slug) }}">{{__('Услуги')}}</a>
            </li>
            <li class="{{ Route::currentRouteNamed('user') ? 'active': '' }}">
                <a href="{{ route('user', $slug) }}">{{__('Специалисты')}}</a>
            </li>
            @if ($catalog)
                <li class="{{ Route::currentRouteNamed('catalog') ? 'active': '' }}">
                    <a href="{{ route('catalog', $slug) }}">{{__('Каталог')}}</a>
                </li>
            @endif
            <li class="{{ Route::currentRouteNamed('info') ? 'active': '' }}">
                <a href="{{ route('info', $slug) }}">{{__('Информация')}}</a>
            </li>
            @if ($package == 'pro')
                <li class="{{ Route::currentRouteNamed('mail') ? 'active': '' }}">
                    <a href="{{ route('mail', $slug) }}">{{__('Рассылка')}}</a>
                </li>
                <li class="{{ Route::currentRouteNamed('share') ? 'active': '' }}">
                    <a href="{{ route('share', $slug) }}">{{__('Акции')}}</a>
                </li>
            @endif
            <li class="{{ Route::currentRouteNamed('client') ? 'active': '' }}">
                <a href="{{ route('client', $slug) }}">{{__('Клиенты')}}</a>
            </li>
            <li class="{{ Route::currentRouteNamed('schedule') ? 'active': '' }}">
                <a href="{{ route('schedule', $slug) }}">{{__('Расписание')}}</a>
            </li>
            @if ($package == 'pro' || $package == 'base')
                <li class="{{ Route::currentRouteNamed('feedback') || Route::currentRouteNamed('review') ? 'active': '' }}">
                    <a href="{{ route('review', $slug) }}">{{__('Отзывы')}}</a>
                </li>
            @endif
            <li class="{{ Route::currentRouteNamed('support') ? 'active': '' }}">
                <a href="{{ route('support', $slug) }}">{{__('Поддержка')}}</a>
            </li>
            @if ($package == 'pro' || $package == 'base')
                <li class="{{ Route::currentRouteNamed('report') ? 'active': '' }}">
                    <a href="{{ route('report', $slug) }}">{{__('Отчёты')}}</a>
                </li>
            @endif
        @endrole
        @role('master')
            <li class="{{ Route::currentRouteNamed('schedule') ? 'active': '' }}">
                <a href="{{ route('schedule', $slug) }}">{{__('Расписание')}}</a>
            </li>
            <li class="{{ Route::currentRouteNamed('report') ? 'active': '' }}">
                <a href="{{ route('report', $slug) }}">{{__('Отчёты')}}</a>
            </li>
        @endrole
    </ul>
    @if (isset($footer))
        @include('layouts.footer')
    @endif
</aside>