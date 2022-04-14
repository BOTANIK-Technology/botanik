<div class="page-content @phone mobile-page-content @endphone">
    @desktop
        @component('root.layouts.sidebar')
            @slot('footer')
                true
            @endslot
        @endcomponent
    @enddesktop
    <article id="main-content" class="main-content">
        {{$header ?? ''}}
        {{ $slot }}
    </article>
    @phone
        @include('layouts.footer', ['class' => 'grid mobile-footer'])
    @endphone
</div>