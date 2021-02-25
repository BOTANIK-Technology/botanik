@extends('layouts.app')

@section('styles')
    <link href="{{ asset('css/mail.css') }}" rel="stylesheet">
    <link href="{{ asset('css/catalog.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script src="{{asset('js/requests.js')}}"></script>
    <script src="{{asset('js/catalog.js')}}"></script>
@endsection

@section('content')
    @component('layouts.content')
        @slot('header')
            @if ($package == 'pro' || $package == 'base')
                <header class="flex align-items-center">
                    <a href="{{route('window.catalog', ['business' => $slug, 'modal' => 'create', 'load' => $load])}}" class="btn full-width text-decoration-none flex justify-content-around align-items-center">{!! file_get_contents(public_path('images/add-w.svg')) !!}{{__('Добавить товар')}}</a>
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
                                <li><a href="{{route('window.catalog', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'load' => $load])}}"><div class="view-icon"></div><span class="more-menu-text">{{__('Просмотр')}}</span></a></li>
                                <li><a href="{{route('window.catalog', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'load' => $load])}}"><div class="edit-icon"></div><span class="more-menu-text">{{__('Редактировать')}}</span></a></li>
                                <li><a href="{{route('window.catalog', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'load' => $load])}}"><div class="delete-icon"></div><span class="more-menu-text red">{{__('Удалить')}}</span></a></li>
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
                        <a href="{{route('window.catalog', ['business' => $slug, 'modal' => 'view', 'id' => $item->id, 'load' => $load])}}"><div class="view-icon"></div></a>
                    </div>

                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.catalog', ['business' => $slug, 'modal' => 'edit', 'id' => $item->id, 'load' => $load])}}"><div class="edit-icon"></div></a>
                    </div>

                    <div class="flex align-items-center justify-content-center">
                        <a href="{{route('window.catalog', ['business' => $slug, 'modal' => 'delete', 'id' => $item->id, 'load' => $load])}}"><div class="delete-icon"></div></a>
                    </div>
                @endforeach
                @endphone
            @endif
        </div>

        @include('layouts.load', ['count' => $countItems, 'load' => $load, 'route' => 'catalog'])

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

                <label for="title" class="col-1">{{__('Заголовок')}}</label>
                <input id="title" class="col-2" type="text" placeholder="{{__('Введите заголовок')}}">

                <label for="img" class="col-1">{{__('Обложка')}}</label>
                <input id="img" class="col-2" type="text" placeholder="{{__('Ссылка на изображение')}}">

                <label for="text" class="col-1 align-self-start">{{__('Текст')}}</label>
                <textarea id="text" class="col-2" placeholder="{{__('Введите текст')}}"></textarea>

                <label for="price" class="col-1 align-self-start">{{__('Цена')}}</label>
                <input id="price" class="col-2" type="text" placeholder="{{__('Цена')}}">

                <label for="count" class="col-1 align-self-start">{{__('Количество')}}</label>
                <input id="count" class="col-2" type="text" placeholder="{{__('Количество')}}">

                <label for="article" class="col-1 align-self-start">{{__('Артикул')}}</label>
                <input id="article" class="col-2" type="text" placeholder="{{__('Артикул')}}">

            </div>

            @slot('buttons')
                <button id="create-btn" data-url="{{route('catalog.create', ['business' => $slug])}}" type="button" class="btn-primary">{{__('Создать')}}</button>
                <a href="{{route('catalog', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @elseif ($modal == 'edit')

            @slot('class')
                create edit
            @endslot

            <div class="create-body grid">

                <label for="title" class="col-1">{{__('Заголовок')}}</label>
                <input id="title" class="col-2 {{$product->title ? 'active' : ''}}" type="text" value="{{$product->title}}" placeholder="{{__('Введите заголовок')}}">

                <label for="img" class="col-1">{{__('Обложка')}}</label>
                <input id="img" class="col-2 {{$product->img ? 'active' : ''}}" type="text" value="{{$product->img}}" placeholder="{{__('Ссылка на изображение')}}">

                <label for="text" class="col-1 align-self-start">{{__('Текст')}}</label>
                <textarea id="text" class="col-2 {{$product->text ? 'active' : ''}}" placeholder="{{__('Введите текст')}}">{{$product->text}}</textarea>

                <label for="price" class="col-1 align-self-start">{{__('Цена')}}</label>
                <input id="price" class="col-2 {{$product->price ? 'active' : ''}}" value="{{$product->price}}" type="text" placeholder="{{__('Цена')}}">

                <label for="count" class="col-1 align-self-start">{{__('Количество')}}</label>
                <input id="count" class="col-2 {{$product->count ? 'active' : ''}}" value="{{$product->count}}" type="text" placeholder="{{__('Количество')}}">

                <label for="article" class="col-1 align-self-start">{{__('Артикул')}}</label>
                <input id="article" class="col-2 {{$product->article ? 'active' : ''}}" value="{{$product->article}}" type="text" placeholder="{{__('Артикул')}}">

            </div>

            @slot('buttons')
                <button id="edit-btn" type="button" data-url="{{route('catalog.edit', ['business' => $slug, 'id' => $product->id])}}" data-id="{{$product->id}}" class="btn-primary">{{__('Сохранить')}}</button>
                <a href="{{route('catalog', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @elseif ($modal == 'delete')

            @slot('class')
                delete
            @endslot

            <div class="delete">
                Вы действительно<br>
                хотите удалить товар <br>
                <b>“{{$product->title}}”</b>?
            </div>
            @slot('buttons')
                <button type="button" id="delete" data-url="{{route('catalog.edit', ['business' => $slug, 'id' => $product->id])}}" data-id="{{$product->id}}" class="btn-primary">
                    {{ __('Удалить') }}
                </button>
                <a href="{{route('catalog', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
            @endslot

        @else
            @slot('class')
                view
            @endslot

            <div class="view-body flex direction-column">
                <h1>{{$product->title}}</h1>
                <span class="modal-date date">{{$product->article}}</span>
                @if (!is_null($product->img))
                    <img src="{{$product->img}}" class="img">
                @endif
                <p class="mail-text">{!! $product->text !!}</p>
                <p class="mail-text"><br>{{ $product->price }} {{__('₴')}}</p>
                <p class="mail-text">{{__('Количество')}} - {{ $product->count }} {{__('единиц')}}</p>
                <span class="modal-date date"><br>{{\Carbon\Carbon::parse($product->created_at)->format('Y-m-d')}}</span>
            </div>

            <a href="{{route('catalog', ['business' => $slug, 'load' => $load])}}" id="refresh-modal"></a>
        @endif
    @endcomponent
@endsection
@endif