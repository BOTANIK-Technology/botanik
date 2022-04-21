@section('scripts')
    <script src="{{asset('js/service/page.js')}}"></script>
    <script src="{{asset('js/cookie.js')}}"></script>
    <script>
        let currentYear = '{{$current_year}}';
        let currentMonth = '{{$current_month}}';
        let id = {{$service_id}};
    </script>

    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/service/common.js')}}"></script>
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
        <input type="hidden" value="{{$slug}}" id="edit_type_slug">
        @if ($view_service)
            <div class="grid add-service">

                <label class="row-1 col-1 align-self-start" for="service-type">{{__('Тип *')}}</label>
                <label class="row-2 col-1 align-self-start" for="service-name">{{__('Услуга *')}}</label>
                <label class="row-3 col-1 align-self-start" for="address">{{__('Адрес *')}}</label>
                <label class="row-4 col-1 align-self-start" for="interval-hours-0">{{__('Длительность')}}
                    <br>{{__('(часы) *')}}</label>
                <label class="row-5 col-1 align-self-start" for="interval-minutes-0">{{__('Длительность')}}
                    <br>{{__('(минуты) *')}}</label>
                <label class="row-6 col-1 align-self-start" for="interval-hours-0">{{__('Интервал')}}
                    <br>{{__('(часы) *')}}</label>
                <label class="row-7 col-1 align-self-start" for="interval-minutes-0">{{__('Интервал')}}
                    <br>{{__('(минуты) *')}}</label>
                {{--            <label class="row-8 col-1 align-self-start" for="range">{{__('Интервал *')}}</label>--}}
                <label class="row-8 col-1 align-self-start" for="price">{{__('Стоимость *')}}</label>
                <label class="row-9 col-1 align-self-start" for="bonus">{{__('Бонусы')}}</label>
                <label class="row-10 col-1 align-self-start" for="calendar">{{__('Расписание')}}</label>
                <label class="row-11 col-1 align-self-center" for="group-1">{{__('Групповая услуга *')}}</label>
                <label class="row-12 col-1 align-self-start" for="prepay-1">{{__('Оплата *')}}</label>

                <div class="row-1 col-2">
                    <select id="service-type">
                        @foreach($types_select as $type)
                            <option
                                value="{{$type->id}}" {{$view_service->typeServices->id == $type->id ? 'selected' : ''}}>{{$type->type}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="row-2 col-2">
                    <input id="service-name" class="inp" type="text"
                           value="{{$view_service->name}}" placeholder="{{__('Введите название')}}">
                </div>


                <div class="row-3 col-2 align-self-start">
                    <div id="addresses" class="addresses">
                        @foreach($view_service->addresses as $address)
                            <select id="address" class="address"
                                    name="addresses[]">
                                @foreach($addresses as $add)
                                    <option value="{{$add->id}}"
                                            @if ($address->id == $add->id) selected @endif>{{$add->address}}</option>
                                @endforeach
                            </select>
                        @endforeach
                    </div>
                    <div id="more-address" class="row-2 col-3 flex more-address">
                        <button id="include-address" data-id="{{$view_service->id}}"
                                class="include-address color">
                            <div class="add-b-icon"></div>&nbsp;&nbsp;{{__('Добавить адрес к услуге')}}</button>
                    </div>
                </div>

                {{-- Длительность --}}
                <div class="checkboxes grid hours row-4 col-2">
                    @php($hours = 0)
                    @while($hours <= 24)
                        <input id="duration-hours-{{$hours}}" type="radio" name="durationHours"
                               value="{{$hours}}" {{$view_service->intervalFields['hours'] == $hours ? 'checked' : ''}}>
                        <label for="duration-hours-{{$hours}}" class="user-select-none">{{$hours}}</label>
                        @php($hours ++)
                    @endwhile
                </div>
                <div class="checkboxes grid minutes row-5 col-2">
                    @php($minutes = 0)
                    @while($minutes <= 55)
                        <input id="duration-minutes-{{$minutes}}" type="radio" name="durationMinutes"
                               value="{{$minutes}}" {{$view_service->intervalFields['minutes'] == $minutes ? 'checked' : ''}}>
                        <label for="duration-minutes-{{$minutes}}" class="user-select-none">{{$minutes}}</label>
                        @php($minutes += 5)
                    @endwhile
                </div>
                {{-- /Длительность --}}

                {{-- Интервал --}}
                <div class="checkboxes grid hours row-6 col-2">
                    @php($hours = 0)
                    @while($hours <= 24)
                        <input id="interval-hours-{{$hours}}" type="radio" name="intervalHours"
                               value="{{$hours}}" {{$view_service->rangeFields['hours'] == $hours ? 'checked' : ''}}>
                        <label for="interval-hours-{{$hours}}" class="user-select-none">{{$hours}}</label>
                        @php($hours ++)
                    @endwhile
                </div>
                <div class="checkboxes grid minutes row-7 col-2">
                    @php($minutes = 0)
                    @while($minutes <= 55)
                        <input id="interval-minutes-{{$minutes}}" type="radio" name="intervalMinutes"
                               value="{{$minutes}}" {{$view_service->rangeFields['minutes'] == $minutes ? 'checked' : ''}}>
                        <label for="interval-minutes-{{$minutes}}" class="user-select-none">{{$minutes}}</label>
                        @php($minutes += 5)
                    @endwhile
                </div>
                {{-- /Интервал --}}

                <div class="row-8 col-2">
                    <input id="price" class="inp" type="text" value="{{$view_service->price}}"
                           placeholder="{{__('Введите сумму')}}">
                </div>

                <div class="row-9 col-2">
                    <input id="bonus" class="inp" type="text" value="{{$view_service->bonus}}"
                           placeholder="{{__('Введите количество')}}">
                </div>

                <div class="row-10 col-2 align-self-center calendar-block">
                    <a id="calendar" class="background-none calendar-a"
                       href="{{route('window.service', [
                                'business' => $slug,
                                  'service_id' => $view_service->id,
                                   'modal' => 'timetable',
                                   'mode' => 'edit'
                    ])}}">
                        <div class="calendar-icon"></div>
                    </a>
                    <div class="filled-months">
                        @foreach($usedMonths as $month)
                            <p>{{$month}}</p>
                        @endforeach
                    </div>
                </div>


                <div class="row-11 col-2 grid intervals">
                    <input id="group-1" type="radio" name="group"
                           value="1" {{isset($view_service->group) ? 'checked' : ''}}>
                    <label for="group-1" class="user-select-none">{{__('Да')}}</label>
                    <input id="group-0" type="radio" name="group"
                           value="0" {{!isset($view_service->group) ? 'checked' : ''}}>
                    <label for="group-0" class="user-select-none">{{__('Нет')}}</label>
                </div>

                <div class="row-12 col-2 grid intervals">
                    <input id="prepay" type="checkbox" name="pre"
                           value="1" {{isset($view_service->prepayment) ? 'checked' : ''}}>
                    <label for="prepay" class="user-select-none">{{__('Предоплата')}}</label>
                    <input id="cashpay" type="checkbox" name="cash"
                           value="1" {{$view_service->cash_pay ? 'checked' : ''}}>
                    <label for="cashpay" class="user-select-none">{{__('На месте')}}</label>
                    <input id="onlinepay" type="checkbox" name="online"
                           value="1" {{$view_service->online_pay ? 'checked' : ''}}>
                    <label for="onlinepay" class="user-select-none">{{__('Онлайн')}}</label>
                    <input id="bonuspay" type="checkbox" name="bonuspay"
                           value="1" {{$view_service->bonus_pay ? 'checked' : ''}}>
                    <label for="bonuspay" class="user-select-none">{{__('Бонусами')}}</label>
                </div>

                <div class="row-1 col-3 align-self-start text-align-center">
                    <button type="button" id="save-service" data-service="{{$view_service->id}}"
                            class="btn-primary">
                        {{ __('Сохранить') }}
                    </button>
                </div>

                <div class="row-2 col-3 align-self-start text-align-center">
                    <button type="button" id="delete" data-service="{{$view_service->id}}"
                            class="btn-primary" name="delete-service">
                        {{ __('Удалить') }}
                    </button>
                </div>

            </div>

            <div id="group-service" class="grid group add-service hide">
                <label class="row-1 col-1 align-self-center"
                       for="quantity">{{__('Количество участников *')}}</label>
                <label class="row-2 col-1 align-self-center"
                       for="message">{{__('Сообщение')}}</label>

                <div class="row-1 col-2">
                    <input id="quantity" class="inp align-self-center" type="text"
                           value="{{$view_service->group->quantity ?? ''}}" placeholder="{{__('Введите количество')}}">
                </div>
                <div class="row-2 col-2 align-self-center">
                    <textarea id="message" class="inp" type="text"
                              placeholder="{{__('Введите сообщение')}}">{{$view_service->group->message ?? ''}}</textarea>
                </div>
            </div>

            <div id="prepay-service" class="grid group add-service hide">
                <label class="row-1 col-1 align-self-center"
                       for="card">{{__('Номер банковской карты *')}}</label>
                <label class="row-2 col-1 align-self-center"
                       for="prepay-message">{{__('Сообщение *')}}</label>

                <div class="row-1 col-2">
                    <input id="card" class="inp align-self-center" type="text"
                           value="{{$view_service->prepayment->card_number ?? ''}}"
                           placeholder="{{__('Введите номер карты')}}">
                </div>
                <div class="row-2 col-2 align-self-center">
                    <textarea id="prepay-message" class="inp" type="text"
                              placeholder="{{__('Введите сообщение')}}">{{$view_service->prepayment->message ?? ''}}</textarea>
                </div>
            </div>

            <div class="line"></div>

        @else
            {{__('Услуги с выбранным типом не созданы.')}}
        @endif
        <a href="{{route('service', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>

    @endcomponent
    <style>
.calendar-block {
    display: flex;
}
.filled-months {
    max-width: 210px;
    margin-left: 30px;
}
.filled-months p {
    display: inline-block;
    margin-right: 15px;
}

    </style>
@endsection
