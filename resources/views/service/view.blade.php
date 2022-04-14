@section('modal')
    @component('modal')
        <div class="view">
            <p class="data"><b>{{$view_service_type->type}}</b></p>
            <div class="line"></div>
            @if($view_service)
                <div class="grid">
                    <span class="label">{{__('Название')}}</span>
                    <span class="data"><b>{{$view_service->name}}</b></span>
                    <span class="label">{{__('Адрес(а)')}}</span>
                    <span class="data flex direction-column">
                        @foreach($view_service->addresses as $addr)
                            <b>{{$addr->address}}</b>
                        @endforeach
                    </span>
                    <span class="label">{{__('Длительность')}}</span>
                    @if(isset($view_service->interval))
                        <span class="data"><b>{{$view_service->interval->name}}</b></span>
                    @else
                        <span class="data"><b>{{__("Не выбрано")}}</b></span>
                    @endif
                    <span class="label">{{__('Интервал')}}</span>
                    <span class="data"><b>{{$view_service->range}} {{__('мин.')}}</b></span>
                    <span class="label">{{__('Цена')}}</span>
                    <span class="data"><b>{{$view_service->price}} {{__('₴')}}</b></span>
                    <span class="label">{{__('Бонусы')}}</span>
                    <span class="data"><b>{{$view_service->bonus}}</b></span>
                    <span class="label">{{__('Оплата на месте')}}</span>
                    <span class="data"><b>{{$view_service->cash_pay ? __('Да') : __('Нет')}}</b></span>
                    <span class="label">{{__('Оплата онлайн')}}</span>
                    <span class="data"><b>{{$view_service->online_pay ? __('Да') : __('Нет')}}</b></span>
                    <span class="label">{{__('Оплата бонусами')}}</span>
                    <span class="data"><b>{{$view_service->bonus_pay ? __('Да') : __('Нет')}}</b></span>
                    <span class="label">{{__('Предоплата')}}</span>
                    <span class="data"><b>{{isset($view_service->prepayment) ? __('Да') : __('Нет')}}</b></span>
                    @if (isset($view_service->prepayment))
                        <span class="label">{{__('Номер карты')}}</span>
                        <span class="data"><b>{{$view_service->prepayment->card_number}}</b></span>
                        <span class="label">{{__('Сообщение')}}</span>
                        <span class="data"><b>{{$view_service->prepayment->message}}</b></span>
                    @endif
                    @if(isset($view_service->group))
                        <span class="label">{{__('Группа')}}</span>
                        <span class="data"><b>{{$view_service->group->quantity}} {{__('человек(а)')}}</b></span>
                        <span class="label">{{__('Сообщение')}}</span>
                        <span class="data"><b>{{$view_service->group->message}}</b></span>
                    @endif
                    <span class="label">{{__('Расписание')}}</span>
                    @if (isset($view_service->timetable))
                        <div class="filled-months">
                            @foreach($usedMonths as $month)
                                <p>{{$month}}</p>
                            @endforeach
                        </div>
                    @else
                        <b>{{__('Специалиста')}}</b>
                    @endif
                </div>
                <div class="line"></div>
            @else
                <span class="label margin">{{__('Услуги с выбранным типом не созданы.')}}</span>
            @endif
        </div>
        <a href="{{route('service', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
    @endcomponent
@endsection
