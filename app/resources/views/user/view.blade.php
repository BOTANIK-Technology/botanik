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

                @foreach($user->slots as $slot)
                    @if($user->hasRole('master'))
                        <span>{{__('Услуга')}}</span>
                        <div>{{$slot->service->name}}</div>
                    @endif
                    <span>{{__('Адрес')}}</span>
                    <div>{{$slot->address->address}}</div>
                    <span>{{__('Расписание')}}</span>
                    <div>

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
