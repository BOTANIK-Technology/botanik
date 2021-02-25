@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/mail.css') }}" rel="stylesheet">
    <link href="{{ asset('css/share.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script>let url = '{{url()->current()}}';</script>
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/share.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            <header class="flex align-items-center">
                <a href="{{route('window.share', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}Создать акцию</a>
                <span class="sort">Сортировка: </span>
                <span>
                    <span class="{{$sort == 'asc' || $sort == 'desc' ? 'active' : ''}}">по дате</span>
                    <a href="{{route('share', ['business' => $slug, 'sort' => 'desc', 'load' => $load])}}"><div class="arrow {{$sort == 'desc' ? 'active' : ''}}"></div></a>
                    <a href="{{route('share', ['business' => $slug, 'sort' => 'asc', 'load' => $load])}}"><div class="arrow down {{$sort == 'asc' ? 'active' : ''}}"></div></a>
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
                            {{$item->title}}<br>
                            <span class="lite-text">{{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}</span>
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <div class="more-icon" data-id="{{$item->id}}"></div>
                            <div id="menu-{{$item->id}}" class="more-menu hide">
                                <ul>
                                    <li><a href="{{route('window.share', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'load' => $load, 'sort' => $sort])}}"><div class="view-icon"></div><span class="more-menu-text">Просмотр</span></a></li>
                                    <li><a href="{{route('window.share', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'load' => $load, 'sort' => $sort])}}"><div class="edit-icon"></div><span class="more-menu-text">Редактировать</span></a></li>
                                    <li><a href="{{route('window.share', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'load' => $load, 'sort' => $sort])}}"><div class="delete-icon"></div><span class="more-menu-text red">Удалить</span></a></li>
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
                            <a href="{{route('window.share', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'load' => $load, 'sort' => $sort])}}"><div class="view-icon"></div></a>
                        </div>

                        <div class="flex align-items-center justify-content-center date">
                            {{\Carbon\Carbon::parse($item->created_at)->format('Y-m-d')}}
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.share', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'load' => $load, 'sort' => $sort])}}"><div class="edit-icon"></div></a>
                        </div>

                        <div class="flex align-items-center justify-content-center">
                            <a href="{{route('window.share', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'load' => $load, 'sort' => $sort])}}"><div class="delete-icon"></div></a>
                        </div>
                    @endforeach
                @endphone
            @endif
        </div>

        @include('layouts.load', ['count' => $countShares, 'inputs' => ['sort' => $sort], 'load' => $load, 'route' => 'share'])

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

                <label for="button" class="col-1" id="best-design">Ссылка / кнопка</label>
                <input id="button" class="col-2 input-bg link" type="text" placeholder="https://">

                <input type="hidden" value="{{Auth::user()->id}}" id="user_id">

            </div>

            @slot('buttons')
                <button id="createShare" type="button" class="btn-primary">Создать</button>
                <a href="{{route('share', ['business' => $slug, 'sort' => $sort])}}" id="refresh-modal"></a>
            @endslot

        @elseif ($modal == 'edit')

            @slot('class')
                create
            @endslot

            <div class="create-body grid">

                <label for="title" class="col-1">Заголовок</label>
                <input id="title" class="col-2 {{$share->title ? 'active' : ''}}" type="text" value="{{$share->title}}" placeholder="Введите заголовок">

                <label for="img" class="col-1">Обложка</label>
                <input id="img" class="col-2 {{$share->img ? 'active' : ''}}" type="text" value="{{$share->img}}" placeholder="Ссылка на изображение">

                <label for="text" class="col-1 align-self-start">Текст</label>
                <textarea id="text" class="col-2 {{$share->text ? 'active' : ''}}" placeholder="Введите текст">{{$share->text}}</textarea>

                <label for="button" class="col-1" id="best-design">Ссылка / кнопка</label>
                <input id="button" class="col-2 input-bg link {{$share->button ? 'active' : ''}}" type="text" value="{{$share->button}}" placeholder="https://">

                <input type="hidden" value="{{Auth::user()->id}}" id="user_id">

            </div>

            @slot('buttons')
                <button id="editShare" type="button" data-id="{{$share->id}}" class="btn-primary">Сохранить</button>
                <a href="{{route('share', ['business' => $slug, 'sort' => $sort])}}" id="refresh-modal"></a>
            @endslot

        @elseif ($modal == 'delete')

            @slot('class')
                delete
            @endslot

            <div class="delete">
                Вы действительно<br>
                хотите удалить акцию <br>
                <b>“{{$share->title}}”</b>?
            </div>
            @slot('buttons')
                <button type="button" id="delete" data-id="{{$share->id}}" class="btn-primary">
                    {{ __('Удалить') }}
                </button>
                <a href="{{route('share', ['business' => $slug, 'sort' => $sort])}}" id="refresh-modal"></a>
            @endslot

        @else
            @slot('class')
                view
            @endslot

            <div class="view-body flex direction-column">
                <h1>{{$share->title}}</h1>
                @if (!is_null($share->img))
                    <img src="{{$share->img}}" class="img">
                @endif
                <p class="mail-text">{{$share->text}}</p>
                <span class="modal-date date">{{\Carbon\Carbon::parse($share->created_at)->format('Y-m-d')}}</span>
                @if (!is_null($share->button))
                    <span id="best-design" class="active">Ссылка / кнопка</span>
                @endif
            </div>

            @slot('buttons')
                @if (!is_null($share->button))
                    <a class="modal-date url" href="{{$share->button}}">{{substr($share->button, 0, 50)}}...</a>
                @endif
                <a href="{{route('share', ['business' => $slug, 'sort' => $sort])}}" id="refresh-modal"></a>
            @endslot
        @endif
    @endcomponent
@endsection
@endif
