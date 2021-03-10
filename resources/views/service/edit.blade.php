@section('scripts')
    <script>
        @if($services)
            let ids = [
                @foreach($services as $service)
                    '{{$service->id}}',
                @endforeach
            ];
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

        @if ($services)
            @foreach($services as $service)
                <div class="grid add-service">

                    <label class="row-1 col-1 align-self-start" for="service-name-{{$service->id}}">{{__('Название *')}}</label>
                    <label class="row-2 col-1 align-self-center" for="service-type-{{$service->id}}">{{__('Тип *')}}</label>
                    <label class="row-3 col-1 align-self-center" for="address-{{$service->id}}">{{__('Адрес *')}}</label>
                    <label class="row-4 col-1 align-self-center" for="interval-1-{{$service->id}}">{{__('Длительность *')}}</label>
                    <label class="row-5 col-1 align-self-start" for="range-{{$service->id}}">{{__('Интервал *')}}</label>
                    <label class="row-6 col-1 align-self-start" for="price-{{$service->id}}">{{__('Стоимость *')}}</label>
                    <label class="row-7 col-1 align-self-start" for="bonus-{{$service->id}}">{{__('Бонусы')}}</label>
                    <label class="row-8 col-1 align-self-center" for="calendar-{{$service->id}}">{{__('Расписание')}}</label>
                    <label class="row-9 col-1 align-self-center" for="group-1-{{$service->id}}">{{__('Групповая услуга *')}}</label>
                    <label class="row-10 col-1 align-self-center" for="prepay-1-{{$service->id}}">{{__('Оплата *')}}</label>

                    <div class="row-1 col-2">
                        <input id="service-name-{{$service->id}}" class="inp" type="text" value="{{$service->name}}" placeholder="{{__('Введите название')}}">
                    </div>

                    <div class="row-2 col-2">
                        <select id="service-type-{{$service->id}}">
                            @foreach($types_select as $type)
                                <option value="{{$type->id}}" {{$service->typeServices->id == $type->id ? 'selected' : ''}}>{{$type->type}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row-3 col-2 align-self-start">
                        <div id="addresses-{{$service->id}}" class="addresses">
                            @foreach($service->addresses as $address)
                                <select id="address-{{$service->id}}" class="address" name="addresses-{{$service->id}}[]">
                                    @foreach($addresses as $add)
                                        <option value="{{$add->id}}" @if ($address->id == $add->id) selected @endif>{{$add->address}}</option>
                                    @endforeach
                                </select>
                            @endforeach
                        </div>
                        <div id="more-address-{{$service->id}}" class="row-2 col-3 flex more-address">
                            <div class="add-b-icon"></div>
                            <button id="include-address-{{$service->id}}" data-id="{{$service->id}}" class="include-address color">{{__('Добавить адрес к услуге')}}</button>
                        </div>
                    </div>

                    <div class="checkboxes grid intervals row-4 col-2">
                        @foreach($intervals as $interval)
                            <input id="interval-{{$interval->id}}-{{$service->id}}" type="radio" name="interval-{{$service->id}}" value="{{$interval->id}}" {{$service->interval_id == $interval->id ? 'checked' : ''}}>
                            <label for="interval-{{$interval->id}}-{{$service->id}}" class="user-select-none">{{$interval->name}}</label>
                        @endforeach
                    </div>

                    <div class="row-5 col-2">
                        <input id="range-{{$service->id}}" class="inp" type="text" value="{{$service->range}}" placeholder="{{__('Введите интервал посещений в минутах')}}">
                    </div>

                    <div class="row-6 col-2">
                        <input id="price-{{$service->id}}" class="inp" type="text" value="{{$service->price}}" placeholder="{{__('Введите сумму')}}">
                    </div>

                    <div class="row-7 col-2">
                        <input id="bonus-{{$service->id}}" class="inp" type="text" value="{{$service->bonus}}" placeholder="{{__('Введите количество')}}">
                    </div>

                    <div class="row-8 col-2 align-self-center">
                        <button
                                id="calendar-{{$service->id}}"
                                class="background-none calendar-a"
                                data-href="{{route('window.service', ['business' => $slug, 'load' => $load, 'type_id' => $service_type->id, 'service_id' => $service->id, 'modal' => 'timetable'])}}"
                        >
                            <div class="calendar-icon"></div>
                        </button>
                    </div>

                    <div class="row-9 col-2 grid intervals">
                        <input id="group-1-{{$service->id}}" type="radio" name="group-{{$service->id}}" value="1" {{!isset($service->group) ? 'checked' : ''}}>
                        <label for="group-1-{{$service->id}}" class="user-select-none">{{__('Да')}}</label>
                        <input id="group-0-{{$service->id}}" type="radio" name="group-{{$service->id}}" value="0" {{ isset($service->group) ? 'checked' : ''}}>
                        <label for="group-0-{{$service->id}}" class="user-select-none">{{__('Нет')}}</label>
                    </div>

                    <div class="row-10 col-2 grid intervals">
                        <input id="prepay-{{$service->id}}" type="checkbox" name="pre-{{$service->id}}" value="1" {{isset($service->prepayment) ? 'checked' : ''}}>
                        <label for="prepay-{{$service->id}}" class="user-select-none">{{__('Предоплата')}}</label>
                        <input id="cashpay-{{$service->id}}" type="checkbox" name="cash-{{$service->id}}" value="1" {{$service->cash_pay ? 'checked' : ''}}>
                        <label for="cashpay-{{$service->id}}" class="user-select-none">{{__('На месте')}}</label>
                        <input id="onlinepay-{{$service->id}}" type="checkbox" name="online-{{$service->id}}" value="1" {{$service->online_pay ? 'checked' : ''}}>
                        <label for="onlinepay-{{$service->id}}" class="user-select-none">{{__('Онлайн')}}</label>
                        <input id="bonuspay-{{$service->id}}" type="checkbox" name="bonuspay-{{$service->id}}" value="1" {{$service->bonus_pay ? 'checked' : ''}}>
                        <label for="bonuspay-{{$service->id}}" class="user-select-none">{{__('Бонусами')}}</label>
                    </div>

                    <div class="row-1 col-3 align-self-start text-align-center">
                        <button type="button" id="edit-{{$service->id}}" data-service="{{$service->id}}" class="btn-primary" name="save-service">
                            {{ __('Сохранить') }}
                        </button>
                    </div>

                    <div class="row-2 col-3 align-self-start text-align-center">
                        <button type="button" id="delete-{{$service->id}}" data-service="{{$service->id}}" class="btn-primary" name="delete-service">
                            {{ __('Удалить') }}
                        </button>
                    </div>

                </div>

                <div id="group-service-{{$service->id}}" class="grid group add-service hide">
                    <label class="row-1 col-1 align-self-center" for="quantity-{{$service->id}}">{{__('Количество участников *')}}</label>
                    <label class="row-2 col-1 align-self-center" for="message-{{$service->id}}">{{__('Сообщение')}}</label>

                    <div class="row-1 col-2">
                        <input id="quantity-{{$service->id}}" class="inp align-self-center" type="text" value="{{$service->group->quantity ?? ''}}" placeholder="{{__('Введите количество')}}">
                    </div>
                    <div class="row-2 col-2 align-self-center">
                        <textarea id="message-{{$service->id}}" class="inp" type="text" placeholder="{{__('Введите сообщение')}}">{{$service->group->message ?? ''}}</textarea>
                    </div>
                </div>

                <div id="prepay-service-{{$service->id}}" class="grid group add-service hide">
                    <label class="row-1 col-1 align-self-center" for="card-{{$service->id}}">{{__('Номер банковской карты *')}}</label>
                    <label class="row-2 col-1 align-self-center" for="prepay-message-{{$service->id}}">{{__('Сообщение *')}}</label>

                    <div class="row-1 col-2">
                        <input id="card-{{$service->id}}" class="inp align-self-center" type="text" value="{{$service->prepayment->card_number ?? ''}}" placeholder="{{__('Введите номер карты')}}">
                    </div>
                    <div class="row-2 col-2 align-self-center">
                        <textarea id="prepay-message-{{$service->id}}" class="inp" type="text" placeholder="{{__('Введите сообщение')}}">{{$service->prepayment->message ?? ''}}</textarea>
                    </div>
                </div>

                <div class="line"></div>
            @endforeach
        @else
            {{__('Услуги с выбранным типом не созданы.')}}
        @endif
        <a href="{{route('service', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>

    @endcomponent
@endsection
