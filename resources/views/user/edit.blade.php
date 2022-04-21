@section('scripts')
    <script src="{{asset('js/user/page.js')}}"></script>
    <script src="{{asset('js/cookie.js')}}"></script>
    <script>
        let id = {{$id}};
        let note = '{{auth()->user()->hasRole('admin') ? route('window.user', ['business' => $slug, 'sort' => $sort, 'modal' => 'note', 'load' => $load]) : ''}}';
        let countService = '{{$moreService}}';
        let editRoute = "{{route('window.user', ['business' => $slug, 'modal' => 'edit', 'id' => $id])}}";
        let services = @json($services || []);
        let addresses = @json($addresses || []);
       if(! Object.keys(getCookie('user') ).length) {
           setCookie('user', @json($user));
       }

        if(! getCookie('userData').length) {
            setCookie('userData', @json($userData));
        }
        if (!getCookie('timetables').length) {
            setCookie('timetables', @json($timetables));
        }

    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/user/manage.js')}}"></script>
    <script src="{{asset('js/user/user_window.js')}}"></script>
@endsection

<div id="service-count" data-count="{{$moreService}}"></div>
@section('modal')
    @component('modal')
        <input id="url_slug" type="hidden" value="{{$slug}}" name="url_slug">
        <input type="hidden" id="token_id" name="_token" value="{{ csrf_token() }}">

        <div class="flex direction-column add-user">
            <div><input disabled type="text" value="{{$user->name}}" placeholder="{{__(' ФИО')}}" id="fio"></div>
            <div><input disabled type="text" value="{{$user->phone}}" placeholder="{{__('Телефон')}}" id="phone"></div>
            <div><input disabled type="email" value="{{$user->email}}" placeholder="Email" id="email"></div>
            <div><input disabled type="text" placeholder="Пароль" id="password"></div>

            <div class="checkboxes flex justify-content-between">
                <input disabled id="master" type="radio" name="role"
                       value="master" {{$user->hasRole('master') ? 'checked' : ''}}>
                <label for="master">{{__('Специалист')}}</label>
                <input id="admin" type="radio" name="role" value="admin" {{$user->hasRole('admin') ? 'checked' : ''}}>
                <label for="admin">{{__('Администратор')}}</label>
            </div>

            <div class="line full-width"></div>

            @for($i = 0; $i < $moreService; $i++)

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
							 'moreService' => $moreService,
                            'modal' => 'timetable',
                            'mode' => 'edit'
                    ])}}">
                        <div class="calendar-icon"></div>
                    </a>
                    <div class="filled-months">
                        @if(isset($usedMonths[$i]))
                            @foreach($usedMonths[$i] as $month)
                                <p>{{$month}}</p>
                            @endforeach
                        @endif
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
            <button type="button" id="edit-user"
                    data-ref="{{route('editUser', ['business' => $slug, 'id' => $id])}}"
                    class="btn-primary">
                {{ __('Сохранить') }}
            </button>
            <a href="{{route('user', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
        @endslot
    @endcomponent
@endsection
