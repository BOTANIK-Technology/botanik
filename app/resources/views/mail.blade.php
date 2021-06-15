@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/mail.css') }}" rel="stylesheet">
    <link href="{{ asset('css/img-label.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script>let slug = '{{$slug}}'</script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/img-label.js')}}"></script>
    <script src="{{asset('js/mail.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <header class="flex align-items-center">
                <a href="{{route('mail.window.create', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}Создать расслылку</a>
                <span class="sort">Сортировка: </span>
                <span>
                    <span class="{{$sort == 'asc' || $sort == 'desc' ? 'active' : ''}}">по дате</span>
                    <a href="{{route('mail', ['business' => $slug, 'sort' => 'desc', 'load' => $load])}}"><div class="arrow {{$sort == 'desc' ? 'active' : ''}}"></div></a>
                    <a href="{{route('mail', ['business' => $slug, 'sort' => 'asc', 'load' => $load])}}"><div class="arrow down {{$sort == 'asc' ? 'active' : ''}}"></div></a>
                </span>
            </header>
        @endslot

        <div class="table grid">
            @if(isset($table) && $table)
                @phone
                    @foreach($table as $item)
                        <div class="flex align-items-center justify-content-center num">
                            {{$loop->iteration}}
                        </div>

                        <div class="flex direction-column justify-content-center text">
                            {{$item->title}}
                            <span class="lite-text">
                                {{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}
                            </span>
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('mail.window.view', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="view-icon"></div></a>
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
                            <a href="{{route('mail.window.view', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'sort' => $sort, 'load' => $load])}}"><div class="view-icon"></div></a>
                        </div>

                        <div class="flex align-items-center justify-content-center date">
                            {{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}
                        </div>
                    @endforeach
                @endphone
            @endif
        </div>

        @include('layouts.load', ['count' => $countMail, 'load' => $load, 'route' => 'mail'])

    @endcomponent
@endsection

@if (isset($modal))
@section('modal')
    @component('modal')
        @if ($modal == 'create')
            @slot('class')
                create
            @endslot

            <div class="create-header">
                <div class="flex justify-content-center">
                    <select name="sex" id="sex" class="none">
                        <option value="">
                            Мужчины / женщины
                        </option>
                        <option value="1">Мужчины</option>
                        <option value="0">Женщины</option>
                    </select>
                    <select name="last_service" id="last_service" class="none">
                        <option value="">
                            Последний сервис
                        </option>
                        @if (isset($types))
                            @foreach($types as $type)
                                <option value="{{$type->id}}">{{$type->type}}</option>
                            @endforeach
                        @else
                            <option value="">
                                Нет типа для выбора
                            </option>
                        @endif
                    </select>
                    <select name="favorite_service" id="favorite_service" class="none">
                        <option value="">
                            Любимый сервис
                        </option>
                        @if (isset($types))
                            @foreach($types as $type)
                                <option value="{{$type->id}}">{{$type->type}}</option>
                            @endforeach
                        @else
                            <option value="">
                                Нет типа для выбора
                            </option>
                        @endif
                    </select>
                </div>

                <div class="flex justify-content-center">
                    <select name="age_start" id="age_start" class="none">
                        <option value="">
                            Возраст (от)
                        </option>
                        @foreach($age as $item)
                            <option value="{{$item}}">
                                {{$item}}
                            </option>
                        @endforeach
                    </select>
                    <select name="age_end" id="age_end" class="none">
                        <option value="">
                            Возраст (до)
                        </option>
                        @foreach($age as $item)
                            <option value="{{$item}}">
                                {{$item}}
                            </option>
                        @endforeach
                    </select>
                    <select name="frequency" id="frequency" class="none">
                        <option value="">
                            Частота посещений
                        </option>
                        @foreach($freq as $item)
                            <option value="{{$loop->index}}">
                                @if ($loop->first)
                                    {{'Ни разу в месяц'}}
                                @elseif ($loop->last)
                                    {{$item.' раз в месяц'}}
                                @else
                                    {{$item.' раза в месяц'}}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="line"></div>

            <div class="create-body grid">

                <label for="title" class="col-1">Заголовок</label>
                <input id="title" class="col-2" type="text" placeholder="Введите заголовок">

                <label for="img" class="col-1">Обложка</label>
                <label for="img" class="image-logo flex align-items-center col-2" id="img-label">
                    <input type="file" accept="image/*" name="image" id="img">
                </label>

                <label for="text" class="col-1 align-self-start">Текст</label>
                <textarea id="text" class="col-2" placeholder="Введите текст"></textarea>

                <label for="button" class="col-1" id="best-design">Ссылка / кнопка</label>
                <input id="button" class="col-2 input-bg link" type="text" placeholder="https://">

            </div>

            @slot('buttons')
                <button
                    onclick="sendEvent(this);"
                    id="createMail"
                    type="button"
                    class="btn-primary"
                    data-storage="{{route('api.storage')}}"
                    data-url="{{route('mail.create', ['business' => $slug])}}"
                >
                    Создать
                </button>
                <a href="{{route('mail', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @else
            @slot('class')
                view
            @endslot

            <div class="view-body flex direction-column">
                <h1>{{$mail->title}}</h1>
                @if (!is_null($mail->img))
                    <img src="{{asset('public/storage/'.$mail->img)}}" class="img">
                @endif
                <p class="mail-text">{{$mail->text}}</p>
                <span class="modal-date date">{{\Carbon\Carbon::parse($mail->created_at)->format('Y-m-d')}}</span>
                @if (!is_null($mail->button))
                    <span id="best-design" class="active">Ссылка / кнопка</span>
                @endif
            </div>

            @slot('buttons')
                @if (!is_null($mail->button))
                    <a class="modal-date url" href="{{$mail->button}}">{{substr($mail->button, 0, 50)}}...</a>
                @endif
                <a href="{{route('mail', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot
        @endif
    @endcomponent
@endsection
@endif
