@section('scripts')
    <script>
        let id = '{{$service_id ?? ''}}';
    </script>
    <script src="{{asset('js/service/page.js')}}"></script>
    <script src="{{asset('js/cookie.js')}}"></script>
    <script src="{{asset('js/timetable.js')}}"></script>
    <script src="{{asset('js/service/timetable.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        @slot('header')
{{--            @if (isset($type_id))--}}
{{--                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'edit', 'id' => $type_id, 'load' => $load])}}"><div class="back-icon"></div></a>--}}
{{--            @else--}}
{{--                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}"><div class="back-icon"></div></a>--}}
{{--            @endif--}}
{{--        @endslot--}}

        @include('layouts.timetable')

        @slot('buttons')
            <button type="button" id="time-confirm" class="btn-primary">
                {{ __('Подтвердить') }}
            </button>
            @if (isset($type_id))
                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'edit', 'id' => $service_id, 'load' => $load, 'no_cookie'=> 1])}}" id="refresh-modal"></a>
            @else
                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'create', 'id' => $service_id, 'load' => $load, 'no_cookie'=> 1])}}" id="refresh-modal"></a>
            @endif

        @endslot

    @endcomponent
@endsection
