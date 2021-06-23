@section('scripts')
    <script>
        let note = '{{Auth::user()->hasRole('admin') ? route('window.user', ['business' => $slug, 'sort' => $sort, 'modal' => 'note', 'load' => $load]) : ''}}';
        let countService = '{{$moreService}}';
        let createRoute = "{{route('window.user', ['business' => $slug, 'modal' => 'create'])}}";
    </script>
    <script src="{{asset('js/user/page.js')}}"></script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/cookie.min.js')}}"></script>
    <script src="{{asset('js/user/manage.js')}}"></script>
    <script src="{{asset('js/user/create.js')}}"></script>
    <script src="{{asset('js/user/user_window.js')}}"></script>
@endsection


@section('modal')
    @component('modal')
        <input id="url_slug" type="hidden" value="{{$slug}}" name="url_slug">
        <input type="hidden" id="token_id" name="_token" value="{{ csrf_token() }}">

        <div class="hide">
            @if ($services)
                <select id="service-type">
                    @foreach($services as $service)
                        <option value="{{$service->id}}">{{$service->name}}</option>
                    @endforeach
                </select>
            @else
                <select id="service-type" class="none">
                    <option>
                        {{__('Нет услуг для выбора')}}
                    </option>
                </select>
            @endif
        </div>

        <div class="flex direction-column add-user">
            <div><input type="text" placeholder="{{__('Название / ФИО')}}" id="fio"></div>
            <div><input type="text" placeholder="{{__('Телефон')}}" id="phone"></div>
            <div><input type="email" placeholder="Email" id="email"></div>
            <div><input type="text" placeholder="{{__('Пароль')}}" id="password"></div>

            <div class="checkboxes flex justify-content-between">
                <input id="master" type="radio" name="role" value="master" checked>
                <label for="master">{{__('Специалист')}}</label>
                @if ($package == 'pro')
                    <input id="admin" type="radio" name="role" value="admin">
                    <label for="admin">{{__('Администратор')}}</label>
                @endif
            </div>

            <div class="line full-width"></div>

            @for($i = 0; $i < $moreService; $i++)
                <div id="service-types-{{$i}}" class="flex direction-column">
                    @if ($services)
                        <select onchange="userWin.changeService({{$i}});" id="service-type-{{$i}}" data-id="{{$i}}" name="service-{{$i}}[]">
                            @foreach($services as $service)
                                <option value="{{$service->id}}">{{$service->name}}</option>
                            @endforeach
                        </select>
                    @else
                        <select id="service-type-{{$i}}" name="service-{{$i}}[]" class="none">
                            <option selected>
                                {{__('Нет услуг для выбора')}}
                            </option>
                        </select>
                    @endif
                </div>

                <div id="addresses-{{$i}}" class="flex direction-column">
                    @if ($addresses)
                        <select id="address-{{$i}}" name="address-{{$i}}[]" style="display:none;">
                            @foreach($addresses as $addr)
                                <option value="{{$addr->id}}">{{$addr->address}}</option>
                            @endforeach
                        </select>
                    @else
                        <select id="address-{{$i}}" name="address-{{$i}}[]" class="none">
                            <option selected>
                                {{__('Нет адреса для выбора')}}
                            </option>
                        </select>
                    @endif
                </div>

                <div class="flex justify-content-between align-items-center">
                    <span class="calendar">{{__('Расписание')}}</span>
                    <button
                            id="calendar-{{$i}}"
                            class="background-none calendar-a"
                            data-href="{{route('window.user', ['business' => $slug, 'modal' => 'timetable', 'sort' => $sort, 'currentService' => $i, 'moreService' => $moreService])}}"
                    >
                        <div class="calendar-icon"></div>
                    </button>
                </div>
            @endfor
        </div>

        <div class="line"></div>

        @slot('buttons')
            <div id="type-block" class="row-1 col-3 flex align-items-center justify-content-center">
                <div class="add-b-icon"></div>
                <a id="add-type" class="color text-decoration-none" data-href="{{route('addService', ['business' => $slug, 'id' => $id ?? 'new', 'modal' => $modal, 'moreService' => $moreService+1, 'sort' => $sort, 'load' => $load])}}">{{__('Добавить услугу к специалисту')}}</a>
            </div><br>
            <button type="button" id="add-user" class="btn-primary">
                {{ __('Создать') }}
            </button>
            <a href="{{route('user', ['business' => $slug, 'sort' => $sort])}}" id="refresh-modal"></a>
        @endslot

    @endcomponent
@endsection
