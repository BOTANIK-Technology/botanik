@section('scripts')
    <script>
        @if($view_service)
            let ids = ['{{$view_service->id}}'];
        @else
            let ids = false;
        @endif
    </script>
    <script src="{{asset('js/service/page.js')}}"></script>
    <script src="{{asset('js/cookie.min.js')}}"></script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/service/edit.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        @slot('class')
            modal-edit
        @endslot

        @if ($addresses)
            <div id="more-addresses" class="hide">
                <select id="address">
                    <option value="none">{{__('Выберете адрес')}}</option>
                    @foreach($addresses as $addr)
                        <option value="{{$addr->id}}">{{$addr->address}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        @if ($view_service)
            <div class="grid add-service">

                <label class="row-1 col-1 align-self-start" for="service-name-{{$view_service->id}}">{{__('Название *')}}</label>
                <label class="row-2 col-1 align-self-center" for="service-type-{{$view_service->id}}">{{__('Тип *')}}</label>
                <label class="row-3 col-1 align-self-center" for="address-{{$view_service->id}}">{{__('Адрес *')}}</label>
                <label class="row-4 col-1 align-self-center" for="interval-1-{{$view_service->id}}">{{__('Длительность *')}}</label>
                <label class="row-5 col-1 align-self-start" for="range-{{$view_service->id}}">{{__('Интервал *')}}</label>
                <label class="row-6 col-1 align-self-start" for="price-{{$view_service->id}}">{{__('Стоимость *')}}</label>
                <label class="row-7 col-1 align-self-start" for="bonus-{{$view_service->id}}">{{__('Бонусы')}}</label>
                <label class="row-8 col-1 align-self-center" for="calendar-{{$view_service->id}}">{{__('Расписание')}}</label>
                <label class="row-9 col-1 align-self-center" for="group-1-{{$view_service->id}}">{{__('Групповая услуга *')}}</label>
                <label class="row-10 col-1 align-self-center" for="prepay-1-{{$view_service->id}}">{{__('Оплата *')}}</label>

                <div class="row-1 col-2">
                    <input id="service-name-{{$view_service->id}}" class="inp" type="text" value="{{$view_service->name}}" placeholder="{{__('Введите название')}}">
                </div>

                <div class="row-2 col-2">
                    <select id="service-type-{{$view_service->id}}">
                        @foreach($types_select as $type)
                            <option value="{{$type->id}}" {{$view_service->typeServices->id == $type->id ? 'selected' : ''}}>{{$type->type}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row-3 col-2 align-self-start">
                    <div id="addresses-{{$view_service->id}}" class="addresses">
                        @foreach($view_service->addresses as $address)
                            <select id="address-{{$view_service->id}}" class="address" name="addresses-{{$view_service->id}}[]">
                                @foreach($addresses as $add)
                                    <option value="{{$add->id}}" @if ($address->id == $add->id) selected @endif>{{$add->address}}</option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>
                    <div id="more-address-{{$view_service->id}}" class="row-2 col-3 flex more-address">
                        <button id="include-address-{{$view_service->id}}" data-id="{{$view_service->id}}" class="include-address color"><div class="add-b-icon"></div>&nbsp;&nbsp;{{__('Добавить адрес к услуге')}}</button>
                    </div>
                </div>

                <div class="checkboxes grid intervals row-4 col-2">
                    @foreach($intervals as $interval)
                        <input id="interval-{{$interval->id}}-{{$view_service->id}}" type="radio" name="interval-{{$view_service->id}}" value="{{$interval->id}}" {{$view_service->interval_id == $interval->id ? 'checked' : ''}}>
                        <label for="interval-{{$interval->id}}-{{$view_service->id}}" class="user-select-none">{{$interval->name}}</label>
                    @endforeach
                </div>

                <div class="row-5 col-2">
                    <input id="range-{{$view_service->id}}" class="inp" type="text" value="{{$view_service->range}}" placeholder="{{__('Введите интервал посещений в минутах')}}">
                </div>

                <div class="row-6 col-2">
                    <input id="price-{{$view_service->id}}" class="inp" type="text" value="{{$view_service->price}}" placeholder="{{__('Введите сумму')}}">
                </div>

                <div class="row-7 col-2">
                    <input id="bonus-{{$view_service->id}}" class="inp" type="text" value="{{$view_service->bonus}}" placeholder="{{__('Введите количество')}}">
                </div>

                <div class="row-8 col-2 align-self-center">
                    <button
                            id="calendar-{{$view_service->id}}"
                            class="background-none calendar-a"
                            data-href="{{route('window.service', ['business' => $slug, 'load' => $load, 'type_id' => $view_service_type->id, 'service_id' => $view_service->id, 'modal' => 'timetable'])}}"
                    >
                        <div class="calendar-icon"></div>
                    </button>
                </div>

                <div class="row-9 col-2 grid intervals">
                    <input id="group-1-{{$view_service->id}}" type="radio" name="group-{{$view_service->id}}" value="1" {{isset($view_service->group) ? 'checked' : ''}}>
                    <label for="group-1-{{$view_service->id}}" class="user-select-none">{{__('Да')}}</label>
                    <input id="group-0-{{$view_service->id}}" type="radio" name="group-{{$view_service->id}}" value="0" {{!isset($view_service->group) ? 'checked' : ''}}>
                    <label for="group-0-{{$view_service->id}}" class="user-select-none">{{__('Нет')}}</label>
                </div>

                <div class="row-10 col-2 grid intervals">
                    <input id="prepay-{{$view_service->id}}" type="checkbox" name="pre-{{$view_service->id}}" value="1" {{isset($view_service->prepayment) ? 'checked' : ''}}>
                    <label for="prepay-{{$view_service->id}}" class="user-select-none">{{__('Предоплата')}}</label>
                    <input id="cashpay-{{$view_service->id}}" type="checkbox" name="cash-{{$view_service->id}}" value="1" {{$view_service->cash_pay ? 'checked' : ''}}>
                    <label for="cashpay-{{$view_service->id}}" class="user-select-none">{{__('На месте')}}</label>
                    <input id="onlinepay-{{$view_service->id}}" type="checkbox" name="online-{{$view_service->id}}" value="1" {{$view_service->online_pay ? 'checked' : ''}}>
                    <label for="onlinepay-{{$view_service->id}}" class="user-select-none">{{__('Онлайн')}}</label>
                    <input id="bonuspay-{{$view_service->id}}" type="checkbox" name="bonuspay-{{$view_service->id}}" value="1" {{$view_service->bonus_pay ? 'checked' : ''}}>
                    <label for="bonuspay-{{$view_service->id}}" class="user-select-none">{{__('Бонусами')}}</label>
                </div>

                <div class="row-1 col-3 align-self-start text-align-center">
                    <button type="button" id="edit-{{$view_service->id}}" data-service="{{$view_service->id}}" class="btn-primary" name="save-service">
                        {{ __('Сохранить') }}
                    </button>
                </div>

                <div class="row-2 col-3 align-self-start text-align-center">
                    <button type="button" id="delete-{{$view_service->id}}" data-service="{{$view_service->id}}" class="btn-primary" name="delete-service">
                        {{ __('Удалить') }}
                    </button>
                </div>

            </div>

            <div id="group-service-{{$view_service->id}}" class="grid group add-service hide">
                <label class="row-1 col-1 align-self-center" for="quantity-{{$view_service->id}}">{{__('Количество участников *')}}</label>
                <label class="row-2 col-1 align-self-center" for="message-{{$view_service->id}}">{{__('Сообщение')}}</label>

                <div class="row-1 col-2">
                    <input id="quantity-{{$view_service->id}}" class="inp align-self-center" type="text" value="{{$view_service->group->quantity ?? ''}}" placeholder="{{__('Введите количество')}}">
                </div>
                <div class="row-2 col-2 align-self-center">
                    <textarea id="message-{{$view_service->id}}" class="inp" type="text" placeholder="{{__('Введите сообщение')}}">{{$view_service->group->message ?? ''}}</textarea>
                </div>
            </div>

            <div id="prepay-service-{{$view_service->id}}" class="grid group add-service hide">
                <label class="row-1 col-1 align-self-center" for="card-{{$view_service->id}}">{{__('Номер банковской карты *')}}</label>
                <label class="row-2 col-1 align-self-center" for="prepay-message-{{$view_service->id}}">{{__('Сообщение *')}}</label>

                <div class="row-1 col-2">
                    <input id="card-{{$view_service->id}}" class="inp align-self-center" type="text" value="{{$view_service->prepayment->card_number ?? ''}}" placeholder="{{__('Введите номер карты')}}">
                </div>
                <div class="row-2 col-2 align-self-center">
                    <textarea id="prepay-message-{{$view_service->id}}" class="inp" type="text" placeholder="{{__('Введите сообщение')}}">{{$view_service->prepayment->message ?? ''}}</textarea>
                </div>
            </div>

            <div class="line"></div>

        @else
            {{__('Услуги с выбранным типом не созданы.')}}
        @endif
        <a href="{{route('service', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>

    @endcomponent
@endsection
