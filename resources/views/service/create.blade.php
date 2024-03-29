@section('scripts')
    <script src="{{asset('js/service/page.js')}}"></script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/cookie.min.js')}}"></script>
    <script src="{{asset('js/service/create.js')}}"></script>
@endsection

@section('modal')
    @component('modal')

        @if ($addresses)
            <div id="more-addresses" class="hide">
                <select name="addresses[]">
                    <option value="none">{{__('Выберете адрес')}}</option>
                    @foreach($addresses as $addr)
                        <option value="{{$addr->id}}">{{$addr->address}}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="grid add-service">

            <label class="row-1 col-1 align-self-start" for="service-name">{{__('Название *')}}</label>
            <label class="row-2 col-1 align-self-center" for="service-type">{{__('Тип *')}}</label>
            <label class="row-3 col-1 align-self-center" for="address">{{__('Адрес *')}}</label>
            <label class="row-4 col-1 align-self-center" for="interval-1">{{__('Длительность *')}}</label>
            <label class="row-5 col-1 align-self-start" for="range">{{__('Интервал *')}}</label>
            <label class="row-6 col-1 align-self-start" for="price">{{__('Стоимость *')}}</label>
            <label class="row-7 col-1 align-self-start" for="bonus">{{__('Бонусы')}}</label>
            <label class="row-8 col-1 align-self-center" for="calendar">{{__('Расписание')}}</label>
            <label class="row-9 col-1 align-self-center" for="group-1">{{__('Групповая услуга *')}}</label>
            <label class="row-10 col-1 align-self-center" for="prepay-1">{{__('Оплата *')}}</label>

            <div class="row-1 col-2">
                <input id="service-name" class="inp" type="text" placeholder="{{__('Введите название')}}">
            </div>

            <div class="row-2 col-2">
                @if ($types)
                    <select id="service-type">
                        @foreach($types_select as $type)
                            <option value="{{$type->id}}">{{$type->type}}</option>
                        @endforeach
                    </select>
                @else
                    <select id="service-type" class="none">
                        <option value="">
                            {{__('Нет услуг для выбора')}}
                        </option>
                    </select>
                @endif
            </div>

            <div class="row-3 col-2 align-self-start">
                @if ($addresses)
                    <div id="addresses">
                        <select id="address" name="addresses[]">
                            @foreach($addresses as $addr)
                                <option value="{{$addr->id}}">{{$addr->address}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="more-address" class="row-2 col-3 flex">
                        <div class="add-b-icon"></div>
                        <button id="include-address" class="color">{{__('Добавить адрес к услуге')}}</button>
                    </div>
                @else
                    <select id="address" class="none">
                        <option value="">
                            {{__('Нет адреса для выбора')}}
                        </option>
                    </select>
                @endif
            </div>

            <div class="checkboxes grid intervals row-4 col-2">
                @foreach($intervals as $interval)
                    <input id="interval-{{$interval->id}}" type="radio" name="interval" value="{{$interval->id}}">
                    <label for="interval-{{$interval->id}}" class="user-select-none">{{$interval->name}}</label>
                @endforeach
            </div>

            <div class="row-5 col-2">
                <input id="range" class="inp" type="text" placeholder="{{__('Введите интервал посещений в минутах')}}">
            </div>

            <div class="row-6 col-2">
                <input id="price" class="inp" type="text" placeholder="{{__('Введите сумму')}}">
            </div>

            <div class="row-7 col-2">
                <input id="bonus" class="inp" type="text" placeholder="{{__('Введите количество')}}">
            </div>

            <div class="row-8 col-2 align-self-center">
                <button
                        id="calendar"
                        class="background-none calendar-a"
                        data-href="{{route('window.service', ['business' => $slug, 'load' => $load, 'modal' => 'timetable', 'url' => url()->current()])}}"
                >
                    <div class="calendar-icon"></div>
                </button>
            </div>

            <div class="row-9 col-2 grid intervals">
                <input id="group-1" type="radio" name="group" value="1">
                <label for="group-1" class="user-select-none">{{__('Да')}}</label>
                <input id="group-0" type="radio" name="group" value="0" checked>
                <label for="group-0" class="user-select-none">{{__('Нет')}}</label>
            </div>

            <div class="row-10 col-2 grid intervals">
                <input id="prepay" type="checkbox" name="pre" value="1" {{!empty($service->prepayment) ? 'checked' : ''}}>
                <label for="prepay" class="user-select-none">{{__('Предоплата')}}</label>
                <input id="cashpay" type="checkbox" name="cas" value="1" {{!empty($service->prepayment) ? 'checked' : ''}}>
                <label for="cashpay" class="user-select-none">{{__('На месте')}}</label>
                <input id="onlinepay" type="checkbox" name="online" value="1" {{!empty($service->prepayment) ? 'checked' : ''}}>
                <label for="onlinepay" class="user-select-none">{{__('Онлайн')}}</label>
                <input id="bonuspay" type="checkbox" name="bonuspay" value="1" {{!empty($service->prepayment) ? 'checked' : ''}}>
                <label for="bonuspay" class="user-select-none">{{__('Бонусами')}}</label>
            </div>

            <div id="type-block" class="row-2 col-3 flex align-items-center">
                <div class="add-b-icon"></div><button id="add-type">{{__('Добавить тип в список')}}</button>
            </div>

            <div id="address-block" class="row-3 col-3 flex align-items-center align-self-start">
                <div class="add-b-icon"></div><button id="add-address">{{__('Добавить адрес в список')}}</button>
            </div>

        </div>

        <div id="group-service" class="grid group add-service hide">
            <label class="row-1 col-1 align-self-center" for="quantity">{{__('Количество участников *')}}</label>
            <label class="row-2 col-1 align-self-center" for="message">{{__('Сообщение')}}</label>

            <div class="row-1 col-2">
                <input id="quantity" class="inp" type="text" placeholder="{{__('Введите количество')}}">
            </div>
            <div class="row-2 col-2">
                <textarea id="message" class="inp" type="text" placeholder="{{__('Введите сообщение')}}"></textarea>
            </div>
        </div>

        <div id="prepay-service" class="grid group add-service hide">
            <label class="row-1 col-1 align-self-center" for="card">{{__('Номер банковской карты *')}}</label>
            <label class="row-2 col-1 align-self-center" for="prepay-message">{{__('Сообщение *')}}</label>

            <div class="row-1 col-2">
                <input id="card" class="inp align-self-center" type="text" placeholder="{{__('Введите номер карты')}}">
            </div>
            <div class="row-2 col-2 align-self-center">
                <textarea id="prepay-message" class="inp" type="text" placeholder="{{__('Введите сообщение')}}"></textarea>
            </div>
        </div>

        @slot('buttons')
            <div class="line margin"></div>
            <button type="button" id="add-service" class="btn-primary">
                {{ __('Создать') }}
            </button>
            <a href="{{route('service', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
        @endslot

    @endcomponent
@endsection
