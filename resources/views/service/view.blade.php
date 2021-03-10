@section('modal')
    @component('modal')
        <div class="view">
            <p class="data"><b>{{$service_type->type}}</b></p>
            <div class="line"></div>
            @if($services)
                @foreach($services as $service)
                    <div class="grid">
                        <span class="label">{{__('Название')}}</span>
                        <span class="data"><b>{{$service->name}}</b></span>
                        <span class="label">{{__('Адрес(а)')}}</span>
                        <span class="data flex direction-column">
                            @foreach($service->addresses as $addr)
                                <b>{{$addr->address}}</b>
                            @endforeach
                        </span>
                        <span class="label">{{__('Длительность')}}</span>
                        <span class="data"><b>{{$service->interval->name}}</b></span>
                        <span class="label">{{__('Интервал')}}</span>
                        <span class="data"><b>{{$service->range}} {{__('мин.')}}</b></span>
                        <span class="label">{{__('Цена')}}</span>
                        <span class="data"><b>{{$service->price}} {{__('₴')}}</b></span>
                        <span class="label">{{__('Бонусы')}}</span>
                        <span class="data"><b>{{$service->bonus}}</b></span>
                        <span class="label">{{__('Оплата на месте')}}</span>
                        <span class="data"><b>{{$service->cash_pay ? __('Да') : __('Нет')}}</b></span>
                        <span class="label">{{__('Оплата онлайн')}}</span>
                        <span class="data"><b>{{$service->online_pay ? __('Да') : __('Нет')}}</b></span>
                        <span class="label">{{__('Оплата бонусами')}}</span>
                        <span class="data"><b>{{$service->bonus_pay ? __('Да') : __('Нет')}}</b></span>
                        <span class="label">{{__('Предоплата')}}</span>
                        <span class="data"><b>{{isset($service->prepayment) ? __('Да') : __('Нет')}}</b></span>
                        @if (isset($service->prepayment))
                            <span class="label">{{__('Номер карты')}}</span>
                            <span class="data"><b>{{$service->prepayment->card_number}}</b></span>
                            <span class="label">{{__('Сообщение')}}</span>
                            <span class="data"><b>{{$service->prepayment->message}}</b></span>
                        @endif
                        @if(isset($service->group))
                            <span class="label">{{__('Группа')}}</span>
                            <span class="data"><b>{{$service->group->quantity}} {{__('человек(а)')}}</b></span>
                            <span class="label">{{__('Сообщение')}}</span>
                            <span class="data"><b>{{$service->group->message}}</b></span>
                        @endif
                        <span class="label">{{__('Расписание')}}</span>
                        @if (isset($service->timetable))
                            <span class="data">
                                @foreach($service->timetable->getDays() as $day => $ru)
                                    @if (!is_null($service->timetable->$day))
                                        @php $val = json_decode($service->timetable->$day) @endphp
                                        <b>{{$ru.'. '.$val[0].' - '.$val[count($val)-1]}}</b><br>
                                    @endif
                                @endforeach
                            </span>
                        @else
                            <b>{{__('Специалиста')}}</b>
                        @endif
                    </div>
                    <div class="line"></div>
                @endforeach
            @else
                <span class="label margin">{{__('Услуги с выбранным типом не созданы.')}}</span>
            @endif
        </div>
        <a href="{{route('service', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
    @endcomponent
@endsection
