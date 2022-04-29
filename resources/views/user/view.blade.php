@section('scripts')
    <script src="{{asset('js/user/page.js')}}"></script>
    <script src="{{asset('js/cookie.js')}}"></script>
    <script>
        let id = {{$id}};
        let note = '{{auth()->user()->hasRole('admin') ? route('window.user', ['business' => $slug, 'sort' => $sort, 'modal' => 'note', 'load' => $load]) : ''}}';
        let countService = '{{$moreService}}';
        let editRoute = "{{route('window.user', ['business' => $slug, 'modal' => 'edit', 'id' => $id])}}";
        let services = @json($services || []);
        let addresses = @json($addresses || []);
       if(! Object.keys(getCookie('user') ).length) {
           setCookie('user', @json($user));
       }

        if(! getCookie('userData').length) {
            setCookie('userData', @json($userData));
        }
        if (!getCookie('timetables').length) {
            setCookie('timetables', @json($timetables));
        }

    </script>
    <script src="{{asset('js/requests.js')}}"></script>
@endsection

<div id="service-count" data-count="{{$moreService}}"></div>
@section('modal')
    @component('modal')
        <input id="url_slug" type="hidden" value="{{$slug}}" name="url_slug">
        <input type="hidden" id="token_id" name="_token" value="{{ csrf_token() }}">

        <div class="flex direction-column view-user">
            <span>{{__('Название / ФИО')}}</span>
            <div>{{$user->name}}</div>
            <span>{{__('Email')}}</span>
            <div>{{$user->email}}</div>
            <span>{{__('Должность')}}</span>
            @foreach($user->roles as $role)
                <div>{{$role->name}}</div>
            @endforeach

            <div class="line full-width"></div>

            @for($i = 0; $i < $moreService; $i++)
                    <span >{{__('Тип услуги')}}</span>
                    <div>{{$types[$i]->type}}</div>

                <span>{{__('Услуга')}}</span>
                 <div>{{$services[$i]->name}}</div>

                <span>{{__('Адрес')}}</span>
                <div>{{$addresses[$i]->address}}</div>
                    <span class="calendar">{{__('Расписание')}}</span>
                    <a id="calendar" class="background-none calendar-a"
                       href="{{route('window.user', [
                            'business' => $slug,
                            'id' => $user->id,
                            'currentService' => $i,
							 'moreService' => $moreService,
                            'modal' => 'timetable',
                            'mode' => 'view'
                    ])}}">
                        <div class="calendar-icon"></div>
                    </a>
                    <div class="filled-months">
                        @if(isset($usedMonths[$i]))
                            @foreach($usedMonths[$i] as $month)
                                <p>{{$month}}</p>
                            @endforeach
                        @endif
                    </div>
                <div class="line full-width"></div>
            @endfor
        </div>
{{--        <div class="line"></div>--}}
        @slot('buttons')
            @if ($user->hasRole('master'))
                <div class="flex direction-column view-user">
                <span>{{__('Кол-во выполненных услуг / работ')}}</span>
                <div>{{$user->completedRecords()}}</div>
                <span>{{__('Сумма прибыли')}}</span>
                <div>{{$user->profit()}}</div>
                </div>
            @endif
        @endslot
    @endcomponent
@endsection


<style>
    .view-user .calendar-icon {
        margin-left: 50px;
    }
</style>
