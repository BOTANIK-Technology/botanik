@section('modal')
    @component('modal')
        <div class="flex direction-column view-user">
            <span>{{__('Название / ФИО')}}</span>
            <div>{{$user->name}}</div>
            <span>{{__('Email')}}</span>
            <div>{{$user->email}}</div>
            <span>{{__('Должность')}}</span>
            @foreach($user->roles as $role)
                <div>{{$role->name}}</div>
            @endforeach
            <div class="line"></div>
            @if ($user->timetables && !$user->timetables->isEmpty())

                @foreach($user->timetables as $timetable)
                    @if($user->hasRole('master'))
                        <span>{{__('Услуга')}}</span>
                        <div>{{$timetable->service->name}}</div>
                    @endif
                    <span>{{__('Адрес')}}</span>
                    <div>{{$timetable->address->address}}</div>
                    <span>{{__('Расписание')}}</span>
                    <div>
                        @foreach($timetable->getDays() as $day => $ru)
                            @if (!empty($timetable->$day))
                                @php $val = json_decode($timetable->$day) @endphp
                                <b>{{$ru.'. '.$val[0].' - '.$val[count($val)-1]}}</b><br>
                            @endif
                        @endforeach
                    </div>
                    <div class="line"></div>
                @endforeach
            @endif

            @if ($user->hasRole('master'))
                <span>{{__('Кол-во выполненых услуг / работ')}}</span>
                <div>{{$user->completedRecords()}}</div>
                <span>{{__('Сумма прибыли')}}</span>
                <div>{{$user->profit()}}</div>
            @endif
        </div>

        <a href="{{route('user', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
    @endcomponent
@endsection
