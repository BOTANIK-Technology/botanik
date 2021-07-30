@section('scripts')
    <script>
        let note = '{{Auth::user()->hasRole('admin') ? route('window.user', ['business' => $slug, 'sort' => $sort, 'modal' => 'note', 'load' => $load]) : ''}}';
        let countService = '{{$moreService}}';
        let editRoute = "{{route('window.user', ['business' => $slug, 'modal' => 'edit', 'id' => $id])}}";
    </script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/cookie.min.js')}}"></script>
    <script src="{{asset('js/user/page.js')}}"></script>
    <script src="{{asset('js/user/manage.js')}}"></script>
    <script src="{{asset('js/user/edit.js')}}"></script>
    <script src="{{asset('js/user/user_window.js')}}"></script>
@endsection

@section('modal')
    @component('modal')
        <input id="url_slug" type="hidden" value="{{$slug}}" name="url_slug">
        <input type="hidden" id="token_id" name="_token" value="{{ csrf_token() }}">
        @slot('class')
            modal-edit
        @endslot

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
            <div><input type="text"  value="{{$user->name}}" placeholder="{{__('Название / ФИО')}}" id="fio"></div>
            <div><input type="text"  value="{{$user->phone}}" placeholder="{{__('Телефон')}}" id="phone"></div>
            <div><input type="email" value="{{$user->email}}" placeholder="Email" id="email"></div>
            <div><input type="text"  placeholder="Пароль" id="password"></div>

            <div class="checkboxes flex justify-content-between">
                <input id="master" type="radio" name="role" value="master" {{$user->hasRole('master') ? 'checked' : ''}}>
                <label for="master">{{__('Специалист')}}</label>
                <input id="admin" type="radio" name="role" value="admin" {{$user->hasRole('admin') ? 'checked' : ''}}>
                <label for="admin">{{__('Администратор')}}</label>
            </div>

            <div class="line full-width"></div>

            @foreach($user->timetables as $timetable)
                <div>
                    @if ($addresses)
                        <select class="master-address hide"  onchange="userWin.changeAddress({{$loop->index}});" id="address-{{$loop->index}}" name="address-{{$loop->index}}[]">
                            @foreach($addresses as $addr)
                                <option value="{{$addr->id}}" {{$timetable->address_id == $addr->id ? 'selected' : ''}}>{{$addr->address}}</option>
                            @endforeach
                        </select>
                    @else
                        <select class="master-address none hide" id="address-{{$loop->index}}" name="address-{{$loop->index}}[]">
                            <option>
                                {{__('Нет адреса для выбора')}}
                            </option>
                        </select>
                    @endif
                </div>

                <div id="admin-addresses-{{$loop->index}}" class="flex direction-column">
                    @if ($addresses)
                        <select onchange="userWin.changeAdminAddress({{$loop->index}});" id="admin-address-{{$loop->index}}" name="admin-address-{{$loop->index}}[]" class="admin-address hide">
                            @foreach($addresses as $addr)
                                <option value="{{$addr->id}}" {{$timetable->address_id == $addr->id ? 'selected' : ''}}>{{$addr->address}}</option>
                            @endforeach
                        </select>
                    @else
                        <select id="admin-address-{{$loop->index}}" name="admin-address-{{$loop->index}}[]" class="admin-address none hide">
                            <option selected>
                                {{__('Нет адреса для выбора')}}
                            </option>
                        </select>
                    @endif
                </div>

                <div id="service-services-{{$loop->index}}" class="flex direction-column">
                    @if ($services)
                        <select class="master-service hide" id="service-type-{{$loop->index}}" data-id="{{$loop->index}}" name="service-{{$loop->index}}[]">
                            @foreach($services as $service)
                                <option value="{{$service->id}}" {{$timetable->service_id == $service->id ? 'selected' : ''}}>
                                    {{$service->name}}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <select class="master-service  hide none" id="service-type-{{$loop->index}}" name="service-{{$loop->index}}[]">
                            <option>
                                {{__('Нет услуг для выбора')}}
                            </option>
                        </select>
                    @endif
                </div>

                <div class="flex justify-content-between align-items-center">
                    <span class="calendar">{{__('Расписание')}}</span>
                    <button
                            id="calendar-{{$loop->index}}"
                            class="background-none calendar-a"
                            data-href="{{route('window.user', ['business' => $slug, 'modal' => 'timetable', 'sort' => $sort, 'currentService' => $loop->index, 'moreService' => $moreService, 'id' => $id])}}"
                    >
                        <div class="calendar cover"></div>
                    </button>
                </div>

            @endforeach

            @if ($moreService > count($user->addresses))
                @for($i = count($user->addresses); $i < $moreService; $i++)
                    <div>
                        @if ($addresses)
                            <select id="address-{{$i}}" name="address-{{$i}}[]">
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
                    <div id="admin-addresses-{{$i}}" class="flex direction-column">
                        @if ($addresses)
                            <select id="admin-address-{{$i}}" name="admin-address-{{$i}}[]" class="admin-address hide">
                                @foreach($addresses as $addr)
                                    <option value="{{$addr->id}}">{{$addr->address}}</option>
                                @endforeach
                            </select>
                        @else
                            <select id="admin-address-{{$i}}" name="admin-address-{{$i}}[]" class="admin-address none hide">
                                <option selected>
                                    {{__('Нет адреса для выбора')}}
                                </option>
                            </select>
                        @endif
                    </div>
                    <div id="service-services-{{$i}}" class="flex direction-column">
                        @if ($services)
                            <select id="service-type-{{$i}}" data-id="{{$i}}" name="service-{{$i}}[]">
                                @foreach($services as $service)
                                    <option value="{{$service->id}}">{{$service->name}}</option>
                                @endforeach
                            </select>
                        @else
                            <select id="service-type-{{$i}}" name="service-{{$loop->index}}[]" class="none">
                                <option selected>
                                    {{__('Нет услуг для выбора')}}
                                </option>
                            </select>
                        @endif
                    </div>
                @endfor
            @endif
        </div>
        <div class="line"></div>
        @slot('buttons')
            <div id="type-block" class="row-1 col-3 flex align-items-center justify-content-center">
                <div class="add-b-icon"></div>
                <a id="add-type" class="color text-decoration-none" href="{{route('addService', ['business' => $slug, 'id' => $user->id, 'modal' => $modal, 'moreService' => $moreService+1, 'sort' => $sort, 'load' => $load])}}">{{__('Добавить услугу к специалисту')}}</a>
            </div><br>
            <button type="button" id="edit-user" class="btn-primary">
                {{ __('Сохранить') }}
            </button>
            <a href="{{route('user', ['business' => $slug, 'sort' => $sort, 'load' => $load])}}" id="refresh-modal"></a>
        @endslot
    @endcomponent
@endsection
