@section('scripts')
    <script>
        let id = {{$id}};
        let currentService = {{$currentService}}
    </script>
    <script src="{{asset('js/user/page.js')}}"></script>

    <script src="{{asset('js/cookie.js')}}"></script>
    <script>
        let timetableDB;
        if(id) {
            timetableDB = @json($timetables);
        }
        else {
            timetableDB = @json([]);
        }

    </script>
    <script src="{{asset('js/timetable.js')}}"></script>
    <script src="{{asset('js/user/timetable.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        {{--        @slot('header')--}}
        {{--            <a href="{{route('addService', ['business' => $slug, 'modal' => isset($id) ? 'edit' : 'create', 'id' => $id ?? 'new', 'sort' => $sort, 'moreService' => $moreService, 'load' => $load])}}"><div class="back-icon"></div></a>--}}
        {{--        @endslot--}}

        @include('layouts.timetable')

        @slot('buttons')
            <button type="button" id="time-confirm" data-id="{{$currentService}}" class="btn-primary">
                {{ __('Подтвердить') }}
            </button>
            @if ($id)
                <a href="{{route('addService', ['business' => $slug, 'modal' => 'edit', 'id' => $id, 'moreService' => $moreService])}}"
                   id="refresh-modal"></a>
            @else
                <a href="{{route('addService', ['business' => $slug, 'modal' => 'create', 'id' => 0,'moreService' => $moreService])}}"
                   id="refresh-modal"></a>
            @endif
        @endslot

    @endcomponent
@endsection
