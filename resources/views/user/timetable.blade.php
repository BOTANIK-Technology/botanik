@section('scripts')
    <script src="{{asset('js/user/page.js')}}"></script>
    <script src="{{asset('js/cookie.js')}}"></script>
    <script>
        let id = {{$id ?? 0}};
        let currentService = {{$currentService}};
        let service_id = 0;
        let master_id = id;
        let timetableDB = @json($timetables ?? []);
        let mode = '{{$mode}}'
    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/timetable.js')}}"></script>

@endsection

@section('modal')
    @component('modal')

        {{--        @slot('header')--}}
        {{--            <a href="{{route('addService', ['business' => $slug, 'modal' => isset($id) ? 'edit' : 'create', 'id' => $id ?? 'new', 'sort' => $sort, 'moreService' => $moreService, 'load' => $load])}}"><div class="back-icon"></div></a>--}}
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
{{--            <button type="button" id="time-confirm" data-id="{{$currentService}}" class="btn-primary">--}}
{{--                {{ __('Подтвердить') }}--}}
{{--            </button>--}}
            @endif
            @if ($id)
                <a href="{{route('addService', ['business' => $slug, 'modal' => $mode, 'id' => $id, 'moreService' => ($mode == 'edit' ? $moreService : null)])}}"
                   id="refresh-modal"></a>
            @else
                <a href="{{route('addService', ['business' => $slug, 'modal' => 'create', 'id' => 0,'moreService' => $moreService])}}"
                   id="refresh-modal"></a>
            @endif
        @endslot

    @endcomponent
@endsection
