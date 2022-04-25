@section('scripts')
    <script src="{{asset('js/service/page.js')}}"></script>
    <script src="{{asset('js/cookie.js')}}"></script>
    <script>
        let id = {{$service_id ?? 0}};
        let service_id = id;
        let master_id = null;
        let currentService = 0;
        let timetableDB = @json($timetables ?? []);
        let mode = '{{$mode}}'
    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/service/common.js')}}"></script>
    <script src="{{asset('js/timetable.js')}}"></script>
@endsection
@section('modal')
    @component('modal')

{{--        @slot('header')--}}
{{--            @if (isset($type_id))--}}
{{--                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'edit', 'id' => $type_id, 'load' => $load])}}"><div class="back-icon"></div></a>--}}
{{--            @else--}}
{{--                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}"><div class="back-icon"></div></a>--}}
{{--            @endif--}}
{{--        @endslot--}}

        @include('layouts.timetable')
@slot('header_buttons')
    @if($mode == 'edit')
        <button type="button" id="time-confirm" class="btn-primary">
            {{ __('Подтвердить') }}
        </button>
    @endif
@endslot

        @slot('buttons')
            @if($mode == 'edit')
{{--            <button type="button" id="time-confirm" class="btn-primary">--}}
{{--                {{ __('Подтвердить') }}--}}
{{--            </button>--}}
            @endif
            @if (isset($type_id))
                <a href="{{route('window.service', ['business' => $slug, 'modal' => $mode, 'id' => $service_id, 'load' => $load, 'no_cookie'=> 1])}}" id="refresh-modal"></a>
            @else
                <a href="{{route('window.service', ['business' => $slug, 'modal' => 'create', 'id' => $service_id, 'load' => $load, 'no_cookie'=> 1])}}" id="refresh-modal"></a>
            @endif

        @endslot

    @endcomponent
@endsection
