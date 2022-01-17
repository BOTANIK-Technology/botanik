@section('scripts')
    <script>
        let id = '{{$id}}';
    </script>
    <script src="{{asset('js/user/page.js')}}"></script>

    <script src="{{asset('js/cookie.min.js')}}"></script>
    <script>
        let checked = [];
        @if (isset($checked) )
            checked = @json($checked);
        @endif
            for (let item in checked) {
            setCookie(item, JSON.stringify(checked[item]));
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
            @if (isset($id))
                <a href="{{route('addService', ['business' => $slug, 'modal' => 'edit', 'id' => $id, 'sort' => $sort, 'moreService' => $moreService, 'load' => $load, 'savedata' => 1])}}"
                   id="refresh-modal"></a>
            @else
                <a href="{{route('addService', ['business' => $slug, 'modal' => 'create', 'id' => 'new', 'sort' => $sort, 'moreService' => $moreService, 'load' => $load, 'savedata' => 1])}}"
                   id="refresh-modal"></a>
            @endif
        @endslot

    @endcomponent
@endsection
