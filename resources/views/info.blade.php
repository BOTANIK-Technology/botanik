@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/mail.css') }}" rel="stylesheet">
    <link href="{{ asset('css/info.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script>let url = '{{url()->current()}}';</script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/info.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            @if ($package == 'pro' || $package == 'base')
                <header class="flex align-items-center">
                    <a href="{{route('window.info', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}Добавить раздел</a>
                </header>
            @endif
        @endslot

        <div class="table grid">
            @if(isset($table) && $table)
                @phone
                    @foreach($table as $item)
                        <div class="flex align-items-center justify-content-center num">
                            {{$loop->iteration}}
                        </div>

                        <div class="flex align-items-center text">
                            {{$item->title}}
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <div class="more-icon" data-id="{{$item->id}}"></div>
                            <div id="menu-{{$item->id}}" class="more-menu hide">
                                <ul>
                                    <li><a href="{{route('window.info', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'load' => $load])}}"><div class="view-icon"></div><span class="more-menu-text">Просмотр</span></a></li>
                                    <li><a href="{{route('window.info', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'load' => $load])}}"><div class="edit-icon"></div><span class="more-menu-text">Редактировать</span></a></li>
                                    <li><a href="{{route('window.info', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'load' => $load])}}"><div class="delete-icon"></div><span class="more-menu-text red">Удалить</span></a></li>
                                </ul>
                                <div data-id="{{$item->id}}" class="more-menu-close"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    @foreach($table as $item)
                        <div class="flex align-items-center justify-content-center num">
                            {{$loop->iteration}}
                        </div>

                        <div class="flex align-items-center text">
                            {{$item->title}}
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.info', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'load' => $load])}}"><div class="view-icon"></div></a>
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.info', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'load' => $load])}}"><div class="edit-icon"></div></a>
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.info', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'load' => $load])}}"><div class="delete-icon"></div></a>
                        </div>
                    @endforeach
                @endphone
            @endif
        </div>

        @include('layouts.load', ['count' => $countInfo, 'load' => $load, 'route' => 'info'])

    @endcomponent
@endsection

@if (isset($modal))
@section('modal')
    @component('modal')
        @if ($modal == 'create')
            @slot('class')
                create
            @endslot

            <div class="create-body grid">

                <label for="title" class="col-1">Заголовок</label>
                <input id="title" class="col-2" type="text" placeholder="Введите заголовок">

                <label for="img" class="col-1">Обложка</label>
                <input id="img" class="col-2" type="text" placeholder="Ссылка на изображение">

                <label for="text" class="col-1 align-self-start">Текст</label>
                <textarea id="text" class="col-2" placeholder="Введите текст"></textarea>

                <label for="button" class="col-1" id="best-design">Кнопка "Подробнее"</label>
                <input id="button" class="col-2 input-bg link" type="text" placeholder="https://">

            </div>

            @slot('buttons')
                <button id="createInfo" type="button" class="btn-primary">Создать</button>
                <a href="{{route('info', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @elseif ($modal == 'edit')

            @slot('class')
                create edit
            @endslot

            <div class="create-body grid">

                <label for="title" class="col-1">Заголовок</label>
                <input id="title" class="col-2 {{$info->title ? 'active' : ''}}" type="text" value="{{$info->title}}" placeholder="Введите заголовок">

                <label for="img" class="col-1">Обложка</label>
                <input id="img" class="col-2 {{$info->img ? 'active' : ''}}" type="text" value="{{$info->img}}" placeholder="Ссылка на изображение">

                <label for="text" class="col-1 align-self-start">Текст</label>
                <textarea id="text" class="col-2 {{$info->text ? 'active' : ''}}" placeholder="Введите текст">{{$info->text}}</textarea>

                <label for="button" class="col-1" id="best-design">Кнопка "Подробнее"</label>
                <input id="button" class="input-bg link col-2 {{$info->button ? 'active' : ''}}" type="text" value="{{$info->button}}" placeholder="https://">

                @if (!is_null($info->addresses))
                    <label for="address" id="design-btn" class="col-1 best-design active">Адрес для маршрута</label>
                    <div id="address-block" class="col-2">
                        @foreach(\GuzzleHttp\json_decode($info->addresses) as $address)
                            <input id="address" name="addresses[]" class="input-bg map-pin {{$address ? 'active' : ''}}" type="text" value="{{$address}}" placeholder="ул. Сумская №1">
                        @endforeach
                    </div>
                    <div class="flex align-items-center" id="add-address-block">
                        {!! file_get_contents(public_path('images/add-b.svg')) !!}<button id="add-address">Добавить адрес</button>
                    </div>
                @endif

            </div>

            @slot('buttons')
                <button id="editInfo" type="button" data-id="{{$info->id}}" class="btn-primary">Сохранить</button>
                <a href="{{route('info', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @elseif ($modal == 'delete')

            @slot('class')
                delete
            @endslot

            <div class="delete">
                Вы действительно<br>
                хотите удалить раздел <br>
                <b>“{{$info->title}}”</b>?
            </div>
            @slot('buttons')
                <button type="button" id="delete" data-id="{{$info->id}}" class="btn-primary">
                    {{ __('Удалить') }}
                </button>
                <a href="{{route('info', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @else
            @slot('class')
                view
            @endslot

            <div class="view-body flex direction-column">
                <h1>{{$info->title}}</h1>
                @if (!is_null($info->img))
                    <img src="{{$info->img}}" class="img">
                @endif
                @if (!is_null($info->addresses))
                    @foreach(\GuzzleHttp\json_decode($info->addresses) as $address)
                        <a class="modal-date date" href="{{'https://www.google.com/maps/search/?api=1&query='.urlencode($address).'&sensor=false'}}">{{$address}}</a>
                    @endforeach
                @endif
                <p class="mail-text">{!! $info->text !!}</p>
                <span class="modal-date date"><br>{{\Carbon\Carbon::parse($info->created_at)->format('Y-m-d')}}</span>
                @if (!is_null($info->button))
                    <span id="best-design" class="active">Кнопка "Подробнее"</span>
                @endif
            </div>

            @slot('buttons')
                @if (!is_null($info->button))
                    <a class="modal-date url" href="{{$info->button}}">{{substr($info->button, 0, 50)}}...</a>
                @endif
                <a href="{{route('info', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot
        @endif
    @endcomponent
@endsection
@endif