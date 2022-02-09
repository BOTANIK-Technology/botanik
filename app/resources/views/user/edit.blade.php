@section('scripts')
    <script src="{{asset('js/cookie.js')}}"></script>
    <script>
        let id = '{{$id}}';
        let saveData = "{{request()->get('savedata', 0)}}";
        if (!saveData) {
            //     resetAll();
        }
        let note = '{{auth()->user()->hasRole('admin') ? route('window.user', ['business' => $slug, 'sort' => $sort, 'modal' => 'note', 'load' => $load]) : ''}}';
        let countService = '{{$moreService}}';
        let editRoute = "{{route('window.user', ['business' => $slug, 'modal' => 'edit', 'id' => $id])}}";
        let services = @json($services);
        let addresses = @json($addresses);
        let timeTables = @json($timetables);

        @if($user)
        setCookie('user', @json($user));
        @endif

        @if($userData)
        setCookie('userData', @json($userData));
        @endif

        for (let item in timeTables) {
            setCookie(item, timeTables[item]);
        }


    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/user/page.js')}}"></script>
    <script src="{{asset('js/user/manage.js')}}"></script>
    <script src="{{asset('js/user/edit.js')}}"></script>
    <script src="{{asset('js/user/user_window.js')}}"></script>
@endsection

<div id="service-count" data-count="{{$moreService}}"></div>
@section('modal')
    @component('modal')
        <input id="url_slug" type="hidden" value="{{$slug}}" name="url_slug">
        <input type="hidden" id="token_id" name="_token" value="{{ csrf_token() }}">

        <div class="flex direction-column add-user">
            <div><input type="text" value="{{$user->name}}" placeholder="{{__(' ФИО')}}" id="fio"></div>
            <div><input type="text" value="{{$user->phone}}" placeholder="{{__('Телефон')}}" id="phone"></div>
            <div><input type="email" value="{{$user->email}}" placeholder="Email" id="email"></div>
            <div><input type="text" placeholder="Пароль" id="password"></div>

            <div class="checkboxes flex justify-content-between">
                <input id="master" type="radio" name="role"
                       value="master" {{$user->hasRole('master') ? 'checked' : ''}}>
                <label for="master">{{__('Специалист')}}</label>
                <input id="admin" type="radio" name="role" value="admin" {{$user->hasRole('admin') ? 'checked' : ''}}>
                <label for="admin">{{__('Администратор')}}</label>
            </div>

            <div class="line full-width"></div>

            @for($i = 0; $i <= $moreService; $i++)
                <div class="flex direction-column master-only">
                    <label class="list-label" for="service-type-{{$i}}">Тип услуги</label>
                    @if ($types)
                        <select class="master-service-type" id="service-type-{{$i}}"
                                onchange="userWin.changeServiceType({{$i}})">
                            <option class="placeholder" value="0" selected>{{__('Выберите тип услуги')}}</option>
                            @foreach($types as $type)
                                <option value="{{$type->id}}">{{$type->type}}</option>
                            @endforeach
                        </select>
                    @else
                        <select id="service-type" class="none">
                            <option value="">
                                {{__('Нет типов услуг для выбора')}}
                            </option>
                        </select>
                    @endif
                </div>

                <div id="service-container-{{$i}}" class="flex direction-column master-only">
                    <label class="list-label" for="service-{{$i}}">Услуга</label>
                    <select class="master-service hide" onchange="userWin.changeService({{$i}});" id="service-{{$i}}"
                            data-id="{{$i}}" name="service-{{$i}}[]">
                    </select>
                </div>

                <div id="addresses-container-{{$i}}" class="flex direction-column master-only">
                    <label class="list-label" for="service-type-{{$i}}">Адрес</label>
                    <select class="master-address hide" onchange="userWin.changeAddress({{$i}});" id="address-{{$i}}"
                            name="address-{{$i}}[]">
                    </select>
                </div>


                <div id="admin-addresses-{{$i}}" class="flex direction-column admin-only">
                    @if ($addresses)
                        <select onchange="userWin.changeAdminAddress({{$i}});" id="admin-address-{{$i}}"
                                class="admin-address" name="admin-address-{{$i}}[]">
                            @foreach($addresses as $addr)
                                <option value="{{$addr->id}}">{{$addr->address}}</option>
                            @endforeach
                        </select>
                    @else
                        <select id="admin-address-{{$i}}" class="admin-address none" name="admin-address-{{$i}}[]">
                            <option selected>
                                {{__('Нет адреса для выбора')}}
                            </option>
                        </select>
                    @endif
                </div>
                <div class="flex justify-content-between align-items-center">
                    <span class="calendar">{{__('Расписание')}}</span>
                    <a id="calendar" class="background-none calendar-a"
                       href="{{route('window.user', [
                                'business' => $slug,
                                'id' => $user->id,
                                  'currentService' => $i,
                                   'modal' => 'timetable'
                    ])}}">
                        <div class="calendar-icon"></div>
                    </a>
                    <div class="filled-months">
                        {{--                        @foreach($usedMonths[$i] as $month)--}}
                        {{--                            <p>{{$month}}</p>--}}
                        {{--                        @endforeach--}}
                    </div>

                </div>
            @endfor
        </div>
        <div class="line"></div>
        @slot('buttons')
            <div id="type-block" class="row-1 col-3 flex align-items-center justify-content-center">
                <div class="add-b-icon"></div>
                <a id="add-type" class="color text-decoration-none"
                   href="{{route('addService', ['business' => $slug, 'id' => $user->id, 'modal' => $modal, 'moreService' => $moreService+1, 'sort' => $sort, 'load' => $load])}}">{{__('Добавить услугу к специалисту')}}</a>
            </div><br>
            <button type="button" id="edit-user" class="btn-primary">
                {{ __('Сохранить') }}
            </button>
            <a href="{{route('user', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
        @endslot
    @endcomponent
@endsection
